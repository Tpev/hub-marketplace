#!/usr/bin/env python3
"""
relink_sync.py

Production sync script for reLink (Shopify) -> Laravel ingestion API.

Modes:
  - full  : fetch ALL product URLs from sitemap, ingest all, then finalize (deactivate missing)
  - daily : fetch sitemap, only ingest products whose sitemap <lastmod> is newer than Laravel's max_source_lastmod,
            then finalize (deactivate missing)

Laravel API endpoints (API routes):
  POST /api/import/relink/medical-devices   (batch upsert)   expects: {"run_id": "...", "items": [...]}
  POST /api/import/relink/finalize         (finalize run)   expects: {"run_id": "..."}
  GET  /api/import/relink/status           (get max_lastmod) returns: {"max_source_lastmod": "..."} (or null)

Requirements:
  pip install requests

Usage:
  python relink_sync.py full
  python relink_sync.py daily

Requested behavior:
  - Fetch (scrape) Shopify items in chunks of 250
  - Post those chunks to Laravel in parallel (no Laravel changes)
  - Keep all other features intact (daily/full logic, finalize, retries, progress logs)

Fixes included (Python-only):
  - Ensure description_html is ALWAYS non-empty (prevents Laravel "required" 422)
  - Auto-fix + retry once on 422 when Laravel complains about description_html indices
  - Sanitize + truncate risky string fields (especially image_url)
  - Drop querystring from image_url (Shopify adds ?v=...)
  - Better 422 logging + payload sample

Note:
  - requests.Session is NOT thread-safe. We use a per-thread Session for Shopify fetch workers.
"""

from __future__ import annotations

import re
import sys
import time
import uuid
import threading
from dataclasses import dataclass
from datetime import datetime
from typing import Dict, List, Optional, Set, Tuple
from urllib.parse import urlparse, urlsplit, urlunsplit

import requests
import xml.etree.ElementTree as ET


# ============================================================
# HARD-CODED CONFIG (edit these)
# ============================================================
LARAVEL_BASE = "http://127.0.0.1:8000"  # no trailing slash
LARAVEL_TOKEN = "change-me-to-a-long-random-string"

API_INGEST_URL = f"{LARAVEL_BASE}/api/import/relink/medical-devices"
API_FINALIZE_URL = f"{LARAVEL_BASE}/api/import/relink/finalize"
API_STATUS_URL = f"{LARAVEL_BASE}/api/import/relink/status"

SITEMAP_ROOT = "https://relinkonline.com/sitemap.xml"
SHOPIFY_PRODUCT_JS_TEMPLATE = "https://relinkonline.com/products/{handle}.js"

USER_AGENT = "HUB-RelinkSync/1.6"

# Performance knobs
MAX_WORKERS = 12          # concurrent product .js fetches (per chunk)
BATCH_SIZE = 300          # kept for compatibility (not used in streaming mode)
HTTP_TIMEOUT = 45
REQUEST_RETRIES = 3
SLEEP_BETWEEN_REQUESTS = 0.08  # politeness delay

# Requested behavior knobs
SCRAPE_CHUNK_SIZE = 250        # scrape 250 items then send
POST_WORKERS = 6               # concurrent POSTs to Laravel (tune based on your server)

# Safety knobs (avoid 422 / DB truncation without Laravel changes)
MAX_LEN_IMAGE_URL = 255
MAX_LEN_VARCHAR = 255

# 422 fix knobs
DESCRIPTION_HTML_FALLBACK = "<p></p>"
# ============================================================


# -------------------------
# Pretty console helpers
# -------------------------
def ts() -> str:
    return datetime.now().strftime("%Y-%m-%d %H:%M:%S")


def log(msg: str) -> None:
    print(f"[{ts()}] {msg}", flush=True)


def pct(done: int, total: int) -> str:
    if total <= 0:
        return "0.0%"
    return f"{(done / total) * 100:.1f}%"


