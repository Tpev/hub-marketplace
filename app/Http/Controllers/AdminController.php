<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MedicalDevice;
use App\Models\DeviceInquiry;

class AdminController extends Controller
{
    private function ensureAdmin(): void
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index()
    {
        $this->ensureAdmin();

        $users = User::withCount([
                'medicalDevices',
                'deviceInquiries as contact_requests_count'
            ])
            ->orderByDesc('created_at')
            ->get();

        $inquiries = \App\Models\BuyerInquiry::latest()->get();

        $kpis = [
            'total_users'     => User::count(),
            'buyers'          => User::where('intent', 'Buyer')->count(),
            'sellers'         => User::where('intent', 'Seller')->count(),
            'both'            => User::where('intent', 'Both')->count(),
            'new_devices'     => MedicalDevice::where('condition', 'new')->count(),
            'used_devices'    => MedicalDevice::where('condition', 'used')->count(),
            'refurb_devices'  => MedicalDevice::where('condition', 'refurbished')->count(),
            'total_devices'   => MedicalDevice::count(),
            'total_inquiries' => DeviceInquiry::count(),
            'total_value'     => MedicalDevice::sum('price'),
            'average_price'   => round(MedicalDevice::avg('price'), 2),
        ];

        // ✅ Define allowed tiers for the UI + validation
        $licenseTiers = [
            'basic'      => 'Basic',
            'pro'        => 'Pro',
            'enterprise' => 'Enterprise',
        ];

        return view('admin.dashboard', compact('users', 'kpis', 'inquiries', 'licenseTiers'));
    }

    public function updateUserLicense(Request $request, User $user)
    {
        $this->ensureAdmin();

        $licenseTiers = ['basic', 'pro', 'enterprise'];

        $data = $request->validate([
            'is_subscribed' => ['required', 'in:0,1'],
            'license_tier'  => ['nullable', 'string', 'in:' . implode(',', $licenseTiers)],
        ]);

        $isSubscribed = (int) $data['is_subscribed'] === 1;

        $user->is_subscribed = $isSubscribed;

        // If not subscribed, clear tier (optional but usually cleaner)
        $user->license_tier = $isSubscribed ? ($data['license_tier'] ?? null) : null;

        $user->save();

        return back()->with('success', "✅ License updated for {$user->name} (User #{$user->id}).");
    }
}
