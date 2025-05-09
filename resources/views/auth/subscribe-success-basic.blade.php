<x-app-layout>
    <div class="max-w-xl mx-auto text-center mt-16">
        <h2 class="text-3xl font-bold text-green-600">Thank you for subscribing to the Basic Plan!</h2>
        <p class="mt-4 text-gray-600">
            Your seller account is now active. You can list one medical device and start connecting with buyers.
        </p>
        <a href="{{ route('dashboard') }}" class="mt-6 inline-block px-6 py-2 bg-green-500 text-white rounded hover:bg-green-600">
            Go to Dashboard
        </a>
    </div>
</x-app-layout>
