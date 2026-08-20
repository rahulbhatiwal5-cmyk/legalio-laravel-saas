<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\DocumentCreated;
use App\Services\AIService;
use App\Models\Document;
use App\Models\DocumentGenerator;
use App\Models\Prompt;
use App\Models\PromptVerification;
use App\Models\Question;
use App\Models\QuestionData;
use App\Models\MultipleChoiceQuestionOption;
use App\Models\QuestionCondition;
use App\Models\SubCondition;
use App\Models\DocumentRightSection;
use App\Models\StandardDocument;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class GenerateDocumentListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    // public $document;
    // public $documentId;
    // public $documentName;
    // public $additionalInfo;
    // public $fileInput;
    // public $isVerified;
    // public $documentGeneratorId;

    // public function __construct(Document $document, $documentId, $documentName, $additionalInfo = null, $fileInput = null, $isVerified = 0, $documentGeneratorId = null)
    // {
    //     $this->document = $document;
    //     $this->documentId = $documentId;
    //     $this->documentName = $documentName;
    //     $this->additionalInfo = $additionalInfo;
    //     $this->fileInput = $fileInput;
    //     $this->isVerified = $isVerified;
    //     $this->documentGeneratorId = $documentGeneratorId;
    // }

    /**
     * Handle the event.
     */
    public function handle(DocumentCreated $event): void
    {
        try{
            $documentId = $event->documentId;
            $document = $event->document;
            $documentName = $event->documentName ?? 'Untitled';
            $additionalInfo = $event->additionalInfo;
            $fileInput = $event->fileInput;
            $isVerified = $event->isVerified;
            $documentGeneratorId = $event->documentGeneratorId;
           
            $prompt = Prompt::where([['key','document_generator'],['location','document']])->first();
            $aiModel = $prompt?->prompt_ai_model;

            $second_prompt = Prompt::where([['key','initial_document_generation'],['location','document']])->first();

            $document = Document::find($documentId);

            if(!$document){
                saveLog("Error:", "GenerateDocumentJob", "Document not found with ID: " . $documentId);
                return; 
            }

            $document_generator_prompt = $prompt?->updated_prompt ?? '';
            $document_generator_prompt2 = $second_prompt?->updated_prompt ?? '';
            $ai_verification_model = $prompt?->ai_verification_model ?? '';

            $promptVerification = PromptVerification::first();
            $verification_prompt = $promptVerification?->ai_prompt ?? '';
            
            $language = web_setting('language')->value;
            $country = web_setting('country')->value;
            $currency = web_setting('country_currency')->value;

            $minPrompt = Prompt::where([['key','minimum_requirements'],['location','document']])->first();
            $minimum_requirements = $minPrompt->updated_prompt;
            $validPrompt = Prompt::where([['key','validation_rules'],['location','document']])->first();
            $validation_rules = $validPrompt->updated_prompt;

            $finalPrompt = str_replace('{document_name}', $documentName, $document_generator_prompt);
            $finalPrompt = str_replace('{language}', $language, $finalPrompt);
            $finalPrompt = str_replace('{country}', $country, $finalPrompt);
            $finalPrompt = str_replace('{currency}', $currency, $finalPrompt);

            if(!empty($request->additional_information)){
                $finalPrompt .= "\n\nAdditional Information: " . $request->additional_information;
            }

            if(!empty($minimum_requirements)){
                $finalPrompt .= "\n\nMinimum Requirements: " . $minimum_requirements;
            }

            if(!empty($validation_rules)){
                $finalPrompt .= "\n\nValidation Rules: " . $validation_rules;
            }

            $filename = '';
            $relativePath = '';


            if($fileInput){
                $file = $fileInput;

                if(is_string($fileInput) && file_exists($fileInput)){
                    $filename = generateFileName($file);
                    $destinationPath = public_path('storage/document_generator');

                    if(!file_exists($destinationPath)){
                        mkdir($destinationPath, 0755, true);
                    }

                    $file->move($destinationPath, $filename);

                    $relativePath = 'storage/document_generator/' . $filename;
                    $publicUrl = asset($relativePath);

                    $finalPrompt .= "\n\nImage here: " . $publicUrl;
                }
            }

            if(!empty($document_generator_prompt2)){
                $finalPrompt2 = str_replace('{document_name}', $documentName, $document_generator_prompt2);
                $finalPrompt2 = str_replace('{language}', $language, $finalPrompt2);
                $finalPrompt2 = str_replace('{country}', $country, $finalPrompt2);

                $finalPrompt .= "\n\nInitial Document Generation: " . $finalPrompt2;
            }

            $document_generator = DocumentGenerator::where([['id', $documentGeneratorId],['document_id', $documentId]])->first() ?? new DocumentGenerator;
            $document_generator->document_name = $documentName;
            $aiOutput = null;
            $decoded = null;
            
            saveLog("AI model:", "GenerateDocumentListener", $aiModel);

            if($aiModel === 'Gemini 2.0' || $aiModel === 'Gemini 2.5 pro'){
                $aiService = new AIService($aiModel);
                saveLog("gemini:", "GenerateDocumentListener", $finalPrompt);

                $aiOutput = $aiService->generateDocumentQuestionAndText($finalPrompt);
                // dd($aiOutput);

                $document_generator->ai_response = json_encode($aiOutput);

                saveLog("Document Generator Output:", "GenerateDocumentListener", $aiOutput);

                $cleanedOutput = trim($aiOutput);
                $cleanedOutput = preg_replace('/^(json|```json|```)\s*/i', '', $cleanedOutput);
                $cleanedOutput = preg_replace('/```$/', '', $cleanedOutput);

                $decoded = json_decode($cleanedOutput, true);
                saveLog("Decoded Gemini:", "GenerateDocumentListener", $decoded);

                if(!is_array($decoded) || empty($decoded)){
                    saveLog("Error:", "GenerateDocumentJob", $aiOutput);
                }
            
                $firstSection = reset($decoded);
                
                if(!is_array($firstSection)){
                    saveLog("Error:", "GenerateDocumentListener", $aiOutput);
                }
                
            }elseif($aiModel === 'chatgpt'){
                $aiService = new AIService($aiModel);

                saveLog("chatgpt:", "GenerateDocumentListener", $finalPrompt);
                $aiOutput = $aiService->generateDocumentQuestionAndTextWithOpenAI($finalPrompt);
                // dd($aiOutput);

                $document_generator->ai_response = json_encode($aiOutput);
                saveLog("Document Generator Output:", "GenerateDocumentListener", $aiOutput);

                $cleanedOutput = trim($aiOutput);
                $cleanedOutput = preg_replace('/^```(?:json)?\s*/mi', '', $cleanedOutput);
                $cleanedOutput = preg_replace('/```$/m', '', $cleanedOutput);               
                $cleanedOutput = preg_replace('/^#+.*\n/m', '', $cleanedOutput);   
                        
                preg_match('/\{(?:[^{}]|(?R))*\}/s', $cleanedOutput, $matches);
                $jsonPart = $matches[0] ?? null;
                
                if(!$jsonPart){
                    saveLog("Error:", "GenerateDocumentListener", "No valid JSON found in AI output");
                }

                $jsonPart = preg_replace('/,\s*([}\]])/', '$1', $jsonPart);
                $jsonPart = trim($jsonPart);
                $decoded = json_decode($jsonPart, true);
                saveLog("Decoded chatgpt:", "GenerateDocumentListener", $decoded);

            }else{
                saveLog("Error:", "GenerateDocumentListener", "Unsupported AI model: " . $aiModel);
                return;
            }
            
            saveLog("Decoded:", "GenerateDocumentListener", $decoded);
            $is_questions = Question::where('document_id', $documentId)->get();

            foreach($is_questions as $q){
                $q->questionData()->delete();
                $q->conditions()->each(function($condition){
                    $condition->subconditions()->delete();
                    $condition->delete();
                });
                $q->options()->delete();
                $q->nextQuestion()->delete();
                $q->delete();
            }
        
            $is_document_right_section = DocumentRightSection::where('document_id', $documentId)->get();
            foreach($is_document_right_section as $s){
                $s->conditions()->delete();
                $s->delete();
            }

            $is_standard_section = StandardDocument::where('document_id', $documentId)->get();

            foreach($is_standard_section as $section){
                $section->delete(); 
            }

            foreach($decoded as $sectionBlocks){
                $this->saveStandardDocument($documentId, $sectionBlocks);
            }

            $document_generator->document_id = $documentId;
            $document_generator->additional_information = $additionalInfo;
            $document_generator->is_verified = $isVerified ?? 0;

            if($document_generator->exists && !empty($document_generator->file_path)){
                $oldFilePath = public_path($document_generator->file_path);
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }
            $document_generator->file_name = $filename;
            $document_generator->file_path = $relativePath;
            $document_generator->ai_status = 2;
            $document_generator->save();

            saveLog("document generator save:", "GenerateDocumentListener", 'Document generation completed');
    
        }catch(Exception $e){
            saveLog("Error Exception class GenerateDocumentListener", "GenerateDocumentListener", $e->getMessage());
            
        }
       
    }

    protected function saveStandardDocument($documentId, $sectionBlocks)
    {
        $standardDocumentID = '';
        $questionData = $sectionBlocks['Questionnaire'] ?? [];
        $rightContentData = $sectionBlocks['Contract_Text'] ?? [];
    
        $qidToRealIdMap = [];
        $questionModels = [];
        $questionDataModels = [];

        foreach($questionData as $qid => $question){
            if(!is_array($question)) continue;

            if(isset($question['TYPE'])){
                $type = match($question['TYPE']){
                    'RADIOBUTTON'   => 'radio-button',
                    'DATEFIELD'     => 'date-field',
                    'NUMBERFIELD'   => 'number-field',
                    'PERCENTAGEBOX' => 'percentage-box',
                    default         => strtolower($question['TYPE']),
                };
            }

            $section_name = $question['section_name'];

            if(isset($question['section_name'])){
                $standardDocument = StandardDocument::where('title',$section_name)
                    ->where('document_id', $documentId)
                    ->first();
                if($standardDocument){
                    $standardDocumentID = $standardDocument->id;
                }else{
                    $standardDocument = new StandardDocument;
                    $standardDocument->title = $section_name;
                    $standardDocument->slug = Str::slug($section_name, '-');
                    $standardDocument->type = 'document';
                    $standardDocument->document_id = $documentId;
                    $standardDocument->save();

                    $standardDocumentID = $standardDocument->id;
                }
            }

            $qId = preg_replace('/[^0-9]/', '', $qid);
            $questionModel = new Question();
            $questionModel->document_id = $documentId;
            $questionModel->standard_section_id = $standardDocumentID ?? null;
            $questionModel->type = $type;

            $lastOrder = Question::where('document_id', $documentId)
                ->orderBy('order_id', 'desc')
                ->first();

            $questionModel->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;

            if(!empty($question['condition_type'])){
                $questionModel->is_condition = 1;
                $questionModel->condition_type = $question['condition_type'];
            }

            $questionModel->is_end = isset($question['goto']) && $question['goto'] === 'END' ? 1 : 0;
            $questionModel->save();

            $qidToRealIdMap[$qId] = $questionModel->id;
            $questionModels[$qId] = $questionModel;

            $questionDataModels[$qId] = [
                'label' => $question['label'] ?? null,
                'userinfo' => $question['userinfo'] ?? null,
                'placeholder' => $question['placeholder'] ?? null,
                'goto' => $question['goto'] ?? null,
                'goto_if' => $question['goto_if'] ?? [],
                'options' => $question['options'] ?? null,
                'condition_type' => $question['condition_type'] ?? null,
                'another_go_to_step' => $question['another_go_to_step'] ?? [],
                'question_label_condition' => $question['question_label_condition'] ?? [],
                'conditional_go_to_step' => $question['conditional_go_to_step'] ?? null,
            ];
        }

        Log::info(["questionDataModels " , $questionDataModels ]);
        
        foreach($questionDataModels as $q_id => $data){
            $goto = $data['goto'] ?? null;

            Log::info(["goto => " , $data['goto'] ]);

            $gotoClean = preg_replace('/[^0-9]/', '', $goto);
            $nextQuestionId = $goto && $goto !== 'END' ? ($qidToRealIdMap[$gotoClean] ?? null) : null;

            Log::info(["nextQuestionId => " , $nextQuestionId ]);

            if($nextQuestionId == null){

                Log::info(["issue in goto => " , $goto ]);
                Log::info(["issue in gotoClean => " , $gotoClean ]);
                // Log::info(["issue qidToRealIdMap[gotoClean] => " , $qidToRealIdMap[$gotoClean]]);
            } 

            $questionDataModel = new QuestionData();
            $questionDataModel->question_id = $qidToRealIdMap[$q_id];
            $questionDataModel->question_label = $data['label'];
            $questionDataModel->question_info_text = $data['userinfo'];
            $questionDataModel->text_box_placeholder = $data['placeholder'];
            $questionDataModel->next_question_id = $nextQuestionId;

            if(!empty($data['options'])){
                $order = 1;

                Log::info(["data goto => " , $data['goto'] ]);
                
                foreach($data['options'] as $opt){
                    $opt = (array)$opt;
                    $option = new MultipleChoiceQuestionOption();
                    $option->question_id = $qidToRealIdMap[$q_id];
                    $option->option_label = $opt['option_label'] ?? '';
                    $option->option_value = $opt['option_value'] ?? '';
                    
                    
                    Log::info(["options => " , $opt ]);


                    $nextQId = null;
                    if(!empty($opt['go_next_step']) && $opt['go_next_step'] !== 'END'){
                        if(preg_match('/QID(\d+)/', $opt['go_next_step'], $match)){
                            $nextQId = $qidToRealIdMap[$match[1]] ?? null;
                        }
                    }

                    // if(!empty($data['goto']) && $data['goto'] !== 'END'){

                    //     Log::info(["before regex  => " , $data['goto'] ]);

                    //     if(preg_match('/QID(\d+)/', $data['goto'], $match)){

                    //         // Log::info(["regex match => " , $match[1] ]);

                    //         // Log::info(["regex RealIdMap => " , $qidToRealIdMap[$match[1]] ]);

                    //         $nextQId = $qidToRealIdMap[$match[1]] ?? null;
                    //     }

                    //     Log::info(["after regex  => " , $nextQId ]);
                    // }
                    
                    Log::info(["nextQId => " , $nextQId ]);

                    $option->next_question_id = $nextQId;
                    $option->order_id = $order++;
                    $option->save();
                }
            }

            if($data['condition_type'] == "1"){
                $labels = is_array($data['question_label_condition']) ? $data['question_label_condition'] : [];

                foreach($labels as $labelCondition){
                    $qc = new QuestionCondition();
                    $qc->question_id = $qidToRealIdMap[$q_id];
                    $qc->condition_type = 'question_label_condition';
                    $qc->question_label = $labelCondition['label'] ?? '';

                    $condQid = null;
                    if(!empty($labelCondition['question_id']) && preg_match('/QID(\d+)/', $labelCondition['question_id'], $match)){
                        $condQid = $qidToRealIdMap[$match[1]] ?? null;
                    }

                    $qc->conditional_question_id = $condQid;
                    $qc->conditional_question_value = $labelCondition['value'] ?? '';
                    $qc->save();
                }
            }

            if($data['condition_type'] == "2"){
                $gotoIfConditions = is_array($data['goto_if']) ? $data['goto_if'] : [];
                $goToStepTarget = null;

                if(isset($data['conditional_go_to_step'])) {
                    if(preg_match('/QID(\d+)/', $data['conditional_go_to_step'], $match)){
                        $goToStepTarget = $qidToRealIdMap[$match[1]] ?? null;
                    }
                }

                foreach($gotoIfConditions as $condition){
                    if(isset($condition['question_id']) && preg_match('/QID(\d+)/', $condition['question_id'], $match)){
                        $checkQid = $qidToRealIdMap[$match[1]] ?? null;

                        $operatorText = strtolower(trim($condition['conditions'] ?? 'is equal to'));
                        $checkType = match ($operatorText) {
                            'is equal to' => 1,
                            'is greater than' => 2,
                            'is less than' => 3,
                            'is not equal to' => 4,
                            default => 1,
                        };

                        $qc = new QuestionCondition();
                        $qc->question_id = $qidToRealIdMap[$q_id];
                        $qc->condition_type = 'go_to_step_condition';
                        $qc->conditional_question_id = $checkQid;
                        $qc->conditional_question_value = $condition['question_value'] ?? '';
                        $qc->conditional_check = $checkType;
                        $qc->save();
                    }
                }

                if($goToStepTarget){
                    $questionDataModel->conditional_go_to_step = $goToStepTarget;
                }

                $another_go_to_step = is_array($data['another_go_to_step']) ? $data['another_go_to_step'] : [];

                foreach($another_go_to_step as $index => $cond){
                    $subGoToStep = null;
                    if(isset($cond['conditional_go_to_step'])){
                        if(preg_match('/QID(\d+)/', $cond['conditional_go_to_step'], $match)){
                            $subGoToStep = $qidToRealIdMap[$match[1]] ?? null;
                        }
                    }

                    $qc = new QuestionCondition();
                    $qc->question_id = $qidToRealIdMap[$q_id];
                    $qc->condition_type = 'another_go_to_step_condition';
                    $qc->go_to_step = $subGoToStep;
                    $qc->save();

                    $subConditions = is_array($cond['subconditions']) ? $cond['subconditions'] : [];

                    foreach($subConditions as $subC){
                        $sub = new SubCondition();
                        $sub->question_condition_id = $qc->id;

                        if(isset($subC['question_id']) && preg_match('/QID(\d+)/', $subC['question_id'], $match)){
                            $sub->conditional_question_id = $qidToRealIdMap[$match[1]] ?? null;
                        }

                        $sub->conditional_question_value = $subC['question_value'] ?? null;

                        $checkType = match(strtolower(trim($subC['conditions'] ?? 'is equal to'))){
                            'is equal to' => 1,
                            'is greater than' => 2,
                            'is less than' => 3,
                            'is not equal to' => 4,
                            default => 1,
                        };

                        $sub->conditional_check = $checkType;
                        $sub->save();
                    }
                }
            }

            if($data['condition_type'] == "3"){
                $labels = is_array($data['question_label_condition']) ? $data['question_label_condition'] : [];

                foreach ($labels as $labelCondition) {
                    $qc = new QuestionCondition();
                    $qc->question_id = $qidToRealIdMap[$q_id];
                    $qc->condition_type = 'question_label_condition';
                    $qc->question_label = $labelCondition['label'] ?? '';

                    $condQid = null;
                    if(!empty($labelCondition['question_id']) && preg_match('/QID(\d+)/', $labelCondition['question_id'], $match)){
                        $condQid = $qidToRealIdMap[$match[1]] ?? null;
                    }

                    $qc->conditional_question_id = $condQid;
                    $qc->conditional_question_value = $labelCondition['value'] ?? '';
                    $qc->save();
                }
                
                $gotoIfConditions = is_array($data['goto_if']) ? $data['goto_if'] : [];
                $goToStepTarget = null;


                if(isset($data['conditional_go_to_step'])) {
                    if (preg_match('/QID(\d+)/', $data['conditional_go_to_step'], $match)) {
                        $goToStepTarget = $qidToRealIdMap[$match[1]] ?? null;
                    }
                }


                foreach($gotoIfConditions as $condition){
                    if(isset($condition['question_id']) && preg_match('/QID(\d+)/', $condition['question_id'], $match)){
                        $checkQid = $qidToRealIdMap[$match[1]] ?? null;

                        $operatorText = strtolower(trim($condition['conditions'] ?? 'is equal to'));
                        $checkType = match($operatorText){
                            'is equal to' => 1,
                            'is greater than' => 2,
                            'is less than' => 3,
                            'is not equal to' => 4,
                            default => 1,
                        };

                        $qc = new QuestionCondition();
                        $qc->question_id = $qidToRealIdMap[$qid];
                        $qc->condition_type = 'go_to_step_condition';
                        $qc->conditional_question_id = $checkQid;
                        $qc->conditional_question_value = $condition['question_value'] ?? '';
                        $qc->conditional_check = $checkType;
                        $qc->save();
                    }
                }

                if($goToStepTarget){
                    $questionDataModel->conditional_go_to_step = $goToStepTarget;
                }

                $another_go_to_step = is_array($data['another_go_to_step']) ? $data['another_go_to_step'] : [];

                foreach($another_go_to_step as $index => $cond){
                    $subGoToStep = null;
                    if(isset($cond['conditional_go_to_step'])) {
                        if (preg_match('/QID(\d+)/', $cond['conditional_go_to_step'], $match)) {
                            $subGoToStep = $qidToRealIdMap[$match[1]] ?? null;
                        }
                    }

                    $qc = new QuestionCondition();
                    $qc->question_id = $qidToRealIdMap[$qid];
                    $qc->condition_type = 'another_go_to_step_condition';
                    $qc->go_to_step = $subGoToStep;
                    $qc->save();

                    $subConditions = is_array($cond['subconditions']) ? $cond['subconditions'] : [];

                    foreach($subConditions as $subC){
                        $sub = new SubCondition();
                        $sub->question_condition_id = $qc->id;

                        if(isset($subC['question_id']) && preg_match('/QID(\d+)/', $subC['question_id'], $match)) {
                            $sub->conditional_question_id = $qidToRealIdMap[$match[1]] ?? null;
                        }

                        $sub->conditional_question_value = $subC['question_value'] ?? null;

                        $checkType = match (strtolower(trim($subC['conditions'] ?? 'is equal to'))) {
                            'is equal to' => 1,
                            'is greater than' => 2,
                            'is less than' => 3,
                            'is not equal to' => 4,
                            default => 1,
                        };

                        $sub->conditional_check = $checkType;
                        $sub->save();
                    }
                }
            }

            $questionDataModel->save();
        }

        foreach($rightContentData as $tid => $content){
            if(!is_array($content)) continue;

            if(isset($content['TYPE'])){
                $type = match ($content['TYPE']) {
                    'HEADLINE'  => 'content_heading',
                    'CONTENT'   => 'content',
                    'SIGNATURE' => 'signature_field',
                    default     => strtolower($content['TYPE']),
                };
            }else{
                $type = null;
            }
    
            $section_name = $content['section_name'];

            if(isset($content['section_name'])){
                $standardDocument = StandardDocument::where('title',$section_name)
                    ->where('document_id', $documentId)
                    ->first();
                if($standardDocument){
                    $standardDocumentID = $standardDocument->id;
                }else{
                    $standardDocument = new StandardDocument;
                    $standardDocument->title = $section_name;
                    $standardDocument->slug = Str::slug($section_name, '-');
                    $standardDocument->type = 'document';
                    $standardDocument->document_id = $documentId;
                    $standardDocument->save();

                    $standardDocumentID = $standardDocument->id;
                }
            }
            $text = $content['TEXT'] ?? '';
            $text = preg_replace_callback('/\{QID(\d+)\}/', function ($matches) use ($qidToRealIdMap){
                $originalQid = $matches[1];
                return isset($qidToRealIdMap[$originalQid]) ? '{' . $qidToRealIdMap[$originalQid] . '}' : $matches[0];
            }, $text);
                    
            $secure_blur_content = isset($content['BLUR_CONTENT']) && $content['BLUR_CONTENT'] ? 1 : 0;
            $is_signature = ($type === 'signature_field') ? 1 : 0;
            $is_condition = (!empty($content['CONDITIONS'])) ? 1 : 0;

            $document_right_section = new DocumentRightSection();
            $document_right_section->type = $type;
            $document_right_section->document_id = $documentId;
            $document_right_section->standard_section_id = $standardDocumentID ?? null;
    
            $lastOrder = DocumentRightSection::where('document_id', $documentId)
                ->orderBy('order_id', 'desc')
                ->first();
            $document_right_section->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;
    
            $document_right_section->content = $text;
            $document_right_section->text_align = $content['ALIGN_TEXT'] ?? 'left';
            $document_right_section->is_condition = $is_condition;
            $document_right_section->signature_field = $is_signature;
            $document_right_section->secure_blur_content = $secure_blur_content;
            $document_right_section->save();
    
            if(!empty($content['CONDITIONS'])){
                foreach($content['CONDITIONS'] as $condition){
                    $condition = (array)$condition;

                    $checkType = match (strtolower(trim($condition['conditions'] ?? 'is equal to'))) {
                        'is equal to'   => 1,
                        'is greater than'=> 2,
                        'is less than'  => 3,
                        'not equal to'  => 4,
                        default         => 1,
                    };

                    $questionId = null;
                    if(!empty($condition['question_id']) && preg_match('/QID(\d+)/', $condition['question_id'], $matches)){
                        $questionId = $qidToRealIdMap[$matches[1]] ?? null;
                    }

                    $condition_type = match ($type) {
                        'content'        => 'content_condition',
                        'signature_field'=> 'signature_field',
                        default          => 'content_condition',
                    };

                    if($questionId !== null){
                        $documentCondition = new QuestionCondition();
                        $documentCondition->condition_type = $condition_type;
                        $documentCondition->document_right_content_id = $document_right_section->id;
                        $documentCondition->conditional_question_id = $questionId;
                        $documentCondition->conditional_check = $checkType;
                        $documentCondition->conditional_question_value = $condition['question_value'] ?? '';
                        $documentCondition->save();
                    }
                }
            }
        }
    }
}
