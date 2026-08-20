<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\DocumentRightSection;
use App\Models\QuestionType;
use App\Models\Question;
use App\Models\GlobalContractText;
use App\Models\GlobalContractQuestion;
use App\Models\GlobalContractQuestionCondition;
use App\Models\GlobalContractQuestionData;
use App\Models\GlobalContractSubCondition;
use App\Models\GlobalContractMultipleChoiceQuestion;
use App\Models\StandardDocument;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    public function header(){
        return view('admin.globals.header');

    }

    public function standardSection(){
        // $standard_documents = StandardDocument::all();
        $standard_documents = StandardDocument::whereNull('parent_id')->get();
        return view('admin.documents.standard_document_section',compact('standard_documents'));
    }

    public function standardContractEdit(){
        return view('admin.documents.standard_contract_edit');
    }
    public function toggleStandardDocumentStatus($slug)
{
    try {
        $document = StandardDocument::where('slug', $slug)->first();

        if (!$document) {
            return redirect()->back()->with('error', 'Standard Document not found.');
        }

        $document->status = $document->status == '1' ? 0 : 1;
        $document->save();

        $statusLabel = $document->status == '1' ? 'activated' : 'deactivated';

        return redirect()->route('admin.document.standard_section')
            ->with('success', "Document \"{$document->title}\" has been {$statusLabel} successfully.");

    } catch (\Exception $e) {
        saveLog("Error:", "GlobalController", $e->getMessage());
        return redirect()->back()->with('error', 'Something went wrong. Please try again.');
    }
}


    public function standardDocument(){
        return view('admin.documents.add_standard_document');
    }

    public function addStandardDocument(Request $request){
        DB::beginTransaction();
        try{
            if(isset($request->id) && $request->id){
                $document = StandardDocument::find($request->id);
                $document->title       = $request->title;
                $document->slug        = $request->slug;
                $document->description = $request->description;
                $document->update();
            } else {
                $document = new StandardDocument;
                $document->title       = $request->title;
                $document->slug        = $request->slug;
                $document->description = $request->description;
                $document->status      = 1;
                $document->save();
            }
            DB::commit();
            return redirect()->route('admin.document.standard_section')
                ->with('success', 'Standard Document saved successfully.');
 
        } catch(\Exception $e){
            DB::rollBack();
            saveLog("Error:", "GlobalController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

public function sceGetQuestions($id)
{
    try {
        $document = StandardDocument::find($id);
        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        $questions = GlobalContractQuestion::where('document_id', $id)
            ->with(['questionData', 'options', 'conditions.subconditions'])
            ->orderByRaw('CAST(order_id AS UNSIGNED) ASC')
            ->get()
            ->map(function ($q) {
                $condGoTo = $q->conditions
                    ->whereIn('condition_type', ['another_go_to_step_condition', 'go_to_step_condition'])
                    ->map(function ($c) {
                        return [
                            'goto'       => $c->go_to_step ?? '',
                            'conditions' => $c->subconditions->map(fn($s) => [
                                'qid'   => (string)$s->conditional_question_id,
                                'type'  => match((int)$s->conditional_check) {
                                    2       => 'is_greater_than',
                                    3       => 'is_less_than',
                                    4       => 'is_not_equal_to',
                                    default => 'is_equal_to',
                                },
                                'value' => $s->conditional_question_value ?? '',
                            ])->values()->toArray(),
                        ];
                    })->values()->toArray();

                foreach ($q->options as $opt) {
                    if ($opt->next_question_id) {
                        $condGoTo[] = [
                            'goto'       => (string)$opt->next_question_id,
                            'conditions' => [[
                                'qid'   => (string)$q->id,
                                'type'  => 'is_equal_to',
                                'value' => $opt->option_value,
                            ]],
                        ];
                    }
                }

                $showConditions = $q->conditions
                    ->where('condition_type', 'question_label_condition')
                    ->map(fn($c) => [
                        'label' => $c->question_label ?? '',
                        'qid'   => (string)$c->conditional_question_id,
                        'value' => $c->conditional_question_value ?? '',
                    ])->values()->toArray();

                return [
                    'id'           => $q->id,
                    'type'         => $q->type,
                    'order_id'     => $q->order_id,
                    'go_to'        => optional($q->questionData)->next_question_id,
                    'questionData' => $q->questionData ? [
                        'question_label'       => $q->questionData->question_label,
                        'question_info_text'   => $q->questionData->question_info_text,
                        'text_box_placeholder' => $q->questionData->text_box_placeholder,
                    ] : null,
                    'options' => $q->options->map(fn($o) => [
                        'id'           => $o->id,
                        'option_label' => $o->option_label,
                        'option_value' => $o->option_value,
                    ])->values(),
                    'condGoTo'   => $condGoTo,
                    'conditions' => $showConditions,
                ];
            });

        return response()->json(['success' => true, 'questions' => $questions]);

    } catch (\Exception $e) {
        saveLog('Error:', 'GlobalController@sceGetQuestions', $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
 

// public function sceGetSections($id)
// {
//     try {
//         $document = StandardDocument::find($id);
//         if (!$document) {
//             return response()->json(['success' => false, 'message' => 'Document not found'], 404);
//         }
 
//         $sections = GlobalContractText::where('document_id', $id)
//             ->orderBy('order_id', 'asc')
//             ->get()
//             ->map(fn($s) => [
//                 'id'                  => $s->id,
//                 'type'                => $s->type,
//                 'content'             => $s->content,
//                 'text_align'          => $s->text_align,
//                 'secure_blur_content' => $s->secure_blur_content,
//                 'order_id'            => $s->order_id,
//             ]);
 
//         return response()->json(['success' => true, 'sections' => $sections]);
 
//     } catch (\Exception $e) {
//         saveLog('Error:', 'GlobalController@sceGetSections', $e->getMessage());
//         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
//     }
// }

public function sceGetSections($id)
{
    try {
        $document = StandardDocument::find($id);
        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        $sections = GlobalContractText::where('document_id', $id)
            ->with('conditions')
            ->orderBy('order_id', 'asc')
            ->get()
            ->map(fn($s) => [
                'id'                  => $s->id,
                'type'                => $s->type,
                'content'             => $s->content,
                'text_align'          => $s->text_align,
                'secure_blur_content' => $s->secure_blur_content,
                'order_id'            => $s->order_id,
                'conditions'          => $s->conditions->map(fn($c) => [
                    'qid'   => (string)$c->conditional_question_id,
                    'type'  => match((int)$c->conditional_check) {
                        2       => 'is_greater_than',
                        3       => 'is_less_than',
                        4       => 'is_not_equal_to',
                        default => 'is_equal_to',
                    },
                    'value' => $c->conditional_question_value ?? '',
                ])->values()->toArray(),
            ]);

        return response()->json(['success' => true, 'sections' => $sections]);

    } catch (\Exception $e) {
        saveLog('Error:', 'GlobalController@sceGetSections', $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

 

// public function sceSave(Request $request)
// {
//     DB::beginTransaction();
//     try {
//         $documentId = $request->document_id;
//         if (!$documentId) {
//             return response()->json(['success' => false, 'message' => 'document_id is required'], 422);
//         }
 
//         $document = StandardDocument::find($documentId);
//         if (!$document) {
//             return response()->json(['success' => false, 'message' => 'Document not found'], 404);
//         }
 
//        if (!empty($request->delete_question_ids)) {
//             foreach ($request->delete_question_ids as $qid) {
//                 $q = GlobalContractQuestion::find($qid);
//                 if ($q) {
//                     optional($q->questionData())->delete();
//                     $q->options()->delete();
//                     $q->conditions()->each(function ($c) {
//                         $c->subconditions()->delete();
//                         $c->delete();
//                     });
//                     $q->delete();
//                 }
//             }
//         }
 
//         if (!empty($request->delete_section_ids)) {
//             foreach ($request->delete_section_ids as $sid) {
//                 $s = GlobalContractText::find($sid);
//                 if ($s) {
//                     $s->conditions()->delete();
//                     $s->delete();
//                 }
//             }
//         }
 
//         $questionsPayload = $request->questions ?? [];
//         foreach ($questionsPayload as $qi => $qData) {
//             if (!empty($qData['id'])) {
//                 $question = GlobalContractQuestion::find($qData['id']);
//                 if (!$question) continue;
 
//                 $question->type     = $qData['type']     ?? $question->type;
//                 $question->order_id = $qData['order_id'] ?? ($qi + 1);
//                 $question->save();
 
//                 $questionData = $question->questionData;
//                 if ($questionData) {
//                     $questionData->question_label       = $qData['label']       ?? '';
//                     $questionData->question_info_text   = $qData['info']        ?? '';
//                     $questionData->text_box_placeholder = $qData['placeholder'] ?? '';
//                     $questionData->next_question_id     = !empty($qData['goTo']) && $qData['goTo'] !== 'END' ? $qData['goTo'] : null;
//                     $questionData->save();
//                 } else {
//                     GlobalContractQuestionData::create([
//                         'question_id'          => $question->id,
//                         'question_label'       => $qData['label']       ?? '',
//                         'question_info_text'   => $qData['info']        ?? '',
//                         'text_box_placeholder' => $qData['placeholder'] ?? '',
//                         'next_question_id'     => !empty($qData['goTo']) && $qData['goTo'] !== 'END' ? $qData['goTo'] : null,
//                     ]);
//                 }
 
//                 if (isset($qData['options'])) {
//                     $incomingOptionIds = collect($qData['options'])->pluck('id')->filter()->all();
//                     $question->options()->whereNotIn('id', $incomingOptionIds)->delete();
 
//                     $order = 1;
//                     foreach ($qData['options'] as $oData) {
//                         if (!empty($oData['id'])) {
//                             $opt = GlobalContractMultipleChoiceQuestion::find($oData['id']);
//                             if ($opt) {
//                                 $opt->option_label = $oData['label'];
//                                 $opt->option_value = $oData['value'] ?? $oData['label'];
//                                 $opt->order_id     = $order++;
//                                 $opt->save();
//                             }
//                         } else {
//                             GlobalContractMultipleChoiceQuestion::create([
//                                 'question_id'  => $question->id,
//                                 'option_label' => $oData['label'],
//                                 'option_value' => $oData['value'] ?? $oData['label'],
//                                 'order_id'     => $order++,
//                             ]);
//                         }
//                     }
//                 }
 
//             } else {
//                 // CREATE new
//                 $question              = new GlobalContractQuestion;
//                 $question->document_id = $documentId;
//                 $question->type        = $qData['type']     ?? 'textbox';
//                 $question->order_id    = $qData['order_id'] ?? ($qi + 1);
//                 $question->is_condition = 0;
//                 $question->is_end       = 0;
//                 $question->save();
 
//                 GlobalContractQuestionData::create([
//                     'question_id'          => $question->id,
//                     'question_label'       => $qData['label']       ?? '',
//                     'question_info_text'   => $qData['info']        ?? '',
//                     'text_box_placeholder' => $qData['placeholder'] ?? '',
//                     'next_question_id'     => !empty($qData['goTo']) && $qData['goTo'] !== 'END' ? $qData['goTo'] : null,
//                 ]);
 
//                 $order = 1;
//                 foreach ($qData['options'] ?? [] as $oData) {
//                     GlobalContractMultipleChoiceQuestion::create([
//                         'question_id'  => $question->id,
//                         'option_label' => $oData['label'],
//                         'option_value' => $oData['value'] ?? $oData['label'],
//                         'order_id'     => $order++,
//                     ]);
//                 }
//             }
//         }
 
//         /* ── Upsert sections ── */
//         $sectionsPayload = $request->sections ?? [];
//         foreach ($sectionsPayload as $si => $sData) {
//             if (!empty($sData['id'])) {
//                 $section = GlobalContractText::find($sData['id']);
//                 if (!$section) continue;
//                 $section->type                = $sData['type']                ?? $section->type;
//                 $section->content             = $sData['content']             ?? '';
//                 $section->text_align          = $sData['text_align']          ?? 'left';
//                 $section->secure_blur_content = $sData['secure_blur_content'] ?? 0;
//                 $section->order_id            = $sData['order_id']            ?? ($si + 1);
//                 $section->save();
//             } else {
//                 GlobalContractText::create([
//                     'document_id'         => $documentId,
//                     'type'                => $sData['type']                ?? 'content',
//                     'content'             => $sData['content']             ?? '',
//                     'text_align'          => $sData['text_align']          ?? 'left',
//                     'secure_blur_content' => $sData['secure_blur_content'] ?? 0,
//                     'order_id'            => $sData['order_id']            ?? ($si + 1),
//                     'published'           => 1,
//                 ]);
//             }
//         }
 
//         DB::commit();
//         return response()->json(['success' => true, 'message' => 'Saved successfully.']);
 
//     } catch (\Exception $e) {
//         DB::rollBack();
//         saveLog('Error:', 'GlobalController@sceSave', $e->getMessage());
//         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
//     }
// }


public function sceSave(Request $request)
{
    DB::beginTransaction();
    try {
        $documentId = $request->document_id;
        if (!$documentId) {
            return response()->json(['success' => false, 'message' => 'document_id is required'], 422);
        }

        $document = StandardDocument::find($documentId);
        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        if (!empty($request->delete_question_ids)) {
            foreach ($request->delete_question_ids as $qid) {
                $q = GlobalContractQuestion::find($qid);
                if ($q) {
                    if ($q->questionData) $q->questionData->delete();
                    $q->options()->delete();
                    $q->conditions()->each(function ($c) {
                        $c->subconditions()->delete();
                        $c->delete();
                    });
                    $q->delete();
                }
            }
        }

        if (!empty($request->delete_section_ids)) {
            foreach ($request->delete_section_ids as $sid) {
                $s = GlobalContractText::find($sid);
                if ($s) {
                    $s->conditions()->delete();
                    $s->delete();
                }
            }
        }

        $questionsPayload = $request->questions ?? [];
        foreach ($questionsPayload as $qi => $qData) {
            if (!empty($qData['id'])) {
                $question = GlobalContractQuestion::find($qData['id']);
                if (!$question) continue;

                $question->type     = $qData['type']     ?? $question->type;
                $question->order_id = $qData['order_id'] ?? ($qi + 1);
                $question->save();

                $questionData = $question->questionData;
                if ($questionData) {
                    $questionData->question_label       = $qData['label']       ?? '';
                    $questionData->question_info_text   = $qData['info']        ?? '';
                    $questionData->text_box_placeholder = $qData['placeholder'] ?? '';
                    $questionData->next_question_id     = !empty($qData['goTo']) && $qData['goTo'] !== 'END' ? $qData['goTo'] : null;
                    $questionData->save();
                } else {
                    GlobalContractQuestionData::create([
                        'question_id'          => $question->id,
                        'question_label'       => $qData['label']       ?? '',
                        'question_info_text'   => $qData['info']        ?? '',
                        'text_box_placeholder' => $qData['placeholder'] ?? '',
                        'next_question_id'     => !empty($qData['goTo']) && $qData['goTo'] !== 'END' ? $qData['goTo'] : null,
                    ]);
                }

                if (isset($qData['options'])) {
                    $incomingOptionIds = collect($qData['options'])->pluck('id')->filter()->all();
                    $question->options()->whereNotIn('id', $incomingOptionIds)->delete();

                    $order = 1;
                    foreach ($qData['options'] as $oData) {
                        if (!empty($oData['id'])) {
                            $opt = GlobalContractMultipleChoiceQuestion::find($oData['id']);
                            if ($opt) {
                                $opt->option_label = $oData['label'];
                                $opt->option_value = $oData['value'] ?? $oData['label'];
                                $opt->order_id     = $order++;
                                $opt->save();
                            }
                        } else {
                            GlobalContractMultipleChoiceQuestion::create([
                                'question_id'  => $question->id,
                                'option_label' => $oData['label'],
                                'option_value' => $oData['value'] ?? $oData['label'],
                                'order_id'     => $order++,
                            ]);
                        }
                    }
                }

            } else {
                $question               = new GlobalContractQuestion;
                $question->document_id  = $documentId;
                $question->type         = $qData['type']     ?? 'textbox';
                $question->order_id     = $qData['order_id'] ?? ($qi + 1);
                $question->is_condition = 0;
                $question->is_end       = 0;
                $question->save();

                GlobalContractQuestionData::create([
                    'question_id'          => $question->id,
                    'question_label'       => $qData['label']       ?? '',
                    'question_info_text'   => $qData['info']        ?? '',
                    'text_box_placeholder' => $qData['placeholder'] ?? '',
                    'next_question_id'     => !empty($qData['goTo']) && $qData['goTo'] !== 'END' ? $qData['goTo'] : null,
                ]);

                $order = 1;
                foreach ($qData['options'] ?? [] as $oData) {
                    GlobalContractMultipleChoiceQuestion::create([
                        'question_id'  => $question->id,
                        'option_label' => $oData['label'],
                        'option_value' => $oData['value'] ?? $oData['label'],
                        'order_id'     => $order++,
                    ]);
                }
            }

            // ── Save show-if conditions ──
            $question->conditions()
    ->where('condition_type', 'question_label_condition')
    ->each(function ($c) {
        $c->subconditions()->delete();
        $c->delete();
    });

$hasShowIf = false;
if (!empty($qData['conditions'])) {
    foreach ($qData['conditions'] as $cond) {
        // Skip if qid is empty
        if (empty($cond['qid'])) continue;
        
        GlobalContractQuestionCondition::create([
            'question_id'                => $question->id,
            'condition_type'             => 'question_label_condition',
            'question_label'             => $cond['label'] ?? '',
            'conditional_question_id'    => $cond['qid'],
            'conditional_question_value' => $cond['value'] ?? '',
            'conditional_check'          => $this->resolveConditionCheck(null),
        ]);
        $hasShowIf = true;
    }
}

            // ── Save cond_go_to ──
            $question->conditions()
    ->whereIn('condition_type', ['go_to_step_condition', 'another_go_to_step_condition'])
    ->each(function ($c) {
        $c->subconditions()->delete();
        $c->delete();
    });

$hasCondGoTo = false;
if (!empty($qData['cond_go_to'])) {
    foreach ($qData['cond_go_to'] as $grp) {
        // Skip if goto is empty
        if (!isset($grp['goto']) || $grp['goto'] === '' || $grp['goto'] === null) continue;

        $goToVal    = $grp['goto'];
        $conditions = $grp['conditions'] ?? [];

        // Filter out conditions with empty qid BEFORE checking isSimpleOption
        $conditions = array_filter($conditions, function($c) {
            return !empty($c['qid']);
        });
        $conditions = array_values($conditions); // re-index

        // Skip entire group if no valid conditions remain
        if (empty($conditions)) continue;

        $isSimpleOption = count($conditions) === 1
            && isset($conditions[0]['qid'])
            && (string)$conditions[0]['qid'] === (string)$question->id
            && ($conditions[0]['type'] ?? '') === 'is_equal_to';

        if ($isSimpleOption) {
            $optionValue = $conditions[0]['value'] ?? '';
            $opt = $question->options()
                ->where('option_value', $optionValue)
                ->first();
            if ($opt) {
                $opt->next_question_id = ($goToVal === 'END') ? null : $goToVal;
                $opt->save();
            }
        } else {
            $qCondition = GlobalContractQuestionCondition::create([
                'question_id'    => $question->id,
                'condition_type' => 'another_go_to_step_condition',
                'go_to_step'     => ($goToVal === 'END') ? null : $goToVal,
            ]);

            foreach ($conditions as $c) {
                if (empty($c['qid'])) continue; // double safety
                GlobalContractSubCondition::create([
                    'question_condition_id'      => $qCondition->id,
                    'conditional_question_id'    => $c['qid'],
                    'conditional_question_value' => $c['value'] ?? '',
                    'conditional_check'          => $this->resolveConditionCheck($c['type'] ?? null),
                ]);
            }
            $hasCondGoTo = true;
        }
    }
}

            // ── Update question condition flags ──
            if ($hasShowIf || $hasCondGoTo) {
                $question->is_condition = 1;
                if ($hasShowIf && $hasCondGoTo) {
                    $question->condition_type = 3;
                } elseif ($hasShowIf) {
                    $question->condition_type = 1;
                } else {
                    $question->condition_type = 2;
                }
            } else {
                $question->is_condition   = 0;
                $question->condition_type = null;
            }
            $question->save();
        }

        // ── Upsert sections ──
        $sectionsPayload = $request->sections ?? [];
        foreach ($sectionsPayload as $si => $sData) {
            if (!empty($sData['id'])) {
                $section = GlobalContractText::find($sData['id']);
                if (!$section) continue;
                $section->type                = $sData['type']                ?? $section->type;
                $section->content             = $sData['content']             ?? '';
                $section->text_align          = $sData['text_align']          ?? 'left';
                $section->secure_blur_content = $sData['secure_blur_content'] ?? 0;
                $section->order_id            = $sData['order_id']            ?? ($si + 1);
                $section->save();
            } else {
                $section = GlobalContractText::create([
                    'document_id'         => $documentId,
                    'type'                => $sData['type']                ?? 'content',
                    'content'             => $sData['content']             ?? '',
                    'text_align'          => $sData['text_align']          ?? 'left',
                    'secure_blur_content' => $sData['secure_blur_content'] ?? 0,
                    'order_id'            => $sData['order_id']            ?? ($si + 1),
                    'published'           => 1,
                ]);
            }

            // ── Save section conditions ──
            $section->conditions()->delete();

            if (!empty($sData['conditions'])) {
                $hasValidCond = false;
                foreach ($sData['conditions'] as $cond) {
                    if (empty($cond['qid'])) continue; // skip empty
                    
                    GlobalContractQuestionCondition::create([
                        'condition_type'             => 'content_condition',
                        'document_right_content_id'  => $section->id,
                        'conditional_question_id'    => $cond['qid'],
                        'conditional_check'          => $this->resolveConditionCheck($cond['type'] ?? null),
                        'conditional_question_value' => $cond['value'] ?? '',
                    ]);
                    $hasValidCond = true;
                }
                
                if ($hasValidCond) {
                    $section->is_condition = 1;
                    $section->save();
                }
            }
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Saved successfully.']);

    } catch (\Exception $e) {
        DB::rollBack();
        saveLog('Error:', 'GlobalController@sceSave', $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

    public function addStateVersion(Request $request)
{
    DB::beginTransaction();

    try {
        $request->validate([
            'parent_id' => 'required|exists:standard_documents,id',
            'states'    => 'required|array|min:1',
        ]);

        $parent = StandardDocument::find($request->parent_id);

        if (!$parent) {
            return response()->json([
                'success' => false,
                'message' => 'Parent document not found.'
            ], 404);
        }

        $states = $request->states ?? [];

        $exists = StandardDocument::where('parent_id', $parent->id)
            ->where(function ($q) use ($states) {
                foreach ($states as $state) {
                    $q->orWhereJsonContains('states', $state);
                }
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A version already exists for one or more selected states.'
            ]);
        }

        $stateSlug = implode('-', array_map(function ($s) {
            return strtolower(str_replace(' ', '-', $s));
        }, $states));

        $baseSlug = $parent->slug . '-' . $stateSlug;
        $slug     = $baseSlug;
        $i        = 1;

        while (StandardDocument::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        $child = new StandardDocument;
        $child->title       = $parent->title ;
        // . ' (' . implode(', ', $states) . ')';
        $child->slug        = $slug;
        $child->description = $parent->description;
        $child->parent_id   = $parent->id;   
        $child->states      = $states;       
        $child->status      = 1;
        $child->save();

        DB::commit();

        return response()->json([
            'success'     => true,
            'document_id' => $child->id,
            'message'     => 'State version created successfully.'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        DB::rollBack();

        saveLog("Error:", "GlobalController@addStateVersion", $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please try again.'
        ], 500);
    }
}
 

private function resolveConditionCheck(?string $type): int
{
    return match($type) {
        'is_greater_than' => 2,
        'is_less_than'    => 3,
        'is_not_equal_to' => 4,
        default           => 1,
    };
}


    public function deleteStateVersion($id){
        DB::beginTransaction();
        try{
            $document = StandardDocument::find($id);
 
            if(!$document){
                return response()->json(['success' => false, 'message' => 'Version not found.'], 404);
            }
 
            if(!$document->parent_id){
                return response()->json(['success' => false, 'message' => 'Cannot delete the default version from here.'], 403);
            }
 
            Question::where('standard_section_id', $document->id)
                ->get()
                ->each(function($q){
                    $q->options()->delete();
                    $q->questionData()->delete();
                    $q->conditions()->each(function($c){
                        $c->subconditions()->delete();
                        $c->delete();
                    });
                    $q->delete();
                });
 
            DocumentRightSection::where('standard_section_id', $document->id)
                ->get()
                ->each(function($s){
                    $s->conditions()->delete();
                    $s->delete();
                });
 
            $document->delete();
            DB::commit();
 
            return response()->json(['success' => true, 'message' => 'State version deleted.']);
 
        } catch(\Exception $e){
            DB::rollBack();
            saveLog("Error:", "GlobalController@deleteStateVersion", $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // public function getStandardDocumentsForContract(Request $request){
    //     try {
    //         $state = $request->get('state');
            
    //         $query = StandardDocument::where('status', 1);
            
    //         if ($state) {
    //             $query->forState($state);
    //         }
            
    //         $documents = $query->get(['id', 'title', 'slug', 'clause_type', 'states', 'description']);
            
    //         return response()->json([
    //             'success' => true,
    //             'data'    => $documents
    //         ]);
    //     } catch(\Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    //     }
    // }

    public function getStandardDocumentsForContract(Request $request)
{
    try {
        $state = $request->get('state');

        $documents = StandardDocument::whereNull('parent_id')
            ->where('status', 1)
            ->with(['stateVersions'])
            ->get();

        $result = [];

        foreach ($documents as $doc) {

            $stateVersion = $doc->stateVersions
                ->first(function ($sv) use ($state) {
                    return $state && in_array($state, $sv->states ?? []);
                });

            $finalDoc = $stateVersion ?? $doc;

            $result[] = [
                'id'    => $finalDoc->id,
                'title' => $finalDoc->title,
                'slug'  => $finalDoc->slug,
                'states'=> $finalDoc->states,
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => $result
        ]);

    } catch(\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function updateStateVersion(Request $request, $id)
{
    $document = StandardDocument::find($id);
    if (!$document) return response()->json(['success' => false, 'message' => 'Version not found'], 404);

    $document->states = $request->states ?? [];
    $document->save();

    return response()->json(['success' => true]);
}

    public function editStandardDocument($slug){
        $document = StandardDocument::where('slug', $slug)                  
        ->with('stateVersions')
        ->first();
        if(!$document){
            return redirect()->back()->with('error', 'Standard Document not found.');
        }
        return view('admin.documents.add_standard_document', compact('document'));
    }
    
    public function contractQuestion($id){
        $types = QuestionType::all();
        $questions = GlobalContractQuestion::where('document_id',$id)->get();
        $document = StandardDocument::find($id);
        $document_questions = GlobalContractQuestion::where('document_id',$id)->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
        ->orderByRaw('CAST(order_id AS UNSIGNED) ASC') 
        ->get();
        // dd($document_questions);
        
        return view('admin.documents.contract_question',compact('types','questions','document_questions','id','document'));
    }

    public function addContractQuestions(Request $request){

        // return $request->all();

        DB::beginTransaction();
        try{
            // End changed question types

            if(isset($request->formdata) && $request->formdata != null){
                $formData = json_decode($request->formdata);
               
                foreach($formData as $data){
                    if($data->is_new == true){
                        $questions = new GlobalContractQuestion;
                        $questions->document_id = $request->document_id;
                        $questions->type = $data->type;

                        if(!empty($data->order_id)){
                            $questions->order_id = $data->order_id;
                        }else{
                            $lastOrder = GlobalContractQuestion::where('document_id', $request->document_id)
                                        ->orderBy('order_id', 'desc')
                                        ->first();
                            $questions->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;
                        }

                        if(!empty($data->is_conditional_question) && !empty($data->is_conditional_step)){
                            $is_condition = 1;
                            $condition_type = 3;
                        }elseif(!empty($data->is_conditional_question)){
                            $is_condition = 1;
                            $condition_type = 1;
                        }elseif(!empty($data->is_conditional_step)){
                            $is_condition = 1;
                            $condition_type = 2;
                        }else{
                            $is_condition = 0;
                            $condition_type = null;
                        }

                        $questions->is_condition = $is_condition;
                        $questions->condition_type = $condition_type;
                        $questions->is_end = $data->is_end;

                        if (!$questions->id) {
                            $questions->save();
                        }
                    
                       

                        // $questions->save();

                        $question_data = new GlobalContractQuestionData;
                        $question_data->question_id = $questions->id;
                        $question_data->question_label = $data->question_label;
                        $question_data->question_info_text = $data->question_info_text;

                        if(isset($data->text_box_placeholder) && $data->text_box_placeholder != null){
                            $question_data->text_box_placeholder = $data->text_box_placeholder;
                        }

                        if($data->type == "dropdown-link"){
                            $question_data->same_contract_link_label = $data->same_contract_link;
                        }

                        if (isset($data->go_to_step)) {
                            if (empty($data->go_to_step) || $data->go_to_step == "0") {
                                $question_data->next_question_id = null;
                            } else {
                                $question_data->next_question_id = (int) $data->go_to_step;
                            }
                        }


                        if($questions->condition_type == 1){
                            $question_condition_type = "question_label_condition";
                            $conditional_question_labels = $data->new_conditional_question_labels;
                            for($i=0; $i<count($conditional_question_labels); $i++){
                                $conditional = $conditional_question_labels[$i];

                                $question_conditions = new GlobalContractQuestionCondition;
                                $question_conditions->question_id = $questions->id;
                                $question_conditions->condition_type = $question_condition_type;
                                $question_conditions->question_label = $conditional->label;
                                $question_conditions->conditional_question_id = $conditional->questionID;
                                $question_conditions->conditional_question_value = $conditional->question_value;
                                $question_conditions->save();
                            }
                        }elseif($questions->condition_type == 2){
                            $question_condition_type = "go_to_step_condition";
                            $step_conditions = $data->new_conditions;
                            for($i=0; $i<count($step_conditions); $i++){
                                $step = $step_conditions[$i];

                                $question_conditions = new GlobalContractQuestionCondition;
                                $question_conditions->question_id = $questions->id;
                                $question_conditions->condition_type = $question_condition_type;

                                if(!empty($step->question_condition)){
                                    if($step->question_condition == "is_equal_to"){
                                        $conditionCheck = 1;
                                    }elseif($step->question_condition == "is_greater_than"){
                                        $conditionCheck = 2;
                                    }elseif($step->question_condition == "is_less_than"){
                                        $conditionCheck = 3;
                                    }elseif($step->question_condition == "not_equal_to"){
                                        $conditionCheck = 4;
                                    }
                                }

                                $question_conditions->conditional_check = $conditionCheck;
                                $question_conditions->conditional_question_id = $step->questionID;
                                $question_conditions->conditional_question_value = $step->question_value;
                                $question_conditions->save();
                            }

                            if(isset($data->condition_go_to_step)){
                                $question_data->conditional_go_to_step = $data->condition_go_to_step;
                            }


                            if(!empty($data->is_another_conditional_step)){
                                $question_condition_type = "another_go_to_step_condition";
                                if(!empty($data->new_another_conditions)){
                                    $step_conditions = json_decode(json_encode($data->new_another_conditions), true);

                                    foreach($step_conditions as $key => $step){
                                        $question_condition = new GlobalContractQuestionCondition();
                                        $question_condition->question_id = $questions->id;
                                        $question_condition->condition_type = $question_condition_type;

                                        if(!empty($step['go_to_step'])){
                                            $question_condition->go_to_step = $step['go_to_step'];
                                        }

                                        $question_condition->save();

                                        if(!empty($step['subconditions']) && is_array($step['subconditions'])){
                                            foreach ($step['subconditions'] as $sub) {
                                                $subcondition = new GlobalContractSubCondition();
                                                $subcondition->question_condition_id = $question_condition->id;
                                                $subcondition->key = $key;
                                                $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                $subcondition->conditional_question_value = $sub['question_value'] ?? null;

                                                if(!empty($sub['question_condition'])){
                                                    if($sub['question_condition'] == "is_equal_to"){
                                                        $conditionCheck = 1;
                                                    }elseif($sub['question_condition'] == "is_greater_than"){
                                                        $conditionCheck = 2;
                                                    }elseif($sub['question_condition'] == "is_less_than"){
                                                        $conditionCheck = 3;
                                                    }elseif($sub['question_condition'] == "not_equal_to"){
                                                        $conditionCheck = 4;
                                                    }
                                                }

                                                $subcondition->conditional_check = $conditionCheck;
                                                $subcondition->save();
                                            }
                                        }
                                    }
                                }
                            }

                        }elseif($questions->condition_type == 3){
                            if(!empty($data->new_conditional_question_labels)){
                                $question_condition_type = "question_label_condition";
                                $conditional_question_labels = $data->new_conditional_question_labels;
                                for($i=0; $i<count($conditional_question_labels); $i++){
                                    $conditional = $conditional_question_labels[$i];

                                    $question_conditions = new GlobalContractQuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;
                                    $question_conditions->question_label = $conditional->label;
                                    $question_conditions->conditional_question_id = $conditional->questionID;
                                    $question_conditions->conditional_question_value = $conditional->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if(!empty($data->new_conditions)){
                                $question_condition_type = "go_to_step_condition";
                                $step_conditions = $data->new_conditions;
                                for($i=0; $i<count($step_conditions); $i++){
                                    $step = $step_conditions[$i];

                                    $question_conditions = new GlobalContractQuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;

                                    if(!empty($step->question_condition)){
                                        if($step->question_condition == "is_equal_to"){
                                            $conditionCheck = 1;
                                        }elseif($step->question_condition == "is_greater_than"){
                                            $conditionCheck = 2;
                                        }elseif($step->question_condition == "is_less_than"){
                                            $conditionCheck = 3;
                                        }elseif($step->question_condition == "not_equal_to"){
                                            $conditionCheck = 4;
                                        }
                                    }

                                    $question_conditions->conditional_check = $conditionCheck;
                                    $question_conditions->conditional_question_id = $step->questionID;
                                    $question_conditions->conditional_question_value = $step->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if(isset($data->condition_go_to_step)){
                                $question_data->conditional_go_to_step = $data->condition_go_to_step;
                            }


                            if(!empty($data->is_another_conditional_step)){
                                $question_condition_type = "another_go_to_step_condition";
                                if(!empty($data->new_another_conditions)){
                                    $step_conditions = json_decode(json_encode($data->new_another_conditions), true);

                                    foreach($step_conditions as $key => $step){
                                        $question_condition = new GlobalContractQuestionCondition();
                                        $question_condition->question_id = $questions->id;
                                        $question_condition->condition_type = $question_condition_type;

                                        if(!empty($step['go_to_step'])){
                                            $question_condition->go_to_step = $step['go_to_step'];
                                        }

                                        $question_condition->save();

                                        if(!empty($step['subconditions']) && is_array($step['subconditions'])){
                                            foreach ($step['subconditions'] as $sub) {
                                                $subcondition = new GlobalContractSubCondition();
                                                $subcondition->question_condition_id = $question_condition->id;
                                                // $subcondition->key = $key;
                                                $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                $subcondition->conditional_question_value = $sub['question_value'] ?? null;

                                                if(!empty($sub['question_condition'])){
                                                    if($sub['question_condition'] == "is_equal_to"){
                                                        $conditionCheck = 1;
                                                    }elseif($sub['question_condition'] == "is_greater_than"){
                                                        $conditionCheck = 2;
                                                    }elseif($sub['question_condition'] == "is_less_than"){
                                                        $conditionCheck = 3;
                                                    }elseif($sub['question_condition'] == "not_equal_to"){
                                                        $conditionCheck = 4;
                                                    }
                                                }

                                                $subcondition->conditional_check = $conditionCheck;
                                                $subcondition->save();
                                            }
                                        }
                                    }
                                }

                            }
                        }

                        if(isset($data->new_options) && $data->new_options != null){
                            $order = 1;
                            for($i=0; $i<count($data->new_options); $i++){
                                $option = $data->new_options[$i];
                                $multiple_options = new GlobalContractMultipleChoiceQuestion;
                                $multiple_options->question_id = $questions->id;
                                $multiple_options->option_label = $option->option_label;
                                $multiple_options->option_value = $option->option_value;
                                $multiple_options->next_question_id = $option->option_go_to_step;
                                $multiple_options->order_id = $order++;
                                $multiple_options->save();
                            }
                        }

                        if(!empty($data->new_rows)){
                            $lastOrder = GlobalContractMultipleChoiceQuestion::where('question_id', $questions->id)->max('order_id');
                            $order = $lastOrder ? $lastOrder + 1 : 1;

                            for($i=0; $i<count($data->new_rows); $i++){
                                $row = $data->new_rows[$i];

                                $multiple_options = new GlobalContractMultipleChoiceQuestion;
                                $multiple_options->question_id = $questions->id;
                                $multiple_options->option_label = $row->label;
                                $multiple_options->contract_link = $row->contract_link;
                                $multiple_options->order_id = $order++;
                                $multiple_options->save();
                            }
                        }
                        $question_data->save();

                    }elseif($data->is_new == false){
                        $questions = GlobalContractQuestion::find($data->id);
                        // return $questions;

                        $order_id = $data->order_id;

                        if(!empty($data->is_conditional_question) && !empty($data->is_conditional_step)){
                            $is_condition = 1;
                            $condition_type = 3;
                        }elseif(!empty($data->is_conditional_question)){
                            $is_condition = 1;
                            $condition_type = 1;
                        }elseif(!empty($data->is_conditional_step)){
                            $is_condition = 1;
                            $condition_type = 2;
                        }else{
                            $is_condition = 0;
                            $condition_type = null;
                        }

                        $questions->is_condition = $is_condition;
                        $questions->condition_type = $condition_type;
                        $questions->is_end = $data->is_end;
                        $questions->order_id = $order_id;
                        $questions->update();

                        $question_data = GlobalContractQuestionData::where('question_id',$data->id)->first();
                        $question_data->question_label = $data->question_label;

                        if(isset($data->text_box_placeholder) && $data->text_box_placeholder != null){
                            $question_data->text_box_placeholder = $data->text_box_placeholder;
                        }

                        if($data->type == "dropdown-link"){
                            $question_data->same_contract_link_label = $data->same_contract_link;
                        }

                        if(isset($data->go_to_step)){
                            if($data->go_to_step == "0"){
                                $question_data->next_question_id = null;
                            }else{
                                $question_data->next_question_id = $data->go_to_step;
                            }
                        }
                        $question_data->question_info_text = $data->question_info_text;
                        $question_data->update();

                        if($questions->condition_type == 1){
                            $question_condition_type = "question_label_condition";

                            if(!empty($data->new_conditional_question_labels)){
                                $new_conditional = $data->new_conditional_question_labels;
                                for($i=0; $i<count($new_conditional); $i++){
                                    $conditional = $new_conditional[$i];

                                    $question_conditions = new GlobalContractQuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;
                                    $question_conditions->question_label = $conditional->label;
                                    $question_conditions->conditional_question_id = $conditional->questionID;
                                    $question_conditions->conditional_question_value = $conditional->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if(!empty($data->conditional_question_labels)){
                                $conditional_question_labels = $data->conditional_question_labels;
                                for($i=0; $i<count($conditional_question_labels); $i++){
                                    $conditional = $conditional_question_labels[$i];

                                    $question_conditions = GlobalContractQuestionCondition::where('id',$conditional->condition_id)->first();
                                    $question_conditions->question_label = $conditional->label;
                                    $question_conditions->conditional_question_id = $conditional->questionID;
                                    $question_conditions->conditional_question_value = $conditional->question_value;
                                    $question_conditions->update();
                                }
                            }
                        }elseif($questions->condition_type == 2){
                            $question_condition_type = "go_to_step_condition";

                            if(!empty($data->new_conditions)){
                                $new_conditions = $data->new_conditions;
                                
                                for($i=0; $i<count($new_conditions); $i++){
                                    $step = $new_conditions[$i];

                                    $question_conditions = new GlobalContractQuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;

                                    if(!empty($step->question_condition)){
                                        if($step->question_condition == "is_equal_to"){
                                            $conditionCheck = 1;
                                        }elseif($step->question_condition == "is_greater_than"){
                                            $conditionCheck = 2;
                                        }elseif($step->question_condition == "is_less_than"){
                                            $conditionCheck = 3;
                                        }elseif($step->question_condition == "not_equal_to"){
                                            $conditionCheck = 4;
                                        }
                                    }

                                    $question_conditions->conditional_check = $conditionCheck;
                                    $question_conditions->conditional_question_id = $step->questionID;
                                    $question_conditions->conditional_question_value = $step->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if(!empty($data->conditions)){
                                $step_conditions = $data->conditions;
                                for($i=0; $i<count($step_conditions); $i++){
                                    $step = $step_conditions[$i];

                                    $question_conditions = GlobalContractQuestionCondition::where('id',$step->condition_id)->first();

                                    if(!empty($step->question_condition)){
                                        if($step->question_condition == "is_equal_to"){
                                            $conditionCheck = 1;
                                        }elseif($step->question_condition == "is_greater_than"){
                                            $conditionCheck = 2;
                                        }elseif($step->question_condition == "is_less_than"){
                                            $conditionCheck = 3;
                                        }elseif($step->question_condition == "not_equal_to"){
                                            $conditionCheck = 4;
                                        }
                                    }

                                    $question_conditions->conditional_check = $conditionCheck;
                                    $question_conditions->conditional_question_id = $step->questionID;
                                    $question_conditions->conditional_question_value = $step->question_value;
                                    $question_conditions->update();
                                }
                            }

                            if(isset($data->condition_go_to_step)){
                                $question_data->conditional_go_to_step = $data->condition_go_to_step;
                                $question_data->save();
                            }

                            if(!empty($data->is_another_conditional_step)){
                                $question_condition_type = "another_go_to_step_condition";
                                if(!empty($data->new_another_conditions)){
                                    $step_conditions = json_decode(json_encode($data->new_another_conditions), true);

                                    foreach($step_conditions as $key => $step){
                                        $question_condition = new GlobalContractQuestionCondition();
                                        $question_condition->question_id = $questions->id;
                                        $question_condition->condition_type = $question_condition_type;

                                        if(!empty($step['go_to_step'])){
                                            if($step['go_to_step'] == '0'){
                                                $question_condition->go_to_step = null;
                                            }else{
                                                $question_condition->go_to_step = $step['go_to_step'];
                                            }
                                            // $question_condition->go_to_step = $step['go_to_step'];
                                        }

                                        $question_condition->save();

                                        if(!empty($step['subconditions']) && is_array($step['subconditions'])){
                                            foreach ($step['subconditions'] as $sub) {
                                                $subcondition = new GlobalContractSubCondition();
                                                $subcondition->question_condition_id = $question_condition->id;
                                                $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                $subcondition->conditional_question_value = $sub['question_value'] ?? null;

                                                if(!empty($sub['question_condition'])){
                                                    if($sub['question_condition'] == "is_equal_to"){
                                                        $conditionCheck = 1;
                                                    }elseif($sub['question_condition'] == "is_greater_than"){
                                                        $conditionCheck = 2;
                                                    }elseif($sub['question_condition'] == "is_less_than"){
                                                        $conditionCheck = 3;
                                                    }elseif($sub['question_condition'] == "not_equal_to"){
                                                        $conditionCheck = 4;
                                                    }
                                                }

                                                $subcondition->conditional_check = $conditionCheck;
                                                $subcondition->save();
                                            }
                                        }
                                    }
                                }

                                if (!empty($data->another_conditions)) {
                                    $step_conditions = json_decode(json_encode($data->another_conditions), true);

                                    foreach ($step_conditions as $key => $step) {
                                        $existing_condition_id = $step['existing_condition_id'] ?? null;

                                        if (!empty($step['subconditions']) && is_array($step['subconditions'])) {
                                            foreach ($step['subconditions'] as $sub) {
                                                $conditionCheck = null;

                                                if (!empty($sub['question_condition'])) {
                                                    switch (strtolower(str_replace(' ', '_', $sub['question_condition']))) {
                                                        case 'is_equal_to':
                                                            $conditionCheck = 1;
                                                            break;
                                                        case 'is_greater_than':
                                                            $conditionCheck = 2;
                                                            break;
                                                        case 'is_less_than':
                                                            $conditionCheck = 3;
                                                            break;
                                                        case 'not_equal_to':
                                                            $conditionCheck = 4;
                                                            break;
                                                    }
                                                }

                                                if($sub['status'] === false){
                                                    $subcondition = GlobalContractSubCondition::find($sub['condition_id']);
                                                    if ($subcondition) {
                                                        $question_id = $subcondition->question_condition_id;
                                                        $question_condition = GlobalContractQuestionCondition::find($question_id);

                                                        if ($question_condition) {
                                                            if (isset($step['go_to_step'])) {
                                                                $question_condition->go_to_step = $step['go_to_step'] == '0' ? null : $step['go_to_step'];
                                                                $question_condition->save();
                                                            }
                                                        }

                                                        $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                        $subcondition->conditional_question_value = $sub['question_value'] ?? null;
                                                        $subcondition->conditional_check = $conditionCheck;
                                                        $subcondition->save();
                                                    }

                                                }elseif ($sub['status'] === true){
                                                    $subcondition = new GlobalContractSubCondition;
                                                    $subcondition->question_condition_id = $existing_condition_id;
                                                    $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                    $subcondition->conditional_question_value = $sub['question_value'] ?? null;
                                                    $subcondition->conditional_check = $conditionCheck;
                                                    $subcondition->save();
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                        }elseif($questions->condition_type == 3){
                            if(!empty($data->new_conditional_question_labels)){
                                $question_condition_type = "question_label_condition";
                                $new_conditional = $data->new_conditional_question_labels;
                                for($i=0; $i<count($new_conditional); $i++){
                                    $conditional = $new_conditional[$i];

                                    $question_conditions = new GlobalContractQuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;
                                    $question_conditions->question_label = $conditional->label;
                                    $question_conditions->conditional_question_id = $conditional->questionID;
                                    $question_conditions->conditional_question_value = $conditional->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if(!empty($data->conditional_question_labels)){
                                $conditional_question_labels = $data->conditional_question_labels;
                                for($i=0; $i<count($conditional_question_labels); $i++){
                                    $conditional = $conditional_question_labels[$i];

                                    $question_conditions = GlobalContractQuestionCondition::where('id',$conditional->condition_id)->first();
                                    $question_conditions->question_label = $conditional->label;
                                    $question_conditions->conditional_question_id = $conditional->questionID;
                                    $question_conditions->conditional_question_value = $conditional->question_value;
                                    $question_conditions->update();
                                }
                            }

                            if(!empty($data->new_conditions)){
                                $question_condition_type = "go_to_step_condition";
                                $new_conditions = $data->new_conditions;
                                for($i=0; $i<count($new_conditions); $i++){
                                    $step = $new_conditions[$i];

                                    $question_conditions = new GlobalContractQuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;

                                    if(!empty($step->question_condition)){
                                        if($step->question_condition == "is_equal_to"){
                                            $conditionCheck = 1;
                                        }elseif($step->question_condition == "is_greater_than"){
                                            $conditionCheck = 2;
                                        }elseif($step->question_condition == "is_less_than"){
                                            $conditionCheck = 3;
                                        }elseif($step->question_condition == "not_equal_to"){
                                            $conditionCheck = 4;
                                        }
                                    }

                                    $question_conditions->conditional_check = $conditionCheck;
                                    $question_conditions->conditional_question_id = $step->questionID;
                                    $question_conditions->conditional_question_value = $step->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if(!empty($data->conditions)){
                                $step_conditions = $data->conditions;
                                for($i=0; $i<count($step_conditions); $i++){
                                    $step = $step_conditions[$i];

                                    $question_conditions = GlobalContractQuestionCondition::where('id',$step->condition_id)->first();

                                    if(!empty($step->question_condition)){
                                        if($step->question_condition == "is_equal_to"){
                                            $conditionCheck = 1;
                                        }elseif($step->question_condition == "is_greater_than"){
                                            $conditionCheck = 2;
                                        }elseif($step->question_condition == "is_less_than"){
                                            $conditionCheck = 3;
                                        }elseif($step->question_condition == "not_equal_to"){
                                            $conditionCheck = 4;
                                        }
                                    }

                                    $question_conditions->conditional_check = $conditionCheck;
                                    $question_conditions->conditional_question_id = $step->questionID;
                                    $question_conditions->conditional_question_value = $step->question_value;
                                    $question_conditions->update();
                                }
                            }

                            if(isset($data->condition_go_to_step)){
                                $question_data->conditional_go_to_step = $data->condition_go_to_step;
                                $question_data->update();
                            }

                            if(!empty($data->is_another_conditional_step)){
                                $question_condition_type = "another_go_to_step_condition";
                                if(!empty($data->new_another_conditions)){
                                    $step_conditions = json_decode(json_encode($data->new_another_conditions), true);

                                    foreach($step_conditions as $key => $step){
                                        $question_condition = new GlobalContractQuestionCondition();
                                        $question_condition->question_id = $questions->id;
                                        $question_condition->condition_type = $question_condition_type;

                                        if(!empty($step['go_to_step'])){
                                            if($step['go_to_step'] == '0'){
                                                $question_condition->go_to_step = null;
                                            }else{
                                                $question_condition->go_to_step = $step['go_to_step'];
                                            }
                                            // $question_condition->go_to_step = $step['go_to_step'];
                                        }

                                        $question_condition->save();

                                        if(!empty($step['subconditions']) && is_array($step['subconditions'])){
                                            foreach ($step['subconditions'] as $sub) {
                                                $subcondition = new GlobalContractSubCondition();
                                                $subcondition->question_condition_id = $question_condition->id;
                                                // $subcondition->key = $key;
                                                $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                $subcondition->conditional_question_value = $sub['question_value'] ?? null;

                                                if(!empty($sub['question_condition'])){
                                                    if($sub['question_condition'] == "is_equal_to"){
                                                        $conditionCheck = 1;
                                                    }elseif($sub['question_condition'] == "is_greater_than"){
                                                        $conditionCheck = 2;
                                                    }elseif($sub['question_condition'] == "is_less_than"){
                                                        $conditionCheck = 3;
                                                    }elseif($sub['question_condition'] == "not_equal_to"){
                                                        $conditionCheck = 4;
                                                    }
                                                }

                                                $subcondition->conditional_check = $conditionCheck;
                                                $subcondition->save();
                                            }
                                        }
                                    }
                                }

                                if (!empty($data->another_conditions)) {
                                    $step_conditions = json_decode(json_encode($data->another_conditions), true);

                                    foreach ($step_conditions as $key => $step) {
                                        $existing_condition_id = $step['existing_condition_id'] ?? null;

                                        if (!empty($step['subconditions']) && is_array($step['subconditions'])) {
                                            foreach ($step['subconditions'] as $sub) {
                                                $conditionCheck = null;

                                                if (!empty($sub['question_condition'])) {
                                                    switch (strtolower(str_replace(' ', '_', $sub['question_condition']))) {
                                                        case 'is_equal_to':
                                                            $conditionCheck = 1;
                                                            break;
                                                        case 'is_greater_than':
                                                            $conditionCheck = 2;
                                                            break;
                                                        case 'is_less_than':
                                                            $conditionCheck = 3;
                                                            break;
                                                        case 'not_equal_to':
                                                            $conditionCheck = 4;
                                                            break;
                                                    }
                                                }

                                                if ($sub['status'] === false) {
                                                    $subcondition = GlobalContractSubCondition::find($sub['condition_id']);
                                                    if ($subcondition) {
                                                        $question_id = $subcondition->question_condition_id;
                                                        $question_condition = GlobalContractQuestionCondition::find($question_id);

                                                        if ($question_condition) {
                                                            if (isset($step['go_to_step'])) {
                                                                $question_condition->go_to_step = $step['go_to_step'] == '0' ? null : $step['go_to_step'];
                                                                $question_condition->save();
                                                            }
                                                        }

                                                        $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                        $subcondition->conditional_question_value = $sub['question_value'] ?? null;
                                                        $subcondition->conditional_check = $conditionCheck;
                                                        $subcondition->save();
                                                    }

                                                } elseif ($sub['status'] === true) {
                                                    $subcondition = new GlobalContractSubCondition;
                                                    $subcondition->question_condition_id = $existing_condition_id;
                                                    $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                    $subcondition->conditional_question_value = $sub['question_value'] ?? null;
                                                    $subcondition->conditional_check = $conditionCheck;
                                                    $subcondition->save();
                                                }
                                            }
                                        }
                                    }
                                }

                            }
                        }

                        if(!empty($data->add_options)){
                            for($i=0; $i<count($data->add_options); $i++){
                                $option = $data->add_options[$i];

                                if($option->option_go_to_step == "0"){
                                    $go_to_step = null;
                                }else{
                                    $go_to_step = $option->option_go_to_step;
                                }

                                $multiple_options = GlobalContractMultipleChoiceQuestion::where('id',$option->option_id)->first();
                                if($multiple_options){
                                    $multiple_options->option_label = $option->option_label;
                                    $multiple_options->option_value = $option->option_value;
                                    $multiple_options->next_question_id = $go_to_step;
                                    $multiple_options->update();
                                }
                            }
                         }

                        if(!empty($data->new_options)){
                            $lastOrder = GlobalContractMultipleChoiceQuestion::where('question_id', $questions->id)->max('order_id');
                            $order = $lastOrder ? $lastOrder + 1 : 1;
                            foreach($data->new_options as $option) {
                                if($option->option_go_to_step == "0"){
                                    $go_to_step = null;
                                }else{
                                    $go_to_step = $option->option_go_to_step;
                                }

                                $multiple_options = new GlobalContractMultipleChoiceQuestion;
                                $multiple_options->question_id = $questions->id;
                                $multiple_options->option_label = $option->option_label;
                                $multiple_options->option_value = $option->option_value;
                                $multiple_options->next_question_id = $go_to_step;
                                $multiple_options->order_id = $order++;
                                $multiple_options->save();
                            }
                        }


                        if(!empty($data->add_rows)){
                            for($i=0; $i<count($data->add_rows); $i++){
                                $row = $data->add_rows[$i];

                                $multiple_options = GlobalContractMultipleChoiceQuestion::where('id',$row->option_id)->first();
                                $multiple_options->option_label = $row->label;
                                $multiple_options->contract_link = $row->contract_link;
                                // $multiple_options->contract_send_to_next_step = $row->next_step;
                                $multiple_options->update();
                            }
                        }

                        if(!empty($data->new_rows)){
                            $lastOrder = GlobalContractMultipleChoiceQuestion::where('question_id', $questions->id)->max('order_id');
                            $order = $lastOrder ? $lastOrder + 1 : 1;

                            for($i=0; $i<count($data->new_rows); $i++){
                                $row = $data->new_rows[$i];

                                $multiple_options = new GlobalContractMultipleChoiceQuestion;
                                $multiple_options->question_id = $questions->id;
                                $multiple_options->option_label = $row->label;
                                $multiple_options->contract_link = $row->contract_link;
                                // $multiple_options->contract_send_to_next_step = $row->next_step;
                                $multiple_options->order_id = $order++;
                                $multiple_options->save();
                            }
                        }
                    }
                }

                if(!empty($request->option_id)){
                    $ids = explode(',',$request->option_id);
                    foreach($ids as $id){
                        $options = GlobalContractMultipleChoiceQuestion::where('id',$id)->first();
                        if($options){
                            $options->delete();
                        }
                    }
                }

                if(!empty($request->condition_id)){
                    $ids = explode(',', $request->condition_id);

                    foreach($ids as $id){
                        $condition = GlobalContractQuestionCondition::where('id', $id)->first();

                        if($condition){
                            $question_id = $condition->question_id;

                            // Delete the condition
                            $condition->delete();

                            // Fetch remaining conditions for the question
                            $remainingConditions = GlobalContractQuestionCondition::where('question_id', $question_id)->get();

                            if($remainingConditions->isEmpty()){
                                // No more conditions, reset values in questions table
                                GlobalContractQuestion::where('id', $question_id)->update([
                                    'is_condition' => 0,
                                    'condition_type' => null
                                ]);
                            }else{
                                // Determine new condition_type based on remaining string types
                                $types = $remainingConditions->pluck('condition_type')->unique()->toArray();

                                $newConditionType = null;
                                $isCondition = 1;

                                // Check what types are present
                                $hasQuestionLabel = in_array('question_label_condition', $types);
                                $hasGoToStep = in_array('go_to_step_condition', $types);

                                if($hasQuestionLabel && $hasGoToStep){
                                    $newConditionType = 3; // Both
                                }elseif($hasQuestionLabel){
                                    $newConditionType = 1; // Only question label
                                }elseif($hasGoToStep){
                                    $newConditionType = 2; // Only go to step
                                }

                                // Update question table
                                GlobalContractQuestion::where('id', $question_id)->update([
                                    'is_condition' => $isCondition,
                                    'condition_type' => $newConditionType
                                ]);
                            }
                        }
                    }
                }

                if(!empty($request->sub_condition_id)){
                    $ids = explode(',', $request->sub_condition_id);

                    foreach($ids as $id){
                        $sub_condition = GlobalContractSubCondition::where('id', $id)->first();

                        // dd($sub_condition);

                        if($sub_condition){
                            $qu_condition_id = $sub_condition->question_condition_id;
                            $sub_condition->delete();

                            $remainingSubConditions = GlobalContractSubCondition::where('question_condition_id', $qu_condition_id)->get();

                            if($remainingSubConditions->isEmpty()){
                                GlobalContractQuestionCondition::where('id', $qu_condition_id)->delete();
                            }
                        }
                    }
                }


                if(!empty($request->remove_question_id)){
                    $deleteIds = explode(',', $request->remove_question_id);
                    foreach($deleteIds as $id){
                        $delete_question = GlobalContractQuestion::where('id',$id)->first();
                        if($delete_question){
                            $delete_question->delete();
                        }
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'Contract Questions added successfully.');
            }
        }catch(Exception $e){
            DB::rollBack();
            saveLog("Error:", "GlobalController", $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());

        }
    }

    public function contractText($id){
        // return $id;
        $document = StandardDocument::find($id);
        $questions = GlobalContractQuestion::where('document_id',$id)->get();
        $documentRight = GlobalContractText::where('document_id',$id)->with('conditions')->orderBy('order_id','asc')->get();
        // dd($documentRight);

        return view('admin.documents.contract_text', compact('documentRight','questions','id','document'));
    }

    public function addContractText(Request $request){
        DB::beginTransaction();
        try{
            if(isset($request->formdata) && $request->formdata != null){
                $formData = json_decode($request->formdata);
                foreach($formData as $data){
                    if($data->section == 'content_heading'){

                        if($data->is_new == true){
                            $text_align = $data->text_align ?? null;
                            $document_right_section = new GlobalContractText;

                            if(!empty($data->order_id)){
                                $document_right_section->order_id = $data->order_id;
                            }else{
                                $lastOrder = GlobalContractText::where('document_id', $request->document_id)
                                            ->orderBy('order_id', 'desc')
                                            ->first();

                                $document_right_section->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;
                            }

                            $document_right_section->document_id = $request->document_id;
                            $document_right_section->content = $data->heading_html;
                            $document_right_section->type = $data->section;
                            $document_right_section->text_align = $text_align;
                            $document_right_section->published = $request->published;
                            $document_right_section->save();

                        }elseif($data->is_new == false){
                            $text_align = $data->text_align ?? null;
                            $document_right_section = GlobalContractText::where([['id',$data->id],['type',$data->section]])->first();
                            $document_right_section->order_id = $data->order_id;
                            $document_right_section->content = $data->heading_html;
                            $document_right_section->text_align = $text_align;
                            $document_right_section->update();
                        }


                    }elseif($data->section == 'content'){
                        if($data->is_new == true){
                            $text_align = $data->text_align ?? null;
                            $document_right_section = new GlobalContractText;
                            if(!empty($data->order_id)){
                                $document_right_section->order_id = $data->order_id;
                            }else{
                                $lastOrder = GlobalContractText::where('document_id', $request->document_id)
                                            ->orderBy('order_id', 'desc')
                                            ->first();
                                $document_right_section->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;
                            }

                            $document_right_section->document_id = $request->document_id;
                            $document_right_section->type = $data->section;
                            $document_right_section->published = $request->published;
                            $document_right_section->content = $data->content_html;
                            $document_right_section->text_align = $text_align;
                            $document_right_section->is_condition = $data->add_condition;
                            $document_right_section->secure_blur_content = $data->secure_blurr_content;
                            $document_right_section->save();

                        }elseif($data->is_new == false){
                            $text_align = $data->text_align ?? null;

                            $document_right_section = GlobalContractText::where([['id',$data->id],['type',$data->section]])->first();
                            $document_right_section->order_id = $data->order_id;
                            $document_right_section->content = $data->content_html;
                            $document_right_section->text_align = $text_align;
                            $document_right_section->is_condition = $data->add_condition;
                            $document_right_section->secure_blur_content = $data->secure_blurr_content;
                            $document_right_section->update();
                        }

                        if(!empty($data->add_condition)){
                            if (!empty($data->new_conditions)) {
                                foreach ($data->new_conditions as $new_condition) {
                                    $condition_value = match ($new_condition->condition) {
                                        'is_equal_to' => 1,
                                        'is_greater_than' => 2,
                                        'is_less_than' => 3,
                                        'not_equal_to' => 4,
                                        default => null,
                                    };


                                    if($condition_value !== null){
                                        $documentCondition = new GlobalContractQuestionCondition();
                                        $documentCondition->condition_type = 'content_condition';
                                        $documentCondition->document_right_content_id = $document_right_section->id;
                                        $documentCondition->conditional_question_id = $new_condition->question_id;
                                        $documentCondition->conditional_check = $condition_value;
                                        $documentCondition->conditional_question_value = $new_condition->question_value;
                                        $documentCondition->save();
                                    }
                                }
                            }

                            // Handle Existing Conditions
                            if (!empty($data->conditions)) {
                                foreach ($data->conditions as $condition) {
                                    $condition_value = match($condition->condition){
                                        'is_equal_to' => 1,
                                        'is_greater_than' => 2,
                                        'is_less_than' => 3,
                                        'not_equal_to' => 4,
                                        default => null,
                                    };

                                    if ($condition_value !== null) {
                                        $documentCondition = GlobalContractQuestionCondition::find($condition->condition_id);
                                        if ($documentCondition) {
                                            $documentCondition->conditional_question_id = $condition->question_id;
                                            $documentCondition->conditional_check = $condition_value;
                                            $documentCondition->conditional_question_value = $condition->question_value;
                                            $documentCondition->update();
                                        }
                                    }
                                }
                            }
                        }

                    }elseif($data->section == 'signature_field'){
                        if($data->is_new == true){

                            $text_align = $data->text_align ?? null;
                            $document_right_section = new GlobalContractText;
                            if(!empty($data->order_id)){
                                $document_right_section->order_id = $data->order_id;
                            }else{
                                $lastOrder = GlobalContractText::where('document_id', $request->document_id)
                                                ->orderBy('order_id', 'desc')
                                                ->first();
                                $document_right_section->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;
                            }

                            if(!empty($data->content) && !empty($data->content2) && !empty($data->content3)){
                                $document_right_section->content = $data->content;
                                $document_right_section->content2 = $data->content2;
                                $document_right_section->content3 = $data->content3;
                            }else{
                                if(!empty($data->sign_content)){
                                    $document_right_section->content = $data->sign_content;
                                }

                                if(!empty($data->new_sign_content)){
                                    foreach($data->new_sign_content as $indx => $signText){
                                        if($indx == 0){
                                            $document_right_section->content2 = $signText;
                                        }elseif($indx == 1){
                                            $document_right_section->content3 = $signText;
                                        }
                                    }
                                }

                            }

                            $document_right_section->document_id = $request->document_id;
                            $document_right_section->type = $data->section;
                            $document_right_section->signature_field = 1;
                            $document_right_section->text_align = $text_align;
                            $document_right_section->is_condition = $data->add_condition;
                            $document_right_section->published = $request->published;
                            $document_right_section->secure_blur_content = $data->secure_blurr_content;
                            $document_right_section->save();

                        }elseif($data->is_new == false){
                            $text_align = $data->text_align ?? null;
                            $document_right_section = GlobalContractText::where([['id',$data->id],['type',$data->section]])->first();

                            if($data->sign_content != null){
                                $document_right_section->content = $data->sign_content;
                            }


                            if(!empty($data->new_sign_content)){
                                $new_text = $data->new_sign_content;

                                if(count($new_text) > 1){

                                    foreach($new_text as $indx=>$signText){
                                        if($indx == 0){
                                            $document_right_section->content2 = $signText;
                                        }elseif($indx == 1){
                                            $document_right_section->content3 = $signText;
                                        }
                                    }

                                }else{
                                    foreach($new_text as $indx=>$signText){
                                        $document_right_section->content3 = $signText;
                                    }
                                }

                            }

                            $document_right_section->order_id = $data->order_id;
                            $document_right_section->signature_field = 1;
                            $document_right_section->text_align = $text_align;
                            $document_right_section->is_condition = $data->add_condition;
                            $document_right_section->secure_blur_content = $data->secure_blurr_content;

                            $document_right_section->update();
                        }

                        if(!empty($data->add_condition)){
                            if (!empty($data->new_conditions)) {
                                foreach ($data->new_conditions as $new_condition) {
                                    $condition_value = match ($new_condition->condition) {
                                        'is_equal_to' => 1,
                                        'is_greater_than' => 2,
                                        'is_less_than' => 3,
                                        'not_equal_to' => 4,
                                        default => null,
                                    };


                                    if($condition_value !== null){
                                        $documentCondition = new GlobalContractQuestionCondition();
                                        $documentCondition->condition_type = 'signature_field';
                                        $documentCondition->document_right_content_id = $document_right_section->id;
                                        $documentCondition->conditional_question_id = $new_condition->question_id;
                                        $documentCondition->conditional_check = $condition_value;
                                        $documentCondition->conditional_question_value = $new_condition->question_value;
                                        $documentCondition->save();
                                    }
                                }
                            }

                            // Handle Existing Conditions
                            if (!empty($data->conditions)) {
                                foreach ($data->conditions as $condition) {
                                    $condition_value = match($condition->condition){
                                        'is_equal_to' => 1,
                                        'is_greater_than' => 2,
                                        'is_less_than' => 3,
                                        'not_equal_to' => 4,
                                        default => null,
                                    };

                                    if ($condition_value !== null) {
                                        $documentCondition = GlobalContractQuestionCondition::find($condition->condition_id);
                                        if ($documentCondition) {
                                            $documentCondition->conditional_question_id = $condition->question_id;
                                            $documentCondition->conditional_check = $condition_value;
                                            $documentCondition->conditional_question_value = $condition->question_value;
                                            $documentCondition->update();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if($request->remove_content != null){
                    $deleteIds = explode(',', $request->remove_content);
                    foreach($deleteIds as $id){
                        $delete_content = GlobalContractText::where([['id',$id],['type','content']])->first();
                        if($delete_content){
                            $delete_content->delete();
                        }
                    }
                }

                if($request->remove_content_heading != null){
                    $deleteIds = explode(',', $request->remove_content_heading);
                    foreach($deleteIds as $id){
                        $delete_heading = GlobalContractText::where([['id',$id],['type','content_heading']])->first();
                        if($delete_heading){
                            $delete_heading->delete();
                        }
                    }
                }

                if($request->remove_signature != null){
                    $deleteIds = explode(',', $request->remove_signature);
                    foreach($deleteIds as $id){
                        $delete_signature = GlobalContractText::where([['id',$id],['type','signature_field']])->first();
                        if($delete_signature){
                            $delete_signature->delete();
                        }
                    }
                }

                if($request->remove_condition != null){
                    $deleteIds = explode(',', $request->remove_condition);
                    foreach($deleteIds as $id){
                        $delete_condition = GlobalContractQuestionCondition::where('id',$id)->first();
                        if($delete_condition){
                            $delete_condition->delete();
                        }
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'Contract Text Successfully Updated.');
            }
        }catch(Exception $e){
            DB::rollBack();
            saveLog("Error:", "GlobalController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    public function configuration(){
        $keys = [
            'language',
            'country',
            'minimum_requirements',
            'validation_rules',
            'country_currency_symbol',
            'currency_separator'
        ];

        $settings = Setting::whereIn('key', $keys)->get()->keyBy('key');

        $data = [
            'language' => $settings['language']->value ?? null,
            'country' => $settings['country']->value ?? null,
            'minimum_requirements' => $settings['minimum_requirements']->value ?? null,
            'validation_rules' => $settings['validation_rules']->value ?? null,
            'country_currency_symbol' => $settings['country_currency_symbol']->value ?? null,
            'currency_separator' => $settings['currency_separator']->value ?? null,
        ];
      
        return view('admin.documents.configuration',compact('data'));
    }

    public function addGlobalConfiguration(Request $request){
        try{
            $configuration = [
                'language' => 'language',
                'country' => 'country',
                'minimum_requirements' => 'minimum_requirements',
                'validation_rules' => 'validation_rules',
                'country_currency_symbol' => 'country_currency_symbol',
                'currency_separator' => 'currency_separator'
            ];

            foreach($configuration as $key=>$input){
                if($request->has($input)) {
                    // $setting = Setting::where([['key', $key],['type', 'global']])->first();
                    $setting = Setting::where('key', $key)
                        ->whereIn('type', ['global', 'config'])
                        ->first();
                    if($setting) {
                        $setting->value = $request->$input;
                        $setting->update();
                    }
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Configuration added successfully.');

        }catch(\Exception $e){
            DB::rollBack();
            saveLog("Error:", "GlobalController", $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deleteStandardDocument($slug){
        try{
            $document = StandardDocument::where('slug', $slug)->first();
        
            if($document){
                $questions = $document->relatedQuestions;
                foreach($questions as $question){
                    $question->options()->delete();
                    $question->questionData()->delete();
            
                    foreach($question->conditions as $condition){
                        $condition->subconditions()->delete();
                        $condition->delete();
                    }

                    $question->delete();
                }
                
                $texts = $document->relatedTexts;
                foreach($texts as $text){
                    $text->conditions()->delete();
                    $text->delete();
                }
        
                $document->delete();
        
                return redirect()->route('admin.document.standard_document')->with('success', 'Deleted successfully.');
            }
            
            return redirect()->route('admin.document.standard_document')->with('error', 'Standard document not found.');
        }catch(\Exception $e){
            saveLog("Error:", "GlobalController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
