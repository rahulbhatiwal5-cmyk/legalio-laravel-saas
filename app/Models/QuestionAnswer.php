<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAnswer extends Model
{
    use HasFactory;

    public function category(){
      return $this->hasOne(FaqCategory::class,'id','category_id');
    }
    
}