def fmt_rate(done: int, start_ts: float) -> str:
    elapsed = max(0.001, time.time() - start_ts)
    r = done / elapsed
    return f"{r:.2f}/s"


def fmt_eta(done: int, total: int, start_ts: float) -> str:
    if done <= 0:
        return "ETA: --"
    elapsed = time.time() - start_ts
    rate = done / max(0.001, elapsed)
    remaining = max(0, total - done)
    eta_s = int(remaining / max(0.001, rate))
    h = eta_s // 3600
    m = (eta_s % 3600) // 60
    s = eta_s % 60
    if h > 0:
        return f"ETA: {h}h{m:02d}m{s:02d}s"
    return f"ETA: {m}m{s:02d}s"


# -------------------------
# Sanitizers (Python-only)
# -------------------------
def _strip_query(u: str) -> str:
    """Remove querystring/fragment to shorten URLs (Shopify adds ?v=...)."""
    try:
        p = urlsplit(u)
        return urlunsplit((p.scheme, p.netloc, p.path, "", ""))
    except Exception:
        return u


def _safe_str(v: Optional[str], max_len: int = MAX_LEN_VARCHAR) -> Optional[str]:
    if v is None:
        return None
    s = str(v)
    if len(s) <= max_len:
        return s
    return s[:max_len]


def _safe_image_url(u: Optional[str]) -> Optional[str]:
    if not u:
        return None
    u = _strip_query(u)
    return _safe_str(u, MAX_LEN_IMAGE_URL)


def _safe_nonempty_html(v: Optional[str]) -> str:
    """
    Laravel validation often uses `required` for description_html.
    `required` fails on empty string -> ensure it's always non-empty.
    """
    s = (v or "").strip()
    return s if s else DESCRIPTION_HTML_FALLBACK


def normalize_ingest_item(item: Dict) -> Dict:
    """
    Last line of defense before POST:
      - ensure required keys exist and are not empty where Laravel expects required
      - ensure description_html is non-empty (prevents 422)
      - keep existing sanitization behavior
    """
    item = dict(item or {})

    # Make sure keys exist (even if blank)
    item["external_id"] = (item.get("external_id") or "").strip()
    item["source_url"] = (item.get("source_url") or "").strip() or _safe_str(item.get("source_url"), 255) or ""
    item["name"] = (item.get("name") or "").strip() or "Untitled"

    # The important one:
    item["description_html"] = _safe_nonempty_html(item.get("description_html"))

    # Keep safety truncations
    item["source_url"] = _safe_str(item["source_url"], 255) or item["source_url"]
    if "image_url" in item:
        item["image_url"] = _safe_image_url(item.get("image_url"))

    return item


@dataclass
class SitemapEntry:
    loc: str
    lastmod: Optional[str]  # ISO8601 string with offset, e.g. 2025-12-30T17:04:05-05:00


def _session(headers: Dict[str, str]) -> requests.Session:
    s = requests.Session()
    s.headers.update(headers)
    return s


def _get_bytes(url: str, s: requests.Session) -> bytes:
    r = s.get(url, timeout=HTTP_TIMEOUT)
    r.raise_for_status()
    return r.content


def _parse_xml(xml_bytes: bytes) -> Tuple[str, ET.Element]:
    root = ET.fromstring(xml_bytes)
    tag = root.tag.split("}")[-1].lower()
    return tag, root


def _sitemap_index_locs(root: ET.Element) -> List[str]:
    locs: List[str] = []
    for sm in root.findall(".//{*}sitemap"):
        loc_el = sm.find("{*}loc")
        if loc_el is not None and loc_el.text:
            locs.append(loc_el.text.strip())
    return locs


