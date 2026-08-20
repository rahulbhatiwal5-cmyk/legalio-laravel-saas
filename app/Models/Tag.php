<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable =['name' , 'slug'];

    public function faqs()
    {
        return $this->belongsToMany(AiFaq::class, 'faq_tag', 'tag_id', 'faq_id')->withTimestamps();
    }
}
