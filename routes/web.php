<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\MedicalDeviceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\DeviceInquiryController;
use App\Http\Controllers\BuyerInquiryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Register web routes for your application here.
*/

// ----------------------------------------------------------------------------
// Static / public assets
// ----------------------------------------------------------------------------
Route::get('/sitemap.xml', function () {
    return response()->file(public_path('sitemap.xml'), ['Content-Type' => 'application/xml']);
});

// ----------------------------------------------------------------------------
/** 1) PUBLIC ROUTES (no auth) */
// ----------------------------------------------------------------------------

// Landing
Route::get('/', fn () => view('landing'));

// Marketplace index & public show
Route::get('/marketplace', [MedicalDeviceController::class, 'index'])->name('medical_devices.index');
Route::get('/list/medical_devices/{medical_device}', [MedicalDeviceController::class, 'show'])->name('medical_devices.show');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/article/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Buyer inquiries (public form -> store). Laisser public si tu veux autoriser les non logués.
Route::post('/buyer-inquiries', [BuyerInquiryController::class, 'store'])->name('buyer-inquiries.store');

// Stripe webhook MUST be public (et CSRF-exempt via middleware kernel)
Route::post('/api/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// ----------------------------------------------------------------------------
/** 2) AUTH-ONLY ROUTES */
// ----------------------------------------------------------------------------
Route::middleware(['auth'])->group(function () {

    // Admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // (Existant) Back-office blog sous /admin/blog
    Route::prefix('admin/blog')->name('admin.blog.')->group(function () {
        // NOTE: la ressource sur "/" est atypique mais je garde ta structure
        Route::resource('/', \App\Http\Controllers\Admin\BlogPostController::class)->parameters(['' => 'blog']);
    });

    // Dashboard user
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Device inquiry (réserver aux connectés)
    Route::post('/device-inquiry', [DeviceInquiryController::class, 'store'])->name('device-inquiry.store');

    // Paywall / Subscribe
    Route::get('/subscribe', fn () => view('auth.subscribe'))->name('subscribe.page');
    Route::get('/subscribe/success/{tier}', [SubscribeController::class, 'success'])->name('subscribe.success');

    // ----------------------------------------------------------------------------
    // Medical Devices CRUD (création & publication soumises au paywall)
    // ----------------------------------------------------------------------------

    // CREATE + STORE protégés par le middleware 'licensed'
    Route::middleware('licensed')->group(function () {
        Route::get('/medical_devices/create', [MedicalDeviceController::class, 'create'])->name('medical_devices.create');
        Route::post('/medical_devices', [MedicalDeviceController::class, 'store'])->name('medical_devices.store');
    });

    // Edit/Update/Destroy : auth requis + contrôle d'ownership dans le controller
    Route::get('/medical_devices/{medical_device}/edit', [MedicalDeviceController::class, 'edit'])->name('medical_devices.edit');
    Route::put('/medical_devices/{medical_device}', [MedicalDeviceController::class, 'update'])->name('medical_devices.update');
    Route::delete('/medical_devices/{medical_device}', [MedicalDeviceController::class, 'destroy'])->name('medical_devices.destroy');
});

// ----------------------------------------------------------------------------
/** 3) AUTH SCF (login/register/forgot...) */
// ----------------------------------------------------------------------------
require __DIR__ . '/auth.php';
