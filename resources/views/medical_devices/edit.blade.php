<!-- resources/views/medical_devices/edit.blade.php -->
<x-app-layout>
    @push('styles')
        <!-- Add any additional styles specific to this page -->
    @endpush

    @push('scripts')
        <!-- Add any additional scripts specific to this page -->
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Medical Device') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto bg-white shadow-md rounded-lg p-6">
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form action="{{ route('medical_devices.update', $medicalDevice) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Device Name -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Device Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $medicalDevice->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="block text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $medicalDevice->description) }}</textarea>
                </div>

                <!-- Price -->
                <div class="mb-4">
                    <label for="price" class="block text-gray-700">Price ($)</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $medicalDevice->price) }}" required step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Condition -->
                <div class="mb-4">
                    <label for="condition" class="block text-gray-700">Condition</label>
                    <select name="condition" id="condition" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Select Condition --</option>
                        <option value="new" {{ old('condition', $medicalDevice->condition) == 'new' ? 'selected' : '' }}>New</option>
                        <option value="used" {{ old('condition', $medicalDevice->condition) == 'used' ? 'selected' : '' }}>Used</option>
                        <option value="refurbished" {{ old('condition', $medicalDevice->condition) == 'refurbished' ? 'selected' : '' }}>Refurbished</option>
                    </select>
                </div>

                <!-- Current Image -->
                @if($medicalDevice->image)
                    <div class="mb-4">
                        <label class="block text-gray-700">Current Image</label>
                        <img src="{{ asset('storage/' . $medicalDevice->image) }}" alt="{{ $medicalDevice->name }}" class="w-32 h-32 object-cover mt-2">
                    </div>
                @endif

                <!-- New Image -->
                <div class="mb-4">
                    <label for="image" class="block text-gray-700">Change Image (optional)</label>
                    <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full">
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-secondary transition-colors">
                        Update Device
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
