<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name','email','password','intent','is_subscribed','license_tier','user_type','business_type',
    ];

    // ✅ Ensure boolean casting
    protected $casts = [
        'is_subscribed' => 'boolean',
    ];

    // Relationships...
    public function medicalDevices() { return $this->hasMany(MedicalDevice::class); }
    public function contactRequests() { return $this->hasMany(ContactRequest::class, 'receiver_id'); }
    public function deviceInquiries() { return $this->hasMany(DeviceInquiry::class); }

    // ✅ Correct helper
    public function hasActiveLicense(): bool
    {
        // Use Eloquent attribute access, not property_exists
        if ((bool) $this->getAttribute('is_subscribed')) {
            return true;
        }

        if (!empty($this->getAttribute('license_tier'))) {
            return true;
        }

        return false;
    }
}

