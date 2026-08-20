<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeCategories extends Model
{
    use HasFactory;

    protected $fillable = [
        'heading',
        'category_description',
        'btn_text',
        'btn_link',
        'category_id', 
    ];

    public function media(){
        return $this->hasOne(Media::class,'id','media_id');
    }

    public function category(){
        return $this->hasOne(DocumentCategory::class,'id','category_id');
    }
}
