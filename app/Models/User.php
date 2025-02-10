<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Fillable fields
    protected $fillable = [
        'name',
        'email',
        'password',
		'role',
    ];

    // Hidden fields
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relationships
    public function medicalDevices()
    {
        return $this->hasMany(MedicalDevice::class);
    }

    public function contactRequests()
    {
        return $this->hasMany(ContactRequest::class, 'receiver_id');
    }
}
