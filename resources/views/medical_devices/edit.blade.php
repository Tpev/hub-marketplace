<!-- resources/views/medical_devices/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Medical Device') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('medical_devices.update', $medicalDevice) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Device Name -->
                <div class="mb-4">
                    <label class="block text-gray-700">Device Name</label>
                    <input type="text" name="name" value="{{ old('name', $medicalDevice->name) }}" required class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Brand -->
                <div class="mb-4">
                    <label class="block text-gray-700">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand', $medicalDevice->brand) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-gray-700">Description</label>
                    <textarea name="description" rows="4" required class="w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $medicalDevice->description) }}</textarea>
                </div>

                <!-- Price -->
                <div class="mb-4">
                    <label class="block text-gray-700">Selling Price ($)</label>
                    <input type="number" name="price" value="{{ old('price', $medicalDevice->price) }}" step="0.01" min="0" required class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- New Price -->
                <div class="mb-4">
                    <label class="block text-gray-700">Original New Price ($)</label>
                    <input type="number" name="price_new" value="{{ old('price_new', $medicalDevice->price_new) }}" step="0.01" min="0" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Quantity -->
                <div class="mb-4">
                    <label class="block text-gray-700">Quantity</label>
                    <input type="number" name="quantity" value="{{ old('quantity', $medicalDevice->quantity) }}" min="1" required class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Condition -->
                <div class="mb-4">
                    <label class="block text-gray-700">Condition</label>
                    <select name="condition" required class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- Select Condition --</option>
                        <option value="new" {{ old('condition', $medicalDevice->condition) == 'new' ? 'selected' : '' }}>New</option>
                        <option value="used" {{ old('condition', $medicalDevice->condition) == 'used' ? 'selected' : '' }}>Used</option>
                        <option value="refurbished" {{ old('condition', $medicalDevice->condition) == 'refurbished' ? 'selected' : '' }}>Refurbished</option>
                    </select>
                </div>

                <!-- Shipping -->
                <div class="mb-4">
                    <label class="block text-gray-700">Shipping Available?</label>
                    <select name="shipping_available" required class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="1" {{ old('shipping_available', $medicalDevice->shipping_available) ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('shipping_available', $medicalDevice->shipping_available) == false ? 'selected' : '' }}>No</option>
                    </select>
                </div>

<!-- Main Category -->
<div class="mb-6">
    <label for="main_category" class="block font-medium text-gray-700">Main Category</label>
    <select name="main_category" id="main_category" required
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
        <option value="">-- Select a Category --</option>
        @foreach([
            'Anesthesia Equipment/ICU',
            'Cardiology Equipment',
            'Cosmetology equipment',
            'Dental Equipment',
            'Dental Lab Equipment',
            'Emt training',
            'Endoscopy Equipment',
            'ENT Equipment',
            'Healthcare IT, Telemedicine',
            'Home Care Rehab',
            'Hospital Equipment',
            'Imaging',
            'Laboratory Equipment',
            'Medical Consumable Supplies',
            'Medical Software and Healthcare IT',
            'Mobile Clinics',
            'Neurology Equipment',
            'OB GYN Equipment',
            'Ophthalmic Equipment',
            'Pediatric equipment',
            'Physiotherapy Equipment',
            'Sterilising Equipment',
            'Surgery Equipment',
            'Urology equipment',
            'Veterinary Equipment',
            'Wellness or Fitness Devices'
        ] as $category)
            <option value="{{ $category }}" {{ old('main_category') == $category ? 'selected' : '' }}>
                {{ $category }}
            </option>
        @endforeach
    </select>
</div>


                <!-- Aux Category -->
                <div class="mb-4">
                    <label class="block text-gray-700">Auxiliary Category</label>
                    <input type="text" name="aux_category" value="{{ old('aux_category', $medicalDevice->aux_category) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- City -->
                <div class="mb-4">
                    <label class="block text-gray-700">City</label>
                    <input type="text" name="city" value="{{ old('city', $medicalDevice->city) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- State -->
                <div class="mb-4">
                    <label class="block text-gray-700">State</label>
                    <input type="text" name="state" value="{{ old('state', $medicalDevice->state) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Country -->
                <div class="mb-4">
                    <label class="block text-gray-700">Country</label>
                    <input type="text" name="country" value="{{ old('country', $medicalDevice->country) }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Hidden Location -->
                <input type="hidden" name="location" value="{{ old('location', $medicalDevice->location) }}">

                <!-- Existing Image -->
                @if($medicalDevice->image)
                    <div class="mb-4">
                        <label class="block text-gray-700">Current Image</label>
                        <img src="{{ asset('storage/' . $medicalDevice->image) }}" class="w-32 h-32 object-cover rounded mt-2">
                    </div>
                @endif

                <!-- New Image -->
                <div class="mb-4">
                    <label class="block text-gray-700">Change Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <!-- Submit -->
                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                        Update Device
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
