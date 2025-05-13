<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 leading-tight">
            {{ __('Edit Medical Device') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-3xl mx-auto bg-white shadow-md rounded-xl p-8 space-y-6">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="p-4 bg-red-100 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('medical_devices.update', $medicalDevice) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Name + Brand --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Device Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $medicalDevice->name) }}" required
                            class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                        <input type="text" name="brand" id="brand" value="{{ old('brand', $medicalDevice->brand) }}"
                            class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" required
                        class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $medicalDevice->description) }}</textarea>
                </div>

                {{-- Price Block --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Selling Price ($)</label>
                        <input type="number" name="price" id="price" step="0.01" min="0" required
                            value="{{ old('price', $medicalDevice->price) }}"
                            class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="price_new" class="block text-sm font-medium text-gray-700">Original New Price ($)</label>
                        <input type="number" name="price_new" id="price_new" step="0.01" min="0"
                            value="{{ old('price_new', $medicalDevice->price_new) }}"
                            class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Quantity + Condition --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" name="quantity" id="quantity" min="1" required
                            value="{{ old('quantity', $medicalDevice->quantity) }}"
                            class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="condition" class="block text-sm font-medium text-gray-700">Condition</label>
                        <select name="condition" id="condition" required
                            class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Select Condition --</option>
                            <option value="new" {{ old('condition', $medicalDevice->condition) === 'new' ? 'selected' : '' }}>New</option>
                            <option value="used" {{ old('condition', $medicalDevice->condition) === 'used' ? 'selected' : '' }}>Used</option>
                            <option value="refurbished" {{ old('condition', $medicalDevice->condition) === 'refurbished' ? 'selected' : '' }}>Refurbished</option>
                        </select>
                    </div>
                </div>

                {{-- Shipping --}}
                <div>
                    <label for="shipping_available" class="block text-sm font-medium text-gray-700">Shipping Available?</label>
                    <select name="shipping_available" id="shipping_available" required
                        class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                        <option value="1" {{ old('shipping_available', $medicalDevice->shipping_available) ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('shipping_available', $medicalDevice->shipping_available) === false ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                {{-- Categories --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="main_category" class="block text-sm font-medium text-gray-700">Main Category</label>
                        <select name="main_category" id="main_category" required
                            class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Select a Category --</option>
                            @foreach([
                                'Anesthesia Equipment/ICU','Cardiology Equipment','Cosmetology equipment',
                                'Dental Equipment','Dental Lab Equipment','Emt training','Endoscopy Equipment',
                                'ENT Equipment','Healthcare IT, Telemedicine','Home Care Rehab','Hospital Equipment',
                                'Imaging','Laboratory Equipment','Medical Consumable Supplies','Medical Software and Healthcare IT',
                                'Mobile Clinics','Neurology Equipment','OB GYN Equipment','Ophthalmic Equipment',
                                'Pediatric equipment','Physiotherapy Equipment','Sterilising Equipment',
                                'Surgery Equipment','Urology equipment','Veterinary Equipment','Wellness or Fitness Devices'
                            ] as $category)
                                <option value="{{ $category }}" {{ old('main_category', $medicalDevice->main_category) === $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="aux_category" class="block text-sm font-medium text-gray-700">Auxiliary Category</label>
                        <input type="text" name="aux_category" id="aux_category"
                            value="{{ old('aux_category', $medicalDevice->aux_category) }}"
                            class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Location --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $medicalDevice->city) }}"
                            class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                        <input type="text" name="state" id="state" value="{{ old('state', $medicalDevice->state) }}"
                            class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                        <input type="text" name="country" id="country" value="{{ old('country', $medicalDevice->country) }}"
                            class="mt-1 w-full px-4 py-2 border rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <input type="hidden" name="location" value="{{ old('location', $medicalDevice->location) }}">

                {{-- Current Image --}}
                @if($medicalDevice->image)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Image</label>
                        <img src="{{ asset('storage/' . $medicalDevice->image) }}"
                             alt="Current Device Image"
                             class="mt-2 w-40 h-40 object-cover rounded-md border">
                    </div>
                @endif

                {{-- Image Upload --}}
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Change Image</label>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                </div>

                {{-- Submit --}}
                <div class="pt-6">
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-md transition">
                        💾 Update Device
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
