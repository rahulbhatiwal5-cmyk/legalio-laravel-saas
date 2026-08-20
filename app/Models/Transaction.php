<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'payment_type',
        'status',
        'type',
        'order_id',
        'pay_type',
        'stripe_customer_id',
        'paypal_sale_id',
    ];

    public function order(){
        return $this->hasOne(Order::class,'id','order_id');
    }
}
