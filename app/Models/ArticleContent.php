<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleContent extends Model
{
    use HasFactory;

    protected $fillable = ['article_id', 'content_heading', 'content_description'];

    public function article()
    {
        return $this->belongsTo(KnowledgeBaseArticle::class ,'article_id');
    }
}
