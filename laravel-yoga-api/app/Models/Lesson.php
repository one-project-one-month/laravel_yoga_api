<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'duration_minutes',
        'order',
    ];

    public function trainers()
    {
        return $this->belongsToMany(Trainer::class, 'lesson_trainer');
    }
}