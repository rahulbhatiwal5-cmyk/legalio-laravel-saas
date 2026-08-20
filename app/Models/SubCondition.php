<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCondition extends Model
{
    use HasFactory;

    public function conditionalQuestion()
    {
        return $this->belongsTo(QuestionCondition::class, 'question_condition_id');
    }
}
