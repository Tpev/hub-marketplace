<x-app-layout>
    @push('head')
        <title>{{ $medicalDevice->name }} – Medical Device Marketplace</title>
        <meta name="description" content="{{ Str::limit(strip_tags($medicalDevice->description), 160) }}">
        <meta name="robots" content="index, follow">
        <meta property="og:title" content="{{ $medicalDevice->name }} – Medical Device Marketplace">
        <meta property="og:description" content="{{ Str::limit(strip_tags($medicalDevice->description), 160) }}">
        <meta property="og:image" content="{{ $medicalDevice->image ? asset(Storage::url($medicalDevice->image)) : asset('images/placeholder.png') }}">
        <meta property="og:type" content="product">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $medicalDevice->name }} – Medical Device Marketplace">
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($medicalDevice->description), 160) }}">
        <meta name="twitter:image" content="{{ $medicalDevice->image ? asset(Storage::url($medicalDevice->image)) : asset('images/placeholder.png') }}">
    @endpush

    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 leading-tight">
            {{ $medicalDevice->name }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div x-data="contactSellerModal()" x-init="$watch('open', value => { if(value) sendInquiry(); })"
             class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                {{-- Image --}}
                <div class="h-64 md:h-96 bg-gray-100">
                    <img src="{{ $medicalDevice->image ? Storage::url($medicalDevice->image) : asset('images/placeholder.png') }}"
                         alt="{{ $medicalDevice->name }}"
                         class="w-full h-full object-cover">
                </div>

                {{-- Content --}}
                <div class="p-6 space-y-6">
                    {{-- Title & Brand --}}
                    <div class="space-y-1">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $medicalDevice->name }}</h3>
                        @if($medicalDevice->brand)
                            <p class="text-sm text-gray-500">Brand: <span class="font-medium text-gray-700">{{ $medicalDevice->brand }}</span></p>
                        @endif
                    </div>

                    {{-- Pricing --}}
                    <div class="bg-gray-50 border border-gray-200 p-4 rounded-md space-y-2">
                        <h4 class="text-sm font-semibold uppercase text-gray-500">Pricing</h4>
                        @if($medicalDevice->price_new)
                            <div class="text-sm text-gray-500">
                                <span class="font-medium text-gray-600">Retail Price:</span>
                                <span class="line-through">${{ number_format($medicalDevice->price_new, 2) }}</span>
                            </div>
                            <div class="text-sm">
                                <span class="font-medium text-gray-700">Seller’s Price:</span>
                                <span class="text-green-700 font-bold text-lg">${{ number_format($medicalDevice->price, 2) }}</span>
                            </div>
                            <div>
                                <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">
                                    Save ${{ number_format($medicalDevice->price_new - $medicalDevice->price, 2) }}
                                    ({{ round((($medicalDevice->price_new - $medicalDevice->price) / $medicalDevice->price_new) * 100) }}% Off)
                                </span>
                            </div>
                        @elseif($medicalDevice->price)
                            <div class="text-sm">
                                <span class="font-medium text-gray-700">Seller’s Price:</span>
                                <span class="text-green-700 font-bold text-lg">${{ number_format($medicalDevice->price, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Description --}}
                    @if($medicalDevice->description)
                        <div>
                            <h4 class="text-sm font-semibold uppercase text-gray-500 mb-1">Description</h4>
                            <p class="text-gray-700 text-sm leading-relaxed">{{ $medicalDevice->description }}</p>
                        </div>
                    @endif

                    {{-- Specs --}}
                    <div>
                        <h4 class="text-sm font-semibold uppercase text-gray-500 mb-2">Device Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                            @if($medicalDevice->quantity)
                                <div><span class="font-medium">Quantity:</span> {{ $medicalDevice->quantity }}</div>
                            @endif
                            @if($medicalDevice->condition)
                                <div><span class="font-medium">Condition:</span> {{ ucfirst($medicalDevice->condition) }}</div>
                            @endif
                            @if(!is_null($medicalDevice->shipping_available))
                                <div><span class="font-medium">Shipping:</span> {{ $medicalDevice->shipping_available ? 'Shipping Available' : 'Pickup Only' }}</div>
                            @endif
                            @if($medicalDevice->main_category)
                                <div><span class="font-medium">Main Category:</span> {{ $medicalDevice->main_category }}</div>
                            @endif
                            @if($medicalDevice->aux_category)
                                <div><span class="font-medium">Subcategory:</span> {{ $medicalDevice->aux_category }}</div>
                            @endif
                            @if($medicalDevice->location)
                                <div><span class="font-medium">Location:</span> 📍 {{ $medicalDevice->location }}</div>
                            @endif
                            <div><span class="font-medium">Listed by:</span> {{ $medicalDevice->user->name }}</div>
                        </div>
                    </div>

                    {{-- CTAs --}}
                    <div class="flex flex-wrap gap-4 mt-6">
                        @auth
                            @if(Auth::id() !== $medicalDevice->user_id)
                                <button @click="open = true"
                                        class="bg-green-600 text-white px-5 py-2.5 rounded-md hover:bg-green-700 transition">
                                    📩 Contact Seller
                                </button>
                            @else
                                <a href="{{ route('medical_devices.edit', $medicalDevice) }}"
                                   class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition">
                                    ✏️ Edit
                                </a>
                                <form action="{{ route('medical_devices.destroy', $medicalDevice) }}"
                                      method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this device?');"
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">
                                        🗑 Delete
                                    </button>
                                </form>
                            @endif
                        @endauth

                        @guest
                            <a href="{{ route('register') }}"
                               class="bg-blue-500 text-white px-5 py-2.5 rounded-md hover:bg-blue-600 transition">
                                Register to Contact Seller
                            </a>
                        @endguest
                    </div>
                </div>
            </div>

            {{-- Modal --}}
            <div x-show="open" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-transition.opacity>
                <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
                    <h2 class="text-lg font-semibold mb-2">📧 Seller Contact</h2>
                    <p class="text-gray-600 mb-4">Here is the seller's email address:</p>
                    <div class="flex justify-between items-center">
                        <span class="text-blue-700 font-medium">{{ $medicalDevice->user->email }}</span>
                        <button @click="copyEmail('{{ $medicalDevice->user->email }}')"
                                class="text-sm text-blue-500 hover:underline">
                            Copy
                        </button>
                    </div>
                    <div class="mt-6 text-right">
                        <button @click="open = false"
                                class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded">
                            Close
                        </button>
                    </div>
                </div>
            </div>

            {{-- JSON-LD Structured Data --}}
            @push('scripts')
                <script type="application/ld+json">
                {
                    "@context": "https://schema.org",
                    "@type": "Product",
                    "name": "{{ $medicalDevice->name }}",
                    "image": ["{{ $medicalDevice->image ? asset(Storage::url($medicalDevice->image)) : asset('images/placeholder.png') }}"],
                    "description": "{{ Str::limit(strip_tags($medicalDevice->description), 160) }}",
                    "sku": "MD-{{ $medicalDevice->id }}",
                    "brand": {
                        "@type": "Brand",
                        "name": "{{ $medicalDevice->brand ?? 'Generic' }}"
                    },
                    "offers": {
                        "@type": "Offer",
                        "url": "{{ url()->current() }}",
                        "priceCurrency": "USD",
                        "price": "{{ $medicalDevice->price }}",
                        "itemCondition": "https://schema.org/{{ ucfirst($medicalDevice->condition) }}Condition",
                        "availability": "{{ $medicalDevice->quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
                        "seller": {
                            "@type": "Organization",
                            "name": "{{ $medicalDevice->user->name }}"
                        }
                    }
                }
                </script>
            @endpush
        </div>
    </div>

    @push('scripts')
        <script src="//unpkg.com/alpinejs" defer></script>
        <script>
            function contactSellerModal() {
                return {
                    open: false,
                    sendInquiry() {
                        fetch('{{ route('device-inquiry.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ device_id: {{ $medicalDevice->id }} })
                        }).catch(console.error);
                    },
                    copyEmail(email) {
                        navigator.clipboard.writeText(email).then(() => {
                            alert('Email copied to clipboard!');
                        }).catch(() => {
                            alert('Failed to copy email.');
                        });
                    }
                };
            }
        </script>
    @endpush
</x-app-layout>
