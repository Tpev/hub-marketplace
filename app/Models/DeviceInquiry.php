<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceInquiry extends Model
{
	protected $fillable = [
    'user_id',
    'medical_device_id',
];

public function user()
{
    return $this->belongsTo(User::class);
}

public function medicalDevice()
{
    return $this->belongsTo(MedicalDevice::class);
}

}
