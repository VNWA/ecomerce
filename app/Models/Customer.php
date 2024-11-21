<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Str;

class Customer extends Model
{
    use HasFactory, HasApiTokens, Notifiable; // Sử dụng trait này

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'company',
        'post_code',
        'city',
        'address',
        'address_number',
        'otp',
        'otp_expires_at',
        'email_verified_at'
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $hidden = ['email_verified_at', 'otp', 'password'];

    protected $appends = ['emailVerified'];

    public function getEmailVerifiedAttribute()
    {
        return !is_null($this->email_verified_at); // Trả về true nếu đã xác thực email
    }

}
