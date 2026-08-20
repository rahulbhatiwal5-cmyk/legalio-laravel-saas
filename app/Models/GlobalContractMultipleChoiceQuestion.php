<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalContractMultipleChoiceQuestion extends Model
{
    use HasFactory;

    public function question()
    {
        return $this->belongsTo(GlobalContractQuestion::class);
    }

    public function nextQuestion()
    {
        return $this->belongsTo(GlobalContractQuestion::class, 'next_question_id');
    }
}
