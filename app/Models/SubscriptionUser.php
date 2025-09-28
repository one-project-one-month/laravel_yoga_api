<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUser extends Model
{
    use HasFactory;

    protected $table = 'subscription_user';
    protected $fillable = ['user_id', 'subscription_id'];
    public $timestamps = true;
}
