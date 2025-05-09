<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
{
    public function success(Request $request, string $tier)
    {
        $sessionId = $request->query('session_id');

        // Optional logging for audit
        logger('Stripe session completed: ' . $sessionId . ' for tier: ' . $tier);

        $user = Auth::user();
        if ($user && !$user->is_subscribed) {
            $user->is_subscribed = true;
            $user->license_tier = in_array($tier, ['basic', 'pro']) ? $tier : null;
            $user->save();
        }

        return view('auth.subscribe-success-' . $tier);
    }
}
