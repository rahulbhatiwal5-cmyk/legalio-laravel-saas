<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
   
class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value','name',           // Add name for human-readable field names
        'model_ref',      // Add model_ref to associate settings with specific models
        'type',];
}

