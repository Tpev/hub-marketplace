<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyerInquiry extends Model
{
    protected $fillable = [
        'name',
        'email',
        'message',
    ];
}
