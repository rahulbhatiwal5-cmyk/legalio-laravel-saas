<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    use HasFactory;
     protected $fillable = [
        'key',
        'value',
        'type', // Add your key here
        'terms',
        'condition'
        // other fields...
    ];
}
