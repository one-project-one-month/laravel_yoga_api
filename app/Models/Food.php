<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'ingredients',
        'created_by',
        'nutrition',
        'image_url',
        'image_public_id',
        'description',
        'rating'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
