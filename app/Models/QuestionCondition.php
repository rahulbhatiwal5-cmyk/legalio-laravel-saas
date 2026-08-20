<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionCondition extends Model
{
    use HasFactory;
    protected $fillable = ['question_id',
                            'condition_type',
                            'question_label',
                            'conditional_question_id',
                            'conditional_question_value',
                            'conditional_check',
                            'status',
                            'go_to_step',
                            'document_right_content_id',
                        ];

    // Define inverse relationship with Question
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Define relationship with conditional question
    public function conditionalQuestion()
    {
        return $this->belongsTo(Question::class, 'conditional_question_id');
    }

    public function subconditions()
    {
        return $this->hasMany(SubCondition::class);
    }

    public function documentRightSection()
    {
        return $this->belongsTo(DocumentRightSection::class, 'document_right_content_id', 'id');
    }
}
