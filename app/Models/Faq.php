<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;
      protected $fillable = [
        'title',
        'slug',
        'main_title',
        'second_banner_heading',
        'second_banner_sub_heading',
        'button_label',
        'button_link',
        'question',
        'answer',
    ];
}
