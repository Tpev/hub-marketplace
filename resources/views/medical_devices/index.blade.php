<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ __('Marketplace – Medical Devices') }}
            </h2>
            <p class="mt-2 text-sm text-gray-500 md:mt-0">Browse and discover certified used medical equipment.</p>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Search & Actions -->
            <div class="mb-8 bg-white p-4 rounded-lg shadow flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Search -->
                <form method="GET" action="{{ route('medical_devices.index') }}" class="w-full lg:w-2/3">
                    <div class="flex">
                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search by name, brand, or location..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-l-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                        >
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700 transition">
                            Search
                        </button>
                    </div>
                </form>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3">
                    @auth
                        <a href="{{ route('medical_devices.create') }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-blue-700 transition">
                            + Add New Device
                        </a>
                    @endauth

                    @guest
                        <a href="{{ route('register') }}"
                           class="bg-green-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-green-700 transition">
                            Register to List
                        </a>
                    @endguest
                </div>
            </div>

            <!-- Flash Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md shadow">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Device Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($devices as $device)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden border border-gray-100">
                        @if($device->image)
                            <img src="{{ asset('storage/' . $device->image) }}" alt="{{ $device->name }}"
                                 class="w-full h-48 object-cover">
                        @endif
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $device->name }}</h3>
                            <p class="text-blue-700 font-bold mt-2">${{ number_format($device->price, 2) }}</p>
                            <p class="text-gray-500 text-sm mt-1">Condition: {{ ucfirst($device->condition) }}</p>
                            <p class="text-gray-400 text-xs mt-1">Location: {{ $device->location }}</p>
                            <div class="mt-4">
                                <a href="{{ route('medical_devices.show', $device) }}"
                                   class="text-sm text-blue-600 hover:underline font-medium">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center text-gray-500 py-12">
                        No medical devices listed yet.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $devices->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
