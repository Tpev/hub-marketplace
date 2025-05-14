<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
            'intent'         => ['required', 'in:Buyer,Seller,Both'],
            'user_type'      => ['required', 'in:pro,public'],
            'business_type'  => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'intent'         => $request->intent,
            'user_type'      => $request->user_type,
            'business_type'  => $request->user_type === 'pro' ? $request->business_type : null,
            'is_subscribed'  => false,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('medical_devices.index');
    }
}
