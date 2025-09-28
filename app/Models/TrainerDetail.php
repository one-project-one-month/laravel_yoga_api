<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id', 'bio', 'description', 'salary', 'branch_location'
    ];

    protected $casts = [
        'salary' => 'decimal:2',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
