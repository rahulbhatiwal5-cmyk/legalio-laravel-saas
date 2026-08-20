<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'plan_id',
        'status',
        'order_id',
        'stripe_status',
        'paypal_subscription_id', 
        'paypal_plan_id',         
        'paypal_status',          
        'payment_gateway',        
        'is_paused',
        'pause_start_at',
        'pause_end_at',
        'pause_behavior',
        'start_date',
        'end_date',
        'current_period_start_date',
        'current_period_end_date',
    ];

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function plan(){
        return $this->hasOne(Plans::class,'id','plan_id');
    }

    public function order(){
        return $this->hasOne(Order::class,'id','order_id');
    }
}
