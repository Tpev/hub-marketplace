{{-- resources/views/medical_devices/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ __('Marketplace – Medical Devices') }}
            </h2>
            <p class="mt-2 text-sm text-gray-500 md:mt-0">
                Browse and discover certified used medical equipment.
            </p>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Search & Actions --}}
            <div class="bg-white p-4 rounded-lg shadow flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <form method="GET" action="{{ route('medical_devices.index') }}" class="flex-1">
                    <div class="flex">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search by name, brand, or location..."
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        >
                        <button
                            type="submit"
                            class="bg-green-600 text-white px-6 py-2 rounded-r-md hover:bg-green-700 transition"
                        >
                            Search
                        </button>
                    </div>
                </form>

                <div class="flex-shrink-0 flex gap-3">
                    @auth
                        <a
                            href="{{ route('medical_devices.create') }}"
                            class="bg-green-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-green-700 transition"
                        >
                            + Add New Device
                        </a>
                    @else
                        <a
                            href="{{ route('register') }}"
                            class="bg-green-600 text-white px-4 py-2 rounded-md font-semibold hover:bg-green-700 transition"
                        >
                            Register to List
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md shadow">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Devices Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
@forelse($devices as $device)
    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition overflow-hidden border border-gray-100 flex flex-col">
{{-- Image --}}
<div class="h-48 bg-gray-100 relative">
    <img
        src="{{ $device->image ? Storage::url($device->image) : asset('images/placeholder.png') }}"
        alt="{{ $device->name }}"
        class="w-full h-full object-cover"
    >

</div>


        {{-- Content --}}
        <div class="p-4 flex-1 flex flex-col">
            {{-- Device Name --}}
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500 font-medium">Device</div>
                <h3 class="text-lg font-semibold text-gray-800 truncate">
                    {{ $device->name }}
                </h3>
            </div>

{{-- Price Section --}}
<div class="mt-3 space-y-1">
    <div class="text-xs uppercase tracking-wide text-gray-500 font-medium">Pricing</div>

    @if($device->price_new)
        <div class="text-sm text-gray-500">
            <span class="font-medium">Retail Price:</span>
            <span class="line-through">${{ number_format($device->price_new, 2) }}</span>
        </div>

        <div class="text-sm text-gray-700">
            <span class="font-medium">Seller’s Price:</span>
            <span class="text-green-700 font-bold text-lg">${{ number_format($device->price, 2) }}</span>
        </div>

        <div class="mt-1">
            <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">
                Save ${{ number_format($device->price_new - $device->price, 2) }}
                ({{ round((($device->price_new - $device->price) / $device->price_new) * 100) }}% Off)
            </span>
        </div>
    @else
        <div class="text-sm text-gray-700">
            <span class="font-medium">Seller’s Price:</span>
            <span class="text-green-700 font-bold text-lg">${{ number_format($device->price, 2) }}</span>
        </div>
    @endif
</div>


            {{-- Meta Details --}}
            <div class="mt-4 grid grid-cols-1 gap-2 text-sm text-gray-700">
                <div>
                    <span class="text-xs uppercase text-gray-500">Condition</span>
                    <div>{{ ucfirst($device->condition) }}</div>
                </div>

                @if($device->location)
                <div>
                    <span class="text-xs uppercase text-gray-500">Location</span>
                    <div>📍 {{ $device->location }}</div>
					    @if($device->shipping)
        <span class="inline-block bg-yellow-500 text-white text-xs font-semibold px-2 py-1 rounded-full">

            Shipping Available
        </span>
    @else
        <span class="inline-block bg-yellow-500 text-white text-xs font-semibold px-2 py-1 rounded-full">

            Pickup Only
        </span>
    @endif
                </div>
                @endif

                @if($device->aux_category)
                <div>
                    <span class="text-xs uppercase text-gray-500">Subcategory</span>
                    <div class="inline-block bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-medium">
                        {{ $device->aux_category }}
                    </div>
                </div>
                @endif
            </div>

            {{-- CTA --}}
            <div class="mt-auto pt-4">
                <a
                    href="{{ route('medical_devices.show', $device) }}"
                    class="inline-block text-sm text-green-600 hover:underline font-medium"
                >
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

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $devices->withQueryString()->links() }}
            </div>

            {{-- Buyer Inquiry Form --}}
            @if(auth()->guest() || auth()->user()->intent === 'Buyer')
                <div class="mt-16 max-w-2xl mx-auto bg-white border border-green-100 rounded-xl shadow p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">
                        Can’t find the device you’re looking for?
                    </h3>
                    <p class="text-gray-600 text-center mb-6">
                        Tell us what you need and we’ll notify our verified sellers.
                    </p>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4 text-sm text-center">
                            {{ session('success') }}
                        </div>
                    @endif

                    {!! NoCaptcha::renderJs() !!}

                    <form action="{{ route('buyer-inquiries.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 shadow-sm"
                            >
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Your Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 shadow-sm"
                            >
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                                What are you looking for?
                            </label>
                            <textarea
                                name="message"
                                id="message"
                                rows="4"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 shadow-sm"
                                placeholder="Describe the device or model you're searching for..."
                            ></textarea>
                        </div>

                        <div>
                            {!! NoCaptcha::display() !!}
                            @error('g-recaptcha-response')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="text-center">
                            <button
                                type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-md shadow transition"
                            >
                                Submit Inquiry
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
