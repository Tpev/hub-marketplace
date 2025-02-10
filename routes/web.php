<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MedicalDeviceController;
use App\Http\Controllers\ContactRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
         ->name('admin.dashboard');
});


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Register web routes for your application here.
|
*/

// ----------------------------------------
// 1. Public Routes
// ----------------------------------------



// Medical Devices Listing (Accessible to all users)
Route::get('/', [MedicalDeviceController::class, 'index'])->name('medical_devices.index');

// Medical Device Details (Accessible to all users)
Route::get('/list/medical_devices/{medical_device}', [MedicalDeviceController::class, 'show'])->name('medical_devices.show');

// ----------------------------------------
// 2. Authenticated Routes
// ----------------------------------------
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Medical Device Management (Create, Store, Edit, Update, Destroy)
    Route::get('/medical_devices/create', [MedicalDeviceController::class, 'create'])->name('medical_devices.create');
    Route::post('/medical_devices', [MedicalDeviceController::class, 'store'])->name('medical_devices.store');
    Route::get('/medical_devices/{medical_device}/edit', [MedicalDeviceController::class, 'edit'])->name('medical_devices.edit');
    Route::put('/medical_devices/{medical_device}', [MedicalDeviceController::class, 'update'])->name('medical_devices.update');
    Route::delete('/medical_devices/{medical_device}', [MedicalDeviceController::class, 'destroy'])->name('medical_devices.destroy');

    // Contact Requests
    Route::get('/medical_devices/{medical_device}/contact', [ContactRequestController::class, 'create'])->name('contact_requests.create');
    Route::post('/medical_devices/{medical_device}/contact', [ContactRequestController::class, 'store'])->name('contact_requests.store');
});


// ----------------------------------------
// 3. Authentication Routes
// ----------------------------------------

// Includes routes for login, registration, password reset, etc.
require __DIR__.'/auth.php';
