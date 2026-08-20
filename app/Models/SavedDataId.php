<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedDataId extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id',
        'status',
    ];

    public function savedQuestions(){
        return $this->hasMany(SaveContractQuestion::class,'saved_id','id');
    }

    public function savedDocument(){
        return $this->hasOne(Document::class,'id','document_id');
    }
}
