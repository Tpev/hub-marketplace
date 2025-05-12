<!-- resources/views/landing.blade.php -->
<x-app-layout>
    @push('styles')
        <!-- Tailwind CSS via CDN -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <!-- Animate.css for animations -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
        <style>
            /* Custom animation delays if needed */
            .delay-1 { animation-delay: 0.3s; }
            .delay-2 { animation-delay: 0.6s; }
            .delay-3 { animation-delay: 0.9s; }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Optional: Additional JavaScript for enhanced interactions
        </script>
    @endpush

    <!-- SEO Meta Tags -->
    <x-slot name="header">
        <title>Hub Healthcare | Your Trusted Medical Equipment Marketplace</title>
        <meta name="description" content="Hub Healthcare is your trusted marketplace for buying and selling new, used, and refurbished medical devices. Experience secure, transparent transactions and connect with healthcare professionals worldwide.">
        <meta name="keywords" content="medical equipment, hospital equipment, used medical devices, new medical devices, healthcare marketplace">
        <meta property="og:title" content="Hub Healthcare | Trusted Medical Equipment Marketplace">
        <meta property="og:description" content="Discover Hub Healthcare, a dynamic marketplace connecting buyers and sellers of high-quality medical devices with direct leads and verified transactions.">
        <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">
        <meta property="og:url" content="{{ url('/') }}">
        <meta name="twitter:card" content="summary_large_image">
    </x-slot>

    <!-- Hero Section -->
    <section class="relative bg-cover bg-center h-screen" style="background-image: url('{{ asset('images/hero-medical-green.webp') }}');">
        <div class="absolute inset-0 bg-green-900 opacity-75"></div>
        <div class="relative flex items-center justify-center h-full px-4">
            <div class="text-center">
                <h1 class="text-white text-5xl md:text-6xl font-bold drop-shadow-lg animate__animated animate__fadeInDown">
                    Transforming Medical Equipment Exchange
                </h1>
                <p class="mt-4 text-xl md:text-2xl text-green-200 drop-shadow-md animate__animated animate__fadeInUp delay-1">
                    Hub Healthcare connects you with trusted buyers and sellers for new, used, and refurbished medical devices.
                </p>
                <div class="mt-8 animate__animated animate__zoomIn delay-2">
                    <a href="{{ route('register') }}" class="bg-green-600 text-white px-8 py-3 rounded-md text-lg font-semibold hover:bg-green-700 transition">
                        I want to Sell
                    </a>
                    <a href="{{ route('medical_devices.index') }}" class="ml-4 bg-white text-green-600 px-8 py-3 rounded-md text-lg font-semibold hover:bg-gray-100 transition">
                        I want to Buy
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-16 bg-green-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center animate__animated animate__fadeInUp">
                <h2 class="text-4xl font-bold text-gray-800">What is Hub Healthcare?</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Hub Healthcare is a comprehensive marketplace for medical and hospital equipment—where you can buy or sell new, used, and refurbished devices, plus access a full services directory.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="text-gray-700 text-lg leading-relaxed animate__animated animate__fadeInLeft">
                    <p>
                        With thousands of listings, Hub Healthcare offers a vast range of products—from diagnostic machines and surgical instruments to home healthcare devices. Our platform is built on trust, transparency, and efficiency.
                    </p>
                    <p class="mt-4">
                        Whether you’re a buyer looking for quality equipment at competitive prices, or a seller aiming to boost your business, our platform provides the perfect environment to connect with healthcare professionals worldwide.
                    </p>
                </div>
                <div class="animate__animated animate__fadeInRight">
                    <img src="{{ asset('images/about-medical-equipment.webp') }}" alt="Medical Equipment" class="w-full rounded-lg shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Advantages Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 animate__animated animate__fadeInUp">
                <h2 class="text-4xl font-bold text-gray-800">Our Key Advantages</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Why Hub Healthcare stands out in the medical equipment marketplace.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Advantage Card -->
                <div class="p-6 border rounded-lg hover:shadow-2xl transition-shadow duration-300 animate__animated animate__fadeInUp delay-1">
                    <div class="flex justify-center mb-4">
                        <!-- Icon: Sales Leads (using Heroicons) -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h0a4 4 0 014 4v2" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11a4 4 0 018 0" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-800 text-center">Direct Sales Leads</h3>
                    <p class="mt-2 text-gray-600 text-center">
                        Receive targeted enquiries from buyers actively searching for quality medical equipment.
                    </p>
                </div>
                <!-- Advantage Card -->
                <div class="p-6 border rounded-lg hover:shadow-2xl transition-shadow duration-300 animate__animated animate__fadeInUp delay-2">
                    <div class="flex justify-center mb-4">
                        <!-- Icon: Global Visibility -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 00-3.16 19.47c.5.09.68-.22.68-.48v-1.7c-2.78.6-3.37-1.34-3.37-1.34-.45-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.61.07-.61 1 .07 1.53 1.03 1.53 1.03.9 1.54 2.36 1.1 2.94.84.09-.65.35-1.1.64-1.35-2.22-.25-4.56-1.11-4.56-4.95 0-1.09.39-1.98 1.03-2.67-.1-.26-.45-1.3.1-2.7 0 0 .84-.27 2.75 1.02A9.56 9.56 0 0112 6.8c.85.004 1.7.115 2.5.338 1.9-1.29 2.73-1.02 2.73-1.02.56 1.4.21 2.44.11 2.7.64.69 1.03 1.58 1.03 2.67 0 3.85-2.35 4.7-4.58 4.95.36.31.68.92.68 1.85v2.74c0 .27.18.58.69.48A10 10 0 0012 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-800 text-center">Global Visibility</h3>
                    <p class="mt-2 text-gray-600 text-center">
                        Expand your reach with our international platform available in multiple languages.
                    </p>
                </div>
                <!-- Advantage Card -->
                <div class="p-6 border rounded-lg hover:shadow-2xl transition-shadow duration-300 animate__animated animate__fadeInUp delay-3">
                    <div class="flex justify-center mb-4">
                        <!-- Icon: Verified Trust -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-800 text-center">Verified Transactions</h3>
                    <p class="mt-2 text-gray-600 text-center">
                        Engage with confidence through our verified enquiry process, ensuring secure and trustworthy deals.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 bg-green-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 animate__animated animate__fadeInUp">
                <h2 class="text-4xl font-bold text-gray-800">How It Works</h2>
                <p class="mt-4 text-lg text-gray-600">
                    A simple and efficient process to buy or sell medical equipment.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center animate__animated animate__fadeInUp delay-1">
                    <div class="w-16 h-16 mx-auto bg-green-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        1
                    </div>
                    <h3 class="mt-4 text-xl font-semibold text-gray-800">Sign Up</h3>
                    <p class="mt-2 text-gray-600">
                        Create your free account in minutes.
                    </p>
                </div>
                <!-- Step 2 -->
                <div class="text-center animate__animated animate__fadeInUp delay-2">
                    <div class="w-16 h-16 mx-auto bg-green-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        2
                    </div>
                    <h3 class="mt-4 text-xl font-semibold text-gray-800">List Your Device</h3>
                    <p class="mt-2 text-gray-600">
                        Post your equipment with detailed info and images.
                    </p>
                </div>
                <!-- Step 3 -->
                <div class="text-center animate__animated animate__fadeInUp delay-3">
                    <div class="w-16 h-16 mx-auto bg-green-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        3
                    </div>
                    <h3 class="mt-4 text-xl font-semibold text-gray-800">Connect</h3>
                    <p class="mt-2 text-gray-600">
                        Engage with interested buyers and sellers directly.
                    </p>
                </div>
                <!-- Step 4 -->
                <div class="text-center animate__animated animate__fadeInUp delay-3">
                    <div class="w-16 h-16 mx-auto bg-green-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        4
                    </div>
                    <h3 class="mt-4 text-xl font-semibold text-gray-800">Close the Deal</h3>
                    <p class="mt-2 text-gray-600">
                        Finalize your transaction with complete confidence.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Marketplace Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 animate__animated animate__fadeInUp">
                <h2 class="text-4xl font-bold text-gray-800">Marketplace at a Glance</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Trusted by thousands of healthcare professionals worldwide.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center animate__animated animate__fadeInUp delay-1">
                    <div class="text-5xl font-bold text-green-600">390,000+</div>
                    <p class="mt-2 text-gray-600">Listings & Offers</p>
                </div>
                <div class="text-center animate__animated animate__fadeInUp delay-2">
                    <div class="text-5xl font-bold text-green-600">2M+</div>
                    <p class="mt-2 text-gray-600">Global Visitors</p>
                </div>
                <div class="text-center animate__animated animate__fadeInUp delay-3">
                    <div class="text-5xl font-bold text-green-600">1,000+</div>
                    <p class="mt-2 text-gray-600">Trusted Partners</p>
                </div>
            </div>
        </div>
    </section>
