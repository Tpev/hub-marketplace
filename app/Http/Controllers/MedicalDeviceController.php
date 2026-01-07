<?php

namespace App\Http\Controllers;

use App\Models\MedicalDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MedicalDeviceController extends Controller
{
    /**
     * Display a listing of the medical devices.
     *
     * @return \Illuminate\View\View
     */
public function index(Request $request)
{
    $search = $request->string('search')->trim()->toString();
    $page   = (int) $request->input('page', 1);

    // Cache key varies by search + page (and bump this version if you change query fields)
    $cacheKey = 'md:index:v1:search=' . md5($search) . ':page=' . $page;

    // Keep it short so new listings show up quickly (tweak as you like)
    $ttl = now()->addSeconds(60);

    $devices = Cache::remember($cacheKey, $ttl, function () use ($search) {
        $query = MedicalDevice::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        return $query
            ->select([
                'id','name','brand','price','price_new','condition','location',
                'shipping_available','aux_category','image','created_at'
            ])
            ->orderByDesc('id')
            ->simplePaginate(24);
    });

    // Keep query string behavior identical to your current code
    $devices->appends($request->query());

    return view('medical_devices.index', compact('devices'));
}


    /**
     * Show the form for creating a new medical device.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
/* 		  $user = auth()->user();
		    if (in_array($user->intent, ['Seller', 'Both']) && !$user->is_subscribed) {
        return redirect()->route('subscribe.page')->with('error', 'You must subscribe before listing a device.');
    } */
        return view('medical_devices.create');
    }

    /**
     * Store a newly created medical device in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'brand' => 'nullable|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'price_new' => 'nullable|numeric|min:0',
        'condition' => 'required|in:new,used,refurbished',
        'quantity' => 'required|integer|min:1',
        'shipping_available' => 'required|boolean',
        'main_category' => 'nullable|string|max:255',
        'aux_category' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'image' => 'nullable|image|max:8048',
    ]);

    $data = $request->only([
        'name', 'brand', 'description', 'price', 'price_new', 'condition', 'quantity',
        'shipping_available', 'main_category', 'aux_category', 'city', 'state', 'country'
    ]);

    $data['user_id'] = auth()->id();

    // For backward compatibility
    $data['location'] = trim("{$request->input('city')} {$request->input('state')} {$request->input('country')}");

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('medical_devices', 'public');
    }

    MedicalDevice::create($data);

    return redirect()->route('medical_devices.index')->with('success', 'Medical device listed successfully.');
}


    /**
     * Display the specified medical device.
     *
     * @param  \App\Models\MedicalDevice  $medicalDevice
     * @return \Illuminate\View\View
     */
    public function show(MedicalDevice $medicalDevice)
    {
        return view('medical_devices.show', compact('medicalDevice'));
    }

    /**
     * Show the form for editing the specified medical device.
     *
     * @param  \App\Models\MedicalDevice  $medicalDevice
     * @return \Illuminate\View\View
     */
    public function edit(MedicalDevice $medicalDevice)
    {
        // Authorization: Ensure the user owns the device
        if ($medicalDevice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('medical_devices.edit', compact('medicalDevice'));
    }

    /**
     * Update the specified medical device in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MedicalDevice  $medicalDevice
     * @return \Illuminate\Http\RedirectResponse
     */
public function update(Request $request, MedicalDevice $medicalDevice)
{
    // Authorization: Ensure the user owns the device
    if ($medicalDevice->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'brand' => 'nullable|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'price_new' => 'nullable|numeric|min:0',
        'condition' => 'required|in:new,used,refurbished',
        'quantity' => 'required|integer|min:1',
        'shipping_available' => 'required|boolean',
        'main_category' => 'nullable|string|max:255',
        'aux_category' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'image' => 'nullable|image|max:8048',
    ]);

    $data = $request->only([
        'name', 'brand', 'description', 'price', 'price_new', 'condition', 'quantity',
        'shipping_available', 'main_category', 'aux_category', 'city', 'state', 'country'
    ]);

    // Update legacy location field
    $data['location'] = trim("{$request->input('city')} {$request->input('state')} {$request->input('country')}");

    if ($request->hasFile('image')) {
        if ($medicalDevice->image) {
            \Storage::disk('public')->delete($medicalDevice->image);
        }
        $data['image'] = $request->file('image')->store('medical_devices', 'public');
    }

    $medicalDevice->update($data);

    return redirect()->route('medical_devices.show', $medicalDevice)->with('success', 'Medical device updated successfully.');
}


    /**
     * Remove the specified medical device from storage.
     *
     * @param  \App\Models\MedicalDevice  $medicalDevice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(MedicalDevice $medicalDevice)
    {
        // Authorization: Ensure the user owns the device
        if ($medicalDevice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the image if exists
        if ($medicalDevice->image) {
            \Storage::disk('public')->delete($medicalDevice->image);
        }

        $medicalDevice->delete();

        return redirect()->route('medical_devices.index')->with('success', 'Medical device deleted successfully.');
    }
}
