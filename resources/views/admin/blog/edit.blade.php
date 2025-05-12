<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">Edit Blog Post</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <form action="{{ route('admin.blog.update', ['blog' => $blogPost->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium">Title</label>
                <input type="text" name="title" value="{{ old('title', $blogPost->title) }}" required class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium">Slug (URL)</label>
                <input type="text" name="slug" value="{{ old('slug', $blogPost->slug) }}" required class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium">Meta Description</label>
                <textarea name="meta_description" rows="2" class="w-full border rounded px-3 py-2">{{ old('meta_description', $blogPost->meta_description) }}</textarea>
            </div>

            <!-- Quill Editor -->
            <div>
                <label class="block font-medium mb-1">Content</label>
                <input type="hidden" name="content" id="content-input">
                <div id="quill-editor" class="bg-white border rounded min-h-[300px]">
                    {!! old('content', $blogPost->content) !!}
                </div>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-medium">Cover Image</label>
                @if($blogPost->cover_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $blogPost->cover_image) }}" class="h-24 rounded shadow">
                    </div>
                @endif
                <input type="file" name="cover_image" accept="image/*">
            </div>

            <div>
                <label class="block font-medium">Linked Articles</label>
                <select name="linked_articles[]" multiple class="w-full border rounded px-3 py-2">
                    @foreach ($allPosts as $p)
                        <option value="{{ $p->id }}" {{ in_array($p->id, old('linked_articles', $linkedIds)) ? 'selected' : '' }}>
                            {{ $p->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                    Update Post
                </button>
            </div>
        </form>
    </div>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mt-6 max-w-4xl mx-auto">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Write your blog content here...',
                modules: {
                    toolbar: [
                        [{ 'font': [] }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],
                        [{ 'header': 1 }, { 'header': 2 }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            // Repopulate Quill with old or existing content
            @if(old('content'))
                quill.root.innerHTML = `{!! addslashes(old('content')) !!}`;
            @else
                quill.root.innerHTML = `{!! addslashes($blogPost->content) !!}`;
            @endif

            function updateHiddenInput() {
                document.getElementById('content-input').value = quill.root.innerHTML;
            }

            quill.on('text-change', function () {
                updateHiddenInput();
            });

            const form = document.querySelector('form');
            form.addEventListener('submit', function () {
                updateHiddenInput();
            });
        });
    </script>
@endpush

</x-app-layout>
