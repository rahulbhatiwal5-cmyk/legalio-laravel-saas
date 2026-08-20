<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailRecoveryPassword extends Model
{
    use HasFactory;
    protected $fillable = [
        'subject',
        'heading',
        'body',
        'button_text',
        'footer',
        'email_name',
        'email_type'
    ];
}
