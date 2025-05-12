<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">Blog Posts</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto">
        <a href="{{ route('admin.blog.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            + New Post
        </a>

        <div class="mt-6 bg-white shadow rounded p-4 divide-y divide-gray-200">
			@foreach ($posts as $blog)
				<div class="py-4">
					<h3 class="text-lg font-semibold text-gray-800">{{ $blog->title }}</h3>
					<p class="text-sm text-gray-500 mb-1">/{{ $blog->slug }}</p>
					<p class="text-sm text-gray-600 mb-3">{{ Str::limit($blog->meta_description, 100) }}</p>

					<div class="flex items-center gap-4 text-sm">
						<a href="{{ route('admin.blog.edit', $blog) }}" class="text-blue-600 hover:underline">
							✏️ Edit
						</a>

						<form action="{{ route('admin.blog.destroy', $blog) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
							@csrf
							@method('DELETE')
							<button type="submit" class="text-red-600 hover:underline">
								🗑️ Delete
							</button>
						</form>
					</div>
				</div>
			@endforeach


            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
