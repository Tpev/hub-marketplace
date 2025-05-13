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
		'location', // legacy field
		'city',
		'state',
		'country',
		'description',
		'price',
		'price_new',
		'quantity',
		'shipping',
		'condition',
		'main_category',
		'aux_category',
		'image',
	];

public function getFullLocationAttribute()
{
    if ($this->city || $this->state || $this->country) {
        return trim(collect([$this->city, $this->state, $this->country])->filter()->implode(', '), ', ');
    }

    return $this->location;
}

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
