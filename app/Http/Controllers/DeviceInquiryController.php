<?php

use App\Models\DeviceInquiry;
use Illuminate\Http\Request;

class DeviceInquiryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:medical_devices,id',
        ]);

        DeviceInquiry::firstOrCreate([
            'user_id' => auth()->id(),
            'medical_device_id' => $request->device_id,
        ]);

        return response()->json(['message' => 'Inquiry logged.']);
    }
}
