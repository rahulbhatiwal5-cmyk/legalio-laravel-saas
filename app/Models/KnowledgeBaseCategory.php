<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'short_description' ,'slug'];
    
    public function article(){
        return $this->hasMany(KnowledgeBaseArticle::class,'category_id');
    }
}
