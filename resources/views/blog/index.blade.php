<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">Latest Articles</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($posts as $post)
            <a href="{{ route('blog.show', $post->slug) }}" class="bg-white shadow rounded overflow-hidden hover:shadow-md transition">
                @if($post->cover_image)
                    <img src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-40 object-cover">
                @endif
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-800">{{ $post->title }}</h3>
                    <p class="text-sm text-gray-600 mt-2">{{ Str::limit($post->meta_description, 100) }}</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</x-app-layout>
