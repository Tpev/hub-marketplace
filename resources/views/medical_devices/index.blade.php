<!-- resources/views/medical_devices/index.blade.php -->
<x-app-layout>
    @push('styles')
        <!-- Tailwind CSS via CDN -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

        <!-- Custom Styles (if any additional styles are needed) -->
        <style>
            /* Additional custom styles can be added here */
        </style>
    @endpush

    @push('scripts')
        <!-- Optional: Add any additional scripts specific to this page -->
        <script>
            // Example: JavaScript can be added here if needed in the future
        </script>
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Medical Devices') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Action Buttons -->
            <div class="mb-6 flex justify-end space-x-4">
                @auth
                    <!-- Add New Device Button for Authenticated Users -->
                    <a href="{{ route('medical_devices.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-md font-medium hover:bg-blue-700 transition-colors duration-300">
                        Add New Device
                    </a>
                @endauth

                @guest
                    <!-- Register to List Your Device Button for Guests -->
                    <a href="{{ route('register') }}"
                       class="bg-green-600 text-white px-4 py-2 rounded-md font-medium hover:bg-green-700 transition-colors duration-300">
                        Register to List Your Device
                    </a>
                @endguest
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Device Listings -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($devices as $device)
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        @if($device->image)
                            <img src="{{ asset('storage/' . $device->image) }}" alt="{{ $device->name }}" class="w-full h-48 object-cover">
                        @endif
                        <div class="p-4">
                            <h3 class="text-xl font-bold mb-2">{{ $device->name }}</h3>
                            <p class="text-gray-600 mb-2">${{ number_format($device->price, 2) }}</p>
                            <p class="text-gray-600 mb-4">Condition: {{ ucfirst($device->condition) }}</p>
                            <a href="{{ route('medical_devices.show', $device) }}" class="text-blue-500 hover:underline">View Details</a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-600">No medical devices listed yet.</p>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $devices->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
