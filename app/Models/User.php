<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Lesson;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Testimonial;
use App\Models\TrainerDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'nick_name',
        'role_id',
        'email',
        'password',
        'profile_url',
        'profile_public_id',
        'ph_no_telegram',
        'ph_no_whatsapp',
        'date_of_birth',
        'place_of_birth',
        'address',
        'daily_routine_for_weekly',
        'special_request'
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
        'is_verified' => 'boolean',
        'is_premium' => 'boolean',
        'is_first_time_appointment' => 'boolean',
        'date_of_birth' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $attributes = [
        'is_verified' => false,
        'is_premium' => false,
        'is_first_time_appointment' => true,
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_user')
            ->withPivot('is_completed')
            ->withTimestamps();
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'subscription_user')
            ->withPivot('status', 'start_date', 'end_date')
            ->withTimestamps();
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    public function trainerDetails()
    {
        return $this->hasOne(TrainerDetail::class, 'trainer_id');
    }
}
