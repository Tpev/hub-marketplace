<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with all users and their counts.
     */
    public function index()
    {
        // Ensure the user is authenticated and has the admin role
        if (! auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Eager load counts for medicalDevices and contactRequests
        $users = User::withCount(['medicalDevices', 'contactRequests'])->get();

        return view('admin.dashboard', compact('users'));
    }
}
