<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingAdress extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company',
        'company_2',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    public function user()
    {
        return $this->belongsTo(User::class ,'user_id' ,'id');
    }

}
