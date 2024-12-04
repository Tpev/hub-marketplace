<!-- resources/views/medical_devices/show.blade.php -->
<x-app-layout>
    @push('styles')
        <!-- Tailwind CSS via CDN (if not already included globally) -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @endpush

    @push('scripts')
        <!-- Include Alpine.js for handling modal state (lightweight) -->
        <script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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
                <img src="{{ Storage::url($medicalDevice->image) }}" alt="{{ $medicalDevice->name }}" class="w-full h-64 object-cover">
            @else
                <!-- Placeholder Image (Optional) -->
                <img src="{{ asset('images/placeholder.png') }}" alt="No Image Available" class="w-full h-64 object-cover">
            @endif

            <div class="p-6">
                <h3 class="text-2xl font-bold mb-2">{{ $medicalDevice->name }}</h3>
                <p class="text-gray-700 mb-4"><strong>Brand:</strong> {{ $medicalDevice->brand }}</p>
                <p class="text-gray-700 mb-4">{{ $medicalDevice->description }}</p>
                <p class="text-gray-600 mb-2"><strong>Price:</strong> ${{ number_format($medicalDevice->price, 2) }}</p>
                <p class="text-gray-600 mb-4"><strong>Condition:</strong> {{ ucfirst($medicalDevice->condition) }}</p>
                <p class="text-gray-600 mb-4"><strong>Location:</strong> {{ $medicalDevice->location }}</p>
                <p class="text-gray-600 mb-4"><strong>Listed by:</strong> {{ $medicalDevice->user->name }}</p>

                <!-- Action Buttons and Modal Wrapper -->
                <div x-data="{ open: false }" class="flex space-x-4">
                    @auth
                        @if(Auth::id() !== $medicalDevice->user_id)
                            <!-- Contact Seller Button for Authenticated Users (Non-Owners) -->
                            <button @click="open = true" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors">
                                Contact Seller
                            </button>
                        @endif

                        @if(Auth::id() === $medicalDevice->user_id)
                            <!-- Edit Button for Owners -->
                            <a href="{{ route('medical_devices.edit', $medicalDevice) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition-colors">
                                Edit
                            </a>

                            <!-- Delete Button for Owners -->
                            <form action="{{ route('medical_devices.destroy', $medicalDevice) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this device?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition-colors">
                                    Delete
                                </button>
                            </form>
                        @endif
                    @endauth

                    @guest
                        <!-- Register to Contact Seller Button for Guests -->
                        <a href="{{ route('register') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                            Register to Contact Seller
                        </a>
                    @endguest

                    <!-- Modal Structure -->
                    <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0" @click.away="open = false" role="dialog" aria-modal="true">
                        <!-- Modal Content -->
                        <div @click.stop class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                            <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <!-- Icon (Optional) -->
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <!-- Heroicon: Mail -->
                                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 12H8m8 0l-4-4m4 4l-4 4"/>
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                            Seller's Contact Email
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                Below is the email address of the seller. Feel free to reach out directly.
                                            </p>
                                            <div class="mt-4 flex items-center">
                                                <span class="text-gray-700">{{ $medicalDevice->user->email }}</span>
                                                <button @click="copyEmail" class="ml-2 text-blue-500 hover:text-blue-700 focus:outline-none">
                                                    <!-- Heroicon: Clipboard -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M8 2a2 2 0 00-2 2v1H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H8zM7 4a1 1 0 011-1h2a1 1 0 011 1v1H7V4z" />
                                                        <path d="M9 12a1 1 0 012 0v3a1 1 0 11-2 0v-3z" />
                                                    </svg>
                                                    Copy
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button @click="open = false" type="button"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Script for Copy Functionality -->
    @push('scripts')
        <script>
            function copyEmail() {
                const email = "{{ $medicalDevice->user->email }}";
                navigator.clipboard.writeText(email).then(() => {
                    alert('Email copied to clipboard!');
                }).catch(err => {
                    alert('Failed to copy email.');
                });
            }
        </script>
    @endpush
</x-app-layout>
