<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCredit extends Model
{
    use HasFactory;
    
    protected $table = 'user_credits';

    protected $fillable = [
        'user_id',
        'document_id',
        'balance',
    ];


}
