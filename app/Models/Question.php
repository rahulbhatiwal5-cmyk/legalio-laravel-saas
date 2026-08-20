<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded=[];
    protected $fillable = [
        'document_id',
        'qid', 
        'type',
        'condition_type',
        'is_end',
        'is_condition',
        'standard_section_id',
        'order_id',
    ];

    // Define relationship with QuestionData
    public function questionData()
    {
        return $this->hasOne(QuestionData::class);
    }

    // Define relationship with QuestionCondition
    public function conditions()
    {
        return $this->hasMany(QuestionCondition::class);
    }

    // Define relationship with MultipleChoiceQuestionOption
    public function options()
    {
        return $this->hasMany(MultipleChoiceQuestionOption::class);
    }

    // Define self-referencing relationship for the next question
    public function nextQuestion()
    {
        return $this->belongsTo(Question::class, 'next_question_id');
    }

    // public function nextQuestion()
    // {
    //     return $this->hasMany(QuestionData::class, 'next_question_id','id');
    // }


    public function getName()
    {
        return $this->id;
    }

}
