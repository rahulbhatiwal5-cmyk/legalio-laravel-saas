<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalContractQuestionData extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'question_label',
        'textbox_id',
        'next_question_id',
        'same_contract_link_label',
        'conditional_go_to_step',
        'text_box_placeholder',
        'question_info_text',
    ];

    protected $table='global_contract_question_data';
   
    public function question()
    {
        return $this->belongsTo(GlobalContractQuestion::class);
    }

  
    public function nextQuestion()
    {
        return $this->belongsTo(GlobalContractQuestion::class, 'next_question_id');
    }
}
