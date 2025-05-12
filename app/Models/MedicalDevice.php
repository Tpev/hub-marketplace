<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
		'brand',
		'location',
        'description',
        'price',
        'condition',
        'image',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contactRequests()
    {
        return $this->hasMany(ContactRequest::class);
    }
	
	public function deviceInquiries()
{
    return $this->hasMany(DeviceInquiry::class);
}

}
