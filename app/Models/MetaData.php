<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaData extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
        'key',
        'value',
        'file_path',
        'type',
        'status'
    ];
}
