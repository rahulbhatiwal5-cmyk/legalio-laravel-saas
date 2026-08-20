<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultipleChoiceQuestionOption extends Model
{
    use HasFactory;
    protected $fillable = ['question_id'];

    // Define inverse relationship with Question
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Self-referencing relationship for the next question
    public function nextQuestion()
    {
        return $this->belongsTo(Question::class, 'next_question_id');
    }
}
