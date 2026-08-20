<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiFaq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'status',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'faq_tag', 'faq_id', 'tag_id')->withTimestamps();
    }

    protected static function booted() {
        static::deleting(function ($faq) {
            $faq->tags()->detach();
        });
    }

}
