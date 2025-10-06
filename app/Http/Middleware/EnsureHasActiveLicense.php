<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureHasActiveLicense
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Must be authenticated first; if not, let 'auth' middleware handle it.
        if (!$user) {
            return redirect()->route('login');
        }

        if (! $user->hasActiveLicense()) {
            // Redirect to your paywall/subscribe page
            return redirect()
                ->route('subscribe.page') // <-- rename to your actual route name
                ->with('error', 'Please subscribe to post a device.');
        }

        return $next($request);
    }
}
