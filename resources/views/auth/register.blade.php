<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" x-data="{ userType: '{{ old('user_type') }}' }">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Intent -->
        <div class="mt-4">
            <x-input-label for="intent" :value="__('I am a')" />
            <select id="intent" name="intent" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">-- Select --</option>
                <option value="Buyer" {{ old('intent') == 'Buyer' ? 'selected' : '' }}>Buyer</option>
                <option value="Seller" {{ old('intent') == 'Seller' ? 'selected' : '' }}>Seller</option>
                <option value="Both" {{ old('intent') == 'Both' ? 'selected' : '' }}>Both</option>
            </select>
            <x-input-error :messages="$errors->get('intent')" class="mt-2" />
        </div>

        <!-- User Type: Yes/No -->
        <div class="mt-4">
            <x-input-label for="user_type" :value="__('Are you a healthcare or industry professional?')" />
            <select id="user_type" name="user_type" x-model="userType" required
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">-- Please Select --</option>
                <option value="pro" {{ old('user_type') == 'pro' ? 'selected' : '' }}>Yes</option>
                <option value="public" {{ old('user_type') == 'public' ? 'selected' : '' }}>No</option>
            </select>
            <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
        </div>

        <!-- Business Type (shown if professional) -->
        <div class="mt-4" x-show="userType === 'pro'">
            <x-input-label for="business_type" :value="__('What type of business are you?')" />
            <select id="business_type" name="business_type"
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">-- Select your business type --</option>
                <option value="Clinic" {{ old('business_type') == 'Clinic' ? 'selected' : '' }}>Clinic</option>
                <option value="Hospital" {{ old('business_type') == 'Hospital' ? 'selected' : '' }}>Hospital</option>
                <option value="Private Practice" {{ old('business_type') == 'Private Practice' ? 'selected' : '' }}>Private Practice</option>
                <option value="Medical Supplier" {{ old('business_type') == 'Medical Supplier' ? 'selected' : '' }}>Medical Supplier</option>
                <option value="Other" {{ old('business_type') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
        </div>

        <!-- Register CTA -->
        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 " href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4 bg-green-600">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
