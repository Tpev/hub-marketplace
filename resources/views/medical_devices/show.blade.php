<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $medicalDevice->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg overflow-hidden" 
             x-data="contactSellerModal()"
             x-init="$watch('open', value => { if(value) sendInquiry(); })">
             
            @if($medicalDevice->image)
                <img src="{{ Storage::url($medicalDevice->image) }}" alt="{{ $medicalDevice->name }}" class="w-full h-64 object-cover">
            @else
                <img src="{{ asset('images/placeholder.png') }}" alt="No Image Available" class="w-full h-64 object-cover">
            @endif

            <div class="p-6">
                <h3 class="text-2xl font-bold mb-4">{{ $medicalDevice->name }}</h3>

                @if($medicalDevice->brand)
                    <p class="text-gray-700 mb-2"><strong>Brand:</strong> {{ $medicalDevice->brand }}</p>
                @endif

                <p class="text-gray-700 mb-4">{{ $medicalDevice->description }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    @if($medicalDevice->price)
                        <p class="text-gray-600"><strong>Price:</strong> ${{ number_format($medicalDevice->price, 2) }}</p>
                    @endif

                    @if($medicalDevice->price_new)
                        <p class="text-gray-600"><strong>New Price:</strong> ${{ number_format($medicalDevice->price_new, 2) }}</p>
                    @endif

                    @if($medicalDevice->quantity)
                        <p class="text-gray-600"><strong>Quantity:</strong> {{ $medicalDevice->quantity }}</p>
                    @endif

                    @if($medicalDevice->condition)
                        <p class="text-gray-600"><strong>Condition:</strong> {{ ucfirst($medicalDevice->condition) }}</p>
                    @endif

                    @if(!is_null($medicalDevice->shipping_available))
                        <p class="text-gray-600"><strong>Shipping:</strong> {{ $medicalDevice->shipping_available ? '1' : '0' }}</p>
                    @endif

                    @if($medicalDevice->main_category)
                        <p class="text-gray-600"><strong>Main Category:</strong> {{ $medicalDevice->main_category }}</p>
                    @endif

                    @if($medicalDevice->aux_category)
                        <p class="text-gray-600"><strong>Aux Category:</strong> {{ $medicalDevice->aux_category }}</p>
                    @endif

                    @if($medicalDevice->location)
                        <p class="text-gray-600"><strong>Location:</strong> {{ $medicalDevice->location }}</p>
                    @endif

                    <p class="text-gray-600"><strong>Listed by:</strong> {{ $medicalDevice->user->name }}</p>
                </div>

                <div class="mt-6 flex space-x-4">
                    @auth
                        @if(Auth::id() !== $medicalDevice->user_id)
                            <button @click="open = true"
                                    class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">
                                Contact Seller
                            </button>
                        @endif

                        @if(Auth::id() === $medicalDevice->user_id)
                            <a href="{{ route('medical_devices.edit', $medicalDevice) }}"
                               class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition">
                                Edit
                            </a>
                            <form action="{{ route('medical_devices.destroy', $medicalDevice) }}"
                                  method="POST" class="inline-block"
                                  onsubmit="return confirm('Are you sure you want to delete this device?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">
                                    Delete
                                </button>
                            </form>
                        @endif
                    @endauth

                    @guest
                        <a href="{{ route('register') }}"
                           class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">
                            Register to Contact Seller
                        </a>
                    @endguest
                </div>

                <!-- Modal -->
                <div x-show="open" x-cloak
                     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                     x-transition.opacity>
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
            </div>
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
