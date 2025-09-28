<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'admin_id', 'trainer_id',
        'appointment_date', 'appointment_fees',
        'meet_link', 'is_approved', 'is_completed'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'appointment_fees' => 'decimal:2',
        'is_approved' => 'boolean',
        'is_completed' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
