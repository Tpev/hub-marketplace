<!-- resources/views/medical_devices/create.blade.php -->
<x-app-layout>
    @push('styles')
        <!-- Tailwind CSS via CDN -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Custom Styles (if any additional styles are needed) -->
        <style>
            /* You can add custom styles here if necessary */
        </style>
    @endpush

    @push('scripts')
        <!-- Optional: Add any additional scripts specific to this page -->
        <script>
            // Example: Function to preview the uploaded image
            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function(){
                    const output = document.getElementById('image-preview');
                    output.src = reader.result;
                    output.classList.remove('hidden');
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        </script>
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('List a New Medical Device') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-8">


            <form action="{{ route('medical_devices.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Device Name -->
                <div class="mb-6">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Device Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>                
				
				<!-- Device Brand -->
                <div class="mb-6">
                    <label for="brand" class="block text-gray-700 font-medium mb-2">Device Brand</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea name="description" id="description" rows="5" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <!-- Price -->
                <div class="mb-6">
                    <label for="price" class="block text-gray-700 font-medium mb-2">Price ($)</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" required step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Condition -->
                <div class="mb-6">
                    <label for="condition" class="block text-gray-700 font-medium mb-2">Condition</label>
                    <select name="condition" id="condition" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Condition --</option>
                        <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>Used</option>
                        <option value="refurbished" {{ old('condition') == 'refurbished' ? 'selected' : '' }}>Refurbished</option>
                    </select>
                </div>
				<!-- Device Location -->
                <div class="mb-6">
                    <label for="location" class="block text-gray-700 font-medium mb-2">Device location</label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <!-- Image -->
                <div class="mb-6">
                    <label for="image" class="block text-gray-700 font-medium mb-2">Device Image (optional)</label>
                    <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <!-- Image Preview -->
                    <img id="image-preview" src="#" alt="Image Preview" class="mt-4 hidden w-40 h-40 object-cover rounded-md">
                </div>

                <!-- Submit Button -->
                <div class="mt-8">
                    <button type="submit"
                        class="w-full bg-blue-600 text-white px-6 py-3 rounded-md font-medium hover:bg-blue-700 transition-colors duration-300">
                        List Device
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
