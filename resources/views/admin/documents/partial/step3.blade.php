@php 
     use Carbon\Carbon; 
@endphp


<div class="card card-bordered card-preview">
     <div class="card-inner">
          <div class="row step3_section d-flex" id="step3_qu_div">
               <div class="col col-md-9 qu_txt_div" style="max-height: 1000px; overflow-y: auto;">
               @foreach($resultSections as $section)
                    @php $standard_section_id = $section['standard_section_id']; @endphp
                    <div class="row qutn_text_div" data-section_id="{{ $standard_section_id ?? '' }}">
                         @php
                              $count = 1;
                              $num = 1;
                              $total_steps = count($questions);
                              $order = 1;
                         @endphp
                         <div class="col col-md-3 questn_div">
                              @foreach($section['questions'] as $question)
                                   @php 
                                        $que = $question['questions']; 
                                        $question_type = $que->type;
                                   @endphp
                                   <div class="questn_block_{{ $que->id ?? '' }} mb-2">
                                        <div class="row mb-2">
                                             <div class="col-md-6">
                                                  <div class="qu_count">
                                                       <p>
                                                            <b>
                                                            QID : {{ $que->id ?? ''}}
                                                            </b>
                                                       </p>
                                                  </div>
                                             </div>
                                             <div class="col-md-6">
                                                  <div class="text-end edit_document_question">
                                                       <span class="edit_question" data-bs-toggle="modal" data-bs-target="#modalDefault{{ $que->id ?? '' }}">
                                                            <i class="fa fa-edit"></i>
                                                       </span>
                                                  </div>
                                             </div>
                                             <div class="modal fade" tabindex="-1" id="modalDefault{{ $que->id ?? '' }}">
                                                  <div class="modal-dialog" role="document">
                                                       <div class="modal-content">
                                                            <div class="modal-header">
                                                                 <h5 class="modal-title">Document Questions</h5>
                                                                 <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                                 <em class="icon ni ni-cross"></em>
                                                                 </a>
                                                            </div>
                                                            <form action="{{ route('admin.edit.questions') }}" method="post" enctype="multipart/form-data">
                                                                 @csrf
                                                                 <input type="hidden" id="is_end" name="is_end" value="">
                                                                 <input type="hidden" id="remove_question_id" name="remove_question_id" value="">
                                                                 <input type="hidden" id="condition_id" name="condition_id" value="">
                                                                 <input type="hidden" id="sub_condition_id" name="sub_condition_id" value="">
                                                                 <input type="hidden" id="option_id" name="option_id" value="">
                                                                 <input type="hidden" id="changed_question_types" name="changed_question_types" value="[]">
                                                                 <input type="hidden" name="documentID" id="documentID" value="{{ $document->id ?? '' }}">
                                                                 <input type="hidden" name="questionID" id="questionID" value="{{ $que->id ?? '' }}">
                                                                 <input type="hidden" name="orderID" id="orderID" value="{{ $order++ ?? '' }}">

                                                                 <div class="modal-body">
                                                                      <div class="add_qu_sec">
                                                                           @if($question_type == 'textbox')
                                                                           <div class="new_que_sec{{ $que->id ?? '' }}" id="for_copy_sec{{ $que->id ?? '' }}">
                                                                                <div class="append_textbox" id="append_textbox{{ $que->id ?? '' }}" data-id="{{ $que->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                                                     <div class="card card-bordered card-preview">
                                                                                          <div class="card-inner main_question_div">
                                                                                               <div class="row add_step">
                                                                                                    <div class="col-md-6 div_hding">
                                                                                                         <div class="qu_count">
                                                                                                              <p>
                                                                                                                   <b>
                                                                                                                   QID : {{ $que->id ?? ''}}
                                                                                                                   </b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         |
                                                                                                         <div class="que_type_heading">
                                                                                                              <p class="drop_options"><b>Textbox
                                                                                                                   <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         <div class="form-group drop_box_option" style="display:none;">
                                                                                                              <!-- <div class="text-end cut_btn">
                                                                                                                   <div class="form-group">
                                                                                                                        <span onclick="removeDropbox(this)">
                                                                                                                             <i class="fa fa-times"></i>
                                                                                                                        </span>
                                                                                                                   </div>
                                                                                                              </div> -->
                                                                                                              <div class="slct_optns">
                                                                                                                   <select class="form-select js-select2" data-que-id="{{ $que->id ?? '' }}" data-change-from="{{ $question->type ?? '' }}" onchange="ChangeQuestionType(this)">
                                                                                                                        @foreach($types as $type)
                                                                                                                             <option value="{{ $type->slug ?? '' }}" {{ isset($que) && $que->type == $type->slug ? 'selected' : '' }}>{{ $type->name ?? '' }}</option>
                                                                                                                        @endforeach
                                                                                                                   </select>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-6 prnt_icon_cls">
                                                                                                         <div class="input_icons d-flex">
                                                                                                              <span class="remove_icon red_hover" onclick="removeFields(this)" data-id="{{ $que->id ?? '' }}" data-field="textbox" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                              {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                                   <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $que->id ?? '' }}" data-field="textbox"><i class="fa-solid fa-plus"></i></span>
                                                                                                                   <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                                        @foreach($types as $type)
                                                                                                                             <a onclick="addQuestionfields('{{ $type->slug ?? '' }}','{{ $que->id ?? '' }}','third',this)">{{ $type->name ?? '' }}</a>
                                                                                                                        @endforeach
                                                                                                                   </div>
                                                                                                              </div> --}}
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>
                                                                                               <hr>

                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType1 = $isCondition && $que->is_condition == 1 && $que->condition_type == 1 || $que->condition_type == 3;
                                                                                                    $conditions = $isConditionType1 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType1)
                                                                                                    <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                         <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($conditions as $condition)
                                                                                                         @if($condition->condition_type == 'question_label_condition')
                                                                                                              <div class="label-condition" id="label-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new="false">
                                                                                                                   <div class="inner-label">
                                                                                                                        <div class="row">
                                                                                                                             <div class="col-md-4">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_label-{{ $condition->id ?? '' }}"
                                                                                                                                            id="condition_question_label-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question Label"
                                                                                                                                            :value="$condition->question_label ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       @php
                                                                                                                                            $questionOptions = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                 return [$q->getName() => $q->getName()];
                                                                                                                                            });
                                                                                                                                       @endphp

                                                                                                                                       <x-document-input-field
                                                                                                                                            type="question_select"
                                                                                                                                            class="js-select2 new_label_question_id"
                                                                                                                                            name="label_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="label_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question ID"
                                                                                                                                            :options="$questionOptions"
                                                                                                                                            :value="$condition->conditional_question_id ?? ''"
                                                                                                                                       />

                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_value-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_value-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Value"
                                                                                                                                            :value="$condition->conditional_question_value ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-2 add_rmv_icn26">
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon red_hover" onclick="removeLabel(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                                  </div>
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','')"><i class="fa-solid fa-add"></i></span>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>                              
                                                                                                              </div>
                                                                                                         @endif
                                                                                                         @endforeach
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @else
                                                                                                    <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                         <div class="col-md-10 form-group qu_label_cls{{ $que->id ?? '' }} label_qu">
                                                                                                              <x-document-input-field
                                                                                                                   type="text"
                                                                                                                   class="form-control question_labl"
                                                                                                                   name="text_qu_label"
                                                                                                                   id="text_qu_label-{{ $que->id ?? '' }}"
                                                                                                                   label="Question Label"
                                                                                                                   :value="$que->questionData->question_label ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                         <div class="col-md-2 form-group prnt_add_cls qu_label_btn{{ $que->id ?? '' }}">
                                                                                                              <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','{{ $que->questionData->question_label ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                         </div>
                                                                                                         <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}"></div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <hr>
                                                                                               <div class="col-md-12 custom_box_{{ $que->id ?? '' }}">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="text"
                                                                                                              class="form-control text_box_placeholder"
                                                                                                              name="text_box_placeholder"
                                                                                                              id="text_placeholder-{{ $que->id ?? '' }}"
                                                                                                              label="Text Box Placeholder"
                                                                                                              :value="$que->questionData->text_box_placeholder ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              @php
                                                                                                                   $goToQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2 new_label_question_id"
                                                                                                                   name="text_go_to_step"
                                                                                                                   id="text_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   label="Go to step"
                                                                                                                   :options="$goToQuestion"
                                                                                                                   :value="$que->questionData->next_question_id ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType2 = $isCondition && $que->is_condition == 1 && $que->condition_type == 2 || $que->condition_type == 3;
                                                                                                    $enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                                    $another_enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType2 && !empty($enable_conditions))
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}">
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              <label class="form-label" for="">Add Conditions</label>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @php
                                                                                                         $goToStepConditions = collect($enable_conditions)->filter(function($c) {
                                                                                                              return $c->condition_type == 'go_to_step_condition';
                                                                                                         })->values();
                                                                                                    @endphp
                                                                                                    <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($goToStepConditions as $condition)
                                                                                                         <div class="sec-condition" id="sec-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new=false>
                                                                                                              <div class="row">
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                       return [$q->getName() => $q->getName()];
                                                                                                                                  });
                                                                                                                             @endphp

                                                                                                                             <x-document-input-field
                                                                                                                                  type="question_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_qu_id[{{ $condition->id ?? '' }}]"
                                                                                                                                  id="page_Setting_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Question ID"
                                                                                                                                  :options="$goToPageQuestion"
                                                                                                                                  :value="$condition->conditional_question_id ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-4">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $conditionOptions = [
                                                                                                                                       'is_equal_to'     => 'is equal to',
                                                                                                                                       'is_greater_than' => 'is greater than',
                                                                                                                                       'is_less_than'    => 'is less than',
                                                                                                                                       'not_equal_to'    => 'not equal to',
                                                                                                                                  ];


                                                                                                                                  $checkValueMap = [
                                                                                                                                       1 => 'is_equal_to',
                                                                                                                                       2 => 'is_greater_than',
                                                                                                                                       3 => 'is_less_than',
                                                                                                                                       4 => 'not_equal_to',
                                                                                                                                  ];

                                                                                                                                  $selectedCondition = $checkValueMap[$condition->conditional_check ?? 0] ?? '';
                                                                                                                             @endphp

                                                                                                                             <x-document-input-field
                                                                                                                                  type="condition_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_conditions[{{ $condition->id ?? '' }}]"
                                                                                                                                  id="page_Setting_conditions-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Condition"
                                                                                                                                  :options="$conditionOptions"
                                                                                                                                  :value="$selectedCondition"
                                                                                                                             />

                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             <x-document-input-field
                                                                                                                                  type="text"
                                                                                                                                  class="form-control"
                                                                                                                                  name="page_Setting_qu_val[{{ $condition->id ?? '' }}]"
                                                                                                                                  id="page_Setting_qu_val-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Value"
                                                                                                                                  :value="$condition->conditional_question_value ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-2 add_rmv_icn27">
                                                                                                                        @if(!$loop->first)
                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon red_hover" onclick="removeCondition(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                        </div>
                                                                                                                        @endif

                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>

                                                                                                         </div>
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              @php
                                                                                                                   $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2"
                                                                                                                   name="conditional_go_to_step"
                                                                                                                   id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   label="Conditional Go to Step"
                                                                                                                   :options="$conditionalgoToPageQuestion"
                                                                                                                   :value="$que->questionData->conditional_go_to_step ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @if(isset($enable_conditions) && count($enable_conditions) > 0)
                                                                                                    <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}">
                                                                                                         @foreach($enable_conditions as $condIndex => $condition)
                                                                                                              @if($condition->condition_type == 'another_go_to_step_condition')
                                                                                                                   <div class="independent_cond_div" id="independent_cond_div_{{ $condition->id }}_{{ $condIndex }}" data-id="{{ $condition->id }}" data-is_new="false">
                                                                                                                        <hr>
                                                                                                                        <div class="text-end">
                                                                                                                             <div class="form-group">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeIndependentDiv(this,'{{ $condition->id }}','{{ $condIndex }}')" data-id="{{ $condition->id }}">
                                                                                                                                       <i class="fa fa-trash"></i>
                                                                                                                                  </span>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  <label class="form-label">Add Conditions</label>
                                                                                                                             </div>
                                                                                                                        </div>

                                                                                                                        <div class="another_page_condition" id="another_page_condition_{{ $condition->id }}_{{ $condIndex }}">
                                                                                                                        <?php $key = 1;?>
                                                                                                                        @foreach($condition->subconditions as $subcondition)
                                                                                                                             <div class="another-condition" id="another-condition-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}" data-id="{{ $subcondition->id }}" data-is_new="false">
                                                                                                                                  <div class="row">
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                           return [$q->getName() => $q->getName()];
                                                                                                                                                      });
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="question_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Question ID"
                                                                                                                                                      :options="$anotherGoToPageQuestion"
                                                                                                                                                      :value="$subcondition->conditional_question_id ?? '' "
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-4">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherconditionOptions = [
                                                                                                                                                           'is_equal_to'     => 'is equal to',
                                                                                                                                                           'is_greater_than' => 'is greater than',
                                                                                                                                                           'is_less_than'    => 'is less than',
                                                                                                                                                           'not_equal_to'    => 'not equal to',
                                                                                                                                                      ];


                                                                                                                                                      $checkValueMap = [
                                                                                                                                                           1 => 'is_equal_to',
                                                                                                                                                           2 => 'is_greater_than',
                                                                                                                                                           3 => 'is_less_than',
                                                                                                                                                           4 => 'not_equal_to',
                                                                                                                                                      ];

                                                                                                                                                      $selectedSubCondition = $checkValueMap[$subcondition->conditional_check ?? 0] ?? '';

                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="condition_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Condition"
                                                                                                                                                      :options="$anotherconditionOptions"
                                                                                                                                                      :value="$selectedSubCondition"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="text"
                                                                                                                                                      class="form-control"
                                                                                                                                                      name="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Value"
                                                                                                                                                      :value="$subcondition->conditional_question_value ?? ''"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-2 add_rmv_icn28">
                                                                                                                                            @if(!$loop->first)
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon red_hover" onclick="removeAnotherCondition(this,'{{ $subcondition->id }}','{{ $condIndex }}','{{ $key ?? '' }}')" data-id="{{ $condition->id }}">
                                                                                                                                                 <i class="fa fa-trash"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                            @endif
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon add_icon" onclick="anotherCondition(this,'{{ $condition->id }}', '{{ $condIndex }}')">
                                                                                                                                                 <i class="fa-solid fa-plus"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                  </div>
                                                                                                                                  <br>
                                                                                                                             </div>
                                                                                                                             <?php $key++ ;?>
                                                                                                                        @endforeach
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $anotherConditionalGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex }}"
                                                                                                                                       id="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex ?? '' }}"
                                                                                                                                       label="Conditional Go to Step"
                                                                                                                                       :options="$anotherConditionalGoToPageQuestion"
                                                                                                                                       :value="$condition->go_to_step ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id[{{ $que->id ?? '' }}]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp


                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions[{{ $que->id ?? '' }}]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val[{{ $que->id ?? '' }}]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <span class="remove_icon add_icon firstCondBtn" onclick="addCondition('{{ $que->id ?? '' }}')">
                                                                                                                        <i class="fa-solid fa-plus"></i>
                                                                                                                   </span>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step"
                                                                                                                        id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoToPageQuestion"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @endif
                                                                                                    </div>
                                                                                               @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none;">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label" for="">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>

                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id[{{ $que->id ?? '' }}]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];

                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions[{{ $que->id ?? '' }}]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val[{{ $que->id ?? '' }}]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoTo = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step"
                                                                                                                        id="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoTo"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="textarea"
                                                                                                              class="form-control question_info_text"
                                                                                                              name="question_info_text"
                                                                                                              id="question_info_text-{{ $que->id ?? '' }}"
                                                                                                              label="Question Info Text"
                                                                                                              :value="$que->questionData->question_info_text ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <br>
                                                                                </div>
                                                                           </div>
                                                                           @elseif($question_type == 'textarea')
                                                                           <div class="new_que_sec{{ $que->id ?? '' }}" id="for_copy_sec{{ $que->id ?? '' }}">
                                                                                <div class="append_textarea" id="append_textarea{{ $que->id ?? '' }}" data-id="{{ $que->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                                                     <div class="card card-bordered card-preview">
                                                                                          <div class="card-inner">
                                                                                               <div class="row add_step">
                                                                                                    <div class="col-md-6 div_hding">
                                                                                                         <div class="qu_count">
                                                                                                              <p>
                                                                                                                   <b>
                                                                                                                   QID : {{ $que->id ?? ''}}
                                                                                                                   </b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         |
                                                                                                         <div class="que_type_heading">
                                                                                                              <p class="drop_options"><b>Textarea
                                                                                                                   <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         <div class="form-group drop_box_option" style="display:none;">
                                                                                                              <!-- <div class="text-end cut_btn">
                                                                                                                   <div class="form-group">
                                                                                                                        <span onclick="removeDropbox(this)">
                                                                                                                             <i class="fa fa-times"></i>
                                                                                                                        </span>
                                                                                                                   </div>
                                                                                                              </div> -->
                                                                                                              <div class="slct_optns">
                                                                                                                   <select class="form-select js-select2 " data-que-id="{{ $que->id ?? '' }}" data-change-from="{{ $que->type ?? '' }}" onchange="ChangeQuestionType(this)"  >
                                                                                                                        @foreach($types as $type)
                                                                                                                             <option value="{{ $type->slug ?? '' }}" {{ isset($question) && $que->type == $type->slug ? 'selected' : '' }}>{{ $type->name ?? '' }}</option>
                                                                                                                        @endforeach
                                                                                                                   </select>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-6 prnt_icon_cls">
                                                                                                         <div class="input_icons d-flex">
                                                                                                              <span class="remove_icon red_hover" onclick="removeFields(this)" data-id="{{ $que->id ?? '' }}" data-field="textarea" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                              {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                                   <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $que->id ?? '' }}" data-field="textarea"><i class="fa-solid fa-plus"></i></span>
                                                                                                                   <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                                        @foreach($types as $type)
                                                                                                                             <a onclick="addQuestionfields('{{ $type->slug ?? '' }}','{{ $que->id ?? '' }}','third',this)">{{ $type->name ?? '' }}</a>
                                                                                                                        @endforeach
                                                                                                                   </div>
                                                                                                              </div> --}}
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>
                                                                                               <hr>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType1 = $isCondition && $que->is_condition == 1 && $que->condition_type == 1 || $que->condition_type == 3;
                                                                                                    $conditions = $isConditionType1 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType1)
                                                                                                    <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                         <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($conditions as $condition)
                                                                                                              @if($condition->condition_type == 'question_label_condition')
                                                                                                              <div class="label-condition" id="label-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new="false">
                                                                                                                   <div class="inner-label">
                                                                                                                        <div class="row">
                                                                                                                             <div class="col-md-4">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_label-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_label-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question Label"
                                                                                                                                            :value="$condition->question_label ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       @php
                                                                                                                                            $questionOptions = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                 return [$q->getName() => $q->getName()];
                                                                                                                                            });
                                                                                                                                       @endphp

                                                                                                                                       <x-document-input-field
                                                                                                                                            type="question_select"
                                                                                                                                            class="js-select2 new_label_question_id"
                                                                                                                                            name="label_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="label_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question ID"
                                                                                                                                            :options="$questionOptions"
                                                                                                                                            :value="$condition->conditional_question_id ?? ''"
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                  
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_value-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_value-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Value"
                                                                                                                                            :value="$condition->conditional_question_value ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-2 add_rmv_icn29">
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon red_hover" onclick="removeLabel(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                                  </div>
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','')"><i class="fa-solid fa-add"></i></span>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>                        
                                                                                                              </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @else
                                                                                                    <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                         <div class="col-md-10 form-group qu_label_cls{{ $que->id ?? '' }} label_qu">
                                                                                                              <x-document-input-field
                                                                                                                   type="text"
                                                                                                                   class="form-control question_labl"
                                                                                                                   name="text_qu_label"
                                                                                                                   id="text_qu_label-{{ $que->id ?? '' }}"
                                                                                                                   label="Question Label"
                                                                                                                   :value="$que->questionData->question_label ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                         <div class="col-md-2 form-group prnt_add_cls qu_label_btn{{ $que->id ?? '' }}">
                                                                                                              <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','{{ $que->questionData->question_label ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                         </div>
                                                                                                         <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}"></div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <hr>
                                                                                               <div class="col-md-12 custom_box_{{ $que->id ?? '' }}">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="text"
                                                                                                              class="form-control text_box_placeholder"
                                                                                                              name="text_box_placeholder"
                                                                                                              id="text_placeholder-{{ $que->id ?? '' }}"
                                                                                                              label="Text Box Placeholder"
                                                                                                              :value="$que->questionData->text_box_placeholder ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">

                                                                                                              @php
                                                                                                                   $goToQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2 new_label_question_id"
                                                                                                                   name="text_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   id="text_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   label="Go to step"
                                                                                                                   :options="$goToQuestion"
                                                                                                                   :value="$que->questionData->next_question_id ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType2 = $isCondition && $que->is_condition == 1 && $que->condition_type == 2 || $que->condition_type == 3;
                                                                                                    $enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                                    $another_enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType2 && !empty($enable_conditions))
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label" for="">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>

                                                                                                         @php
                                                                                                              $goToStepConditions = collect($enable_conditions)->filter(function($c) {
                                                                                                                   return $c->condition_type == 'go_to_step_condition';
                                                                                                              })->values();
                                                                                                         @endphp
                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              @foreach($goToStepConditions as $condition)
                                                                                                              <div class="sec-condition" id="sec-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new=false>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="$condition->conditional_question_id ?? '' "
                                                                                                                                  />

                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];

                                                                                                                                       $selectedCondition = $checkValueMap[$condition->conditional_check ?? 0] ?? '';

                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $condition->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $condition->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="$selectedCondition"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $condition->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $condition->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="$condition->conditional_question_value ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 add_rmv_icn30">
                                                                                                                             @if(!$loop->first)
                                                                                                                             <div class="form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeCondition(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                             </div>
                                                                                                                             @endif

                                                                                                                             <div class="form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>

                                                                                                              </div>
                                                                                                              @endforeach
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoToPageQuestion"
                                                                                                                        :value="$que->questionData->conditional_go_to_step ?? '' "
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         @if(isset($enable_conditions) && count($enable_conditions) > 0)
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}">
                                                                                                              @foreach($enable_conditions as $condIndex => $condition)
                                                                                                                   @if($condition->condition_type == 'another_go_to_step_condition')
                                                                                                                        <div class="independent_cond_div" id="independent_cond_div_{{ $condition->id }}_{{ $condIndex }}" data-id="{{ $condition->id }}" data-is_new="false">
                                                                                                                             <hr>
                                                                                                                             <div class="text-end">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <span class="remove_icon red_hover" onclick="removeIndependentDiv(this,'{{ $condition->id }}','{{ $condIndex }}')" data-id="{{ $condition->id }}">
                                                                                                                                            <i class="fa fa-trash"></i>
                                                                                                                                       </span>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-12">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <label class="form-label">Add Conditions</label>
                                                                                                                                  </div>
                                                                                                                             </div>

                                                                                                                             <div class="another_page_condition" id="another_page_condition_{{ $condition->id }}_{{ $condIndex }}">
                                                                                                                             <?php $key = 1;?>
                                                                                                                             @foreach($condition->subconditions as $subcondition)
                                                                                                                                  <div class="another-condition" id="another-condition-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}" data-id="{{ $subcondition->id }}" data-is_new="false">
                                                                                                                                       <div class="row">
                                                                                                                                            <div class="col-md-3">
                                                                                                                                                 <div class="form-group">
                                                                                                                                                      @php
                                                                                                                                                           $anotherGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                                return [$q->getName() => $q->getName()];
                                                                                                                                                           });
                                                                                                                                                      @endphp

                                                                                                                                                      <x-document-input-field
                                                                                                                                                           type="question_select"
                                                                                                                                                           class="js-select2"
                                                                                                                                                           name="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                           id="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                           label="Question ID"
                                                                                                                                                           :options="$anotherGoToPageQuestion"
                                                                                                                                                           :value="$subcondition->conditional_question_id ?? '' "
                                                                                                                                                      />
                                                                                                                                                 </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-4">
                                                                                                                                                 <div class="form-group">
                                                                                                                                                      @php
                                                                                                                                                           $anotherconditionOptions = [
                                                                                                                                                                'is_equal_to'     => 'is equal to',
                                                                                                                                                                'is_greater_than' => 'is greater than',
                                                                                                                                                                'is_less_than'    => 'is less than',
                                                                                                                                                                'not_equal_to'    => 'not equal to',
                                                                                                                                                           ];


                                                                                                                                                           $checkValueMap = [
                                                                                                                                                                1 => 'is_equal_to',
                                                                                                                                                                2 => 'is_greater_than',
                                                                                                                                                                3 => 'is_less_than',
                                                                                                                                                                4 => 'not_equal_to',
                                                                                                                                                           ];

                                                                                                                                                           $selectedSubCondition = $checkValueMap[$subcondition->conditional_check ?? 0] ?? '';

                                                                                                                                                      @endphp

                                                                                                                                                      <x-document-input-field
                                                                                                                                                           type="condition_select"
                                                                                                                                                           class="js-select2"
                                                                                                                                                           name="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                           id="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                           label="Condition"
                                                                                                                                                           :options="$anotherconditionOptions"
                                                                                                                                                           :value="$selectedSubCondition"
                                                                                                                                                      />
                                                                                                                                                 </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-3">
                                                                                                                                                 <div class="form-group">
                                                                                                                                                      <x-document-input-field
                                                                                                                                                           type="text"
                                                                                                                                                           class="form-control"
                                                                                                                                                           name="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                           id="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                           label="Value"
                                                                                                                                                           :value="$subcondition->conditional_question_value ?? ''"
                                                                                                                                                      />
                                                                                                                                                 </div>
                                                                                                                                            </div>
                                                                                                                                            <div class="col-md-2 add_rmv_icn31">
                                                                                                                                                 @if(!$loop->first)
                                                                                                                                                 <div class="form-group prnt_add_cls">
                                                                                                                                                      <span class="remove_icon red_hover" onclick="removeAnotherCondition(this,'{{ $subcondition->id }}','{{ $condIndex }}','{{ $key ?? '' }}')" data-id="{{ $condition->id }}">
                                                                                                                                                      <i class="fa fa-trash"></i>
                                                                                                                                                      </span>
                                                                                                                                                 </div>
                                                                                                                                                 @endif
                                                                                                                                                 <div class="form-group prnt_add_cls">
                                                                                                                                                      <span class="remove_icon add_icon" onclick="anotherCondition(this,'{{ $condition->id }}', '{{ $condIndex }}')">
                                                                                                                                                      <i class="fa-solid fa-plus"></i>
                                                                                                                                                      </span>
                                                                                                                                                 </div>
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <br>
                                                                                                                                  </div>
                                                                                                                                  <?php $key++ ;?>
                                                                                                                             @endforeach
                                                                                                                             </div>
                                                                                                                             <div class="col-md-12">
                                                                                                                                  <div class="form-group">
                                                                                                                                       @php
                                                                                                                                            $anotherConditionalGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                 return [$q->getName() => $q->getName()];
                                                                                                                                            });
                                                                                                                                       @endphp

                                                                                                                                       <x-document-input-field
                                                                                                                                            type="select"
                                                                                                                                            class="js-select2"
                                                                                                                                            name="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex ?? '' }}"
                                                                                                                                            id="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex ?? '' }}"
                                                                                                                                            label="Conditional Go to Step"
                                                                                                                                            :options="$anotherConditionalGoToPageQuestion"
                                                                                                                                            :value="$condition->go_to_step ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   @endif
                                                                                                              @endforeach
                                                                                                         </div>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         @else
                                                                                                         <div class="grey_btn_div">
                                                                                                              <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                         </div>
                                                                                                         <div class="cond_div{{ $que->id ?? '' }}" style="display:none">
                                                                                                              <div class="col-md-12">
                                                                                                                   <div class="form-group">
                                                                                                                        <label class="form-label">Add Conditions</label>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                              <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                                   <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                        <div class="row">
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       @php
                                                                                                                                            $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                 return [$q->getName() => $q->getName()];
                                                                                                                                            });
                                                                                                                                       @endphp

                                                                                                                                       <x-document-input-field
                                                                                                                                            type="question_select"
                                                                                                                                            class="js-select2"
                                                                                                                                            name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                            id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                            label="Question ID"
                                                                                                                                            :options="$goToPageQuestion"
                                                                                                                                            :value="null"
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-4">
                                                                                                                                  <div class="form-group">
                                                                                                                                       @php
                                                                                                                                            $conditionOptions = [
                                                                                                                                                 'is_equal_to'     => 'is equal to',
                                                                                                                                                 'is_greater_than' => 'is greater than',
                                                                                                                                                 'is_less_than'    => 'is less than',
                                                                                                                                                 'not_equal_to'    => 'not equal to',
                                                                                                                                            ];


                                                                                                                                            $checkValueMap = [
                                                                                                                                                 1 => 'is_equal_to',
                                                                                                                                                 2 => 'is_greater_than',
                                                                                                                                                 3 => 'is_less_than',
                                                                                                                                                 4 => 'not_equal_to',
                                                                                                                                            ];
                                                                                                                                       @endphp


                                                                                                                                       <x-document-input-field
                                                                                                                                            type="condition_select"
                                                                                                                                            class="js-select2"
                                                                                                                                            name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                            id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                            label="Condition"
                                                                                                                                            :options="$conditionOptions"
                                                                                                                                            :value="null"
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                            id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                            label="Value"
                                                                                                                                            :value="null"
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <span class="remove_icon add_icon firstCondBtn" onclick="addCondition('{{ $que->id ?? '' }}')">
                                                                                                                             <i class="fa-solid fa-plus"></i>
                                                                                                                        </span>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                              <div class="col-md-12">
                                                                                                                   <div class="form-group">
                                                                                                                        @php
                                                                                                                             $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                  return [$q->getName() => $q->getName()];
                                                                                                                             });
                                                                                                                        @endphp

                                                                                                                        <x-document-input-field
                                                                                                                             type="select"
                                                                                                                             class="js-select2"
                                                                                                                             name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                             id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                             label="Conditional Go to Step"
                                                                                                                             :options="$conditionalgoToPageQuestion"
                                                                                                                             :value="null"
                                                                                                                        />
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                              <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                              <hr>
                                                                                                              <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                                   <div class="text-end">
                                                                                                                        <div class="form-group">
                                                                                                                             <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         @endif
                                                                                                    </div>
                                                                                               @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none;">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label" for="">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>

                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp


                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoTo = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoTo"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="textarea"
                                                                                                              class="form-control question_info_text"
                                                                                                              name="question_info_text"
                                                                                                              id="question_info_text-{{ $que->id ?? '' }}"
                                                                                                              label="Question Info Text"
                                                                                                              :value="$que->questionData->question_info_text ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <br>
                                                                                </div>
                                                                           </div>
                                                                           @elseif($question_type == 'dropdown')
                                                                           <div class="new_que_sec{{ $que->id ?? '' }}" id="for_copy_sec{{ $que->id ?? '' }}">
                                                                                <div class="append_dropdown" id="append_dropdown{{ $que->id ?? '' }}" data-id="{{ $que->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                                                     <div class="card card-bordered card-preview">
                                                                                          <div class="card-inner">
                                                                                               <div class="row add_step">
                                                                                                    <div class="col-md-6 div_hding">
                                                                                                         <div class="qu_count">
                                                                                                              <p>
                                                                                                                   <b>
                                                                                                                   QID : {{ $que->id ?? ''}}
                                                                                                                   </b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         |
                                                                                                         <div class="que_type_heading">
                                                                                                              <p class="drop_options"><b>Dropdown
                                                                                                                   <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         <div class="form-group drop_box_option" style="display:none;">
                                                                                                              <!-- <div class="text-end cut_btn">
                                                                                                                   <div class="form-group">
                                                                                                                        <span onclick="removeDropbox(this)">
                                                                                                                             <i class="fa fa-times"></i>
                                                                                                                        </span>
                                                                                                                   </div>
                                                                                                              </div> -->
                                                                                                              <div class="slct_optns">
                                                                                                                   <select class="form-select js-select2 " data-que-id="{{ $que->id ?? '' }}" data-change-from="{{ $que->type ?? '' }}" onchange="ChangeQuestionType(this)"  >
                                                                                                                        @foreach($types as $type)
                                                                                                                             <option value="{{ $type->slug ?? '' }}" {{ isset($question) && $que->type == $type->slug ? 'selected' : '' }}>{{ $type->name ?? '' }}</option>
                                                                                                                        @endforeach
                                                                                                                   </select>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-6 prnt_icon_cls">
                                                                                                         <div class="input_icons d-flex">
                                                                                                              <span class="remove_icon red_hover" onclick="removeFields(this)" data-id="{{ $que->id ?? '' }}" data-field="dropdown" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                              {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                                   <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $que->id ?? '' }}" data-field="dropdown"><i class="fa-solid fa-plus"></i></span>
                                                                                                                   <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                                        @foreach($types as $type)
                                                                                                                             <a onclick="addQuestionfields('{{ $type->slug ?? '' }}','{{ $que->id ?? '' }}','third',this)">{{ $type->name ?? '' }}</a>
                                                                                                                        @endforeach
                                                                                                                   </div>
                                                                                                              </div> --}}
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>

                                                                                               <hr>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType1 = $isCondition && $que->is_condition == 1 && $que->condition_type == 1 || $que->condition_type == 3;
                                                                                                    $conditions = $isConditionType1 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType1)
                                                                                               <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                    <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}">
                                                                                                    @foreach($conditions as $condition)
                                                                                                         @if($condition->condition_type == 'question_label_condition')
                                                                                                         <div class="label-condition" id="label-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new="false">
                                                                                                              <div class="inner-label">
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="condition_question_label-{{ $condition->id ?? '' }}[]"
                                                                                                                                       id="condition_question_label-{{ $condition->id ?? '' }}"
                                                                                                                                       label="Question Label"
                                                                                                                                       :value="$condition->question_label ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $questionOptions = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2 new_label_question_id"
                                                                                                                                       name="label_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                       id="label_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$questionOptions"
                                                                                                                                       :value="$condition->conditional_question_id ?? ''"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="condition_question_value-{{ $condition->id ?? '' }}[]"
                                                                                                                                       id="condition_question_value-{{ $condition->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="$condition->conditional_question_value ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 add_rmv_icn32">
                                                                                                                             <div class="form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeLabel(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                             </div>
                                                                                                                             <div class="form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','')"><i class="fa-solid fa-add"></i></span>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>                         
                                                                                                         </div>
                                                                                                         @endif
                                                                                                    @endforeach
                                                                                                    </div>
                                                                                               </div>
                                                                                               @else
                                                                                                    <div class="row">
                                                                                                         <div class="col-md-10 form-group qu_label_cls{{ $que->id ?? '' }} label_qu">
                                                                                                              <x-document-input-field
                                                                                                                   type="text"
                                                                                                                   class="form-control question_labl"
                                                                                                                   name="text_qu_label"
                                                                                                                   id="text_qu_label-{{ $que->id ?? '' }}"
                                                                                                                   label="Question Label"
                                                                                                                   :value="$que->questionData->question_label ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                         <div class="col-md-2 form-group prnt_add_cls qu_label_btn{{ $que->id ?? '' }}">
                                                                                                              <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','{{ $que->questionData->question_label ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                         </div>
                                                                                                         <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}"></div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <hr>
                                                                                               <div class="col-md-12 custom_box_{{ $que->id ?? '' }}">
                                                                                                    <div class="form-group">
                                                                                                         <label class="form-label" for="">Add Dropdown Option</label>
                                                                                                    </div>
                                                                                                    <div class="append_options" id="append_options{{ $que->id ?? '' }}">
                                                                                                         @if(isset($que->options) && $que->options != null)
                                                                                                         <?php $options = json_decode($que->options); ?>
                                                                                                         @foreach($options as $key => $option)
                                                                                                         <div class="dropdown-option" id="dropdown-option{{ $option->id ?? '' }}" data-id="{{ $option->id ?? '' }}" data-is_new=false>
                                                                                                              <div class="inner_dropdown">
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="dropdown_option_label-{{ $option->id ?? '' }}[]"
                                                                                                                                       id="dropdown_option_label-{{ $option->id ?? '' }}"
                                                                                                                                       label="Label"
                                                                                                                                       :value="$option->option_label ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="dropdown_option_value-{{ $option->id ?? '' }}[]"
                                                                                                                                       id="dropdown_option_value-{{ $option->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="$option->option_value ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $dropdownOptions = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="select"
                                                                                                                                       class="js-select2 new_label_question_id"
                                                                                                                                       name="dropdown_go_to_step-{{ $option->id ?? '' }}[]"
                                                                                                                                       id="dropdown_go_to_step-{{ $option->id ?? '' }}"
                                                                                                                                       label="Go to Step"
                                                                                                                                       :options="$dropdownOptions"
                                                                                                                                       :value="$option->next_question_id ?? ''"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 add_rmv_icn33">
                                                                                                                        @if(!$loop->first)
                                                                                                                             <div class="form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeOptions(this)" data-id="{{ $option->id ?? '' }}" data-field="dropdown">
                                                                                                                                       <i class="fa fa-trash"></i>
                                                                                                                                  </span>
                                                                                                                             </div>
                                                                                                                        @endif
                                                                                                                        
                                                                                                                        @if($loop->last)
                                                                                                                             <div class="form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon add_icon" onclick="addOptions('dropdown','{{ $que->id ?? '' }}')">
                                                                                                                                       <i class="fa-solid fa-add"></i>
                                                                                                                                  </span>
                                                                                                                             </div>
                                                                                                                        @else
                                                                                                                             <div class="form-group prnt_add_cls" style="display:none;">
                                                                                                                                  <span class="remove_icon add_icon" onclick="addOptions('dropdown','{{ $que->id ?? '' }}')">
                                                                                                                                       <i class="fa-solid fa-add"></i>
                                                                                                                                  </span>
                                                                                                                             </div>    
                                                                                                                        @endif
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                              <br>
                                                                                                         </div>
                                                                                                         @endforeach
                                                                                                         @else
                                                                                                         <div class="text-end firstOptBtn">
                                                                                                              <div class="form-group">
                                                                                                                   <span class="remove_icon add_icon" onclick="addOptions('dropdown','{{ $que->id ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    @endif
                                                                                               </div>
                                                                                               </div>
                                                                                               <hr>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType2 = $isCondition && $que->is_condition == 1 && $que->condition_type == 2 || $que->condition_type == 3;
                                                                                                    $enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                                    $another_enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType2 && !empty($enable_conditions))
                                                                                               <div class="cond_div{{ $que->id ?? '' }}">
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              <label class="form-label" for="">Add Conditions</label>
                                                                                                         </div>
                                                                                                    </div>

                                                                                                    @php
                                                                                                         $goToStepConditions = collect($enable_conditions)->filter(function($c) {
                                                                                                              return $c->condition_type == 'go_to_step_condition';
                                                                                                         })->values();
                                                                                                    @endphp
                                                                                                    <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($goToStepConditions as $condition)
                                                                                                         <div class="sec-condition" id="sec-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new=false>
                                                                                                              <div class="row">
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                       return [$q->getName() => $q->getName()];
                                                                                                                                  });
                                                                                                                             @endphp

                                                                                                                             <x-document-input-field
                                                                                                                                  type="question_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Question ID"
                                                                                                                                  :options="$goToPageQuestion"
                                                                                                                                  :value="$condition->conditional_question_id ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-4">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $conditionOptions = [
                                                                                                                                       'is_equal_to'     => 'is equal to',
                                                                                                                                       'is_greater_than' => 'is greater than',
                                                                                                                                       'is_less_than'    => 'is less than',
                                                                                                                                       'not_equal_to'    => 'not equal to',
                                                                                                                                  ];


                                                                                                                                  $checkValueMap = [
                                                                                                                                       1 => 'is_equal_to',
                                                                                                                                       2 => 'is_greater_than',
                                                                                                                                       3 => 'is_less_than',
                                                                                                                                       4 => 'not_equal_to',
                                                                                                                                  ];

                                                                                                                                  $selectedCondition = $checkValueMap[$condition->conditional_check ?? 0] ?? '';
                                                                                                                             @endphp

                                                                                                                             <x-document-input-field
                                                                                                                                  type="condition_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_conditions-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_conditions-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Condition"
                                                                                                                                  :options="$conditionOptions"
                                                                                                                                  :value="$selectedCondition"
                                                                                                                             />

                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             <x-document-input-field
                                                                                                                                  type="text"
                                                                                                                                  class="form-control"
                                                                                                                                  name="page_Setting_qu_val-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_val-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Value"
                                                                                                                                  :value="$condition->conditional_question_value ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-2 add_rmv_icn1">
                                                                                                                        @if(!$loop->first)
                                                                                                                             <div class="form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeCondition(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                             </div>
                                                                                                                        @endif

                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>

                                                                                                         </div>
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              @php
                                                                                                                   $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2"
                                                                                                                   name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   label="Conditional Go to Step"
                                                                                                                   :options="$conditionalgoToPageQuestion"
                                                                                                                   :value="$que->questionData->conditional_go_to_step ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @if(isset($enable_conditions) && count($enable_conditions) > 0)
                                                                                                    <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}">
                                                                                                         @foreach($enable_conditions as $condIndex => $condition)
                                                                                                              @if($condition->condition_type == 'another_go_to_step_condition')
                                                                                                                   <div class="independent_cond_div" id="independent_cond_div_{{ $condition->id }}_{{ $condIndex }}" data-id="{{ $condition->id }}" data-is_new="false">
                                                                                                                        <hr>
                                                                                                                        <div class="text-end">
                                                                                                                             <div class="form-group">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeIndependentDiv(this,'{{ $condition->id }}','{{ $condIndex }}')" data-id="{{ $condition->id }}">
                                                                                                                                       <i class="fa fa-trash"></i>
                                                                                                                                  </span>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  <label class="form-label">Add Conditions</label>
                                                                                                                             </div>
                                                                                                                        </div>

                                                                                                                        <div class="another_page_condition" id="another_page_condition_{{ $condition->id }}_{{ $condIndex }}">
                                                                                                                        <?php $key = 1;?>
                                                                                                                        @foreach($condition->subconditions as $subcondition)
                                                                                                                             <div class="another-condition" id="another-condition-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}" data-id="{{ $subcondition->id }}" data-is_new="false">
                                                                                                                                  <div class="row">
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                           return [$q->getName() => $q->getName()];
                                                                                                                                                      });
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="question_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Question ID"
                                                                                                                                                      :options="$anotherGoToPageQuestion"
                                                                                                                                                      :value="$subcondition->conditional_question_id ?? '' "
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-4">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherconditionOptions = [
                                                                                                                                                           'is_equal_to'     => 'is equal to',
                                                                                                                                                           'is_greater_than' => 'is greater than',
                                                                                                                                                           'is_less_than'    => 'is less than',
                                                                                                                                                           'not_equal_to'    => 'not equal to',
                                                                                                                                                      ];

                                                                                                                                                      $checkValueMap = [
                                                                                                                                                           1 => 'is_equal_to',
                                                                                                                                                           2 => 'is_greater_than',
                                                                                                                                                           3 => 'is_less_than',
                                                                                                                                                           4 => 'not_equal_to',
                                                                                                                                                      ];

                                                                                                                                                      $selectedSubCondition = $checkValueMap[$subcondition->conditional_check ?? 0] ?? '';
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="condition_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Condition"
                                                                                                                                                      :options="$anotherconditionOptions"
                                                                                                                                                      :value="$selectedSubCondition"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="text"
                                                                                                                                                      class="form-control"
                                                                                                                                                      name="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Value"
                                                                                                                                                      :value="$subcondition->conditional_question_value ?? ''"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-2 add_rmv_icn2">
                                                                                                                                            @if(!$loop->first)
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon red_hover" onclick="removeAnotherCondition(this,'{{ $subcondition->id }}','{{ $condIndex }}','{{ $key ?? '' }}')" data-id="{{ $condition->id }}">
                                                                                                                                                 <i class="fa fa-trash"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                            @endif
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon add_icon" onclick="anotherCondition(this,'{{ $condition->id }}', '{{ $condIndex }}')">
                                                                                                                                                 <i class="fa-solid fa-plus"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                  </div>
                                                                                                                                  <br>
                                                                                                                             </div>
                                                                                                                             <?php $key++ ;?>
                                                                                                                        @endforeach
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $anotherConditionalGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex }}"
                                                                                                                                       id="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex ?? '' }}"
                                                                                                                                       label="Conditional Go to Step"
                                                                                                                                       :options="$anotherConditionalGoToPageQuestion"
                                                                                                                                       :value="$condition->go_to_step ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp


                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />


                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <span class="remove_icon add_icon firstCondBtn" onclick="addCondition('{{ $que->id ?? '' }}')">
                                                                                                                        <i class="fa-solid fa-plus"></i>
                                                                                                                   </span>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoToPageQuestion"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @endif
                                                                                               </div>
                                                                                               @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none;">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label" for="">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];

                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp


                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $questionquestion->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoTo = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoTo"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="textarea"
                                                                                                              class="form-control question_info_text"
                                                                                                              name="question_info_text"
                                                                                                              id="question_info_text-{{ $que->id ?? '' }}"
                                                                                                              label="Question Info Text"
                                                                                                              :value="$que->questionData->question_info_text ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <br>
                                                                                </div>
                                                                           </div>
                                                                           @elseif($question_type == 'radio-button')
                                                                           <div class="new_que_sec{{ $que->id ?? '' }}" id="for_copy_sec{{ $que->id ?? '' }}">
                                                                                <div class="append_radio" id="append_radio{{ $que->id ?? '' }}" data-id="{{ $que->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                                                     <div class="card card-bordered card-preview">
                                                                                          <div class="card-inner">
                                                                                               <div class="row add_step">
                                                                                                    <div class="col-md-6 div_hding">
                                                                                                         <div class="qu_count">
                                                                                                              <p>
                                                                                                                   <b>
                                                                                                                   QID : {{ $que->id ?? ''}}
                                                                                                                   </b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         |
                                                                                                         <div class="que_type_heading">
                                                                                                              <p class="drop_options"><b>Radio button
                                                                                                                   <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         <div class="form-group drop_box_option" style="display:none;">
                                                                                                              <!-- <div class="text-end cut_btn">
                                                                                                                   <div class="form-group">
                                                                                                                        <span onclick="removeDropbox(this)">
                                                                                                                             <i class="fa fa-times"></i>
                                                                                                                        </span>
                                                                                                                   </div>
                                                                                                              </div> -->
                                                                                                              <div class="slct_optns">
                                                                                                                   <select class="form-select js-select2 " data-que-id="{{ $que->id ?? '' }}" data-change-from="{{ $que->type ?? '' }}" onchange="ChangeQuestionType(this)"  >
                                                                                                                        @foreach($types as $type)
                                                                                                                             <option value="{{ $type->slug ?? '' }}" {{ isset($question) && $que->type == $type->slug ? 'selected' : '' }}>{{ $type->name ?? '' }}</option>
                                                                                                                        @endforeach
                                                                                                                   </select>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-6 prnt_icon_cls">
                                                                                                         <div class="input_icons d-flex">
                                                                                                              <span class="remove_icon red_hover" onclick="removeFields(this)" data-id="{{ $que->id ?? '' }}" data-field="radio" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                              {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                                   <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $que->id ?? '' }}" data-field="radio"><i class="fa-solid fa-plus"></i></span>
                                                                                                                   <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                                        @foreach($types as $type)
                                                                                                                             <a onclick="addQuestionfields('{{ $type->slug ?? '' }}','{{ $que->id ?? '' }}','third',this)">{{ $type->name ?? '' }}</a>
                                                                                                                        @endforeach
                                                                                                                   </div>
                                                                                                              </div> --}}
                                                                                                              
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>

                                                                                          <hr>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType1 = $isCondition && $que->is_condition == 1 && $que->condition_type == 1 || $que->condition_type == 3;
                                                                                                    $conditions = $isConditionType1 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType1 && !empty($conditions))
                                                                                               <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                    <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($conditions as $condition)
                                                                                                              @if($condition->condition_type == 'question_label_condition')
                                                                                                              <div class="label-condition" id="label-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new="false">
                                                                                                                   <div class="inner-label">    
                                                                                                                        <div class="row">
                                                                                                                             <div class="col-md-4">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_label-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_label-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question Label"
                                                                                                                                            :value="$condition->question_label ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       @php
                                                                                                                                            $questionOptions = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                 return [$q->getName() => $q->getName()];
                                                                                                                                            });
                                                                                                                                       @endphp

                                                                                                                                       <x-document-input-field
                                                                                                                                            type="question_select"
                                                                                                                                            class="js-select2 new_label_question_id"
                                                                                                                                            name="label_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="label_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question ID"
                                                                                                                                            :options="$questionOptions"
                                                                                                                                            :value="$condition->conditional_question_id ?? ''"
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_value-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_value-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Value"
                                                                                                                                            :value="$condition->conditional_question_value ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-2 add_rmv_icn3">
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon red_hover" onclick="removeLabel(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                                  </div>
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','')"><i class="fa-solid fa-add"></i></span>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div> 
                                                                                                              </div>                            
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                               </div>
                                                                                               @else
                                                                                               <div class="row">
                                                                                                    <div class="col-md-10 form-group qu_label_cls{{ $que->id ?? '' }} label_qu">
                                                                                                         <x-document-input-field
                                                                                                              type="text"
                                                                                                              class="form-control question_labl"
                                                                                                              name="text_qu_label"
                                                                                                              id="text_qu_label-{{ $que->id ?? '' }}"
                                                                                                              label="Question Label"
                                                                                                              :value="$que->questionData->question_label ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                                    <div class="col-md-2 form-group prnt_add_cls qu_label_btn{{ $que->id ?? '' }}">
                                                                                                         <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','{{ $que->questionData->question_label ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                    </div>
                                                                                                    <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}"></div>
                                                                                               </div>
                                                                                               @endif
                                                                                               <hr>
                                                                                               <div class="col-md-12 custom_box_{{ $que->id ?? '' }}">
                                                                                                    <div class="form-group">
                                                                                                         <label class="form-label" for="">Add Radio Option</label>
                                                                                                    </div>
                                                                                                    <div class="append_options" id="append_options{{ $que->id ?? '' }}">
                                                                                                         @if(isset($que->options) && $que->options != null)
                                                                                                         <?php $options = json_decode($que->options); ?>
                                                                                                         @foreach($options as $key => $option)
                                                                                                         <div class="radio-option" id="radio-option{{ $option->id ?? '' }}" data-id="{{ $option->id ?? '' }}" data-is_new=false>
                                                                                                              <div class="inner_radio">
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="radio_option_label-{{ $option->id ?? '' }}[]"
                                                                                                                                       id="radio_option_label-{{ $option->id ?? '' }}"
                                                                                                                                       label="Label"
                                                                                                                                       :value="$option->option_label ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="radio_option_value-{{ $option->id ?? '' }}[]"
                                                                                                                                       id="radio_option_value-{{ $option->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="$option->option_value ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $radioOptions = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="select"
                                                                                                                                       class="js-select2 new_label_question_id"
                                                                                                                                       name="radio-button_go_to_step-{{ $option->id ?? '' }}[]"
                                                                                                                                       id="radio-button_go_to_step-{{ $option->id ?? '' }}"
                                                                                                                                       label="Go to Step"
                                                                                                                                       :options="$radioOptions"
                                                                                                                                       :value="$option->next_question_id ?? ''"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 add_rmv_icn4">
                                                                                                                             @if(!$loop->first)
                                                                                                                             <div class="form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeOptions(this)" data-id="{{ $option->id ?? '' }}" data-field="radio-button">
                                                                                                                                       <i class="fa fa-trash"></i>
                                                                                                                                  </span>
                                                                                                                             </div>
                                                                                                                             @endif
                                                                                                                             
                                                                                                                             @if($loop->last)
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon add_icon" onclick="addOptions('radio-button','{{ $que->id ?? '' }}')">
                                                                                                                                            <i class="fa-solid fa-add"></i>
                                                                                                                                       </span>
                                                                                                                                  </div>
                                                                                                                             @else
                                                                                                                                  <div class="form-group prnt_add_cls" style="display:none;">
                                                                                                                                       <span class="remove_icon add_icon" onclick="addOptions('radio-button','{{ $que->id ?? '' }}')">
                                                                                                                                            <i class="fa-solid fa-add"></i>
                                                                                                                                       </span>
                                                                                                                                  </div>
                                                                                                                             @endif
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                              <br>
                                                                                                         </div>
                                                                                                         @endforeach
                                                                                                         @else
                                                                                                         <div class="text-end firstOptBtn">
                                                                                                              <div class="form-group">
                                                                                                                   <span class="remove_icon add_icon" onclick="addOptions('radio-button','{{ $que->id ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         @endif
                                                                                                    </div>
                                                                                               </div>
                                                                                               <hr>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType2 = $isCondition && $que->is_condition == 1 && $que->condition_type == 2 || $que->condition_type == 3;
                                                                                                    $enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                                    $another_enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType2 && !empty($enable_conditions))
                                                                                               <div class="cond_div{{ $que->id ?? '' }}">
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              <label class="form-label" for="">Add Conditions</label>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @php
                                                                                                         $goToStepConditions = collect($enable_conditions)->filter(function($c) {
                                                                                                              return $c->condition_type == 'go_to_step_condition';
                                                                                                         })->values();
                                                                                                    @endphp
                                                                                                    <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($goToStepConditions as $condition)
                                                                                                         <div class="sec-condition" id="sec-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new=false>
                                                                                                              <div class="row">
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                       return [$q->getName() => $q->getName()];
                                                                                                                                  });
                                                                                                                             @endphp

                                                                                                                             <x-document-input-field
                                                                                                                                  type="question_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Question ID"
                                                                                                                                  :options="$goToPageQuestion"
                                                                                                                                  :value="$condition->conditional_question_id ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-4">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $conditionOptions = [
                                                                                                                                       'is_equal_to'     => 'is equal to',
                                                                                                                                       'is_greater_than' => 'is greater than',
                                                                                                                                       'is_less_than'    => 'is less than',
                                                                                                                                       'not_equal_to'    => 'not equal to',
                                                                                                                                  ];


                                                                                                                                  $checkValueMap = [
                                                                                                                                       1 => 'is_equal_to',
                                                                                                                                       2 => 'is_greater_than',
                                                                                                                                       3 => 'is_less_than',
                                                                                                                                       4 => 'not_equal_to',
                                                                                                                                  ];

                                                                                                                                  $selectedCondition = $checkValueMap[$condition->conditional_check ?? 0] ?? '';
                                                                                                                             @endphp

                                                                                                                             <x-document-input-field
                                                                                                                                  type="condition_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_conditions-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_conditions-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Condition"
                                                                                                                                  :options="$conditionOptions"
                                                                                                                                  :value="$selectedCondition"
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             <x-document-input-field
                                                                                                                                  type="text"
                                                                                                                                  class="form-control"
                                                                                                                                  name="page_Setting_qu_val-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_val-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Value"
                                                                                                                                  :value="$condition->conditional_question_value ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-2 add_rmv_icn5">
                                                                                                                        @if(!$loop->first)
                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon red_hover" onclick="removeCondition(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                        </div>
                                                                                                                        @endif
                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>

                                                                                                         </div>
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              @php
                                                                                                                   $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2"
                                                                                                                   name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   label="Conditional Go to Step"
                                                                                                                   :options="$conditionalgoToPageQuestion"
                                                                                                                   :value="$que->questionData->conditional_go_to_step ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @if(isset($enable_conditions) && count($enable_conditions) > 0)
                                                                                                    <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}">
                                                                                                         @foreach($enable_conditions as $condIndex => $condition)
                                                                                                              @if($condition->condition_type == 'another_go_to_step_condition')
                                                                                                                   <div class="independent_cond_div" id="independent_cond_div_{{ $condition->id }}_{{ $condIndex }}" data-id="{{ $condition->id }}" data-is_new="false">
                                                                                                                        <hr>
                                                                                                                        <div class="text-end">
                                                                                                                             <div class="form-group">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeIndependentDiv(this,'{{ $condition->id }}','{{ $condIndex }}')" data-id="{{ $condition->id }}">
                                                                                                                                       <i class="fa fa-trash"></i>
                                                                                                                                  </span>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  <label class="form-label">Add Conditions</label>
                                                                                                                             </div>
                                                                                                                        </div>

                                                                                                                        <div class="another_page_condition" id="another_page_condition_{{ $condition->id }}_{{ $condIndex }}">
                                                                                                                        <?php $key = 1;?>
                                                                                                                        @foreach($condition->subconditions as $subcondition)
                                                                                                                             <div class="another-condition" id="another-condition-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}" data-id="{{ $subcondition->id }}" data-is_new="false">
                                                                                                                                  <div class="row">
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                           return [$q->getName() => $q->getName()];
                                                                                                                                                      });
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="question_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Question ID"
                                                                                                                                                      :options="$anotherGoToPageQuestion"
                                                                                                                                                      :value="$subcondition->conditional_question_id ?? '' "
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-4">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherconditionOptions = [
                                                                                                                                                           'is_equal_to'     => 'is equal to',
                                                                                                                                                           'is_greater_than' => 'is greater than',
                                                                                                                                                           'is_less_than'    => 'is less than',
                                                                                                                                                           'not_equal_to'    => 'not equal to',
                                                                                                                                                      ];


                                                                                                                                                      $checkValueMap = [
                                                                                                                                                           1 => 'is_equal_to',
                                                                                                                                                           2 => 'is_greater_than',
                                                                                                                                                           3 => 'is_less_than',
                                                                                                                                                           4 => 'not_equal_to',
                                                                                                                                                      ];

                                                                                                                                                      $selectedSubCondition = $checkValueMap[$subcondition->conditional_check ?? 0] ?? '';
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="condition_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Condition"
                                                                                                                                                      :options="$anotherconditionOptions"
                                                                                                                                                      :value="$selectedSubCondition"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="text"
                                                                                                                                                      class="form-control"
                                                                                                                                                      name="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Value"
                                                                                                                                                      :value="$subcondition->conditional_question_value ?? ''"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-2 add_rmv_icn6">
                                                                                                                                            @if(!$loop->first)
                                                                                                                                                 <div class="form-group prnt_add_cls">
                                                                                                                                                      <span class="remove_icon red_hover" onclick="removeAnotherCondition(this,'{{ $subcondition->id }}','{{ $condIndex }}','{{ $key ?? '' }}')" data-id="{{ $condition->id }}">
                                                                                                                                                      <i class="fa fa-trash"></i>
                                                                                                                                                      </span>
                                                                                                                                                 </div>
                                                                                                                                            @endif
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon add_icon" onclick="anotherCondition(this,'{{ $condition->id }}', '{{ $condIndex }}')">
                                                                                                                                                 <i class="fa-solid fa-plus"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                  </div>
                                                                                                                                  <br>
                                                                                                                             </div>
                                                                                                                             <?php $key++ ;?>
                                                                                                                        @endforeach
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $anotherConditionalGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex }}"
                                                                                                                                       id="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex ?? '' }}"
                                                                                                                                       label="Conditional Go to Step"
                                                                                                                                       :options="$anotherConditionalGoToPageQuestion"
                                                                                                                                       :value="$condition->go_to_step ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp


                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <span class="remove_icon add_icon firstCondBtn" onclick="addCondition('{{ $que->id ?? '' }}')">
                                                                                                                        <i class="fa-solid fa-plus"></i>
                                                                                                                   </span>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoToPageQuestion"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @endif
                                                                                               </div>
                                                                                               @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none;">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label" for="">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>

                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp


                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoTo = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoTo"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="textarea"
                                                                                                              class="form-control question_info_text"
                                                                                                              name="question_info_text"
                                                                                                              id="question_info_text-{{ $que->id ?? '' }}"
                                                                                                              label="Question Info Text"
                                                                                                              :value="$que->questionData->question_info_text ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <br>
                                                                                </div>
                                                                           </div>
                                                                           @elseif($question_type == 'date-field')
                                                                           <div class="new_que_sec{{ $que->id ?? '' }}" id="for_copy_sec{{ $que->id ?? '' }}">
                                                                                <div class="append_dateField" id="append_dateField{{ $que->id ?? '' }}" data-id="{{ $que->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                                                     <div class="card card-bordered card-preview">
                                                                                          <div class="card-inner">
                                                                                               <div class="row add_step">
                                                                                                    <div class="col-md-6 div_hding">
                                                                                                         <div class="qu_count">
                                                                                                              <p>
                                                                                                                   <b>
                                                                                                                   QID: {{ $que->id ?? ''}}
                                                                                                                   </b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         |
                                                                                                         <div class="que_type_heading">
                                                                                                              <p class="drop_options"><b>Date field
                                                                                                                   <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         <div class="form-group drop_box_option" style="display:none;">
                                                                                                              <!-- <div class="text-end cut_btn">
                                                                                                                   <div class="form-group">
                                                                                                                        <span onclick="removeDropbox(this)">
                                                                                                                             <i class="fa fa-times"></i>
                                                                                                                        </span>
                                                                                                                   </div>
                                                                                                              </div> -->
                                                                                                              <div class="slct_optns">
                                                                                                                   <select class="form-select js-select2 " data-que-id="{{ $que->id ?? '' }}" data-change-from="{{ $que->type ?? '' }}" onchange="ChangeQuestionType(this)"  >
                                                                                                                        @foreach($types as $type)
                                                                                                                             <option value="{{ $type->slug ?? '' }}" {{ isset($question) && $que->type == $type->slug ? 'selected' : '' }}>{{ $type->name ?? '' }}</option>
                                                                                                                        @endforeach
                                                                                                                   </select>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-6 prnt_icon_cls">
                                                                                                         <div class="input_icons d-flex">
                                                                                                              <span class="remove_icon red_hover" onclick="removeFields(this)" data-id="{{ $que->id ?? '' }}" data-field="date-field" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                              {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                                   <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $que->id ?? '' }}" data-field="date-field"><i class="fa-solid fa-plus"></i></span>
                                                                                                                   <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                                        @foreach($types as $type)
                                                                                                                             <a onclick="addQuestionfields('{{ $type->slug ?? '' }}','{{ $que->id ?? '' }}','third',this)">{{ $type->name ?? '' }}</a>
                                                                                                                        @endforeach
                                                                                                                   </div>
                                                                                                              </div> --}}
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>
                                                                                               <hr>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType1 = $isCondition && $que->is_condition == 1 && $que->condition_type == 1 || $que->condition_type == 3;
                                                                                                    $conditions = $isConditionType1 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType1 && !empty($conditions))
                                                                                               <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                    <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($conditions as $condition)
                                                                                                              @if($condition->condition_type == 'question_label_condition')
                                                                                                              <div class="label-condition" id="label-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new="false">
                                                                                                                   <div class="inner-label">    
                                                                                                                        <div class="row">
                                                                                                                             <div class="col-md-4">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_label-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_label-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question Label"
                                                                                                                                            :value="$condition->question_label ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       @php
                                                                                                                                            $questionOptions = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                 return [$q->getName() => $q->getName()];
                                                                                                                                            });
                                                                                                                                       @endphp

                                                                                                                                       <x-document-input-field
                                                                                                                                            type="question_select"
                                                                                                                                            class="js-select2 new_label_question_id"
                                                                                                                                            name="label_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="label_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question ID"
                                                                                                                                            :options="$questionOptions"
                                                                                                                                            :value="$condition->conditional_question_id ?? ''"
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_value-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_value-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Value"
                                                                                                                                            :value="$condition->conditional_question_value ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-2 add_rmv_icn7">
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon red_hover" onclick="removeLabel(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                                  </div>
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','')"><i class="fa-solid fa-add"></i></span>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>                         
                                                                                                              </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                               </div>
                                                                                               @else
                                                                                                    <div class="row ">
                                                                                                         <div class="col-md-10 form-group qu_label_cls{{ $que->id ?? '' }} label_qu">
                                                                                                              <x-document-input-field
                                                                                                                   type="text"
                                                                                                                   class="form-control question_labl"
                                                                                                                   name="text_qu_label"
                                                                                                                   id="text_qu_label-{{ $que->id ?? '' }}"
                                                                                                                   label="Question Label"
                                                                                                                   :value="$que->questionData->question_label ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                         <div class="col-md-2 form-group prnt_add_cls qu_label_btn{{ $que->id ?? '' }}">
                                                                                                              <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','{{ $que->questionData->question_label ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                         </div>
                                                                                                         <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}"></div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <div class="col-md-12 custom_box_{{ $que->id ?? '' }}">
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              @php
                                                                                                                   $dateGoToStep = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2"
                                                                                                                   name="date_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   id="date_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   label="Go to step"
                                                                                                                   :options="$dateGoToStep"
                                                                                                                   :value="$que->questionData->next_question_id ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                    </div>

                                                                                               </div>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType2 = $isCondition && $que->is_condition == 1 && $que->condition_type == 2 || $que->condition_type == 3;
                                                                                                    $enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                                    $another_enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType2 && !empty($enable_conditions))
                                                                                               <div class="cond_div{{ $que->id ?? '' }}">
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              <label class="form-label" for="">Add Conditions</label>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @php
                                                                                                         $goToStepConditions = collect($enable_conditions)->filter(function($c) {
                                                                                                              return $c->condition_type == 'go_to_step_condition';
                                                                                                         })->values();
                                                                                                    @endphp
                                                                                                    <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($goToStepConditions as $condition)
                                                                                                         <div class="sec-condition" id="sec-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new=false>
                                                                                                              <div class="row">
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                       return [$q->getName() => $q->getName()];
                                                                                                                                  });
                                                                                                                             @endphp

                                                                                                                             <x-document-input-field
                                                                                                                                  type="question_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Question ID"
                                                                                                                                  :options="$goToPageQuestion"
                                                                                                                                  :value="$condition->conditional_question_id ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-4">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $conditionOptions = [
                                                                                                                                       'is_equal_to'     => 'is equal to',
                                                                                                                                       'is_greater_than' => 'is greater than',
                                                                                                                                       'is_less_than'    => 'is less than',
                                                                                                                                       'not_equal_to'    => 'not equal to',
                                                                                                                                  ];


                                                                                                                                  $checkValueMap = [
                                                                                                                                       1 => 'is_equal_to',
                                                                                                                                       2 => 'is_greater_than',
                                                                                                                                       3 => 'is_less_than',
                                                                                                                                       4 => 'not_equal_to',
                                                                                                                                  ];

                                                                                                                                  $selectedCondition = $checkValueMap[$condition->conditional_check ?? 0] ?? '';
                                                                                                                             @endphp


                                                                                                                             <x-document-input-field
                                                                                                                                  type="condition_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_conditions-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_conditions-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Condition"
                                                                                                                                  :options="$conditionOptions"
                                                                                                                                  :value="$selectedCondition"
                                                                                                                             />

                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             <x-document-input-field
                                                                                                                                  type="text"
                                                                                                                                  class="form-control"
                                                                                                                                  name="page_Setting_qu_val-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_val-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Value"
                                                                                                                                  :value="$condition->conditional_question_value ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>

                                                                                                                   <div class="col-md-2 add_rmv_icn8">
                                                                                                                        @if(!$loop->first)
                                                                                                                             <div class="form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeCondition(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                             </div>
                                                                                                                        @endif

                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>

                                                                                                         </div>
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              @php
                                                                                                                   $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2"
                                                                                                                   name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   label="Conditional Go to Step"
                                                                                                                   :options="$conditionalgoToPageQuestion"
                                                                                                                   :value="$que->questionData->conditional_go_to_step ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @if(isset($enable_conditions) && count($enable_conditions) > 0)
                                                                                                    <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}">
                                                                                                         @foreach($enable_conditions as $condIndex => $condition)
                                                                                                              @if($condition->condition_type == 'another_go_to_step_condition')
                                                                                                                   <div class="independent_cond_div" id="independent_cond_div_{{ $condition->id }}_{{ $condIndex }}" data-id="{{ $condition->id }}" data-is_new="false">
                                                                                                                        <hr>
                                                                                                                        <div class="text-end">
                                                                                                                             <div class="form-group">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeIndependentDiv(this,'{{ $condition->id }}','{{ $condIndex }}')" data-id="{{ $condition->id }}">
                                                                                                                                       <i class="fa fa-trash"></i>
                                                                                                                                  </span>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  <label class="form-label">Add Conditions</label>
                                                                                                                             </div>
                                                                                                                        </div>

                                                                                                                        <div class="another_page_condition" id="another_page_condition_{{ $condition->id }}_{{ $condIndex }}">
                                                                                                                        <?php $key = 1;?>
                                                                                                                        @foreach($condition->subconditions as $subcondition)
                                                                                                                             <div class="another-condition" id="another-condition-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}" data-id="{{ $subcondition->id }}" data-is_new="false">
                                                                                                                                  <div class="row">
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                           return [$q->getName() => $q->getName()];
                                                                                                                                                      });
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="question_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Question ID"
                                                                                                                                                      :options="$anotherGoToPageQuestion"
                                                                                                                                                      :value="$subcondition->conditional_question_id ?? '' "
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-4">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 {{-- <label class="form-label">Condition</label>
                                                                                                                                                 <select class="form-select js-select2" name="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]" id="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}">
                                                                                                                                                      <?php
                                                                                                                                                      $options = [
                                                                                                                                                           1 => 'is equal to',
                                                                                                                                                           2 => 'is greater than',
                                                                                                                                                           3 => 'is less than',
                                                                                                                                                           4 => 'not equal to'
                                                                                                                                                      ];
                                                                                                                                                      ?>

                                                                                                                                                      <option value="" selected disabled>Select</option>
                                                                                                                                                      @foreach($options as $opt_key => $opt_value)
                                                                                                                                                      <option value="{{ $opt_value }}" {{ isset($subcondition->conditional_check) && $subcondition->conditional_check == $opt_key ? 'selected' : '' }}>
                                                                                                                                                           {{ $opt_value }}
                                                                                                                                                      </option>
                                                                                                                                                      @endforeach
                                                                                                                                                 </select> --}}
                                                                                                                                                 @php
                                                                                                                                                      $anotherconditionOptions = [
                                                                                                                                                           'is_equal_to'     => 'is equal to',
                                                                                                                                                           'is_greater_than' => 'is greater than',
                                                                                                                                                           'is_less_than'    => 'is less than',
                                                                                                                                                           'not_equal_to'    => 'not equal to',
                                                                                                                                                      ];


                                                                                                                                                      $checkValueMap = [
                                                                                                                                                           1 => 'is_equal_to',
                                                                                                                                                           2 => 'is_greater_than',
                                                                                                                                                           3 => 'is_less_than',
                                                                                                                                                           4 => 'not_equal_to',
                                                                                                                                                      ];

                                                                                                                                                      $selectedSubCondition = $checkValueMap[$subcondition->conditional_check ?? 0] ?? '';
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="condition_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Condition"
                                                                                                                                                      :options="$anotherconditionOptions"
                                                                                                                                                      :value="$selectedSubCondition"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="text"
                                                                                                                                                      class="form-control"
                                                                                                                                                      name="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Value"
                                                                                                                                                      :value="$subcondition->conditional_question_value ?? ''"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-2 add_rmv_icn9">
                                                                                                                                            @if(!$loop->first)
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon red_hover" onclick="removeAnotherCondition(this,'{{ $subcondition->id }}','{{ $condIndex }}','{{ $key ?? '' }}')" data-id="{{ $condition->id }}">
                                                                                                                                                 <i class="fa fa-trash"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                            @endif
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon add_icon" onclick="anotherCondition(this,'{{ $condition->id }}', '{{ $condIndex }}')">
                                                                                                                                                 <i class="fa-solid fa-plus"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                  </div>
                                                                                                                                  <br>
                                                                                                                             </div>
                                                                                                                             <?php $key++ ;?>
                                                                                                                        @endforeach
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $anotherConditionalGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex }}"
                                                                                                                                       id="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex ?? '' }}"
                                                                                                                                       label="Conditional Go to Step"
                                                                                                                                       :options="$anotherConditionalGoToPageQuestion"
                                                                                                                                       :value="$condition->go_to_step ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];

                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp


                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />


                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <span class="remove_icon add_icon firstCondBtn" onclick="addCondition('{{ $que->id ?? '' }}')">
                                                                                                                        <i class="fa-solid fa-plus"></i>
                                                                                                                   </span>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoToPageQuestion"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @endif
                                                                                               </div>
                                                                                               @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none;">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label" for="">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoTo = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoTo"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="textarea"
                                                                                                              class="form-control question_info_text"
                                                                                                              name="question_info_text"
                                                                                                              id="question_info_text-{{ $que->id ?? '' }}"
                                                                                                              label="Question Info Text"
                                                                                                              :value="$que->questionData->question_info_text ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>

                                                                                          </div>
                                                                                     </div>
                                                                                     <br>
                                                                                </div>
                                                                           </div>
                                                                           @elseif($question_type == 'pricebox')
                                                                           <div class="new_que_sec{{ $que->id ?? '' }}" id="for_copy_sec{{ $que->id ?? '' }}">
                                                                                <div class="append_priceBox" id="append_priceBox{{ $que->id ?? '' }}" data-id="{{ $que->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                                                     <div class="card card-bordered card-preview">
                                                                                          <div class="card-inner">
                                                                                               <div class="row add_step">
                                                                                                    <div class="col-md-6 div_hding">
                                                                                                         <div class="qu_count">
                                                                                                              <p>
                                                                                                                   <b>
                                                                                                                   QID: {{ $que->id ?? ''}}
                                                                                                                   </b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         |
                                                                                                         <div class="que_type_heading">
                                                                                                              <p class="drop_options"><b>Pricebox
                                                                                                                   <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         <div class="form-group drop_box_option" style="display:none;">
                                                                                                              <!-- <div class="text-end cut_btn">
                                                                                                                   <div class="form-group">
                                                                                                                        <span onclick="removeDropbox(this)">
                                                                                                                             <i class="fa fa-times"></i>
                                                                                                                        </span>
                                                                                                                   </div>
                                                                                                              </div> -->
                                                                                                              <div class="slct_optns">
                                                                                                                   <select class="form-select js-select2 " data-que-id="{{ $que->id ?? '' }}" data-change-from="{{ $que->type ?? '' }}" onchange="ChangeQuestionType(this)"  >
                                                                                                                        @foreach($types as $type)
                                                                                                                             <option value="{{ $type->slug ?? '' }}" {{ isset($question) && $que->type == $type->slug ? 'selected' : '' }}>{{ $type->name ?? '' }}</option>
                                                                                                                        @endforeach
                                                                                                                   </select>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-6 prnt_icon_cls">
                                                                                                         <div class="input_icons d-flex">
                                                                                                              <span class="remove_icon red_hover" onclick="removeFields(this)" data-id="{{ $que->id ?? '' }}" data-field="pricebox" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                              {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                                   <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $que->id ?? '' }}" data-field="pricebox"><i class="fa-solid fa-plus"></i></span>
                                                                                                                   <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                                        @foreach($types as $type)
                                                                                                                             <a onclick="addQuestionfields('{{ $type->slug ?? '' }}','{{ $que->id ?? '' }}','third',this)">{{ $type->name ?? '' }}</a>
                                                                                                                        @endforeach
                                                                                                                   </div>
                                                                                                              </div> --}}
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>
                                                                                               <hr>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType1 = $isCondition && $que->is_condition == 1 && $que->condition_type == 1 || $que->condition_type == 3;
                                                                                                    $conditions = $isConditionType1 ? json_decode($que->conditions) : [];
                                                                                               @endphp
                                                                                               @if($isConditionType1)
                                                                                               <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                    <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($conditions as $condition)
                                                                                                              @if($condition->condition_type == 'question_label_condition')
                                                                                                              <div class="label-condition" id="label-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new="false">
                                                                                                                   <div class="inner-label">      
                                                                                                                        <div class="row">
                                                                                                                             <div class="col-md-4">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_label-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_label-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question Label"
                                                                                                                                            :value="$condition->question_label ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       @php
                                                                                                                                            $questionOptions = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                 return [$q->getName() => $q->getName()];
                                                                                                                                            });
                                                                                                                                       @endphp

                                                                                                                                       <x-document-input-field
                                                                                                                                            type="question_select"
                                                                                                                                            class="js-select2 new_label_question_id"
                                                                                                                                            name="label_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="label_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question ID"
                                                                                                                                            :options="$questionOptions"
                                                                                                                                            :value="$condition->conditional_question_id ?? ''"
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_value-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_value-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Value"
                                                                                                                                            :value="$condition->conditional_question_value ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-2 add_rmv_icn10">
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon red_hover" onclick="removeLabel(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                                  </div>
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','')"><i class="fa-solid fa-add"></i></span>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>                        
                                                                                                              </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                               </div>
                                                                                               @else
                                                                                               <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                    <div class="col-md-10 form-group qu_label_cls{{ $que->id ?? '' }} label_qu">
                                                                                                         <x-document-input-field
                                                                                                              type="text"
                                                                                                              class="form-control question_labl"
                                                                                                              name="text_qu_label"
                                                                                                              id="text_qu_label-{{ $que->id ?? '' }}"
                                                                                                              label="Question Label"
                                                                                                              :value="$que->questionData->question_label ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                                    <div class="col-md-2 form-group prnt_add_cls qu_label_btn{{ $que->id ?? '' }}">
                                                                                                         <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','{{ $que->questionData->question_label ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                    </div>
                                                                                                    <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}"></div>
                                                                                               </div>
                                                                                               @endif
                                                                                               <hr>
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="text"
                                                                                                              class="form-control text_box_placeholder"
                                                                                                              name="text_box_placeholder"
                                                                                                              id="text_placeholder-{{ $que->id ?? '' }}"
                                                                                                              label="Text Box Placeholder"
                                                                                                              :value="$que->questionData->text_box_placeholder ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                               <hr>
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         @php
                                                                                                              $textGoToStep = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                   return [$q->getName() => $q->getName()];
                                                                                                              });
                                                                                                         @endphp

                                                                                                         <x-document-input-field
                                                                                                              type="select"
                                                                                                              class="js-select2 new_label_question_id"
                                                                                                              name="text_go_to_step-{{ $que->id ?? '' }}"
                                                                                                              id="text_go_to_step-{{ $que->id ?? '' }}"
                                                                                                              label="Go to step"
                                                                                                              :options="$textGoToStep"
                                                                                                              :value="$que->questionData->next_question_id ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType2 = $isCondition && $que->is_condition == 1 && $que->condition_type == 2 || $que->condition_type == 3;
                                                                                                    $enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                                    $another_enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType2 && !empty($enable_conditions))
                                                                                               <div class="cond_div{{ $que->id ?? '' }}">
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              <label class="form-label" for="">Add Conditions</label>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @php
                                                                                                         $goToStepConditions = collect($enable_conditions)->filter(function($c) {
                                                                                                              return $c->condition_type == 'go_to_step_condition';
                                                                                                         })->values();
                                                                                                    @endphp
                                                                                                    <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($goToStepConditions as $condition)
                                                                                                         <div class="sec-condition" id="sec-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new=false>
                                                                                                              <div class="row">
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                       return [$q->getName() => $q->getName()];
                                                                                                                                  });
                                                                                                                             @endphp

                                                                                                                             <x-document-input-field
                                                                                                                                  type="question_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Question ID"
                                                                                                                                  :options="$goToPageQuestion"
                                                                                                                                  :value="$condition->conditional_question_id ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-4">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $conditionOptions = [
                                                                                                                                       'is_equal_to'     => 'is equal to',
                                                                                                                                       'is_greater_than' => 'is greater than',
                                                                                                                                       'is_less_than'    => 'is less than',
                                                                                                                                       'not_equal_to'    => 'not equal to',
                                                                                                                                  ];


                                                                                                                                  $checkValueMap = [
                                                                                                                                       1 => 'is_equal_to',
                                                                                                                                       2 => 'is_greater_than',
                                                                                                                                       3 => 'is_less_than',
                                                                                                                                       4 => 'not_equal_to',
                                                                                                                                  ];

                                                                                                                                  $selectedCondition = $checkValueMap[$condition->conditional_check ?? 0] ?? '';


                                                                                                                             @endphp

                                                                                                                             <x-document-input-field
                                                                                                                                  type="condition_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_conditions-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_conditions-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Condition"
                                                                                                                                  :options="$conditionOptions"
                                                                                                                                  :value="$selectedCondition"
                                                                                                                             />

                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             <x-document-input-field
                                                                                                                                  type="text"
                                                                                                                                  class="form-control"
                                                                                                                                  name="page_Setting_qu_val-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_val-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Value"
                                                                                                                                  :value="$condition->conditional_question_value ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-2 add_rmv_icn11">
                                                                                                                        @if(!$loop->first)
                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon red_hover" onclick="removeCondition(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                        </div>
                                                                                                                        @endif
                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>

                                                                                                         </div>
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              @php
                                                                                                                   $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2"
                                                                                                                   name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   label="Conditional Go to Step"
                                                                                                                   :options="$conditionalgoToPageQuestion"
                                                                                                                   :value="$que->questionData->conditional_go_to_step ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @if(isset($enable_conditions) && count($enable_conditions) > 0)
                                                                                                    <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}">
                                                                                                         @foreach($enable_conditions as $condIndex => $condition)
                                                                                                              @if($condition->condition_type == 'another_go_to_step_condition')
                                                                                                                   <div class="independent_cond_div" id="independent_cond_div_{{ $condition->id }}_{{ $condIndex }}" data-id="{{ $condition->id }}" data-is_new="false">
                                                                                                                        <hr>
                                                                                                                        <div class="text-end">
                                                                                                                             <div class="form-group">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeIndependentDiv(this,'{{ $condition->id }}','{{ $condIndex }}')" data-id="{{ $condition->id }}">
                                                                                                                                       <i class="fa fa-trash"></i>
                                                                                                                                  </span>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  <label class="form-label">Add Conditions</label>
                                                                                                                             </div>
                                                                                                                        </div>

                                                                                                                        <div class="another_page_condition" id="another_page_condition_{{ $condition->id }}_{{ $condIndex }}">
                                                                                                                        <?php $key = 1;?>
                                                                                                                        @foreach($condition->subconditions as $subcondition)
                                                                                                                             <div class="another-condition" id="another-condition-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}" data-id="{{ $subcondition->id }}" data-is_new="false">
                                                                                                                                  <div class="row">
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                           return [$q->getName() => $q->getName()];
                                                                                                                                                      });
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="question_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Question ID"
                                                                                                                                                      :options="$anotherGoToPageQuestion"
                                                                                                                                                      :value="$subcondition->conditional_question_id ?? '' "
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-4">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherconditionOptions = [
                                                                                                                                                           'is_equal_to'     => 'is equal to',
                                                                                                                                                           'is_greater_than' => 'is greater than',
                                                                                                                                                           'is_less_than'    => 'is less than',
                                                                                                                                                           'not_equal_to'    => 'not equal to',
                                                                                                                                                      ];


                                                                                                                                                      $checkValueMap = [
                                                                                                                                                           1 => 'is_equal_to',
                                                                                                                                                           2 => 'is_greater_than',
                                                                                                                                                           3 => 'is_less_than',
                                                                                                                                                           4 => 'not_equal_to',
                                                                                                                                                      ];

                                                                                                                                                      $selectedSubCondition = $checkValueMap[$subcondition->conditional_check ?? 0] ?? '';
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="condition_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Condition"
                                                                                                                                                      :options="$anotherconditionOptions"
                                                                                                                                                      :value="$selectedSubCondition"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="text"
                                                                                                                                                      class="form-control"
                                                                                                                                                      name="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Value"
                                                                                                                                                      :value="$subcondition->conditional_question_value ?? ''"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-2 add_rmv_icn12">
                                                                                                                                            @if(!$loop->first)
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon red_hover" onclick="removeAnotherCondition(this,'{{ $subcondition->id }}','{{ $condIndex }}','{{ $key ?? '' }}')" data-id="{{ $condition->id }}">
                                                                                                                                                 <i class="fa fa-trash"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                            @endif
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon add_icon" onclick="anotherCondition(this,'{{ $condition->id }}', '{{ $condIndex }}')">
                                                                                                                                                 <i class="fa-solid fa-plus"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                  </div>
                                                                                                                                  <br>
                                                                                                                             </div>
                                                                                                                             <?php $key++ ;?>
                                                                                                                        @endforeach
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $anotherConditionalGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex }}"
                                                                                                                                       id="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex ?? '' }}"
                                                                                                                                       label="Conditional Go to Step"
                                                                                                                                       :options="$anotherConditionalGoToPageQuestion"
                                                                                                                                       :value="$condition->go_to_step ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $questionquestion->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <span class="remove_icon add_icon firstCondBtn" onclick="addCondition('{{ $que->id ?? '' }}')">
                                                                                                                        <i class="fa-solid fa-plus"></i>
                                                                                                                   </span>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoToPageQuestion"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @endif
                                                                                               </div>
                                                                                               @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none;">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label" for="">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>

                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoTo = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoTo"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="textarea"
                                                                                                              class="form-control question_info_text"
                                                                                                              name="question_info_text"
                                                                                                              id="question_info_text-{{ $que->id ?? '' }}"
                                                                                                              label="Question Info Text"
                                                                                                              :value="$que->questionData->question_info_text ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <br>
                                                                                </div>
                                                                           </div>
                                                                           @elseif($question_type == 'number-field')
                                                                           <div class="new_que_sec{{ $que->id ?? '' }}" id="for_copy_sec{{ $que->id ?? '' }}">
                                                                                <div class="append_numberField" id="append_numberField{{ $que->id ?? '' }}" data-id="{{ $que->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                                                     <div class="card card-bordered card-preview">
                                                                                          <div class="card-inner">
                                                                                               <div class="row add_step">
                                                                                                    <div class="col-md-6 div_hding">
                                                                                                         <div class="qu_count">
                                                                                                              <p>
                                                                                                                   <b>
                                                                                                                   QID : {{ $que->id ?? ''}}
                                                                                                                   </b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         |
                                                                                                         <div class="que_type_heading">
                                                                                                              <p class="drop_options"><b>Number field
                                                                                                                   <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         <div class="form-group drop_box_option" style="display:none;">
                                                                                                              <!-- <div class="text-end cut_btn">
                                                                                                                   <div class="form-group">
                                                                                                                        <span onclick="removeDropbox(this)">
                                                                                                                             <i class="fa fa-times"></i>
                                                                                                                        </span>
                                                                                                                   </div>
                                                                                                              </div> -->
                                                                                                              <div class="slct_optns">
                                                                                                                   <select class="form-select js-select2 " data-que-id="{{ $que->id ?? '' }}" data-change-from="{{ $que->type ?? '' }}" onchange="ChangeQuestionType(this)"  >
                                                                                                                        @foreach($types as $type)
                                                                                                                             <option value="{{ $type->slug ?? '' }}" {{ isset($question) && $que->type == $type->slug ? 'selected' : '' }}>{{ $type->name ?? '' }}</option>
                                                                                                                        @endforeach
                                                                                                                   </select>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-6 prnt_icon_cls">
                                                                                                         <div class="input_icons d-flex">
                                                                                                              <span class="remove_icon red_hover" onclick="removeFields(this)" data-id="{{ $que->id ?? '' }}" data-field="number-field" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                              {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                                   <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $que->id ?? '' }}" data-field="number-field"><i class="fa-solid fa-plus"></i></span>
                                                                                                                   <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                                        @foreach($types as $type)
                                                                                                                             <a onclick="addQuestionfields('{{ $type->slug ?? '' }}','{{ $que->id ?? '' }}','third',this)">{{ $type->name ?? '' }}</a>
                                                                                                                        @endforeach
                                                                                                                   </div>
                                                                                                              </div> --}}
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>

                                                                                               <hr>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType1 = $isCondition && $que->is_condition == 1 && $que->condition_type == 1 || $que->condition_type == 3;
                                                                                                    $conditions = $isConditionType1 ? json_decode($que->conditions) : [];
                                                                                               @endphp
                                                                                               @if($isConditionType1)
                                                                                               <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                    <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($conditions as $condition)
                                                                                                              @if($condition->condition_type == 'question_label_condition')
                                                                                                              <div class="label-condition" id="label-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new="false">
                                                                                                                   <div class="inner-label"> 
                                                                                                                        <div class="row">
                                                                                                                             <div class="col-md-4">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_label-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_label-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question Label"
                                                                                                                                            :value="$condition->question_label ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       @php
                                                                                                                                            $questionOptions = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                 return [$q->getName() => $q->getName()];
                                                                                                                                            });
                                                                                                                                       @endphp

                                                                                                                                       <x-document-input-field
                                                                                                                                            type="question_select"
                                                                                                                                            class="js-select2 new_label_question_id"
                                                                                                                                            name="label_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="label_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question ID"
                                                                                                                                            :options="$questionOptions"
                                                                                                                                            :value="$condition->conditional_question_id ?? ''"
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_value-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_value-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Value"
                                                                                                                                            :value="$condition->conditional_question_value ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-2 add_rmv_icn13">
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon red_hover" onclick="removeLabel(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                                  </div>
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','')"><i class="fa-solid fa-add"></i></span>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>                              
                                                                                                              </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                               </div>

                                                                                               @else
                                                                                               <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}" >
                                                                                                    <div class="col-md-10 form-group qu_label_cls{{ $que->id ?? '' }} label_qu">
                                                                                                         <x-document-input-field
                                                                                                              type="text"
                                                                                                              class="form-control question_labl"
                                                                                                              name="text_qu_label"
                                                                                                              id="text_qu_label-{{ $que->id ?? '' }}"
                                                                                                              label="Question Label"
                                                                                                              :value="$que->questionData->question_label ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                                    <div class="col-md-2 form-group prnt_add_cls qu_label_btn{{ $que->id ?? '' }}">
                                                                                                         <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','{{ $que->questionData->question_label ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                    </div>
                                                                                                    <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}"></div>
                                                                                               </div>
                                                                                               @endif
                                                                                               <div class="col-md-12 custom_box_{{ $que->id ?? '' }}">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="text"
                                                                                                              class="form-control number_placeholder"
                                                                                                              name="text_box_placeholder"
                                                                                                              id="text_placeholder-{{ $que->id ?? '' }}"
                                                                                                              label="Number field Placeholder"
                                                                                                              :value="$que->questionData->text_box_placeholder ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              @php
                                                                                                                   $numberGoToStep = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2 new_label_question_id"
                                                                                                                   name="text_go_to_step-{{ $condition->id ?? '' }}[]"
                                                                                                                   id="text_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                   label="Go to step"
                                                                                                                   :options="$numberGoToStep"
                                                                                                                   :value="$que->questionData->next_question_id ?? ''"
                                                                                                              />
                                                                                                         </div>
                                                                                               </div>
                                                                                               </div>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType2 = $isCondition && $que->is_condition == 1 && $que->condition_type == 2 || $que->condition_type == 3;
                                                                                                    $enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                                    $another_enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType2 && !empty($enable_conditions))
                                                                                               <div class="cond_div{{ $que->id ?? '' }}">
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              <label class="form-label" for="">Add Conditions</label>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @php
                                                                                                         $goToStepConditions = collect($enable_conditions)->filter(function($c) {
                                                                                                              return $c->condition_type == 'go_to_step_condition';
                                                                                                         })->values();
                                                                                                    @endphp
                                                                                                    <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($goToStepConditions as $condition)
                                                                                                         <div class="sec-condition" id="sec-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new=false>
                                                                                                              <div class="row">
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                       return [$q->getName() => $q->getName()];
                                                                                                                                  });
                                                                                                                             @endphp

                                                                                                                             <x-document-input-field
                                                                                                                                  type="question_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Question ID"
                                                                                                                                  :options="$goToPageQuestion"
                                                                                                                                  :value="$condition->conditional_question_id ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-4">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $conditionOptions = [
                                                                                                                                       'is_equal_to'     => 'is equal to',
                                                                                                                                       'is_greater_than' => 'is greater than',
                                                                                                                                       'is_less_than'    => 'is less than',
                                                                                                                                       'not_equal_to'    => 'not equal to',
                                                                                                                                  ];


                                                                                                                                  $checkValueMap = [
                                                                                                                                       1 => 'is_equal_to',
                                                                                                                                       2 => 'is_greater_than',
                                                                                                                                       3 => 'is_less_than',
                                                                                                                                       4 => 'not_equal_to',
                                                                                                                                  ];

                                                                                                                                  $selectedCondition = $checkValueMap[$condition->conditional_check ?? 0] ?? '';
                                                                                                                             @endphp


                                                                                                                             <x-document-input-field
                                                                                                                                  type="condition_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_conditions-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_conditions-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Condition"
                                                                                                                                  :options="$conditionOptions"
                                                                                                                                  :value="$selectedCondition"
                                                                                                                             />

                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             <x-document-input-field
                                                                                                                                  type="text"
                                                                                                                                  class="form-control"
                                                                                                                                  name="page_Setting_qu_val-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_val-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Value"
                                                                                                                                  :value="$condition->conditional_question_value ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-2 add_rmv_icn14">
                                                                                                                        @if(!$loop->first)
                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon red_hover" onclick="removeCondition(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                        </div>
                                                                                                                        @endif

                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>

                                                                                                         </div>
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              @php
                                                                                                                   $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2"
                                                                                                                   name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   label="Conditional Go to Step"
                                                                                                                   :options="$conditionalgoToPageQuestion"
                                                                                                                   :value="$que->questionData->conditional_go_to_step ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @if(isset($enable_conditions) && count($enable_conditions) > 0)
                                                                                                    <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}">
                                                                                                         @foreach($enable_conditions as $condIndex => $condition)
                                                                                                              @if($condition->condition_type == 'another_go_to_step_condition')
                                                                                                                   <div class="independent_cond_div" id="independent_cond_div_{{ $condition->id }}_{{ $condIndex }}" data-id="{{ $condition->id }}" data-is_new="false">
                                                                                                                        <hr>
                                                                                                                        <div class="text-end">
                                                                                                                             <div class="form-group">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeIndependentDiv(this,'{{ $condition->id }}','{{ $condIndex }}')" data-id="{{ $condition->id }}">
                                                                                                                                       <i class="fa fa-trash"></i>
                                                                                                                                  </span>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  <label class="form-label">Add Conditions</label>
                                                                                                                             </div>
                                                                                                                        </div>

                                                                                                                        <div class="another_page_condition" id="another_page_condition_{{ $condition->id }}_{{ $condIndex }}">
                                                                                                                        <?php $key = 1;?>
                                                                                                                        @foreach($condition->subconditions as $subcondition)
                                                                                                                             <div class="another-condition" id="another-condition-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}" data-id="{{ $subcondition->id }}" data-is_new="false">
                                                                                                                                  <div class="row">
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                           return [$q->getName() => $q->getName()];
                                                                                                                                                      });
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="question_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Question ID"
                                                                                                                                                      :options="$anotherGoToPageQuestion"
                                                                                                                                                      :value="$subcondition->conditional_question_id ?? '' "
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-4">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherconditionOptions = [
                                                                                                                                                           'is_equal_to'     => 'is equal to',
                                                                                                                                                           'is_greater_than' => 'is greater than',
                                                                                                                                                           'is_less_than'    => 'is less than',
                                                                                                                                                           'not_equal_to'    => 'not equal to',
                                                                                                                                                      ];


                                                                                                                                                      $checkValueMap = [
                                                                                                                                                           1 => 'is_equal_to',
                                                                                                                                                           2 => 'is_greater_than',
                                                                                                                                                           3 => 'is_less_than',
                                                                                                                                                           4 => 'not_equal_to',
                                                                                                                                                      ];

                                                                                                                                                      $selectedSubCondition = $checkValueMap[$subcondition->conditional_check ?? 0] ?? '';
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="condition_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Condition"
                                                                                                                                                      :options="$anotherconditionOptions"
                                                                                                                                                      :value="$selectedSubCondition"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="text"
                                                                                                                                                      class="form-control"
                                                                                                                                                      name="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Value"
                                                                                                                                                      :value="$subcondition->conditional_question_value ?? ''"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-2 add_rmv_icn15">
                                                                                                                                            @if(!$loop->first)
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon red_hover" onclick="removeAnotherCondition(this,'{{ $subcondition->id }}','{{ $condIndex }}','{{ $key ?? '' }}')" data-id="{{ $condition->id }}">
                                                                                                                                                 <i class="fa fa-trash"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                            @endif
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon add_icon" onclick="anotherCondition(this,'{{ $condition->id }}', '{{ $condIndex }}')">
                                                                                                                                                 <i class="fa-solid fa-plus"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                  </div>
                                                                                                                                  <br>
                                                                                                                             </div>
                                                                                                                             <?php $key++ ;?>
                                                                                                                        @endforeach
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $anotherConditionalGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex }}"
                                                                                                                                       id="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex ?? '' }}"
                                                                                                                                       label="Conditional Go to Step"
                                                                                                                                       :options="$anotherConditionalGoToPageQuestion"
                                                                                                                                       :value="$condition->go_to_step ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp


                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />


                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <span class="remove_icon add_icon firstCondBtn" onclick="addCondition('{{ $que->id ?? '' }}')">
                                                                                                                        <i class="fa-solid fa-plus"></i>
                                                                                                                   </span>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoToPageQuestion"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @endif
                                                                                               </div>
                                                                                               @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none;">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label" for="">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>

                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp


                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoTo = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoTo"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="textarea"
                                                                                                              class="form-control question_info_text"
                                                                                                              name="question_info_text"
                                                                                                              id="question_info_text-{{ $que->id ?? '' }}"
                                                                                                              label="Question Info Text"
                                                                                                              :value="$que->questionData->question_info_text ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <br>
                                                                                </div>
                                                                           </div>
                                                                           @elseif($question_type == 'percentage-box')
                                                                           <div class="new_que_sec{{ $que->id ?? '' }}" id="for_copy_sec{{ $que->id ?? '' }}">
                                                                                <div class="appendPercentageBox" id="appendPercentageBox{{ $que->id ?? '' }}" data-id="{{ $que->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                                                     <div class="card card-bordered card-preview">
                                                                                          <div class="card-inner">
                                                                                               <div class="row add_step">
                                                                                                    <div class="col-md-6 div_hding">
                                                                                                         <div class="qu_count">
                                                                                                              <p>
                                                                                                                   <b>
                                                                                                                   QID : {{ $que->id ?? ''}}
                                                                                                                   </b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         |
                                                                                                         <div class="que_type_heading">
                                                                                                              <p class="drop_options"><b>Percentage Box
                                                                                                                   <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         <div class="form-group drop_box_option" style="display:none;">
                                                                                                              <!-- <div class="text-end cut_btn">
                                                                                                                   <div class="form-group">
                                                                                                                        <span onclick="removeDropbox(this)">
                                                                                                                             <i class="fa fa-times"></i>
                                                                                                                        </span>
                                                                                                                   </div>
                                                                                                              </div> -->
                                                                                                              <div class="slct_optns">
                                                                                                                   <select class="form-select js-select2 " data-que-id="{{ $que->id ?? '' }}" data-change-from="{{ $que->type ?? '' }}" onchange="ChangeQuestionType(this)"  >
                                                                                                                        @foreach($types as $type)
                                                                                                                             <option value="{{ $type->slug ?? '' }}" {{ isset($question) && $que->type == $type->slug ? 'selected' : '' }}>{{ $type->name ?? '' }}</option>
                                                                                                                        @endforeach
                                                                                                                   </select>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-6 prnt_icon_cls">
                                                                                                         <div class="input_icons d-flex">
                                                                                                              <span class="remove_icon red_hover" onclick="removeFields(this)" data-id="{{ $que->id ?? '' }}" data-field="percentBox" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                              {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                                   <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $que->id ?? '' }}" data-field="percentBox"><i class="fa-solid fa-plus"></i></span>
                                                                                                                   <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                                        @foreach($types as $type)
                                                                                                                             <a onclick="addQuestionfields('{{ $type->slug ?? '' }}','{{ $que->id ?? '' }}','third',this)">{{ $type->name ?? '' }}</a>
                                                                                                                        @endforeach
                                                                                                                   </div>
                                                                                                              </div> --}}
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>

                                                                                               <hr>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType1 = $isCondition && $que->is_condition == 1 && $que->condition_type == 1 || $que->condition_type == 3;
                                                                                                    $conditions = $isConditionType1 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType1)
                                                                                               <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                    <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($conditions as $condition)
                                                                                                              @if($condition->condition_type == 'question_label_condition')
                                                                                                              <div class="label-condition" id="label-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new="false">
                                                                                                                   <div class="inner-label">      
                                                                                                                        <div class="row">
                                                                                                                             <div class="col-md-4">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_label-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_label-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question Label"
                                                                                                                                            :value="$condition->question_label ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       @php
                                                                                                                                            $questionOptions = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                 return [$q->getName() => $q->getName()];
                                                                                                                                            });
                                                                                                                                       @endphp

                                                                                                                                       <x-document-input-field
                                                                                                                                            type="question_select"
                                                                                                                                            class="js-select2 new_label_question_id"
                                                                                                                                            name="label_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="label_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Question ID"
                                                                                                                                            :options="$questionOptions"
                                                                                                                                            :value="$condition->conditional_question_id ?? ''"
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-3">
                                                                                                                                  <div class="form-group">
                                                                                                                                       <x-document-input-field
                                                                                                                                            type="text"
                                                                                                                                            class="form-control"
                                                                                                                                            name="condition_question_value-{{ $condition->id ?? '' }}[]"
                                                                                                                                            id="condition_question_value-{{ $condition->id ?? '' }}"
                                                                                                                                            label="Value"
                                                                                                                                            :value="$condition->conditional_question_value ?? '' "
                                                                                                                                       />
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                             <div class="col-md-2 add_rmv_icn16">
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon red_hover" onclick="removeLabel(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                                  </div>
                                                                                                                                  <div class="form-group prnt_add_cls">
                                                                                                                                       <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','')"><i class="fa-solid fa-add"></i></span>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>                        
                                                                                                              </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                               </div>
                                                                                               @else
                                                                                               <div class="row hide_question_label" id="hide_question_label{{ $que->id ?? '' }}">
                                                                                                    <div class="col-md-10 form-group qu_label_cls{{ $que->id ?? '' }} label_qu">
                                                                                                         <x-document-input-field
                                                                                                              type="text"
                                                                                                              class="form-control question_labl"
                                                                                                              name="text_qu_label"
                                                                                                              id="text_qu_label-{{ $que->id ?? '' }}"
                                                                                                              label="Question Label"
                                                                                                              :value="$que->questionData->question_label ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                                    <div class="col-md-2 form-group prnt_add_cls qu_label_btn{{ $que->id ?? '' }}">
                                                                                                         <span class="remove_icon add_icon" onclick="addLabel('{{ $que->id ?? '' }}','{{ $que->questionData->question_label ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                    </div>
                                                                                                    <div class="append_label_condition" id="append_label_condition{{ $que->id ?? '' }}"></div>
                                                                                               </div>
                                                                                               @endif
                                                                                               <hr>
                                                                                               <div class="col-md-12 custom_box_{{ $que->id ?? ''}}">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="text"
                                                                                                              class="form-control text_box_placeholder"
                                                                                                              name="text_box_placeholder"
                                                                                                              id="text_placeholder-{{ $que->id ?? '' }}"
                                                                                                              label="Text Box Placeholder"
                                                                                                              :value="$que->questionData->text_box_placeholder ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                                    <div class="col-md-12 custom_box_{{ $que->id ?? ''}}" >
                                                                                                    <div class="form-group">
                                                                                                              @php
                                                                                                                   $textGoToStep = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2 new_label_question_id"
                                                                                                                   name="text_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   id="text_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   label="Go to step"
                                                                                                                   :options="$textGoToStep"
                                                                                                                   :value="$que->questionData->next_question_id ?? ''"
                                                                                                              />
                                                                                                    </div>
                                                                                               </div>
                                                                                               </div>
                                                                                               @php
                                                                                                    $isCondition = isset($que->is_condition) && $que->is_condition != null;
                                                                                                    $isConditionType2 = $isCondition && $que->is_condition == 1 && $que->condition_type == 2 || $que->condition_type == 3;
                                                                                                    $enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                                    $another_enable_conditions = $isConditionType2 ? json_decode($que->conditions) : [];
                                                                                               @endphp

                                                                                               @if($isConditionType2 && !empty($enable_conditions))
                                                                                               <div class="cond_div{{ $que->id ?? '' }}">
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              <label class="form-label" for="">Add Conditions</label>
                                                                                                         </div>
                                                                                                    </div>


                                                                                                    @php
                                                                                                         $goToStepConditions = collect($enable_conditions)->filter(function($c) {
                                                                                                              return $c->condition_type == 'go_to_step_condition';
                                                                                                         })->values();
                                                                                                    @endphp
                                                                                                    <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                         @foreach($goToStepConditions as $condition)
                                                                                                         <div class="sec-condition" id="sec-condition{{ $condition->id ?? '' }}" data-id="{{ $condition->id ?? '' }}" data-is_new=false>
                                                                                                              <div class="row">
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                       return [$q->getName() => $q->getName()];
                                                                                                                                  });
                                                                                                                             @endphp

                                                                                                                             <x-document-input-field
                                                                                                                                  type="question_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_qu_id-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_id-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Question ID"
                                                                                                                                  :options="$goToPageQuestion"
                                                                                                                                  :value="$condition->conditional_question_id ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-4">
                                                                                                                        <div class="form-group">
                                                                                                                             @php
                                                                                                                                  $conditionOptions = [
                                                                                                                                       'is_equal_to'     => 'is equal to',
                                                                                                                                       'is_greater_than' => 'is greater than',
                                                                                                                                       'is_less_than'    => 'is less than',
                                                                                                                                       'not_equal_to'    => 'not equal to',
                                                                                                                                  ];


                                                                                                                                  $checkValueMap = [
                                                                                                                                       1 => 'is_equal_to',
                                                                                                                                       2 => 'is_greater_than',
                                                                                                                                       3 => 'is_less_than',
                                                                                                                                       4 => 'not_equal_to',
                                                                                                                                  ];

                                                                                                                                  $selectedCondition = $checkValueMap[$condition->conditional_check ?? 0] ?? '';
                                                                                                                             @endphp


                                                                                                                             <x-document-input-field
                                                                                                                                  type="condition_select"
                                                                                                                                  class="js-select2"
                                                                                                                                  name="page_Setting_conditions-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_conditions-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Condition"
                                                                                                                                  :options="$conditionOptions"
                                                                                                                                  :value="$selectedCondition"
                                                                                                                             />

                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             <x-document-input-field
                                                                                                                                  type="text"
                                                                                                                                  class="form-control"
                                                                                                                                  name="page_Setting_qu_val-{{ $condition->id ?? '' }}[]"
                                                                                                                                  id="page_Setting_qu_val-{{ $condition->id ?? '' }}"
                                                                                                                                  label="Value"
                                                                                                                                  :value="$condition->conditional_question_value ?? '' "
                                                                                                                             />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-2 add_rmv_icn17">
                                                                                                                        @if(!$loop->first)
                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon red_hover" onclick="removeCondition(this)" data-id="{{ $condition->id ?? '' }}"><i class="fa fa-trash"></i></span>
                                                                                                                        </div>
                                                                                                                        @endif

                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="col-md-12">
                                                                                                         <div class="form-group">
                                                                                                              @php
                                                                                                                   $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                        return [$q->getName() => $q->getName()];
                                                                                                                   });
                                                                                                              @endphp

                                                                                                              <x-document-input-field
                                                                                                                   type="select"
                                                                                                                   class="js-select2"
                                                                                                                   name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                   label="Conditional Go to Step"
                                                                                                                   :options="$conditionalgoToPageQuestion"
                                                                                                                   :value="$que->questionData->conditional_go_to_step ?? '' "
                                                                                                              />
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @if(isset($enable_conditions) && count($enable_conditions) > 0)
                                                                                                    <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}">
                                                                                                         @foreach($enable_conditions as $condIndex => $condition)
                                                                                                              @if($condition->condition_type == 'another_go_to_step_condition')
                                                                                                                   <div class="independent_cond_div" id="independent_cond_div_{{ $condition->id }}_{{ $condIndex }}" data-id="{{ $condition->id }}" data-is_new="false">
                                                                                                                        <hr>
                                                                                                                        <div class="text-end">
                                                                                                                             <div class="form-group">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeIndependentDiv(this,'{{ $condition->id }}','{{ $condIndex }}')" data-id="{{ $condition->id }}">
                                                                                                                                       <i class="fa fa-trash"></i>
                                                                                                                                  </span>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  <label class="form-label">Add Conditions</label>
                                                                                                                             </div>
                                                                                                                        </div>

                                                                                                                        <div class="another_page_condition" id="another_page_condition_{{ $condition->id }}_{{ $condIndex }}">
                                                                                                                        <?php $key = 1;?>
                                                                                                                        @foreach($condition->subconditions as $subcondition)
                                                                                                                             <div class="another-condition" id="another-condition-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}" data-id="{{ $subcondition->id }}" data-is_new="false">
                                                                                                                                  <div class="row">
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                                           return [$q->getName() => $q->getName()];
                                                                                                                                                      });
                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="question_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_que_id-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Question ID"
                                                                                                                                                      :options="$anotherGoToPageQuestion"
                                                                                                                                                      :value="$subcondition->conditional_question_id ?? '' "
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-4">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 @php
                                                                                                                                                      $anotherconditionOptions = [
                                                                                                                                                           'is_equal_to'     => 'is equal to',
                                                                                                                                                           'is_greater_than' => 'is greater than',
                                                                                                                                                           'is_less_than'    => 'is less than',
                                                                                                                                                           'not_equal_to'    => 'not equal to',
                                                                                                                                                      ];


                                                                                                                                                      $checkValueMap = [
                                                                                                                                                           1 => 'is_equal_to',
                                                                                                                                                           2 => 'is_greater_than',
                                                                                                                                                           3 => 'is_less_than',
                                                                                                                                                           4 => 'not_equal_to',
                                                                                                                                                      ];

                                                                                                                                                      $selectedSubCondition = $checkValueMap[$subcondition->conditional_check ?? 0] ?? '';


                                                                                                                                                 @endphp

                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="condition_select"
                                                                                                                                                      class="js-select2"
                                                                                                                                                      name="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_conditions_step-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Condition"
                                                                                                                                                      :options="$anotherconditionOptions"
                                                                                                                                                      :value="$selectedSubCondition"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-3">
                                                                                                                                            <div class="form-group">
                                                                                                                                                 <x-document-input-field
                                                                                                                                                      type="text"
                                                                                                                                                      class="form-control"
                                                                                                                                                      name="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}[]"
                                                                                                                                                      id="another_qu_val-{{ $subcondition->id }}-{{ $condIndex }}-{{ $key ?? '' }}"
                                                                                                                                                      label="Value"
                                                                                                                                                      :value="$subcondition->conditional_question_value ?? ''"
                                                                                                                                                 />
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                       <div class="col-md-2 add_rmv_icn18">
                                                                                                                                            @if(!$loop->first)
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon red_hover" onclick="removeAnotherCondition(this,'{{ $subcondition->id }}','{{ $condIndex }}','{{ $key ?? '' }}')" data-id="{{ $condition->id }}">
                                                                                                                                                 <i class="fa fa-trash"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                            @endif
                                                                                                                                            <div class="form-group prnt_add_cls">
                                                                                                                                                 <span class="remove_icon add_icon" onclick="anotherCondition(this,'{{ $condition->id }}', '{{ $condIndex }}')">
                                                                                                                                                 <i class="fa-solid fa-plus"></i>
                                                                                                                                                 </span>
                                                                                                                                            </div>
                                                                                                                                       </div>
                                                                                                                                  </div>
                                                                                                                                  <br>
                                                                                                                             </div>
                                                                                                                             <?php $key++ ;?>
                                                                                                                        @endforeach
                                                                                                                        </div>
                                                                                                                        <div class="col-md-12">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $anotherConditionalGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex }}"
                                                                                                                                       id="another_conditional_go_to_step-{{ $condition->id }}-{{ $condIndex ?? '' }}"
                                                                                                                                       label="Conditional Go to Step"
                                                                                                                                       :options="$anotherConditionalGoToPageQuestion"
                                                                                                                                       :value="$condition->go_to_step ?? '' "
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    </div>
                                                                                                    <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp


                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />


                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="text-end">
                                                                                                              <div class="form-group">
                                                                                                                   <span class="remove_icon add_icon firstCondBtn" onclick="addCondition('{{ $que->id ?? '' }}')">
                                                                                                                        <i class="fa-solid fa-plus"></i>
                                                                                                                   </span>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $que->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoToPageQuestion"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    @endif
                                                                                               </div>
                                                                                               @else
                                                                                                    <div class="grey_btn_div">
                                                                                                         <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step{{ $que->id ?? '' }}" onclick="addGoToStep('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                    </div>
                                                                                                    <div class="cond_div{{ $que->id ?? '' }}" style="display:none;">
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   <label class="form-label" for="">Add Conditions</label>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="append_page_condition" id="append_page_condition{{ $que->id ?? '' }}">
                                                                                                              <div class="sec-condition" id="sec-condition{{ $que->id ?? '' }}" value="appended" data-is_new=true>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $goToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                                            return [$q->getName() => $q->getName()];
                                                                                                                                       });
                                                                                                                                  @endphp

                                                                                                                                  <x-document-input-field
                                                                                                                                       type="question_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_qu_id-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_id-{{ $que->id ?? '' }}"
                                                                                                                                       label="Question ID"
                                                                                                                                       :options="$goToPageQuestion"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group">
                                                                                                                                  @php
                                                                                                                                       $conditionOptions = [
                                                                                                                                            'is_equal_to'     => 'is equal to',
                                                                                                                                            'is_greater_than' => 'is greater than',
                                                                                                                                            'is_less_than'    => 'is less than',
                                                                                                                                            'not_equal_to'    => 'not equal to',
                                                                                                                                       ];


                                                                                                                                       $checkValueMap = [
                                                                                                                                            1 => 'is_equal_to',
                                                                                                                                            2 => 'is_greater_than',
                                                                                                                                            3 => 'is_less_than',
                                                                                                                                            4 => 'not_equal_to',
                                                                                                                                       ];
                                                                                                                                  @endphp


                                                                                                                                  <x-document-input-field
                                                                                                                                       type="condition_select"
                                                                                                                                       class="js-select2"
                                                                                                                                       name="page_Setting_conditions-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_conditions-{{ $que->id ?? '' }}"
                                                                                                                                       label="Condition"
                                                                                                                                       :options="$conditionOptions"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group">
                                                                                                                                  <x-document-input-field
                                                                                                                                       type="text"
                                                                                                                                       class="form-control"
                                                                                                                                       name="page_Setting_qu_val-{{ $que->id ?? '' }}[]"
                                                                                                                                       id="page_Setting_qu_val-{{ $que->id ?? '' }}"
                                                                                                                                       label="Value"
                                                                                                                                       :value="null"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addCondition('{{ $que->id ?? '' }}')"><i class="fa-solid fa-plus"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-12">
                                                                                                              <div class="form-group">
                                                                                                                   @php
                                                                                                                        $conditionalgoTo = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                             return [$q->getName() => $q->getName()];
                                                                                                                        });
                                                                                                                   @endphp

                                                                                                                   <x-document-input-field
                                                                                                                        type="select"
                                                                                                                        class="js-select2"
                                                                                                                        name="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        id="conditional_go_to_step-{{ $condition->id ?? '' }}"
                                                                                                                        label="Conditional Go to Step"
                                                                                                                        :options="$conditionalgoTo"
                                                                                                                        :value="null"
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="independent_cond_container" id="independent_cond_container{{ $que->id ?? '' }}"></div>
                                                                                                         <hr>
                                                                                                         <div class="another_cond_div{{ $que->id ?? '' }}">
                                                                                                              <div class="text-end">
                                                                                                                   <div class="form-group">
                                                                                                                        <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('{{ $que->id ?? '' }}')">Add Condition</button>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @endif
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="textarea"
                                                                                                              class="form-control question_info_text"
                                                                                                              name="question_info_text"
                                                                                                              id="question_info_text-{{ $que->id ?? '' }}"
                                                                                                              label="Question Info Text"
                                                                                                              :value="$que->questionData->question_info_text ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <br>
                                                                                </div>
                                                                           </div>
                                                                           @elseif($question_type == 'dropdown-link')
                                                                           <div class="new_que_sec{{ $que->id ?? '' }}" id="for_copy_sec{{ $que->id ?? '' }}">
                                                                                <div class="append_dropdownLink" id="append_dropdownLink{{ $que->id ?? '' }}" data-id="{{ $que->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                                                     <div class="card card-bordered card-preview">
                                                                                          <div class="card-inner">
                                                                                               <div class="row add_step">
                                                                                                    <div class="col-md-6 div_hding">
                                                                                                         <div class="qu_count">
                                                                                                              <p>
                                                                                                                   <b>
                                                                                                                   QID : {{ $que->id ?? ''}}
                                                                                                                   </b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         |
                                                                                                         <div class="que_type_heading">
                                                                                                              <p class="drop_options"><b>Dropdown link
                                                                                                                   <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                              </p>
                                                                                                         </div>
                                                                                                         <div class="form-group drop_box_option" style="display:none;">
                                                                                                              <!-- <div class="text-end cut_btn">
                                                                                                                   <div class="form-group">
                                                                                                                        <span onclick="removeDropbox(this)">
                                                                                                                             <i class="fa fa-times"></i>
                                                                                                                        </span>
                                                                                                                   </div>
                                                                                                              </div> -->
                                                                                                              <div class="slct_optns">
                                                                                                                   <select class="form-select js-select2 " data-que-id="{{ $que->id ?? '' }}" data-change-from="{{ $que->type ?? '' }}" onchange="ChangeQuestionType(this)"  >
                                                                                                                        @foreach($types as $type)
                                                                                                                             <option value="{{ $type->slug ?? '' }}" {{ isset($question) && $que->type == $type->slug ? 'selected' : '' }}>{{ $type->name ?? '' }}</option>
                                                                                                                        @endforeach
                                                                                                                   </select>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-6 prnt_icon_cls">
                                                                                                         <div class="input_icons d-flex">
                                                                                                              <span class="remove_icon red_hover" onclick="removeFields(this)" data-id="{{ $que->id ?? '' }}" data-field="dropdown-link" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                              {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                                   <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $que->id ?? '' }}" data-field="dropdown-link"><i class="fa-solid fa-plus"></i></span>
                                                                                                                   <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                                        @foreach($types as $type)
                                                                                                                             <a onclick="addQuestionfields('{{ $type->slug ?? '' }}','{{ $que->id ?? '' }}','third',this)">{{ $type->name ?? '' }}</a>
                                                                                                                        @endforeach
                                                                                                                   </div>
                                                                                                              </div> --}}
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>
                                                                                               <hr>
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="text"
                                                                                                              class="form-control dropdown_ques"
                                                                                                              name="text_qu_label"
                                                                                                              id="text_qu_label-{{ $que->id ?? '' }}"
                                                                                                              label="Question Label"
                                                                                                              :value="$que->questionData->question_label ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                               <hr>
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="text"
                                                                                                              class="form-control same_contract"
                                                                                                              name="same_contract_link-{{ $que->id ?? '' }}"
                                                                                                              id="same_contract_link-{{ $que->id ?? '' }}"
                                                                                                              label="Same Contract Link Label"
                                                                                                              :value="$que->questionData->same_contract_link_label ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                               <hr>
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <label class="form-label" for="">Different Contract Link</label>
                                                                                                    </div>
                                                                                               </div>
                                                                                               <div class="append_cont_btn" id="append_cont_btn{{ $que->id ?? '' }}"></div>
                                                                                               <div class="add_cont_rw" id="add_cont_rw{{ $que->id ?? '' }}">
                                                                                               @if(isset($question->options) && $question->options != null)
                                                                                               <?php $options = json_decode($question->options); ?>
                                                                                               @foreach($options as $option)
                                                                                               <div class="contract-option" id="contract-option{{ $option->id ?? '' }}" data-id="{{ $option->id ?? '' }}" data-is_new=false>
                                                                                                    <div class="row">
                                                                                                         <div class="col-md-5">
                                                                                                              <div class="form-group">
                                                                                                                   <x-document-input-field
                                                                                                                        type="text"
                                                                                                                        class="form-control"
                                                                                                                        name="dropdown_link_label{{ $option->id ?? '' }}"
                                                                                                                        id="dropdown_link_label{{ $option->id ?? '' }}"
                                                                                                                        label="Label"
                                                                                                                        :value="$option->option_label ?? '' "
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-5">
                                                                                                              <div class="form-group">
                                                                                                                   <x-document-input-field
                                                                                                                        type="text"
                                                                                                                        class="form-control"
                                                                                                                        name="contract_link{{ $option->id ?? '' }}"
                                                                                                                        id="contract_link{{ $option->id ?? '' }}"
                                                                                                                        label="Contract Link"
                                                                                                                        :value="$option->contract_link ?? '' "
                                                                                                                   />
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-2 add_rmv_icn19">
                                                                                                              @if(!$loop->first)
                                                                                                              <div class="form-group prnt_add_cls">
                                                                                                                   <span class="remove_icon red_hover" onclick="removeContract(this)" data-id="{{ $option->id ?? '' }}">
                                                                                                                        <i class="fa fa-trash"></i>
                                                                                                                   </span>
                                                                                                              </div>
                                                                                                              @endif
                                                                                                              <div class="form-group prnt_add_cls">
                                                                                                                   <span class="remove_icon add_icon" onclick="addContractRow('{{ $que->id ?? '' }}','')"><i class="fa-solid fa-add"></i></span>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    <br>
                                                                                               </div>
                                                                                               <br>
                                                                                               @endforeach
                                                                                               @else
                                                                                               <div class="text-end">
                                                                                                    <div class="form-group">
                                                                                                         <span class="remove_icon add_icon contract_btn{{ $que->id ?? '' }}" onclick="addContractRow('{{ $que->id ?? '' }}','')"><i class="fa-solid fa-add"></i></span>
                                                                                                    </div>
                                                                                               </div>
                                                                                               <hr>
                                                                                               @endif
                                                                                               </div>

                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         @php
                                                                                                              $linkGoTo = collect($questions)->mapWithKeys(function ($q) {
                                                                                                                   return [$q->getName() => $q->getName()];
                                                                                                              });
                                                                                                         @endphp

                                                                                                         <x-document-input-field
                                                                                                              type="select"
                                                                                                              class="js-select2 new_label_question_id"
                                                                                                              name="text_go_to_step-{{ $que->id ?? '' }}"
                                                                                                              id="text_go_to_step-{{ $que->id ?? '' }}"
                                                                                                              label="Go to Step"
                                                                                                              :options="$linkGoTo"
                                                                                                              :value="$que->questionData->next_question_id ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                               <hr>
                                                                                               <div class="col-md-12">
                                                                                                    <div class="form-group">
                                                                                                         <x-document-input-field
                                                                                                              type="textarea"
                                                                                                              class="form-control question_info_text"
                                                                                                              name="question_info_text"
                                                                                                              id="question_info_text-{{ $que->id ?? '' }}"
                                                                                                              label="Question Info Text"
                                                                                                              :value="$que->questionData->question_info_text ?? '' "
                                                                                                         />
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <br>
                                                                                </div>
                                                                           </div>
                                                                           @endif
                                                                      </div>
                                                                      <div class="row">
                                                                           <div class="col-md-6">
                                                                                <div class="nk-block-head-content">
                                                                                     <div class="up-btn mbsc-form-group">
                                                                                          <button class="btn btn-sm btn-primary" type="submit">Update</button>
                                                                                     </div>
                                                                                </div>
                                                                           </div>
                                                                           {{-- <div class="col-md-6">                                  
                                                                                <div class="text-end">
                                                                                     <div class="dropdown">
                                                                                          <button type="button" class="btn btn-sm btn-primary question_dropbtn dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add question</button>
                                                                                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                               @foreach($types as $type)
                                                                                                    <a onclick="addQuestionfields('{{ $type->slug ?? '' }}','${unqId}','first')">{{ $type->name ?? '' }}</a>
                                                                                               @endforeach
                                                                                          </div>
                                                                                     </div>
                                                                                </div>
                                                                           </div> --}}
                                                                      </div>
                                                                 </div>
                                                            </form>
                                                       </div>
                                                       {{-- TEXT INPUT TEMPLATE --}}

                                                       <div id="template-text" style="display: none;">
                                                       <x-document-input-field
                                                            type="text"
                                                            class="__CLASS__"
                                                            name="__NAME__"
                                                            id="__ID__"
                                                            label="__LABEL__"
                                                            :value="''"
                                                            :alwaysActive="true"
                                                            :error="false"
                                                       />
                                                       </div>


                                                       {{-- TEXTAREA TEMPLATE --}}
                                                       <div id="template-textarea" style="display: none;">
                                                       <x-document-input-field
                                                            type="text"
                                                            class="__CLASS__"
                                                            name="__NAME__"
                                                            id="__ID__"
                                                            label="__LABEL__"
                                                            :value="''"
                                                            :alwaysActive="true"
                                                            :error="false"
                                                       />
                                                       </div>

                                                       @php
                                                            $questionIDs = collect($questions)->mapWithKeys(function ($q) {
                                                                 return [$q->getName() => $q->getName()];
                                                            });
                                                       @endphp
                                                       <div id="template-question_select" style="display: none;">
                                                       <x-document-input-field
                                                            type="question_select"
                                                            class="__CLASS__"
                                                            name="__NAME__"
                                                            id="__ID__"
                                                            label="__LABEL__"
                                                            :options="$questionIDs"
                                                            :value="''"
                                                            :alwaysActive="true"
                                                            :error="false"
                                                       />
                                                       </div>

                                                       {{-- SELECT TEMPLATE --}}
                                                       @php
                                                            $dynamicGoToPageQuestion = collect($questions)->mapWithKeys(function ($q) {
                                                                 return [$q->getName() => $q->getName()];
                                                            });
                                                       @endphp
                                                       <div id="template-select" style="display: none;">
                                                       <x-document-input-field
                                                            type="select"
                                                            class="__CLASS__"
                                                            name="__NAME__"
                                                            id="__ID__"
                                                            label="__LABEL__"
                                                            :options="$dynamicGoToPageQuestion"
                                                            :value="''"
                                                            :alwaysActive="true"
                                                            :error="false"
                                                       />
                                                       </div>

                                                       {{-- CONDITION SELECT TEMPLATE --}}
                                                       @php
                                                            $dynamicConditionOptions = [
                                                                 'is_equal_to'     => 'is equal to',
                                                                 'is_greater_than' => 'is greater than',
                                                                 'is_less_than'    => 'is less than',
                                                                 'not_equal_to'    => 'not equal to',
                                                            ];
                                                       @endphp
                                                       <div id="template-condition-select" style="display: none;">
                                                       <x-document-input-field
                                                            type="condition_select"
                                                            class="__CLASS__"
                                                            name="__NAME__"
                                                            id="__ID__"
                                                            label="__LABEL__"
                                                            :options="$dynamicConditionOptions"
                                                            :value="''"
                                                            :alwaysActive="true"
                                                            :error="false"
                                                       />
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        <label class="que_heading lbl-{{ $que->id }}">
                                             @if($que->is_condition == 1)
                                                  <?php
                                                  $labelCondition = App\Models\QuestionCondition::where('question_id', $que->id)->where('condition_type', 'question_label_condition')->first();

                                                  echo $labelCondition?->question_label ?? $que->questionData->question_label;
                                                  ?>
                                             @else
                                                  {{ $que->questionData->question_label ?? '' }}
                                             @endif

                                        </label>
                                        @if($question_type == 'textbox')
                                             @php
                                                  $next_qid = $que->questionData->next_question_id ?? '';
                                             @endphp
                                             <input type="text" target-id="qidtarget-{{ $que->id ?? '' }}"
                                                  id="{{ $que->id ?? '' }}" name="{{ $que->id ?? '' }}"
                                                  onkeyup="storeAnswers(this, '{{ $que->id ?? '' }}','{{ $question_type ?? '' }}', '{{ $next_qid ?? '' }}')"
                                                  placeholder="{{ $que->questionData->text_box_placeholder ?? '' }}"
                                                  data-placeholdertext="__________" />
                                        @elseif($question_type == 'textarea')
                                             @php
                                                  $next_qid = $que->questionData->next_question_id ?? '';
                                             @endphp
                                             <textarea class="contract_textarea" target-id="qidtarget-{{ $que->id ?? '' }}" id="{{ $que->id ?? '' }}"
                                                  name="{{ $que->id ?? '' }}"
                                                  onkeyup="storeAnswers(this, '{{ $que->id ?? '' }}','{{ $question_type ?? '' }}', '{{ $next_qid ?? '' }}')"
                                                  placeholder="{{ $que->questionData->text_box_placeholder ?? '' }}" data-placeholdertext="__________"></textarea>
                                        @elseif($question_type == 'dropdown')
                                             @php
                                                  $next_qid = $que->options->first()->next_question_id ?? '';
                                             @endphp
                                             <div class="custom-select-wrapper">
                                                  <select
                                                       onchange="updateNextButton(this, '{{ $que->id ?? '' }}'); storeAnswers(this, '{{ $que->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}') "
                                                       target-id="qidtarget-{{ $que->id ?? '' }}"
                                                       id="{{ $que->id ?? '' }}" name="{{ $que->id ?? '' }}">
                                                       @foreach($que->options as $option)
                                                       <option my_ref_nxt=".nxt_btn_{{ $que->id ?? '' }}"
                                                            que_id="{{ $option->next_question_id ?? '' }}"
                                                            value="{{ $option->option_value ?? '' }}"
                                                            {{ $loop->first ? 'selected' : '' }}>
                                                            {{ $option->option_label }} </option>
                                                       @endforeach
                                                  </select>
                                             </div>
                                        @elseif($question_type == 'radio-button')
                                             @php
                                                  $next_qid = $que->options->first()->next_question_id ?? '';
                                             @endphp
                                             @foreach($que->options as $option)
                                                  <div class="radio_div">
                                                       <label>
                                                       <input type="radio" name="question_{{ $que->id ?? '' }}"
                                                            target-id="qidtarget-{{ $que->id ?? '' }}"
                                                            id="radio_{{ $que->id ?? '' }}{{ $num++ ?? '' }}"
                                                            onchange="updateNextButtonR(this); storeAnswers(this, '{{ $que->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}')"
                                                            my_ref_nxt=".nxt_btn_{{ $que->id ?? '' }}"
                                                            que_id="{{ $option->next_question_id ?? '' }}"
                                                            value="{{ $option->option_value ?? '' }}"
                                                            {{ $loop->first ? 'checked' : '' }} />
                                                       {{ $option->option_label }}
                                                       </label>
                                                  </div>
                                             @endforeach
                                        @elseif($question_type == 'date-field')
                                             @php
                                                  $next_qid = $que->questionData->next_question_id ?? '';
                                             @endphp
                                             <div class="date-container">
                                                  <input type="date" class="contract_date"
                                                       target-id="qidtarget-{{ $que->id ?? '' }}"
                                                       id="{{ $que->id ?? '' }}" name="{{ $que->id ?? '' }}"
                                                       onchange="storeAnswers(this, '{{ $que->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}')"
                                                       placeholder="dd/mm/aaaa" autocomplete="off" />
                                                  <!-- <img src="{{ asset('assets/images/icon-calendar.svg') }}"
                                                       alt="calender" class="custom-icon"> -->
                                             </div>
                                        @elseif($question_type == 'pricebox')
                                             @php
                                                  $next_qid = $que->questionData->next_question_id ?? '';
                                             @endphp
                                             <input type="text" target-id="qidtarget-{{ $que->id ?? '' }}"
                                                  id="{{ $que->id ?? '' }}" name="{{ $que->id ?? '' }}"
                                                  onkeyup="storeAnswers(this, '{{ $que->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}')"
                                                  placeholder="{{ $que->questionData->text_box_placeholder ?? '' }}"
                                                  data-placeholdertext="__________" />
                                        @elseif($question_type == 'number-field')
                                             @php
                                                  $next_qid = $que->questionData->next_question_id ?? '';
                                             @endphp
                                             <input type="text" target-id="qidtarget-{{ $que->id ?? '' }}"
                                                  id="{{ $que->id ?? '' }}" name="{{ $que->id ?? '' }}"
                                                  onkeyup="storeAnswers(this, '{{ $que->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}')"
                                                  placeholder="{{ $que->questionData->text_box_placeholder ?? '' }}"
                                                  data-placeholdertext="__________" />
                                        @elseif($question_type == 'percentage-box')
                                             @php
                                                  $next_qid = $que->questionData->next_question_id ?? '';
                                             @endphp
                                             <input type="text" target-id="qidtarget-{{ $que->id ?? '' }}"
                                                  id="{{ $que->id ?? '' }}" name="{{ $que->id ?? '' }}"
                                                  onkeyup="storeAnswers(this, '{{ $que->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}')"
                                                  placeholder="{{ $que->questionData->text_box_placeholder ?? '' }}"
                                                  data-placeholdertext="__________" />
                                        @elseif($question_type == 'dropdown-link')
                                             @php
                                                  $next_qid = $que->questionData->next_question_id ?? '';
                                             @endphp
                                             <div class="custom-select-wrapper">
                                                  <select
                                                       onchange="updateDropdownLInk(this, '{{ $que->id ?? '' }}'); storeAnswers(this, '{{ $que->id ?? '' }}','{{ $question_type ?? '' }}','{{ $next_qid ?? '' }}') "
                                                       target-id="qidtarget-{{ $que->id ?? '' }}"
                                                       id="{{ $que->id }}" name="{{ $que->id ?? '' }}">
                                                       <option
                                                       value="{{ $que->questionData->same_contract_link_label ?? '' }}"
                                                       selected>
                                                       {{ $que->questionData->same_contract_link_label ?? '' }}
                                                       </option>
                                                       @foreach ($que->options as $option)
                                                       <option my_ref_nxt=".nxt_btn_{{ $que->id ?? '' }}"
                                                            que_id="{{ $que->questionData->next_question_id ?? '' }}"
                                                            value="{{ $option->contract_link ?? '' }}">
                                                            {{ $option->option_label ?? '' }}</option>
                                                       @endforeach
                                                  </select>
                                             </div>
                                        @endif
                                   </div>
                              @endforeach
                         </div>
                         
                         <div class="col col-md-6 text_div">
                              @php
                                   $count1 = 1;
                                   $num1 = 1;
                                   $unqId = Carbon::now()->valueOf();
                                   $order1 = 1;
                                   $section_id = $section['section_id'];
                                   $txt = $section['text'];
                                   $txt2 = $section['content2'];
                                   $txt3 = $section['content3'];
                                   $section_type = $section['type'];
                                   $textAlign = $section['text_align'];
                                   $textAlignment = $section['text_alignment'];
                                   $isCondition = $section['is_condition'];
                                   $conditions = $section['conditions'];
                                   $blurr = $section['blurr_content'];
                                   
                              @endphp
                              @if(empty($section_id))
                                   <div class="standalone-question">
                                        
                                   </div>
                              @else
                              <div class="question_mapping" id="section_{{ $section['section_id'] ?? '' }}">
                                   <div class="text_block_{{ $section['section_id'] ?? '' }}">
                                        <div class="row mb-2">
                                             <div class="col-md-6">
                                                  <div class="cnt_count">
                                                       <p><b>
                                                            TID : {{ $section_id ?? '' }}
                                                       </b></p>
                                                  </div>
                                             </div>
                                             <div class="col-md-6">
                                                  <div class="text-end edit_document_text">
                                                       <span class="edit_text" data-bs-toggle="modal" data-bs-target="#textmodal{{ $section_id }}">
                                                            <i class="fa fa-edit"></i>
                                                       </span>
                                                  </div>
                                             </div>
                                        </div>
                                        <div class="modal fade" tabindex="-1" id="textmodal{{ $section_id }}">
                                             <div class="modal-dialog" role="document">
                                                  <div class="modal-content">
                                                       <div class="modal-header">
                                                            <h5 class="modal-title">Document Text</h5>
                                                            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                            <em class="icon ni ni-cross"></em>
                                                            </a>
                                                       </div>
                                                       <form action="{{ url('/admin-dashboard/update-document-right-content') }}" id="updatecontentForm" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" id="published" name="published" value="">
                                                            <input type="hidden" id="remove_content_heading" name="remove_content_heading" value="">
                                                            <input type="hidden" id="remove_content" name="remove_content" value="">
                                                            <input type="hidden" id="remove_signature" name="remove_signature" value="">
                                                            <input type="hidden" id="remove_condition" name="remove_condition" value="">
                                                            <input type="hidden" id="contentdata" name="contentdata" value="">
                                                            <input type="hidden" id="changed_content_type" name="changed_content_type" value=[]>
                                                            <input type="hidden" name="documentId" id="documentId" value="{{ $document->id ?? '' }}">

                                                            <div class="modal-body">
                                                                 <div class="add_contents">
                                                                      @if($section_type == 'content_heading')
                                                                      <div class="append_content_heading" id="content_heading{{ $section_id ?? '' }}" data-id="{{ $section_id ?? '' }}" data-is_new=false data-order_id="{{ $order1++ ?? '' }}">
                                                                           <div class="card card-bordered card-preview">
                                                                                <div class="card-inner">
                                                                                     <div class="row">
                                                                                          <div class="col-md-6 div_hding">
                                                                                               <div class="cnt_count">
                                                                                                    <p><b>
                                                                                                         TID : {{ $section_id ?? '' }}
                                                                                                    </b></p>
                                                                                               </div>
                                                                                               |
                                                                                               <div class="cnt_heding">
                                                                                                    <p class="drop_options"><b>Content Heading
                                                                                                         <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                    </p>
                                                                                               </div>
                                                                                               <div class="form-group drop_box_option" style="display:none;">
                                                                                                    <!-- <div class="text-end cut_btn">
                                                                                                         <div class="form-group">
                                                                                                              <span class="rmv_btn" onclick="removeTextDropbox(this)">
                                                                                                                   <i class="fa fa-times"></i>
                                                                                                              </span>
                                                                                                         </div>
                                                                                                    </div> -->
                                                                                                    <div class="slct_optns">
                                                                                                         <select class="form-select js-select2 " data-content-id="{{ $section_id ?? '' }}" data-change-from="content_heading" onchange="changeContentType(this)">
                                                                                                              <option value="content_heading" @selected($section_type == 'content_heading')>Headline</option>
                                                                                                              <option value="content" @selected($section_type == 'content')>Content</option>
                                                                                                              <option value="signature_field" @selected($section_type == 'signature_field')>Signature</option>
                                                                                                         </select>
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                          <div class="col-md-6 prnt_icon_cls">
                                                                                               <div class="input_icons d-flex">
                                                                                                    <span class="remove_icon red_hover" onclick="removeContent(this)" data-id="{{ $section_id ?? '' }}" data-field="content_heading" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                    {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                         <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $section_id ?? '' }}" data-field="content_heading"><i class="fa-solid fa-plus"></i></span>
                                                                                                         <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                              <a onclick="addContent('content_heading','{{ $section_id ?? '' }}','third',this)">Heading</a>
                                                                                                              <a onclick="addContent('content','{{ $section_id ?? '' }}','third',this)">Content</a>
                                                                                                              <a onclick="addContent('signature_field','{{ $section_id ?? '' }}','third',this)">Signature field</a>
                                                                                                         </div>
                                                                                                    </div> --}}
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <hr>
                                                                                     <div class="col-md-12 custom_box_{{ $section_id ?? '' }}">
                                                                                          <div class="form-group">
                                                                                               <div class="row add_content_heading">
                                                                                                    <div class="col-md-6 hide_box_{{ $section_id ?? '' }} text_align_div">
                                                                                                         @if(isset($textAlign) && $textAlign != null)
                                                                                                              <div class="form-group">
                                                                                                                   <div class="form-control-wrap">
                                                                                                                        <select class="form-select js-select2" name="text_align-{{ $section_id ?? '' }}" id="text_align{{ $section_id ?? '' }}">
                                                                                                                             <option value="" selected disabled>Select</option>
                                                                                                                             @if(isset($textAlign) && $textAlign != null)
                                                                                                                                  @if($textAlignment == 'left')
                                                                                                                                  <option value="left" selected>Align Left</option>
                                                                                                                                  @else
                                                                                                                                  <option value="left">Align Left</option>
                                                                                                                                  @endif

                                                                                                                                  @if($textAlign == 'right')
                                                                                                                                  <option value="right" selected>Align Right</option>
                                                                                                                                  @else
                                                                                                                                  <option value="right">Align Right</option>
                                                                                                                                  @endif

                                                                                                                                  @if($textAlign == 'center')
                                                                                                                                  <option value="center" selected>Align Center</option>
                                                                                                                                  @else
                                                                                                                                  <option value="center">Align Center</option>
                                                                                                                                  @endif
                                                                                                                             @else
                                                                                                                             <option value="left">Align Left</option>
                                                                                                                             <option value="right">Align Right</option>
                                                                                                                             <option value="center">Align Center</option>
                                                                                                                             @endif
                                                                                                                        </select>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         @else
                                                                                                              <div class="form-group">
                                                                                                                   <div class="form-control-wrap">
                                                                                                                        <select class="form-select js-select2 text_align" name="text_align-{{ $section_id ?? '' }}" id="text_align{{ $section_id ?? '' }}">
                                                                                                                             <option value="left" selected>Align Left</option>
                                                                                                                             <option value="right">Align Right</option>
                                                                                                                             <option value="center">Align Center</option>
                                                                                                                        </select>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         @endif
                                                                                                    </div>
                                                                                               </div>
                                                                                               <x-text-document-input-field
                                                                                                    class="form-control content_heading_html mt-2"
                                                                                                    type="text"
                                                                                                    name="content_heading_html-{{ $section_id ?? '' }}"
                                                                                                    id="content_heading_html{{ $section_id ?? '' }}"
                                                                                                    label="Text"
                                                                                                    :value="$txt ?? ''"
                                                                                               />
                                                                                          </div>
                                                                                     </div>
                                                                                </div>
                                                                           </div>
                                                                           <br>
                                                                      </div>
                                                                      @elseif($section_type == 'content')
                                                                      <div class="append_content" id="content{{ $section_id ?? '' }}" data-id="{{ $section_id ?? '' }}" data-is_new=false data-order_id="{{ $order1++ ?? '' }}">
                                                                           <div class="card card-bordered card-preview">
                                                                                <div class="card-inner">
                                                                                     <div class="row">  
                                                                                          <div class="col-md-6 div_hding" style="display: flex;">
                                                                                               <div class="cnt_count">
                                                                                                    <p><b>
                                                                                                         TID : {{ $section_id ?? '' }}
                                                                                                    </b></p>
                                                                                               </div>
                                                                                               |
                                                                                               <div class="cnt_heding">
                                                                                                    <p class="drop_options"><b>Content
                                                                                                         <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                    </p>
                                                                                               </div>
                                                                                               <div class="form-group drop_box_option" style="display:none;">
                                                                                                    <!-- <div class="text-end cut_btn">
                                                                                                         <div class="form-group">
                                                                                                              <span class="rmv_btn" onclick="removeTextDropbox(this)">
                                                                                                                   <i class="fa fa-times"></i>
                                                                                                              </span>
                                                                                                         </div>
                                                                                                    </div> -->
                                                                                                    <div class="slct_optns">
                                                                                                         <select class="form-select js-select2 " data-content-id="{{ $section_id ?? '' }}" data-change-from="content" onchange="changeContentType(this)">
                                                                                                              <option value="content_heading" @selected($section_type == 'content_heading')>Headline</option>
                                                                                                              <option value="content" @selected($section_type == 'content')>Content</option>
                                                                                                              <option value="signature_field" @selected($section_type == 'signature_field')>Signature</option>
                                                                                                         </select>
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                          <div class="col-md-6 prnt_icon_cls">
                                                                                               <div class="input_icons d-flex">
                                                                                                    <span class="remove_icon red_hover" onclick="removeContent(this)" data-id="{{ $section_id ?? '' }}" data-field="content" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                    {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                         <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $section_id ?? '' }}" data-field="content"><i class="fa-solid fa-plus"></i></span>
                                                                                                         <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                              <a onclick="addContent('content_heading','{{ $section_id ?? '' }}','third',this)">Heading</a>
                                                                                                              <a onclick="addContent('content','{{ $section_id ?? '' }}','third',this)">Content</a>
                                                                                                              <a onclick="addContent('signature_field','{{ $section_id ?? '' }}','third',this)">Signature field</a>
                                                                                                         </div>
                                                                                                    </div> --}}
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <hr>
                                                                                     <div class="col-md-12 custom_box_{{ $section_id ?? '' }}">
                                                                                     <div class="col-md-12">
                                                                                          <div class="form-group">
                                                                                               <div class="row add_content_heading">
                                                                                                    <div class="col-md-6 text_align_div">
                                                                                                         @if(isset($textAlign) && $textAlign != null)
                                                                                                              <div class="form-group">
                                                                                                                   <div class="form-control-wrap">
                                                                                                                        <select class="form-select js-select2" name="text_align-{{ $section_id ?? '' }}" id="text_align{{ $section_id ?? '' }}">
                                                                                                                             <option value="" selected disabled>Select</option>
                                                                                                                             @if(isset($textAlign) && $textAlign != null)
                                                                                                                                  @if($textAlignment == 'left')
                                                                                                                                  <option value="left" selected>Align Left</option>
                                                                                                                                  @else
                                                                                                                                  <option value="left">Align Left</option>
                                                                                                                                  @endif

                                                                                                                                  @if($textAlign == 'right')
                                                                                                                                  <option value="right" selected>Align Right</option>
                                                                                                                                  @else
                                                                                                                                  <option value="right">Align Right</option>
                                                                                                                                  @endif

                                                                                                                                  @if($textAlign == 'center')
                                                                                                                                  <option value="center" selected>Align Center</option>
                                                                                                                                  @else
                                                                                                                                  <option value="center">Align Center</option>
                                                                                                                                  @endif
                                                                                                                             @else
                                                                                                                             <option value="left">Align Left</option>
                                                                                                                             <option value="right">Align Right</option>
                                                                                                                             <option value="center">Align Center</option>
                                                                                                                             @endif
                                                                                                                        </select>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         @else
                                                                                                              <div class="form-group">
                                                                                                                   <div class="form-control-wrap">
                                                                                                                        <select class="form-select js-select2 text_align" name="text_align-{{ $section_id ?? '' }}" id="text_align{{ $section_id ?? '' }}">
                                                                                                                             <option value="left" selected>Align Left</option>
                                                                                                                             <option value="right">Align Right</option>
                                                                                                                             <option value="center">Align Center</option>
                                                                                                                        </select>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         @endif
                                                                                                    </div>
                                                                                               </div>
                                                                                               <x-text-document-input-field
                                                                                                    class="form-control content_content_html mt-2"
                                                                                                    type="textarea"
                                                                                                    name="content_content_html-{{ $section_id ?? '' }}"
                                                                                                    id="content_content_html{{ $section_id ?? '' }}"
                                                                                                    label="Text"
                                                                                                    :value="$txt ?? ''"
                                                                                               />
                                                                                          </div>
                                                                                     </div>
                                                                                     <hr>
                                                                                     @if(isset($isCondition) && $isCondition != null)
                                                                                     <div class="ad_cnd_div" id="ad_cnd_div{{ $section_id ?? '' }}">
                                                                                          <div class="append_condition" id="append_condition{{ $section_id ?? '' }}">
                                                                                               @if(isset($conditions) && $conditions != null)
                                                                                                    @foreach($conditions as $qu_conditions)
                                                                                                         @if($qu_conditions->condition_type == 'content_condition')
                                                                                                         <div class="condition-section" id="condition-section{{ $qu_conditions->id ?? '' }}" data-id="{{ $qu_conditions->id ?? '' }}" data-is_new=false>
                                                                                                              <div class="row">
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             <div class="form-control-wrap">
                                                                                                                                  <x-text-document-input-field
                                                                                                                                       class="form-select js-select2"
                                                                                                                                       type="select"
                                                                                                                                       name="$qu_conditions->id"
                                                                                                                                       id="$qu_conditions->id"
                                                                                                                                       label="Question ID"
                                                                                                                                       :questions="$questions"
                                                                                                                                       :qu_conditions="$qu_conditions"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-4">
                                                                                                                        <div class="form-group">
                                                                                                                             <div class="form-control-wrap">
                                                                                                                                  <x-text-document-input-field
                                                                                                                                       class="form-select js-select2"
                                                                                                                                       type="select_conditions"
                                                                                                                                       name="$qu_conditions->id"
                                                                                                                                       id="$qu_conditions->id"
                                                                                                                                       label="Condition"
                                                                                                                                       :questions="$questions"
                                                                                                                                       :qu_conditions="$qu_conditions"
                                                                                                                                  />
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-3">
                                                                                                                        <div class="form-group">
                                                                                                                             <x-text-document-input-field
                                                                                                                             class="form-control"
                                                                                                                             type="text"
                                                                                                                             name="condition_question_value-{{ $qu_conditions->id ?? '' }}"
                                                                                                                             id="condition_question_value-{{ $qu_conditions->id ?? '' }}"
                                                                                                                             label="Question Value"
                                                                                                                             :value="$qu_conditions->conditional_question_value ?? '' "
                                                                                                                        />
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="col-md-2 cont_add_rmv1">
                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon red_hover" onclick="removeContractCondition(this,'content')" data-id="{{ $qu_conditions->id ?? '' }}">
                                                                                                                                  <i class="fa fa-trash"></i>
                                                                                                                             </span>
                                                                                                                        </div>

                                                                                                                        <div class="form-group prnt_add_cls">
                                                                                                                             <span class="remove_icon add_icon" onclick="addContractCondition('{{ $section_id ?? '' }}','content')"><i class="fa-solid fa-add"></i></span>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                              <br>
                                                                                                         </div>
                                                                                                         @endif
                                                                                                    @endforeach
                                                                                               @endif
                                                                                          </div>
                                                                                     </div>
                                                                                     @else
                                                                                     <div class="ad_cnd_div" id="ad_cnd_div{{ $section_id ?? '' }}">
                                                                                          <div class="grey_btn_div">
                                                                                               <button type="button" class="btn btn-sm btn-primary add_btn{{ $section_id ?? '' }} grey-btn" onclick="addContractCondition('{{ $section_id ?? '' }}','content')">Add Condition</button>
                                                                                          </div>
                                                                                          <div class="append_condition" id="append_condition{{ $section_id ?? '' }}"></div>
                                                                                     </div>
                                                                                     @endif
                                                                                     <hr>
                                                                                     <div class="row">
                                                                                          <div class="col-md-6">
                                                                                               <div class="custom-control custom-checkbox">
                                                                                               @if(isset($blurr) && $blurr != null)
                                                                                                    @if($blurr == '1')
                                                                                                    <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $section_id ?? '' }}" name="secure_blurr_content{{ $section_id ?? '' }}" checked>
                                                                                                    <label class="custom-control-label" for="secure_blurr_content{{ $section_id ?? '' }}">Blur Content</label>
                                                                                                    @else
                                                                                                    <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $section_id ?? '' }}" name="secure_blurr_content{{ $section_id ?? '' }}">
                                                                                                    <label class="custom-control-label" for="secure_blurr_content{{ $section_id ?? '' }}">Blur Content</label>
                                                                                                    @endif
                                                                                               @else
                                                                                                    <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $section_id ?? '' }}" name="secure_blurr_content{{ $section_id ?? '' }}">
                                                                                                    <label class="custom-control-label" for="secure_blurr_content{{ $section_id ?? '' }}">Blur Content</label>
                                                                                               @endif
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     </div>
                                                                                </div>
                                                                           </div>
                                                                           <br>
                                                                      </div>
                                                                      @elseif($section_type == 'signature_field')
                                                                      <div class="append_signature_field" id="signature{{ $section_id ?? '' }}" data-id="{{ $section_id ?? '' }}" data-is_new=false data-order_id="{{ $order1++ ?? '' }}">
                                                                           <div class="card card-bordered card-preview">
                                                                                <div class="card-inner">
                                                                                     <div class="row">
                                                                                          <div class="col-md-6 div_hding" style="display: flex;">
                                                                                               <div class="cnt_count">
                                                                                                    <p><b>
                                                                                                         TID : {{ $section_id ?? '' }}
                                                                                                    </b></p>
                                                                                               </div>
                                                                                               |
                                                                                               <div class="cnt_heding">
                                                                                                    <p class="drop_options"><b>Signature Field
                                                                                                         <em class="icon ni ni-edit drop_options"></em></b>
                                                                                                    </p>
                                                                                               </div>
                                                                                               <div class="form-group drop_box_option" style="display:none;">
                                                                                                    <!-- <div class="text-end cut_btn">
                                                                                                         <div class="form-group">
                                                                                                              <span class="rmv_btn" onclick="removeTextDropbox(this)">
                                                                                                                   <i class="fa fa-times"></i>
                                                                                                              </span>
                                                                                                         </div>
                                                                                                    </div> -->
                                                                                                    <div class="slct_optns">
                                                                                                         <select class="form-select js-select2 " data-content-id="{{ $section_id ?? '' }}" data-change-from="signature_field" onchange="changeContentType(this)">
                                                                                                              <option value="content_heading" @selected($section_type == 'content_heading')>Headline</option>
                                                                                                              <option value="content" @selected($section_type == 'content')>Content</option>
                                                                                                              <option value="signature_field" @selected($section_type == 'signature_field')>Signature</option>
                                                                                                         </select>
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                          <div class="col-md-6 prnt_icon_cls">
                                                                                               <div class="input_icons d-flex">
                                                                                                    <span class="remove_icon red_hover" onclick="removeContent(this)" data-id="{{ $section_id ?? '' }}" data-field="signature" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                                    {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                                         <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $section_id ?? '' }}" data-field="signature"><i class="fa-solid fa-plus"></i></span>
                                                                                                         <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                                              <a onclick="addContent('content_heading','{{ $section_id ?? '' }}','third',this)">Heading</a>
                                                                                                              <a onclick="addContent('content','{{ $section_id ?? '' }}','third',this)">Content</a>
                                                                                                              <a onclick="addContent('signature_field','{{ $section_id ?? '' }}','third',this)">Signature field</a>
                                                                                                         </div>
                                                                                                    </div> --}}
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <hr>
                                                                                     <div class="col-md-12 custom_box_{{ $section_id ?? '' }}">
                                                                                          @php
                                                                                               $filledInputs = [];
                                                                                               if(!empty($txt)) $filledInputs[] = $txt;
                                                                                               if(!empty($txt2)) $filledInputs[] = $txt2;
                                                                                               if(!empty($txt3)) $filledInputs[] = $txt3;
                                                                                               $filledCount = count($filledInputs);
                                                                                               $maxInputs = 3;
                                                                                               $remainingInputs = $maxInputs - $filledCount;
                                                                                          @endphp

                                                                                          <div class="append_text" id="append_text{{ $section_id ?? '' }}">
                                                                                          @if($filledCount > 0)
                                                                                               @if(!empty($txt))
                                                                                                    <div class="row textbox_section" id="textbox_section{{ $section_id ?? '' }}">
                                                                                                         <div class="col-md-10">
                                                                                                              <div class="form-group">
                                                                                                                   <div class="row add_content_heading">
                                                                                                                        <div class="col-md-6 text_align_div">
                                                                                                                             @if(isset($textAlign) && $textAlign != null)
                                                                                                                                  <div class="form-group">
                                                                                                                                       <div class="form-control-wrap">
                                                                                                                                            <select class="form-select js-select2" name="text_align-{{ $section_id ?? '' }}" id="text_align{{ $section_id ?? '' }}">
                                                                                                                                                 <option value="" selected disabled>Select</option>
                                                                                                                                                 @if(isset($textAlign) && $textAlign != null)
                                                                                                                                                      @if($textAlignment == 'left')
                                                                                                                                                      <option value="left" selected>Align Left</option>
                                                                                                                                                      @else
                                                                                                                                                      <option value="left">Align Left</option>
                                                                                                                                                      @endif

                                                                                                                                                      @if($textAlign == 'right')
                                                                                                                                                      <option value="right" selected>Align Right</option>
                                                                                                                                                      @else
                                                                                                                                                      <option value="right">Align Right</option>
                                                                                                                                                      @endif

                                                                                                                                                      @if($textAlign == 'center')
                                                                                                                                                      <option value="center" selected>Align Center</option>
                                                                                                                                                      @else
                                                                                                                                                      <option value="center">Align Center</option>
                                                                                                                                                      @endif
                                                                                                                                                 @else
                                                                                                                                                 <option value="left">Align Left</option>
                                                                                                                                                 <option value="right">Align Right</option>
                                                                                                                                                 <option value="center">Align Center</option>
                                                                                                                                                 @endif
                                                                                                                                            </select>
                                                                                                                                       </div>
                                                                                                                                  </div>
                                                                                                                             @else
                                                                                                                                  <div class="form-group">
                                                                                                                                       <div class="form-control-wrap">
                                                                                                                                            <select class="form-select js-select2 text_align" name="text_align-{{ $section_id ?? '' }}" id="text_align{{ $section_id ?? '' }}">
                                                                                                                                                 <option value="left" selected>Align Left</option>
                                                                                                                                                 <option value="right">Align Right</option>
                                                                                                                                                 <option value="center">Align Center</option>
                                                                                                                                            </select>
                                                                                                                                       </div>
                                                                                                                                  </div>
                                                                                                                             @endif
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <div class="form-group input-box active">
                                                                                                                        <label class="form-label" for="sign_content_1">Text</label>
                                                                                                                        <input type="text" class="form-control signature_text mt-2" name="sign_content_1" id="sign_content_1" value="{{ $txt ?? '' }}">
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                         </div>
                                                                                                         <div class="col-md-2 form-group prnt_add_cls">
                                                                                                         @if($remainingInputs > 0)
                                                                                                              <span class="remove_icon add_icon" id="textbox_add_btn{{ $section_id ?? '' }}" onclick="addTextbox('{{ $section_id ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                         @endif
                                                                                                         </div>
                                                                                                    </div>

                                                                                               @endif

                                                                                               @if(!empty($txt2))
                                                                                                    <div class="row textbox_section" id="textbox_section{{ $section_id ?? '' }}">
                                                                                                         <div class="col-md-10">
                                                                                                              <div class="form-group input-box active">
                                                                                                                   <label class="form-label" for="sign_content2">Text</label>
                                                                                                                   <input type="text" class="form-control signature_text" name="sign_content2" id="sign_content2" value="{{ $txt2 }}">
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @endif

                                                                                               @if(!empty($txt3))
                                                                                                    <div class="row textbox_section" id="textbox_section{{ $section_id ?? '' }}">
                                                                                                         <div class="col-md-10">
                                                                                                              <div class="form-group input-box active">
                                                                                                                   <label class="form-label" for="sign_content3">Text</label>
                                                                                                                   <input type="text" class="form-control signature_text" name="sign_content3" id="sign_content3" value="{{ $txt3 }}">
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                               @endif
                                                                                          @else
                                                                                               <div class="row textbox_section" id="textbox_section{{ $section_id ?? '' }}">
                                                                                                    <div class="col-md-10">
                                                                                                         <div class="form-group">
                                                                                                              <div class="row add_content_heading">
                                                                                                                   <div class="col-md-6 text_align_div">
                                                                                                                        @if(isset($textAlign) && $textAlign != null)
                                                                                                                             <div class="form-group">
                                                                                                                                  <div class="form-control-wrap">
                                                                                                                                       <select class="form-select js-select2" name="text_align-{{ $section_id ?? '' }}" id="text_align{{ $section_id ?? '' }}">
                                                                                                                                            <option value="" selected disabled>Select</option>
                                                                                                                                            @if(isset($textAlign) && $textAlign != null)
                                                                                                                                                 @if($textAlignment == 'left')
                                                                                                                                                 <option value="left" selected>Align Left</option>
                                                                                                                                                 @else
                                                                                                                                                 <option value="left">Align Left</option>
                                                                                                                                                 @endif

                                                                                                                                                 @if($textAlign == 'right')
                                                                                                                                                 <option value="right" selected>Align Right</option>
                                                                                                                                                 @else
                                                                                                                                                 <option value="right">Align Right</option>
                                                                                                                                                 @endif

                                                                                                                                                 @if($textAlign == 'center')
                                                                                                                                                 <option value="center" selected>Align Center</option>
                                                                                                                                                 @else
                                                                                                                                                 <option value="center">Align Center</option>
                                                                                                                                                 @endif
                                                                                                                                            @else
                                                                                                                                            <option value="left">Align Left</option>
                                                                                                                                            <option value="right">Align Right</option>
                                                                                                                                            <option value="center">Align Center</option>
                                                                                                                                            @endif
                                                                                                                                       </select>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        @else
                                                                                                                             <div class="form-group">
                                                                                                                                  <div class="form-control-wrap">
                                                                                                                                       <select class="form-select js-select2 text_align" name="text_align-{{ $section_id ?? '' }}" id="text_align{{ $section_id ?? '' }}">
                                                                                                                                            <option value="left" selected>Align Left</option>
                                                                                                                                            <option value="right">Align Right</option>
                                                                                                                                            <option value="center">Align Center</option>
                                                                                                                                       </select>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        @endif
                                                                                                                   </div>
                                                                                                              </div>
                                                                                                              <div class="form-group input-box active">
                                                                                                                   <label class="form-label" for="sign_content">Text</label>
                                                                                                                   <input type="text" class="form-control signature_text mt-2" name="sign_content" id="sign_content" value="">
                                                                                                              </div>
                                                                                                         </div>
                                                                                                    </div>
                                                                                                    <div class="col-md-2 form-group prnt_add_cls">
                                                                                                         <span class="remove_icon add_icon" id="textbox_add_btn{{ $section_id ?? '' }}" onclick="addTextbox('{{ $section_id ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                                    </div>
                                                                                               </div>
                                                                                          @endif

                                                                                          </div>
                                                                                          <hr>
                                                                                          @if(isset($isCondition) && $isCondition != null)
                                                                                          <div class="ad_cnd_div" id="ad_cnd_div{{ $section_id ?? '' }}">
                                                                                               <div class="append_signature_condition" id="append_signature_condition{{ $section_id ?? '' }}">
                                                                                                    @if(isset($conditions) && $conditions != null)
                                                                                                         @foreach($conditions as $qu_conditions)
                                                                                                              @if($qu_conditions->condition_type == 'signature_field')
                                                                                                              <div class="condition-section" id="condition-section{{ $qu_conditions->id ?? '' }}" data-id="{{ $qu_conditions->id ?? '' }}" data-is_new=false>
                                                                                                                   <div class="row">
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group input-box active">
                                                                                                                                  <label class="form-label" for="condition_question_id-{{ $qu_conditions->id ?? '' }}">Question ID</label>
                                                                                                                                  <div class="form-control-wrap">
                                                                                                                                       <select class="form-select js-select2" data-search="on" name="condition_question_id-{{ $qu_conditions->id ?? '' }}[]" id="condition_question_id-{{ $qu_conditions->id ?? '' }}">
                                                                                                                                            @if(isset($questions) && $questions != null)
                                                                                                                                                 @foreach($questions as $question)
                                                                                                                                                      <option value="{{ $question->getName() }}"
                                                                                                                                                           {{ isset($qu_conditions->conditional_question_id) && $qu_conditions->conditional_question_id == $question->getName() ? 'selected' : '' }}>
                                                                                                                                                           {{ $question->getName() }}
                                                                                                                                                      </option>
                                                                                                                                                 @endforeach
                                                                                                                                            @endif
                                                                                                                                       </select>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-4">
                                                                                                                             <div class="form-group input-box active">
                                                                                                                                  <label class="form-label" for="conditions-{{ $qu_conditions->id ?? '' }}">Condition</label>
                                                                                                                                  <div class="form-control-wrap">
                                                                                                                                       <select class="form-select js-select2" name="conditions-{{ $qu_conditions->id ?? '' }}[]" id="conditions-{{ $qu_conditions->id ?? '' }}">
                                                                                                                                            <option value="" selected disabled>Select</option>
                                                                                                                                            @if(isset($qu_conditions->conditional_check) && $qu_conditions->conditional_check != null)
                                                                                                                                                 @if($qu_conditions->conditional_check == '1')
                                                                                                                                                 <option value="is_equal_to" selected>is equal to</option>
                                                                                                                                                 @else
                                                                                                                                                 <option value="is_equal_to">is equal to</option>
                                                                                                                                                 @endif

                                                                                                                                                 @if($qu_conditions->conditional_check == '2')
                                                                                                                                                 <option value="is_greater_than" selected>is greater than</option>
                                                                                                                                                 @else
                                                                                                                                                 <option value="is_greater_than">is greater than</option>
                                                                                                                                                 @endif

                                                                                                                                                 @if($qu_conditions->conditional_check == '3')
                                                                                                                                                 <option value="is_less_than" selected>is less than</option>
                                                                                                                                                 @else
                                                                                                                                                 <option value="is_less_than">is less than</option>
                                                                                                                                                 @endif

                                                                                                                                                 @if($qu_conditions->conditional_check == '4')
                                                                                                                                                 <option value="not_equal_to" selected>not equal to</option>
                                                                                                                                                 @else
                                                                                                                                                 <option value="not_equal_to">not equal to</option>
                                                                                                                                                 @endif

                                                                                                                                            @else
                                                                                                                                            <option value="is_equal_to">is equal to</option>
                                                                                                                                            <option value="is_greater_than">is greater than</option>
                                                                                                                                            <option value="is_less_than">is less than</option>
                                                                                                                                            <option value="not_equal_to">not equal to</option>
                                                                                                                                            @endif
                                                                                                                                       </select>
                                                                                                                                  </div>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-3">
                                                                                                                             <div class="form-group input-box active">
                                                                                                                                  <label class="form-label" for="condition_question_value-{{ $qu_conditions->id ?? '' }}">Question Value</label>
                                                                                                                                  <input type="text" class="form-control" id="condition_question_value-{{ $qu_conditions->id ?? '' }}" name="condition_question_value-{{ $qu_conditions->id ?? '' }}[]" value="{{ $qu_conditions->conditional_question_value ?? '' }}">
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2 cont_add_rmv2">
                                                                                                                             <div class="form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon red_hover" onclick="removeContractCondition(this,'signature_field')" data-id="{{ $qu_conditions->id ?? '' }}">
                                                                                                                                       <i class="fa fa-trash"></i>
                                                                                                                                  </span>
                                                                                                                             </div>
                                                                                                                             <div class="form-group prnt_add_cls">
                                                                                                                                  <span class="remove_icon add_icon" onclick="addContractCondition('{{ $section_id ?? '' }}','signature_field')"><i class="fa-solid fa-add"></i></span>
                                                                                                                             </div>
                                                                                                                        </div>
                                                                                                                   </div>
                                                                                                                   <br>
                                                                                                              </div>
                                                                                                              @endif
                                                                                                         @endforeach
                                                                                                    @endif
                                                                                               </div>
                                                                                          </div>
                                                                                          @else
                                                                                          <div class="ad_cnd_div" id="ad_cnd_div{{ $section_id ?? '' }}">
                                                                                               <div class="grey_btn_div">
                                                                                                    <button type="button" class="btn btn-sm btn-primary add_btn{{ $section_id ?? '' }} grey-btn" onclick="addContractCondition('{{ $section_id ?? '' }}','signature_field')">Add Condition</button>
                                                                                               </div>
                                                                                               <div class="append_signature_condition" id="append_signature_condition{{ $section_id ?? '' }}"></div>
                                                                                          </div>
                                                                                          @endif
                                                                                          <hr>
                                                                                          <div class="row">
                                                                                               <div class="col-md-6">
                                                                                                    <div class="custom-control custom-checkbox">
                                                                                                    @if(isset($blurr) && $blurr != null)
                                                                                                         @if($blurr == '1')
                                                                                                         <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $section_id ?? '' }}" name="secure_blurr_content{{ $section_id ?? '' }}" checked>
                                                                                                         <label class="custom-control-label" for="secure_blurr_content{{ $section_id ?? '' }}">Blur Content</label>
                                                                                                         @else
                                                                                                         <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $section_id ?? '' }}" name="secure_blurr_content{{ $section_id ?? '' }}">
                                                                                                         <label class="custom-control-label" for="secure_blurr_content{{ $section_id ?? '' }}">Blur Content</label>
                                                                                                         @endif
                                                                                                    @else
                                                                                                         <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $section_id ?? '' }}" name="secure_blurr_content{{ $section_id ?? '' }}">
                                                                                                         <label class="custom-control-label" for="secure_blurr_content{{ $section_id ?? '' }}">Blur Content</label>
                                                                                                    @endif
                                                                                                    </div>
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                </div>
                                                                           </div>
                                                                           <br>
                                                                      </div>
                                                                      @endif
                                                                 </div>
                                                                 <div class="nk-block-head-content">
                                                                      <div class="up-btn mbsc-form-group">
                                                                           <button class="btn btn-sm btn-primary saveFormdata" type="button">Update</button>
                                                                      </div>
                                                                 </div>
                                                            </div>
                                                       </form>
                                                  </div>
                                             </div>
                                        </div>
                                        <div class="mapped_text" data-section_id="{{ $standard_section_id ?? '' }}">
                                             {{-- Main Content --}}
                                             <div class="stndrd_txt">
                                                  {!! $section['text'] ?? '' !!}
                                             </div>
                                             <br>
                                             {{-- Additional Content --}}
                                             @if(!empty($section['content2']))
                                                  <div class="stndrd_txt">
                                                       {!! $section['content2'] !!}
                                                  </div>
                                                  <br>
                                             @endif
                                             @if(!empty($section['content3']))
                                                  <div class="stndrd_txt">
                                                       {!! $section['content3'] !!}
                                                  </div>
                                                  <br>
                                             @endif
                                        </div>
                                   </div>
                              </div>   
                              @endif 
                         </div>
                    </div>
               <hr>
               @endforeach
               </div>
               <div class="col col-md-3">
                    <div class="right_Section mySortableDiv">
                    @if($standardDocuments && $standardDocuments != null)
                         @foreach($standardDocuments as $standard)
                              <div class="item card card-bordered card-preview right_section_inner"
                                   id="new_section_{{ $standard->id ?? '' }}">
                                   <div class="section_name text-center fw-bold"
                                        data-id="{{ $standard->id ?? '' }}">
                                        {{ $standard->title ?? '' }}
                                   </div>
                              </div>
                              <br>
                         @endforeach
                    @endif
                    </div>
               </div>
          </div>    
     </div>
