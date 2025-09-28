<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonUser extends Model
{
    use HasFactory;

    protected $table = 'lesson_user';
    protected $fillable = ['user_id', 'lesson_id', 'is_completed'];
    public $timestamps = true;
}