def _urlset_entries(root: ET.Element) -> List[SitemapEntry]:
    out: List[SitemapEntry] = []
    for url_el in root.findall(".//{*}url"):
        loc_el = url_el.find("{*}loc")
        if loc_el is None or not loc_el.text:
            continue
        loc = loc_el.text.strip()
        lastmod_el = url_el.find("{*}lastmod")
        lastmod = lastmod_el.text.strip() if (lastmod_el is not None and lastmod_el.text) else None
        out.append(SitemapEntry(loc=loc, lastmod=lastmod))
    return out


def _is_product_url(url: str) -> bool:
    p = urlparse(url)
    return p.netloc and p.path.startswith("/products/")


def _handle_from_product_url(product_url: str) -> Optional[str]:
    p = urlparse(product_url)
    m = re.match(r"^/products/([^/?#]+)$", p.path)
    return m.group(1) if m else None


def _parse_iso(dt_str: Optional[str]) -> Optional[datetime]:
    """
    Parse ISO 8601 timestamps with offsets (e.g. 2025-12-30T17:04:05-05:00).
    Returns aware datetime (if offset present).
    """
    if not dt_str:
        return None
    try:
        return datetime.fromisoformat(dt_str.replace("Z", "+00:00"))
    except Exception:
        return None


def crawl_product_entries_from_sitemaps() -> List[SitemapEntry]:
    """
    Crawls sitemap.xml, handling sitemapindex and urlset, returns all product URL entries.
    """
    visited: Set[str] = set()
    queue: List[str] = [SITEMAP_ROOT]
    products: List[SitemapEntry] = []

    with _session({"User-Agent": USER_AGENT, "Accept": "application/xml,text/xml,*/*"}) as s:
        start = time.time()
        log(f"Sitemap crawl started: {SITEMAP_ROOT}")

        while queue:
            url = queue.pop(0)
            if url in visited:
                continue
            visited.add(url)

            try:
                xml_bytes = _get_bytes(url, s)
            except Exception as e:
                log(f"WARNING sitemap fetch failed: {url} ({e})")
                continue

            tag, root = _parse_xml(xml_bytes)

            if tag == "sitemapindex":
                new_locs = _sitemap_index_locs(root)
                queue.extend(new_locs)
                log(f"Sitemapindex: visited={len(visited)} discovered_queue={len(queue)} (+{len(new_locs)})")
            else:
                entries = _urlset_entries(root)
                added = 0
                for entry in entries:
                    if _is_product_url(entry.loc):
                        products.append(entry)
                        added += 1
                log(f"Urlset: visited={len(visited)} +products={added} total_products={len(products)}")

            time.sleep(SLEEP_BETWEEN_REQUESTS)

        log(f"Sitemap crawl finished: products={len(products)} elapsed={time.time()-start:.1f}s")

    return products


def laravel_get_max_lastmod() -> Optional[datetime]:
    """
    Calls GET /api/import/relink/status and returns max_source_lastmod as datetime.
    Expected JSON: {"ok": true, "max_source_lastmod": "...." }
    """
    headers = {
        "User-Agent": USER_AGENT,
        "Authorization": f"Bearer {LARAVEL_TOKEN}",
        "Accept": "application/json",
    }
    with _session(headers) as s:
        r = s.get(API_STATUS_URL, timeout=HTTP_TIMEOUT)
        r.raise_for_status()
        data = r.json()
        return _parse_iso(data.get("max_source_lastmod"))


def fetch_shopify_product_json(handle: str, s: requests.Session) -> Dict:
    url = SHOPIFY_PRODUCT_JS_TEMPLATE.format(handle=handle)
    r = s.get(url, timeout=HTTP_TIMEOUT, headers={"User-Agent": USER_AGENT, "Accept": "application/json,*/*"})
    r.raise_for_status()
    return r.json()


def _absolutize_image_url(u: str) -> str:
    return ("https:" + u) if u.startswith("//") else u


