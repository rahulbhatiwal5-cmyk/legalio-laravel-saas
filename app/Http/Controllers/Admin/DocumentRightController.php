<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentRightSection;
use App\Models\RightSectionCondition;
use App\Models\QuestionCondition;
use App\Models\Question;
use Illuminate\Support\Str;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Exception;

class DocumentRightController extends Controller
{
    public function allRightContent(){
        $documentRightContent = DocumentRightSection::with('document')->get();
        $documents = Document::where('published',1)->get();
        return view('admin.document_right_content.all_document_right_content',compact('documents'));
        }


    public function documentRightContent(Request $request){
        $documents = Document::where('published',1)->get();
        $questions = '';
        $document = '';
        $slug = '';
        $documentRight = '';

        if(isset($request->id) && $request->id!= null){
            $questions = Question::where('document_id',$request->id)->get();
            $document = Document::where('id',$request->id)->first();
            $slug = $document->slug;
            $documentRight = DocumentRightSection::where('document_id', $request->id)->with('conditions','document')->orderBy('order_id','asc')->get();
            // dd($documentRight->toArray(), $questions->toArray());

        }

        return view('admin.document_right_content.document_right_content',compact('document','documentRight','questions','slug'));

    }

    public function updateRightContent(Request $request){
        //return $formData = json_decode($request->contentdata);
        // return $request->all();

        DB::beginTransaction();
        try{

            // Change the Content type
            if(isset($request->changed_content_type) && $request->changed_content_type != null){
                $changed_content_type = json_decode($request->changed_content_type);
                foreach($changed_content_type as $Types){
                    if($Types->change_from == $Types->change_to){
                        continue;
                    }

                    $content_id = $Types->content_id;

                    $docContent = DocumentRightSection::find($content_id);

                    if($docContent){
                        $docContent->update(['type' => $Types->change_to]);
                    }
                }
            }

            if(isset($request->contentdata) && $request->contentdata != null){
                $formData = json_decode($request->contentdata);
                foreach($formData as $data){
                    if($data->section == 'content_heading'){

                        if($data->is_new == true){
                            $text_align = $data->text_align ?? null;
                            $document_right_section = new DocumentRightSection;

                            if(!empty($data->order_id)){
                                $document_right_section->order_id = $data->order_id;
                            }else{
                                $lastOrder = DocumentRightSection::where('document_id', $request->documentId)
                                                    ->orderBy('order_id', 'desc')
                                                    ->first();
                                $document_right_section->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;
                            }

                            $document_right_section->content = $data->heading_html;
                            $document_right_section->type = $data->section;
                            $document_right_section->text_align = $text_align;
                            $document_right_section->document_id = $request->documentId;
                            $document_right_section->published = $request->published;
                            $document_right_section->save();

                        }elseif($data->is_new == false){
                            $text_align = $data->text_align ?? null;
                            $document_right_section = DocumentRightSection::where([['id',$data->id],['type',$data->section]])->first();
                            $document_right_section->order_id = $data->order_id;
                            $document_right_section->content = $data->heading_html;
                            $document_right_section->text_align = $text_align;
                            $document_right_section->update();
                        }


                    }elseif($data->section == 'content'){
                        if($data->is_new == true){
                            $text_align = $data->text_align ?? null;
                            $document_right_section = new DocumentRightSection;
                            if(!empty($data->order_id)){
                                $document_right_section->order_id = $data->order_id;
                            }else{
                                $lastOrder = DocumentRightSection::where('document_id', $request->documentId)
                                                    ->orderBy('order_id', 'desc')
                                                    ->first();
                                $document_right_section->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;
                            }

                            $document_right_section->type = $data->section;
                            $document_right_section->document_id = $request->documentId;
                            $document_right_section->published = $request->published;
                            $document_right_section->content = $data->content_html;
                            $document_right_section->text_align = $text_align;
                            $document_right_section->is_condition = $data->add_condition;
                            $document_right_section->secure_blur_content = $data->secure_blurr_content;
                            $document_right_section->save();

                        }elseif($data->is_new == false){
                            $text_align = $data->text_align ?? null;

                            $document_right_section = DocumentRightSection::where([['id',$data->id],['type',$data->section]])->first();
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
                                        $documentCondition = new QuestionCondition();
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
                                        $documentCondition = QuestionCondition::find($condition->condition_id);
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
                            $document_right_section = new DocumentRightSection;
                            if(!empty($data->order_id)){
                                $document_right_section->order_id = $data->order_id;
                            }else{
                                $lastOrder = DocumentRightSection::where('document_id', $request->documentId)
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


                            $document_right_section->type = $data->section;
                            $document_right_section->document_id = $request->documentId;
                            $document_right_section->signature_field = 1;
                            $document_right_section->text_align = $text_align;
                            $document_right_section->is_condition = $data->add_condition;
                            $document_right_section->published = $request->published;
                            $document_right_section->secure_blur_content = $data->secure_blurr_content;
                            $document_right_section->save();

                        }elseif($data->is_new == false){
                            $text_align = $data->text_align ?? null;
                            $document_right_section = DocumentRightSection::where([['id',$data->id],['type',$data->section]])->first();

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
                                        $documentCondition = new QuestionCondition();
                                        $documentCondition->condition_type = 'signature_field';
                                        $documentCondition->document_right_content_id = $document_right_section->id;
                                        $documentCondition->conditional_question_id = $new_condition->question_id;
                                        $documentCondition->conditional_check = $condition_value;
                                        $documentCondition->conditional_question_value = $new_condition->question_value;
                                        $documentCondition->save();
                                    }
                                }
                            }

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
                                        $documentCondition = QuestionCondition::find($condition->condition_id);
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
                        $delete_content = DocumentRightSection::where([['id',$id],['type','content']])->first();
                        if($delete_content){
                            $delete_content->delete();
                        }
                    }
                }

                if($request->remove_content_heading != null){
                    $deleteIds = explode(',', $request->remove_content_heading);
                    foreach($deleteIds as $id){
                        $delete_heading = DocumentRightSection::where([['id',$id],['type','content_heading']])->first();
                        if($delete_heading){
                            $delete_heading->delete();
                        }
                    }
                }

                if($request->remove_signature != null){
                    $deleteIds = explode(',', $request->remove_signature);
                    foreach($deleteIds as $id){
                        $delete_signature = DocumentRightSection::where([['id',$id],['type','signature_field']])->first();
                        if($delete_signature){
                            $delete_signature->delete();
                        }
                    }
                }

                if($request->remove_condition != null){
                    $deleteIds = explode(',', $request->remove_condition);
                    foreach($deleteIds as $id){
                        $delete_condition = QuestionCondition::where('id',$id)->first();
                        if($delete_condition){
                            $delete_condition->delete();
                        }
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'Document Right Section Successfully Updated.');
            }
        }catch(Exception $e){
            DB::rollBack();
            saveLog("Error:", "DocumentRightController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }

    }
}
