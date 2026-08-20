<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalContractQuestionCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
    ];

    public function question()
    {
        return $this->belongsTo(GlobalContractQuestion::class, 'question_id');
    }

    public function conditionalQuestion()
    {
        return $this->belongsTo(GlobalContractQuestion::class, 'conditional_question_id');
    }

    public function subconditions()
    {
        return $this->hasMany(GlobalContractSubCondition::class, 'question_condition_id');
    }
}