def _pick_city_state_from_tags(tags: List[str]) -> Tuple[Optional[str], Optional[str]]:
    """
    Sample tags can contain 'Location_Twinsburg OH' etc.
    Parse that into (city, state) naive.
    """
    loc_tag = next((t for t in tags if t.startswith("Location_")), None)
    if not loc_tag:
        return None, None
    raw = loc_tag[len("Location_"):].strip()
    if not raw:
        return None, None

    parts = raw.split()
    if len(parts) >= 2 and len(parts[-1]) == 2 and parts[-1].isalpha():
        return " ".join(parts[:-1]).strip() or None, parts[-1].upper()
    return raw, None


def _normalize_condition(shopify_json: Dict) -> str:
    variants = shopify_json.get("variants") or []
    if variants:
        t = (variants[0].get("title") or "").strip().lower()
        if "refurb" in t:
            return "refurbished"
        if "new" in t:
            return "new"
        if "used" in t or "pre-owned" in t:
            return "used"
    desc = (shopify_json.get("description") or "").lower()
    if "condition:" in desc and "refurb" in desc:
        return "refurbished"
    if "condition:" in desc and "new" in desc:
        return "new"
    if "condition:" in desc and "used" in desc:
        return "used"
    return "used"


def build_ingest_item(product_url: str, lastmod: Optional[str], shopify_json: Dict) -> Dict:
    """
    Maps Shopify product JSON to Laravel ingestion payload item schema.
    Adds sanitization to avoid 422 / truncation issues without changing Laravel.
    """
    tags = shopify_json.get("tags") or []
    vendor = shopify_json.get("vendor") or None
    title = shopify_json.get("title") or "Untitled"

    price_cents = shopify_json.get("price") or 0
    price = float(price_cents) / 100.0

    city, state = _pick_city_state_from_tags(tags)

    image_url = shopify_json.get("featured_image") or None
    if not image_url:
        imgs = shopify_json.get("images") or []
        if imgs:
            image_url = imgs[0]
    if isinstance(image_url, str):
        image_url = _absolutize_image_url(image_url)
        image_url = _safe_image_url(image_url)  # drop query + truncate to 255
    else:
        image_url = None

    external_id = shopify_json.get("id")
    external_id_str = str(external_id) if external_id else ""

    # IMPORTANT: ensure non-empty HTML to satisfy Laravel "required" rule
    description_html = _safe_nonempty_html(shopify_json.get("description"))

    item = {
        "external_id": _safe_str(external_id_str, 64) or "",
        "source_url": _safe_str(product_url, 255) or product_url,
        "lastmod": lastmod,
        "name": _safe_str(title, 255) or "Untitled",
        "brand": _safe_str(vendor, 255),
        "description_html": description_html,
        "condition": _safe_str(_normalize_condition(shopify_json), 32) or "used",
        "price": round(price, 2),
        "price_new": None,
        "quantity": 1,
        "shipping_available": True,
        "main_category": _safe_str(shopify_json.get("type") or None, 255),
        "aux_category": None,
        "city": _safe_str(city, 255),
        "state": _safe_str(state, 8),
        "country": "USA",
        "image_url": image_url,
    }

    return normalize_ingest_item(item)


def _extract_422_body(resp: requests.Response):
    try:
        return resp.json()
    except Exception:
        return resp.text


def _try_autofix_422(payload: Dict, body) -> bool:
    """
    If Laravel complains about items.{idx}.description_html required,
    patch those indices and return True (so caller can retry once).
    """
    if not isinstance(payload, dict):
        return False
    items = payload.get("items")
    if not isinstance(items, list) or not items:
        return False
    if not isinstance(body, dict):
        return False

    errors = body.get("errors")
    if not isinstance(errors, dict) or not errors:
        return False

    # Find indices for description_html errors
    bad_idxs: Set[int] = set()
    for k in errors.keys():
        # expected: items.215.description_html
        if not isinstance(k, str):
            continue
        if not k.endswith(".description_html"):
            continue
        parts = k.split(".")
        if len(parts) >= 3 and parts[0] == "items":
            try:
                bad_idxs.add(int(parts[1]))
            except Exception:
                pass

    if not bad_idxs:
        return False

    max_i = len(items) - 1
    bad_idxs = {i for i in bad_idxs if 0 <= i <= max_i}
    if not bad_idxs:
        return False

    # Patch those indices
    for i in sorted(bad_idxs):
        it = items[i] if isinstance(items[i], dict) else {}
        ext = it.get("external_id")
        url = it.get("source_url")
        log(f"Auto-fix 422: patching description_html idx={i} external_id={ext} url={url}")
        if not isinstance(it, dict):
            it = {}
            items[i] = it
        it["description_html"] = DESCRIPTION_HTML_FALLBACK

    # Also normalize everything else (cheap and safe)
    payload["items"] = [normalize_ingest_item(it) for it in items]
    return True


