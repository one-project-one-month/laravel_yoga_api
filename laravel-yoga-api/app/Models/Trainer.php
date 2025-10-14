<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'bio',
    ];

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_trainer');
    }
}