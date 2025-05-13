<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 leading-tight">
            {{ __('List a New Medical Device') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-3xl mx-auto bg-white shadow-md rounded-xl p-8 space-y-8">

            <form action="{{ route('medical_devices.store') }}" method="POST" enctype="multipart/form-data" onsubmit="combineLocation()">
                @csrf

                {{-- Basic Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Device Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                        <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="description" rows="5" required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                </div>

                {{-- Pricing --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Seller’s Price ($) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" required step="0.01" min="0"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="price_new" class="block text-sm font-medium text-gray-700">Retail (New) Price ($)</label>
                        <input type="number" name="price_new" id="price_new" value="{{ old('price_new') }}" step="0.01" min="0"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Quantity and Condition --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" required min="1"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="condition" class="block text-sm font-medium text-gray-700">Condition <span class="text-red-500">*</span></label>
                        <select name="condition" id="condition" required
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Select Condition --</option>
                            <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>Used</option>
                            <option value="refurbished" {{ old('condition') == 'refurbished' ? 'selected' : '' }}>Refurbished</option>
                        </select>
                    </div>
                </div>

                {{-- Shipping --}}
                <div>
                    <label for="shipping_available" class="block text-sm font-medium text-gray-700">Shipping Available? <span class="text-red-500">*</span></label>
                    <select name="shipping_available" id="shipping_available" required
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="1" {{ old('shipping_available') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('shipping_available') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                {{-- Categories --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="main_category" class="block text-sm font-medium text-gray-700">Main Category <span class="text-red-500">*</span></label>
                        <select name="main_category" id="main_category" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
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
                                <option value="{{ $category }}" {{ old('main_category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="aux_category" class="block text-sm font-medium text-gray-700">Auxiliary Category</label>
                        <input type="text" name="aux_category" id="aux_category" value="{{ old('aux_category') }}"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Location --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                        <input type="text" name="state" id="state" value="{{ old('state') }}"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                        <input type="text" name="country" id="country" value="{{ old('country') }}"
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <input type="hidden" name="location" id="location">

                {{-- Image Upload --}}
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Device Image</label>
                    <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                    <img id="image-preview" class="hidden mt-4 w-40 h-40 object-cover rounded-md" />
                </div>

                {{-- Submit --}}
                <div class="pt-6">
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-md transition">
                        📦 List Device
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function () {
                    const preview = document.getElementById('image-preview');
                    preview.src = reader.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(event.target.files[0]);
            }

            function combineLocation() {
                const city = document.getElementById('city').value || '';
                const state = document.getElementById('state').value || '';
                const country = document.getElementById('country').value || '';
                document.getElementById('location').value = `${city} ${state} ${country}`.trim();
            }
        </script>
    @endpush
</x-app-layout>
