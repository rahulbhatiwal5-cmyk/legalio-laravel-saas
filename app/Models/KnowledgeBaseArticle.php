<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseArticle extends Model
{
    use HasFactory;

    protected $fillable = [  'category_id', 'title', 'seo', 'seo_description', 
        'heading', 'sub_heading', 'article_overview','slug'];

    public function category(){
        return $this->belongsTo(KnowledgeBaseCategory::class,'category_id');
    }

    public function contents()
    {
        return $this->hasMany(ArticleContent::class,'article_id','id');
    }
}
