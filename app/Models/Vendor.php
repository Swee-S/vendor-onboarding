<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'pan',
        'user_id',
        'status'
    ];

   
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function statusLogs()
    {
        return $this->hasMany(VendorStatusLog::class);
    }

  
    public function latestStatusLog()
    {
        return $this->hasOne(VendorStatusLog::class)->latest();
    }
}