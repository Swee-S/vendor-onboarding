<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        // Existing
        'user_id',
        'status',

        // Business
        'business_name',
        'business_type',

        // Contact
        'contact_person_name',
        'contact_email',
        'contact_mobile',

        // Identity
        'pan_number',
        'gst_number',

        // Address
        'address',
        'city',
        'state',
        'pincode',

        // Bank — encrypted column only, never plain account_number
        'account_holder_name',
        'account_number_encrypted',
        'ifsc_code',
    ];

    // -------------------------------------------------------
    // Encryption: decrypt account number when reading
    // -------------------------------------------------------

    public function getAccountNumberAttribute(): string
    {
        if (empty($this->account_number_encrypted)) {
            return '';
        }

        try {
            return Crypt::decryptString($this->account_number_encrypted);
        } catch (\Exception $e) {
            return ''; // corrupted or wrong key
        }
    }

    // -------------------------------------------------------
    // Masking helpers — call these in views/resources
    // -------------------------------------------------------

    public function maskedAccountNumber(): string
    {
        $plain = $this->account_number;
        return 'XXXXXXXX' . substr($plain, -4);
    }

    public function maskedPan(): string
    {
        // ABCDE1234F → ABCDE****F
        return substr($this->pan_number, 0, 5)
             . '****'
             . substr($this->pan_number, -1);
    }

    public function maskedGst(): string
    {
        if (empty($this->gst_number)) return '';
        // 27ABCDE1234F1Z5 → 27ABCDE****F1Z5
        return substr($this->gst_number, 0, 7)
             . '****'
             . substr($this->gst_number, 11);
    }

    public function maskedMobile(): string
    {
        // 9876543210 → 98XXXX3210
        return substr($this->contact_mobile, 0, 2)
             . 'XXXX'
             . substr($this->contact_mobile, -4);
    }

    // -------------------------------------------------------
    // Convenience: should this viewer see unmasked data?
    // -------------------------------------------------------

    public function isVisibleTo(User $user): bool
    {
        return $user->id === $this->user_id || $user->is_admin;
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

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