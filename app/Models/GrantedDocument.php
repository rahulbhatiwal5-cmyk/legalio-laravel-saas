<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrantedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'document_id',
        'start_date', 
        'granted_document_id',
        'plan_id',
        'free_interval',
        'interval_type'
    ];
}
