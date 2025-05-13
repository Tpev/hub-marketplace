<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('List a New Medical Device') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-8">
            <form action="{{ route('medical_devices.store') }}" method="POST" enctype="multipart/form-data" onsubmit="combineLocation()">
                @csrf

                <!-- Device Name -->
                <div class="mb-6">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Device Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>                

                <!-- Brand -->
                <div class="mb-6">
                    <label for="brand" class="block text-gray-700 font-medium mb-2">Brand</label>
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
                    <label for="price" class="block text-gray-700 font-medium mb-2">Selling Price ($)</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" required step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- New Price -->
                <div class="mb-6">
                    <label for="price_new" class="block text-gray-700 font-medium mb-2">Original New Price ($)</label>
                    <input type="number" name="price_new" id="price_new" value="{{ old('price_new') }}" step="0.01" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Quantity -->
                <div class="mb-6">
                    <label for="quantity" class="block text-gray-700 font-medium mb-2">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" required min="1"
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

                <!-- Shipping -->
                <div class="mb-6">
                    <label for="shipping_available" class="block text-gray-700 font-medium mb-2">Shipping Available?</label>
                    <select name="shipping_available" id="shipping_available" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1" {{ old('shipping_available') == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('shipping_available') == '0' ? 'selected' : '' }}>No</option>
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
                <div class="mb-6">
                    <label for="aux_category" class="block text-gray-700 font-medium mb-2">Auxiliary Category</label>
                    <input type="text" name="aux_category" id="aux_category" value="{{ old('aux_category') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Location Fields -->
                <div class="mb-6">
                    <label for="city" class="block text-gray-700 font-medium mb-2">City</label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-6">
                    <label for="state" class="block text-gray-700 font-medium mb-2">State</label>
                    <input type="text" name="state" id="state" value="{{ old('state') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-6">
                    <label for="country" class="block text-gray-700 font-medium mb-2">Country</label>
                    <input type="text" name="country" id="country" value="{{ old('country') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Legacy Location (hidden) -->
                <input type="hidden" name="location" id="location">

                <!-- Image -->
                <div class="mb-6">
                    <label for="image" class="block text-gray-700 font-medium mb-2">Device Image</label>
                    <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md">
                    <img id="image-preview" class="hidden mt-4 w-40 h-40 object-cover rounded-md" />
                </div>

                <!-- Submit -->
                <div class="mt-8">
                    <button type="submit"
                        class="w-full bg-blue-600 text-white px-6 py-3 rounded-md font-medium hover:bg-blue-700 transition">
                        List Device
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
