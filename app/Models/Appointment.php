<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appointment_date',
        'appointment_time',
        'appointment_type',
        'appointment_fees',
        'meet_link',
        'is_approved',
        'is_completed'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_fees' => 'decimal:2',
        'is_completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
