<x-app-layout>
    <x-slot name="header">
        <title>{{ $post->title }}</title>
        <meta name="description" content="{{ $post->meta_description }}">
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 bg-white p-8 rounded-lg shadow">
            <h1 class="text-4xl font-extrabold text-gray-900 leading-tight mb-2 tracking-tight">
                {{ $post->title }}
            </h1>
            <p class="text-sm text-gray-500 mb-6">Published on {{ $post->created_at->format('F j, Y') }}</p>

            @if($post->cover_image)
                <img src="{{ asset('storage/' . $post->cover_image) }}" alt="Cover image for {{ $post->title }}"
                     class="w-full rounded-lg mb-8 shadow-sm">
            @endif

<div class="quill-content text-gray-800 leading-relaxed">
    {!! $post->content !!}
</div>


            <!-- Linked Articles -->
            @if($linked->count())
                <div class="mt-12 pt-6 border-t border-gray-200">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-3">Related Articles</h3>
                    <ul class="list-disc list-inside text-green-700 space-y-1">
                        @foreach ($linked as $l)
                            <li>
                                <a href="{{ route('blog.show', $l->slug) }}" class="hover:underline font-medium">
                                    {{ $l->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
<style>
    /* === Quill Font Sizes === */
    .ql-size-small { font-size: 0.875rem; }    /* Tailwind text-sm */
    .ql-size-large { font-size: 1.25rem; }     /* Tailwind text-lg */
    .ql-size-huge  { font-size: 1.5rem; }      /* Tailwind text-2xl */

    /* === Quill Text Colors === */
    .ql-color-red   { color: #e3342f; }
    .ql-color-blue  { color: #3490dc; }
    .ql-color-green { color: #38a169; }
    .ql-color-orange { color: #f6993f; }

    /* Catch-all inline Quill color styling */
    [class^="ql-color-"] {
        color: inherit;
    }

    /* === Quill Alignments === */
    .ql-align-center { text-align: center; }
    .ql-align-right  { text-align: right; }
    .ql-align-justify { text-align: justify; }

    /* === Font Families (if used via Quill config) === */
    .ql-font-serif { font-family: Georgia, serif; }
    .ql-font-monospace { font-family: monospace; }
    .ql-font-sans { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }

    /* === Layout Tweaks for Blog Readability === */
    .quill-content p {
        margin-bottom: 1rem;
        line-height: 1.75;
    }

    .quill-content h1 {
        font-size: 2rem;
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: bold;
    }

    .quill-content h2 {
        font-size: 1.5rem;
        margin-top: 1.75rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .quill-content h3 {
        font-size: 1.25rem;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .quill-content ul,
    .quill-content ol {
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }

    .quill-content ul li::marker {
        color: #38a169; /* Tailwind green-600 */
    }

    .quill-content blockquote {
        border-left: 4px solid #cbd5e0;
        padding-left: 1rem;
        margin: 1.5rem 0;
        color: #4a5568;
        font-style: italic;
        background-color: #f7fafc;
    }

    .quill-content img {
        max-width: 100%;
        border-radius: 0.5rem;
        margin: 1rem 0;
    }
</style>

