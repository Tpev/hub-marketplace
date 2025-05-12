<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MedicalDevice;
use App\Models\DeviceInquiry;

class AdminController extends Controller
{
    public function index()
    {
        if (! auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $users = User::withCount([
            'medicalDevices',
            'deviceInquiries as contact_requests_count'
        ])->get();

		$kpis = [
			'total_users'     => User::count(),
			'buyers'          => User::where('intent', 'Buyer')->count(),
			'sellers'         => User::where('intent', 'Seller')->count(),
			'both'            => User::where('intent', 'Both')->count(),
			'new_devices'     => MedicalDevice::where('condition', 'new')->count(),
			'used_devices'    => MedicalDevice::where('condition', 'used')->count(),
			'refurb_devices'  => MedicalDevice::where('condition', 'refurbished')->count(),
			'total_inquiries' => DeviceInquiry::count(),
			'total_value'     => MedicalDevice::sum('price'),
			'average_price'   => round(MedicalDevice::avg('price'), 2),
		];


        return view('admin.dashboard', compact('users', 'kpis'));
    }
}
