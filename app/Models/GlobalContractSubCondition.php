<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalContractSubCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_condition_id',
        'conditional_question_id',
        'conditional_question_value',
        'conditional_check',
    ];

    public function conditionalQuestion()
    {
        return $this->belongsTo(GlobalContractQuestionCondition::class, 'question_condition_id');
    }
}
