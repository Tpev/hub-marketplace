<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $event = json_decode($request->getContent());

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Make sure metadata contains the user ID
            $userId = $session->metadata->user_id ?? null;

            if ($userId && $user = User::find($userId)) {
                $user->is_subscribed = true;
                $user->save();
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