<!-- Seller Plans Section -->
<section class="py-16 bg-white border-t border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 animate__animated animate__fadeInUp">
            <h2 class="text-4xl font-bold text-gray-800">Choose Your Seller Plan</h2>
            <p class="mt-4 text-lg text-gray-600">Whether you're listing one item or managing a full catalog, we've got you covered.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Basic Plan -->
            <div class="border border-gray-200 rounded-lg p-8 shadow hover:shadow-lg transition-all duration-300 animate__animated animate__fadeInUp delay-1">
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">Basic Seller Plan</h3>
                <p class="text-gray-600 mb-4">For clinics or individuals listing a single medical device.</p>
                <div class="text-4xl font-bold text-green-600 mb-2">$4.99<span class="text-lg font-medium text-gray-500">/mo</span></div>
                <p class="text-sm text-gray-500 mb-4 italic">Cancel anytime</p>
                <ul class="mt-4 text-gray-700 space-y-2">
                    <li>✅ List 1 active device</li>
                    <li>✅ Visible in search results</li>
                    <li>✅ Basic seller dashboard</li>
                    <li>✅ Standard email support</li>
                </ul>
                <a href="{{ route('register') }}" class="mt-6 inline-block bg-green-600 text-white px-6 py-3 rounded-md font-medium hover:bg-green-700 transition">
                    Start Selling
                </a>
            </div>

            <!-- Pro Plan -->
            <div class="border border-green-400 rounded-lg p-8 shadow-lg ring-2 ring-green-300 animate__animated animate__fadeInUp delay-2">
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">Pro Seller Plan</h3>
                <p class="text-gray-600 mb-4">For professional resellers and healthcare businesses managing multiple listings.</p>
                <div class="text-4xl font-bold text-green-700 mb-2">$49<span class="text-lg font-medium text-gray-500">/mo</span></div>
                <p class="text-sm text-gray-500 mb-4 italic">Cancel anytime</p>
                <ul class="mt-4 text-gray-700 space-y-2">
                    <li>🚀 Unlimited listings</li>
                    <li>🚀 Featured placement in search</li>
                    <li>🚀 Full performance dashboard</li>
                    <li>🚀 Priority support & invoicing</li>
                </ul>
                <a href="{{ route('register') }}" class="mt-6 inline-block bg-green-700 text-white px-6 py-3 rounded-md font-medium hover:bg-green-800 transition">
                    Go Pro Now
                </a>
            </div>
        </div>
    </div>
