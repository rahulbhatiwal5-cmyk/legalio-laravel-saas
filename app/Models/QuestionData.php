<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionData extends Model
{
    use HasFactory;
    protected $fillable = ['question_id'];

    protected $table='question_datas';
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
