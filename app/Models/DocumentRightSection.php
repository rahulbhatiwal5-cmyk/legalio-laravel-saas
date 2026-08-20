<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRightSection extends Model
{
    use HasFactory;
    // protected $fillable = ['type'];
    protected $fillable = [
        'document_id', 'standard_section_id', 'type', 'content', 'content2', 'content3',
        'order_id', 'is_condition', 'text_align', 'text_alignment', 'signature_field',
        'secure_blur_content', 'published'
    ];

    public function conditions(){
        return $this->hasMany(QuestionCondition::class,'document_right_content_id','id');
    }

    public function document(){
        return $this->hasMany(Document::class,'id','document_id');
    }

    public function standardDocument()
{
    return $this->belongsTo(StandardDocument::class, 'standard_section_id');
}
}
