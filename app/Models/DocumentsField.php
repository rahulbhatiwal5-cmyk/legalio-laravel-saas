<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentsField extends Model
{
    use HasFactory;
    protected $fillable = [
        'document_id',
        'heading',
        'description',
        'description2',
        'media_id'
    ];
    

    public function media(){
        return $this->hasOne(Media::class,'id','media_id');
    }
}