def _post_with_retries(url: str, json_payload: Dict) -> Dict:
    headers = {
        "User-Agent": USER_AGENT,
        "Authorization": f"Bearer {LARAVEL_TOKEN}",
        "Accept": "application/json",
        "Content-Type": "application/json",
    }

    last_err = None
    for attempt in range(1, REQUEST_RETRIES + 1):
        try:
            with _session(headers) as s:
                # We allow ONE auto-fix retry on 422 per attempt.
                for fix_round in range(2):
                    r = s.post(url, json=json_payload, timeout=HTTP_TIMEOUT)

                    if r.status_code == 422:
                        body = _extract_422_body(r)
                        log(f"422 from Laravel. Response body: {body}")

                        items = (json_payload or {}).get("items") or []
                        if items:
                            sample = items[0] if isinstance(items[0], dict) else {}
                            log(
                                "422 payload sample[0]: "
                                f"external_id={sample.get('external_id')} "
                                f"source_url_len={len(sample.get('source_url') or '')} "
                                f"image_url_len={len(sample.get('image_url') or '')} "
                                f"description_html_len={len(sample.get('description_html') or '')}"
                            )

                        # Try to auto-fix description_html-required errors and retry once.
                        if fix_round == 0 and _try_autofix_422(json_payload, body):
                            log("422 auto-fix applied. Retrying POST once with patched payload…")
                            continue

                        r.raise_for_status()

                    r.raise_for_status()
                    return r.json()

        except Exception as e:
            last_err = e
            if attempt == REQUEST_RETRIES:
                raise
            time.sleep(1.2 * attempt)

    raise RuntimeError(str(last_err))


def laravel_post_batch(run_id: str, items: List[Dict]) -> Dict:
    # Normalize everything right before send
    safe_items = [normalize_ingest_item(it) for it in (items or [])]
    payload = {"run_id": run_id, "items": safe_items}
    return _post_with_retries(API_INGEST_URL, payload)


def laravel_finalize(run_id: str) -> Dict:
    payload = {"run_id": run_id}
    return _post_with_retries(API_FINALIZE_URL, payload)


def chunked(items: List, size: int):
    for i in range(0, len(items), size):
        yield items[i: i + size]


