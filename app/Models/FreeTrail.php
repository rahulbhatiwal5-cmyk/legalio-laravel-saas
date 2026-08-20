<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeTrail extends Model
{
    protected $table = 'free_trial_subscriptions';
    
    protected $fillable = [
        'user_id',
        'order_id',
        'subscription_id',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}