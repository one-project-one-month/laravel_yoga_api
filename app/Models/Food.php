<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'ingredients',
        'created_by',
        'nutrition',
        'profile_url',
        'profile_public_id',
        'description',
        'rating'
    ];
}
