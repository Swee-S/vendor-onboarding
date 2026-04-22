<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorStatusLog extends Model
{
    protected $fillable = [
        'vendor_id',
        'user_id',
        'from_status',
        'to_status',
        'remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}