<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalContractQuestion extends Model
{
    use HasFactory;


    public function questionData()
    {
        return $this->hasOne(GlobalContractQuestionData::class, 'question_id');
    }

    public function conditions()
    {
        return $this->hasMany(GlobalContractQuestionCondition::class, 'question_id');
    }


    public function options()
    {
        return $this->hasMany(GlobalContractMultipleChoiceQuestion::class, 'question_id');
    }

    public function nextQuestion()
    {
        return $this->belongsTo(GlobalContractQuestion::class, 'next_question_id');
    }


    public function getName()
    {
        return $this->id;
    }

    public function toPromptFormat()
    {
        $questionData = $this->questionData;
        
        $formatted = [
            'TYPE' => $this->type ?? 'TEXTBOX',
            'label' => $questionData->question_label ?? '',
            'placeholder' => $questionData->text_box_placeholder ?? '',
            'userinfo' => $questionData->question_info_text ?? '',
            'standard_document_id' => $this->document_id ?? '',
        ];

        if (!empty($questionData->goto)) {
            $formatted['next_question_id'] = 'QID' . $questionData->next_question_id;
        }

        if (in_array($this->type, ['dropdown', 'radio-button'])) {
            $formatted['options'] = $this->options->map(function ($opt) {
                return [
                    'option_label' => $opt->option_label,
                    'option_value' => $opt->option_value,
                    'go_next_step' => 'QID' . $opt->next_question_id,
                ];
            })->toArray();
        }

        if ($this->conditions && $this->conditions->isNotEmpty()) {
            foreach ($this->conditions as $condition) {
                if ($condition->condition_type === 'go_to_step_condition') {
                    $formatted['goto_if'][] = [
                        'question_id' => 'QID' . $condition->conditional_question_id,
                        'conditions' => $condition->conditional_check,
                        'question_value' => $condition->conditional_question_value,
                    ];
                    $formatted['conditional_go_to_step'] = 'QID' . $questionData->conditional_go_to_step;
                    $formatted['is_condition'] = $this->is_condition;
                    $formatted['condition_type'] = 2;

                } elseif ($condition->condition_type === 'question_label_condition') {
                    $formatted['question_label_condition'][] = [
                        'label' => $condition->question_label,
                        'question_id' => 'QID' . $condition->conditional_question_id,
                        'value' => $condition->conditional_question_value,
                    ];
                    $formatted['is_condition'] = $this->is_condition;
                    $formatted['condition_type'] = 1;

                } elseif ($condition->condition_type === 'another_go_to_step_condition') {
                    $formatted['another_go_to_step_condition'] ??= [];
                    $subconditionsFormatted = [];

                    foreach ($condition->subconditions as $sub) {
                        $subconditionsFormatted[] = [
                            'question_id' => 'QID' . $sub->conditional_question_id,
                            'conditions' => $sub->conditional_check,
                            'question_value' => $sub->conditional_question_value,
                        ];
                    }

                    $formatted['another_go_to_step_condition'][] = [
                        'conditional_go_to_step' => 'QID' . $condition->go_to_step,
                        'subconditions' => $subconditionsFormatted,
                    ];
                }                 
            }
        }
        return $formatted;
    }

}