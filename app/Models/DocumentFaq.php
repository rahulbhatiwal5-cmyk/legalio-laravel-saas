<?php

namespace App\Models;
use App\models\document;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentFaq extends Model
{
    use HasFactory;
  
    protected $table = 'document_faqs';

    protected $fillable = [
        'document_id',
        'question',
        'answer',
        'is_ai',   
        'status',
    ];


    public function document()
    {
        return $this->belongsTo(Document::class);
    }
    
}
