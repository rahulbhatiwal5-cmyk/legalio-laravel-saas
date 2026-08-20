<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalContractText extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'document_id',
        'content',
        'order_id',
        'is_condition',
        'text_align',
        'text_alignment',
        'signature_field',
        'content2',
        'content3',
        'secure_blur_content',
        'published',
    ];

    public function conditions(){
        return $this->hasMany(GlobalContractQuestionCondition::class,'document_right_content_id','id');
    }

    public function document(){
        return $this->hasMany(StandardDocument::class,'id','document_id');
    }

    public function toPromptFormat()
    {
        $formatted = [
            'TYPE' => $this->type ?? 'CONTENT',
            'TEXT' => $this->content ?? '',
            'ALIGN_TEXT' => strtolower($this->text_align) ?? 'left',
            'BLUR_CONTENT' => (bool) $this->secure_blur_content,
            'standard_document_id' => $this->document_id ?? '',
        ];
        
        if ($this->conditions && $this->conditions->isNotEmpty()) {
            $formatted['CONDITIONS'] = $this->conditions->map(function ($condition) {
                return [
                    'question_id' => 'QID' . $condition->conditional_question_id,
                    'question_value' => $condition->conditional_question_value,
                    'conditions' => $condition->conditional_check,
                ];
            })->toArray();
        }

        return $formatted;
    }

}
