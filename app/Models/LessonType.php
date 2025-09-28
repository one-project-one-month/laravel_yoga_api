<?php

namespace App\Models;

use App\Models\Lesson;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function trainers()
    {
        return $this->belongsToMany(User::class, 'lesson_trainer', 'lesson_type_id', 'trainer_id')
                    ->withTimestamps();
    }
}
