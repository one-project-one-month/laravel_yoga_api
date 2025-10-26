<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOtps extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'otp_code',
        'expired_at',
        'token'
    ];
}
