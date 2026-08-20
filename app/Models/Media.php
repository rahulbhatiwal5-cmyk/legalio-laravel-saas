<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'directory_name',
        'file_path',
        'file_size',
        'file_format',
        'status'
        
    ];

}
