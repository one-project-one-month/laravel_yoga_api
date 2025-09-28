<?php

namespace App\Models;

use App\Models\LessonType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'level',
        'video_url', 'lesson_type_id', 'duration_minutes',
        'is_free', 'is_premium', 'trainer_id'
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'is_free' => 'boolean',
        'is_premium' => 'boolean',
    ];

    public function lessonType()
    {
        return $this->belongsTo(LessonType::class);
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'lesson_user')
                    ->withPivot('is_completed')
                    ->withTimestamps();
    }
}
