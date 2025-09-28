<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonTrainer extends Model
{
    use HasFactory;

    protected $table = 'lesson_trainer';
    protected $fillable = ['trainer_id', 'lesson_type_id'];
    public $timestamps = true;
}