# ============================================================
# NEW: scrape 250 then POST in parallel while continuing scrape
# ============================================================
def fetch_items_for_entries(entries: List[SitemapEntry]) -> List[Dict]:
    """
    Fetch Shopify product JSON for the provided entries concurrently and build ingest items.
    Returns only valid items with external_id.

    IMPORTANT: requests.Session is not thread-safe -> use a per-thread Session.
    """
    from concurrent.futures import ThreadPoolExecutor, as_completed

    results: List[Dict] = []
    tls = threading.local()

    def get_thread_session() -> requests.Session:
        sess = getattr(tls, "sess", None)
        if sess is None:
            sess = requests.Session()
            sess.headers.update({"User-Agent": USER_AGENT, "Accept": "application/json,*/*"})
            tls.sess = sess
        return sess

    def worker(entry: SitemapEntry) -> Optional[Dict]:
        handle = _handle_from_product_url(entry.loc)
        if not handle:
            return None

        for attempt in range(1, REQUEST_RETRIES + 1):
            try:
                sess = get_thread_session()
                data = fetch_shopify_product_json(handle, sess)
                item = build_ingest_item(entry.loc, entry.lastmod, data)
                if not item.get("external_id"):
                    return None
                return item
            except Exception:
                if attempt == REQUEST_RETRIES:
                    return None
                time.sleep(0.8 * attempt)
        return None

    with ThreadPoolExecutor(max_workers=MAX_WORKERS) as ex:
        futures = [ex.submit(worker, e) for e in entries]
        for fut in as_completed(futures):
            item = fut.result()
            if item:
                results.append(item)
            time.sleep(SLEEP_BETWEEN_REQUESTS)

    # Close thread sessions if they exist (best-effort)
    # Not strictly required, but nice.
    try:
        sess = getattr(tls, "sess", None)
        if sess:
            sess.close()
    except Exception:
        pass

    return results


def ingest_streaming_parallel(run_id: str, entries: List[SitemapEntry]) -> Tuple[int, int]:
    """
    Behavior:
      - scrape 250 entries at a time
      - submit those batches to Laravel in parallel
    Keeps retries + timestamp progress logs.
    Returns (total_created, total_updated).
    """
    from concurrent.futures import ThreadPoolExecutor, as_completed

    total_entries = len(entries)
    created_total = 0
    updated_total = 0

    scrape_chunks = list(chunked(entries, SCRAPE_CHUNK_SIZE))
    total_scrape_chunks = len(scrape_chunks)

    post_pool = ThreadPoolExecutor(max_workers=POST_WORKERS)
    post_futures = []

    start = time.time()
    posted_batches_done = 0

    def post_job(batch_items: List[Dict], batch_idx: int) -> Dict:
        resp = laravel_post_batch(run_id, batch_items)
        return {
            "batch_idx": batch_idx,
            "size": len(batch_items),
            "created": int(resp.get("created", 0) or 0),
            "updated": int(resp.get("updated", 0) or 0),
        }

    log(
        f"Streaming ingest started: entries={total_entries} "
        f"scrape_chunk={SCRAPE_CHUNK_SIZE} fetch_workers={MAX_WORKERS} post_workers={POST_WORKERS}"
    )

    for sc_idx, entry_chunk in enumerate(scrape_chunks, start=1):
        sc_start = time.time()
        log(
            f"Scrape chunk {sc_idx}/{total_scrape_chunks} ({pct(sc_idx, total_scrape_chunks)}) "
            f"entries_in_chunk={len(entry_chunk)}"
        )

        items = fetch_items_for_entries(entry_chunk)

        log(
            f"Scrape chunk {sc_idx} done: items_ready={len(items)} "
            f"elapsed={time.time()-sc_start:.1f}s"
        )

        if items:
            post_futures.append(post_pool.submit(post_job, items, sc_idx))

        # Drain completed posts (so logs update while we continue scraping)
        for f in list(post_futures):
            if f.done():
                post_futures.remove(f)
                res = f.result()
                posted_batches_done += 1
                created_total += res["created"]
                updated_total += res["updated"]

                log(
                    f"POST complete batch {res['batch_idx']}/{total_scrape_chunks} "
                    f"({pct(posted_batches_done, total_scrape_chunks)}) "
                    f"size={res['size']} created={res['created']} updated={res['updated']} "
                    f"totals(created={created_total}, updated={updated_total}) "
                    f"rate={fmt_rate(posted_batches_done, start)} {fmt_eta(posted_batches_done, total_scrape_chunks, start)}"
                )

        time.sleep(SLEEP_BETWEEN_REQUESTS)

    # Wait for remaining posts
    if post_futures:
        log(f"Waiting for remaining POST jobs: remaining={len(post_futures)}")
        for f in as_completed(post_futures):
            res = f.result()
            posted_batches_done += 1
            created_total += res["created"]
            updated_total += res["updated"]
            log(
                f"POST complete batch {res['batch_idx']}/{total_scrape_chunks} "
                f"({pct(posted_batches_done, total_scrape_chunks)}) "
                f"size={res['size']} created={res['created']} updated={res['updated']} "
                f"totals(created={created_total}, updated={updated_total}) "
                f"rate={fmt_rate(posted_batches_done, start)} {fmt_eta(posted_batches_done, total_scrape_chunks, start)}"
            )

    post_pool.shutdown(wait=True)

    log(
        f"Streaming ingest finished: posted_batches={posted_batches_done}/{total_scrape_chunks} "
        f"created={created_total} updated={updated_total} elapsed={time.time()-start:.1f}s"
    )
    return created_total, updated_total


