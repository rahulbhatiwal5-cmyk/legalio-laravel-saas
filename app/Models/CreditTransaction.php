<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'document_id',
        'period_start',
        'period_end',
        'interval',
        'carry_forward',
        'used_amount',
        'amount',
        'type',
        'purchase_date',
        'transaction_date',
        'description',
        'subscription_id',
    ];
}