</div>

<script>
     $(function(){
          const $container = $('.qu_txt_div'); 
          const $items = $('.qutn_text_div', $container); 

          function activateSection(sectionId){
               $(".right_section_inner").removeClass("active");
               $("#new_section_" + sectionId).addClass("active");
          }

          function isVisible($el, offset = 50){
               const scrollTop = $container.scrollTop();
               const containerHeight = $container.innerHeight();
               const elTop = $el.position().top;
               const elBottom = elTop + $el.outerHeight();

               return elBottom > scrollTop + offset && elTop < scrollTop + containerHeight;
          }

          function atScrollBottom(){
               const el = $container.get(0);
               return $container.scrollTop() + $container.innerHeight() >= el.scrollHeight - 3;
          }

          function checkVisible(){
               let found = false;

               $items.each(function(){
                    const $it = $(this);
                    if (isVisible($it)) {
                         activateSection($it.data('section_id') ?? $it.attr('data-section_id'));
                         found = true;
                         return false;
                    }
               });

               if(!found && $items.length && atScrollBottom()){
                    const $last = $items.last();
                    activateSection($last.data('section_id') ?? $last.attr('data-section_id'));
               }
          }

          let ticking = false;
          $container.on('scroll', function(){
               if (!ticking) {
                    ticking = true;
                    requestAnimationFrame(function(){
                         checkVisible();
                         ticking = false;
                    });
               }
          });

          checkVisible();
     });


     function savetheFinalStep(step){
          let id = $('#SaveStep3').data('documentid');
          if(!id){
               alert('Document ID is missing. Please try again.');
               return;
          }
          $.ajax({
               url: "{{ route('admin.generator.save_final_step') }}",
               type: "POST",
               data: {
                    document_id: id,
                    step: step,
                    _token: "{{ csrf_token() }}"
               },
               success: function(response) {
                    if(response.status == true){
                         location.reload();
                         // console.log(response);
                    }
               },
               error: function(xhr) {
                    alert('An error occurred while processing your request.');
               }
          });
     }

</script>