def main() -> int:
    mode = "daily"
    if len(sys.argv) >= 2:
        mode = sys.argv[1].strip().lower()

    if mode not in ("full", "daily"):
        print("Usage: python relink_sync.py [full|daily]")
        return 2

    run_id = uuid.uuid4().hex
    log(f"Starting reLink sync mode={mode} run_id={run_id}")
    log(f"Laravel ingest:   {API_INGEST_URL}")
    log(f"Laravel finalize: {API_FINALIZE_URL}")
    log(f"Laravel status:   {API_STATUS_URL}")
    log(f"Sitemap root:     {SITEMAP_ROOT}")
    print("")

    try:
        overall_start = time.time()

        # 1) Crawl sitemap(s)
        log("Step 1/5: Crawling sitemap(s)…")
        entries_all = crawl_product_entries_from_sitemaps()
        log(f"Step 1/5 complete: Sitemap product URLs discovered: {len(entries_all)}")
        print("")

        # 2) Decide what to fetch in daily mode
        log("Step 2/5: Filtering entries (mode-dependent)…")
        entries = entries_all
        if mode == "daily":
            max_lastmod = laravel_get_max_lastmod()
            log(f"Laravel max_source_lastmod: {max_lastmod.isoformat() if max_lastmod else 'None'}")

            if max_lastmod:
                filtered: List[SitemapEntry] = []
                missing_lastmod = 0
                for e in entries_all:
                    dt = _parse_iso(e.lastmod)
                    if dt is None:
                        missing_lastmod += 1
                        continue
                    if dt > max_lastmod:
                        filtered.append(e)

                entries = filtered
                log(f"Daily filter -> {len(entries)} to fetch (skipped missing lastmod: {missing_lastmod})")
            else:
                log("No previous lastmod found in Laravel; daily will behave like full.")
                entries = entries_all

        log("Step 2/5 complete.")
        print("")

        # If nothing changed, still finalize to clean removals? (optional)
        if not entries:
            log("No changed products to ingest. Step 5/5: Finalizing run to handle removals…")
            fin = laravel_finalize(run_id)
            log(f"Finalize response: {fin}")
            log("Done.")
            return 0

        # 3-4) Streaming scrape+post
        log(f"Step 3-4/5: Streaming scrape+post for {len(entries)} products…")
        total_created, total_updated = ingest_streaming_parallel(run_id, entries)
        log(f"Step 3-4/5 complete: created_total={total_created} updated_total={total_updated}")
        print("")

        # 5) Finalize run (deactivate missing)
        log("Step 5/5: Finalizing run (deactivate missing)…")
        fin = laravel_finalize(run_id)
        log(f"Finalize response: {fin}")
        log("Step 5/5 complete.")
        print("")

        log(
            f"SUCCESS. created={total_created} updated={total_updated} run_id={run_id} "
            f"elapsed={time.time()-overall_start:.1f}s"
        )
        return 0

    except Exception as e:
        print("")
        log(f"ERROR: {str(e)}")
        log(f"Run failed. run_id={run_id}")
        return 1


if __name__ == "__main__":
    raise SystemExit(main())
