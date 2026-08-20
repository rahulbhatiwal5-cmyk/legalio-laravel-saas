<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'user_id',
        'event_type',
        'description',
        'payload',
        'status'
    ];
}
