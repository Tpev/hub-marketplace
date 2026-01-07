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
        'shipping', // boolean stored in DB
        'condition',
        'main_category',
        'aux_category',
        'image',
		'shipping_available',


        // relink sync fields
        'source',
        'source_external_id',
        'source_url',
        'source_lastmod',
        'last_seen_run_id',
        'is_active',
        'synced_at',
    ];

    protected $casts = [
        'shipping' => 'boolean',
		'shipping_available' => 'boolean',
        'is_active' => 'boolean',
        'source_lastmod' => 'datetime',
        'synced_at' => 'datetime',
        'price' => 'decimal:2',
        'price_new' => 'decimal:2',
		'source_lastmod' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function getFullLocationAttribute()
    {
        if ($this->city || $this->state || $this->country) {
            return trim(collect([$this->city, $this->state, $this->country])->filter()->implode(', '), ', ');
        }

        return $this->location;
    }

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
