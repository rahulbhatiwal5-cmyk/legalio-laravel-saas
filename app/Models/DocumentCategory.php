<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    use HasFactory;

    protected $table = 'document_categories';

    public function documents(){
        return $this->belongsToMany(Document::class,'document_with_categories','category_id','document_id')->where('published',1)->withCount('orders')->orderByDesc('orders_count') ;
    }
}
