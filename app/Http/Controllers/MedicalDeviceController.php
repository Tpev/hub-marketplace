<?php

namespace App\Http\Controllers;

use App\Models\MedicalDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalDeviceController extends Controller
{
    /**
     * Display a listing of the medical devices.
     *
     * @return \Illuminate\View\View
     */
public function index(Request $request)
{
    $query = MedicalDevice::query();

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
    }

    $devices = $query->latest()->paginate(10)->withQueryString();

    return view('medical_devices.index', compact('devices'));
}


    /**
     * Show the form for creating a new medical device.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
		  $user = auth()->user();
		    if (in_array($user->intent, ['Seller', 'Both']) && !$user->is_subscribed) {
        return redirect()->route('subscribe.page')->with('error', 'You must subscribe before listing a device.');
    }
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
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:new,used,refurbished',
            'image' => 'nullable|image|max:8048',
			'location' => 'required|string',
            'brand' => 'nullable|string',
        ]);

        $data = $request->only(['name', 'description', 'price', 'condition','brand','location']);
        $data['user_id'] = Auth::id();

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
            'description' => 'required|string',
            'location' => 'required|string',
            'brand' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:new,used,refurbished',
            'image' => 'nullable|image|max:8048',
        ]);

        $data = $request->only(['name', 'description', 'price', 'condition']);

        if ($request->hasFile('image')) {
            // Delete the old image if exists
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