</section>


    <!-- Testimonials Section -->
    <section class="py-16 bg-green-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 animate__animated animate__fadeInUp">
                <h2 class="text-4xl font-bold text-gray-800">What Our Clients Say</h2>
                <p class="mt-4 text-lg text-gray-600">
                    Hear from our satisfied customers and partners.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Testimonial 1 -->
                <div class="p-6 bg-white rounded-lg shadow-lg animate__animated animate__fadeInUp delay-1">
                    <p class="text-gray-600 italic">
                        "Hub Healthcare has completely transformed the way we source our medical equipment. The platform is intuitive and the direct leads are a game-changer."
                    </p>
                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Dr. Sarah Thompson</h3>
                        <p class="text-gray-500">Hospital Administrator</p>
                    </div>
                </div>
                <!-- Testimonial 2 -->
                <div class="p-6 bg-white rounded-lg shadow-lg animate__animated animate__fadeInUp delay-2">
                    <p class="text-gray-600 italic">
                        "Since joining Hub Healthcare, our international visibility has increased dramatically. The platform delivers quality enquiries and helps us close deals faster."
                    </p>
                    <div class="mt-4">
                        <h3 class="text-lg font-semibold text-gray-800">Mark Johnson</h3>
                        <p class="text-gray-500">Medical Equipment Supplier</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call To Action Section -->
    <section class="py-16 bg-green-600">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center animate__animated animate__fadeInUp">
            <h2 class="text-4xl font-bold text-white">Ready to Elevate Your Business?</h2>
            <p class="mt-4 text-xl text-green-100">
                Join Hub Healthcare today and unlock the full potential of our trusted marketplace.
            </p>
            <div class="mt-8">
                <a href="{{ route('register') }}" class="bg-white text-green-600 px-8 py-3 rounded-md text-lg font-semibold hover:bg-gray-100 transition">
                    Get Started Now
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
