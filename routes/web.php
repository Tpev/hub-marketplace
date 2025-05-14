<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MedicalDeviceController;
use App\Http\Controllers\ContactRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\DeviceInquiryController;
use App\Http\Controllers\BuyerInquiryController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Register web routes for your application here.
|
*/
Route::get('/sitemap.xml', function () {
    return response()->file(public_path('sitemap.xml'), ['Content-Type' => 'application/xml']);
});

// ----------------------------------------
// 1. Public Routes
// ----------------------------------------
Route::prefix('admin/blog')->middleware('auth')->name('admin.blog.')->group(function () {
    Route::resource('/', \App\Http\Controllers\Admin\BlogPostController::class)->parameters(['' => 'blog']);
});


Route::post('/buyer-inquiries', [BuyerInquiryController::class, 'store'])->name('buyer-inquiries.store');

Route::post('/device-inquiry', [\App\Http\Controllers\DeviceInquiryController::class, 'store'])
    ->name('device-inquiry.store')->middleware('auth');



Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])
         ->name('admin.dashboard');
});


// Medical Devices Listing (Accessible to all users)
Route::get('/', function () {
    return view('landing');
});
Route::get('/marketplace', [MedicalDeviceController::class, 'index'])->name('medical_devices.index');
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
    
	// sub
	Route::get('/subscribe', fn() => view('auth.subscribe'))->name('subscribe.page');
	Route::post('/api/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handle']);
	Route::get('/subscribe/success/{tier}', [SubscribeController::class, 'success'])->name('subscribe.success');


});

Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/article/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');


// ----------------------------------------
// 3. Authentication Routes
// ----------------------------------------

// Includes routes for login, registration, password reset, etc.
require __DIR__.'/auth.php';
