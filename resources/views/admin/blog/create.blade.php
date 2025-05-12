<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">Create New Blog Post</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="block font-medium">Title</label>
                <input type="text" name="title" required class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium">Slug (URL)</label>
                <input type="text" name="slug" required class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium">Meta Description</label>
                <textarea name="meta_description" rows="2" class="w-full border rounded px-3 py-2"></textarea>
            </div>

<!-- Quill Editor Field -->
<div>
    <label class="block font-medium mb-1">Content</label>

    <!-- Hidden input to store the HTML from Quill -->
    <input type="hidden" name="content" id="content-input" />

    <!-- Quill container -->
    <div id="quill-editor" style="height: 300px;" class="bg-white border rounded"></div>

    @error('content')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>





            <div>
                <label class="block font-medium">Cover Image</label>
                <input type="file" name="cover_image" accept="image/*">
            </div>

            <div>
                <label class="block font-medium">Linked Articles</label>
                <select name="linked_articles[]" multiple class="w-full border rounded px-3 py-2">
                    @foreach ($allPosts as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-4">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                    Publish
                </button>
            </div>
        </form>
    </div>
	@if($errors->any())
    <div class="bg-red-100 text-red-700 p-4 rounded">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

	@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Quill
    var quill = new Quill('#quill-editor', {
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

    // Repopulate old content (if validation fails)
    @if(old('content'))
        quill.root.innerHTML = `{!! addslashes(old('content')) !!}`;
    @endif
    // Function to update hidden input from Quill
    function updateHiddenInput() {
        document.getElementById('content-input').value = quill.root.innerHTML;
    }

    // Update hidden input on text-change
    quill.on('text-change', function() {
        updateHiddenInput();
    });
    // Update the hidden input on submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function () {
        document.getElementById('content-input').value = quill.root.innerHTML;
    });
});
</script>



</x-app-layout>
