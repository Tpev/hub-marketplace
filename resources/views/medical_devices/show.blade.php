<!-- resources/views/medical_devices/show.blade.php -->
<x-app-layout>
    @push('styles')
        <!-- Add any additional styles specific to this page -->
    @endpush

    @push('scripts')
        <!-- Add any additional scripts specific to this page -->
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $medicalDevice->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
            <!-- Device Image -->
            @if($medicalDevice->image)
                <img src="{{ asset('storage/' . $medicalDevice->image) }}" alt="{{ $medicalDevice->name }}" class="w-full h-64 object-cover">
            @endif

            <div class="p-6">
                <h3 class="text-2xl font-bold mb-2">{{ $medicalDevice->name }}</h3>
                <p class="text-gray-700 mb-4">{{ $medicalDevice->brand }}</p>
                <p class="text-gray-700 mb-4">{{ $medicalDevice->description }}</p>
                <p class="text-gray-600 mb-2">Price: ${{ number_format($medicalDevice->price, 2) }}</p>
                <p class="text-gray-600 mb-4">Condition: {{ ucfirst($medicalDevice->condition) }}</p>
                <p class="text-gray-600 mb-4">{{ $medicalDevice->location }}</p>
                <p class="text-gray-600 mb-4">Listed by: {{ $medicalDevice->user->name }}</p>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    @auth
                        @if(Auth::id() !== $medicalDevice->user_id)
                            <a href="{{ route('contact_requests.create', $medicalDevice) }}" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors">
                                Contact Seller
                            </a>
                        @endif

                        @if(Auth::id() === $medicalDevice->user_id)
                            <a href="{{ route('medical_devices.edit', $medicalDevice) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition-colors">
                                Edit
                            </a>

                            <form action="{{ route('medical_devices.destroy', $medicalDevice) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this device?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors">
                                    Delete
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
