<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreeSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'status',
        'subscription_id',
        'start_date',
        'end_date',
        'type'
    ];
}
