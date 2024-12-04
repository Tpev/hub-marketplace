<!-- resources/views/contact_requests/create.blade.php -->
<x-app-layout>
    @push('styles')
        <!-- Add any additional styles specific to this page -->
    @endpush

    @push('scripts')
        <!-- Add any additional scripts specific to this page -->
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Seller for ') }} "{{ $medicalDevice->name }}"
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto bg-white shadow-md rounded-lg p-6">
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form action="{{ route('contact_requests.store', $medicalDevice) }}" method="POST">
                @csrf

                <!-- Message -->
                <div class="mb-4">
                    <label for="message" class="block text-gray-700">Message (optional)</label>
                    <textarea name="message" id="message" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('message') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors">
                        Send Contact Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
