<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use App\Models\MedicalDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactRequestController extends Controller
{
    // Ensure authentication
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show the form to send a contact request
    public function create(MedicalDevice $medicalDevice)
    {
        return view('contact_requests.create', compact('medicalDevice'));
    }

    // Store the contact request
    public function store(Request $request, MedicalDevice $medicalDevice)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        // Prevent users from contacting themselves
        if ($medicalDevice->user_id === Auth::id()) {
            return redirect()->back()->withErrors('You cannot contact yourself.');
        }

        ContactRequest::create([
            'medical_device_id' => $medicalDevice->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $medicalDevice->user_id,
            'message' => $request->input('message'),
        ]);

        // Optionally, send an email notification to the receiver
        // Mail::to($medicalDevice->user->email)->send(new ContactRequestMail(Auth::user(), $medicalDevice, $request->input('message')));

        return redirect()->route('medical_devices.show', $medicalDevice)->with('success', 'Contact request sent successfully.');
    }

    // Optionally, add methods to view received contact requests
}
