<?php

namespace App\Models;

use App\Models\LessonType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'lesson_type_id', 'duration'];

    protected $casts = ['price' => 'decimal:2'];

    public function lessonType()
    {
        return $this->belongsTo(LessonType::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'subscription_user')
                    ->withTimestamps();
    }
}
