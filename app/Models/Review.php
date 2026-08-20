<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',     
        'document_id',
        'rating',
        'description',
        'status',
        'type',
    ];

    public function media(){
        return $this->hasOne(Media::class,'id','media_id');
    }

    public function document(){
        return $this->hasOne(Document::class,'id','document_id');
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}