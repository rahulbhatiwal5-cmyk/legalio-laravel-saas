@extends('admin_layout.master')
@section('content')

@php use Carbon\Carbon; @endphp
<div class="nk-content Carta_Voluntaria">
     <div class="container-fluid">
          <form action="{{ route('admin.global.text.add') }}" id="updatecontentForm" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" id="published" name="published" value="">
               <input type="hidden" id="remove_content_heading" name="remove_content_heading" value="">
               <input type="hidden" id="remove_content" name="remove_content" value="">
               <input type="hidden" id="remove_signature" name="remove_signature" value="">
               <input type="hidden" id="remove_condition" name="remove_condition" value="">
               <input type="hidden" id="formdata" name="formdata" value="">
               <input type="hidden" id="document_id" name="document_id" value="{{ $document->id ?? '' }}">
               <input type="hidden" id="changed_content_type" name="changed_content_type" value=[]>
             
               <div class="nk-block-head doc-outer-div">
                    <div class="nk-block-head-content wrapper">
                         <div class="tab">
                              @if(isset($document) && $document != null)
                              <a href="{{ route('admin.document.edit_standard_document', ['slug' => $document->slug]) }}"
                                   class="btn tab_btn">Standard Document</a>
                              @else
                              <a href="{{ route('admin.document.standard_document') }}"
                                   class="btn tab_btn">Standard Document</a>
                              @endif
                         
                              @if($id != null)
                              <a href="{{ route('admin.global.question',['id' => $id ]) }}" class="btn tab_btn" target="_blank">Document questions</a>
                              @else
                              <a href="javascript:void(0);" class="btn tab_btn">Document questions</a>
                              @endif
                              @if($id != null)
                              <a href="{{ route('admin.global.text',['id' => $id ]) }}" class="btn tab_btn active">Document Text</a>
                              @else
                              <a href="javascript:void(0);" class="btn tab_btn active">Document Text</a>
                              @endif
                         </div>
                    </div>
               </div>
               <div class="row main_section mt-4">
                    <div class="col col-md-8 left-content">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <h6>Contract Text</h6>
                                   <hr>
                                   <?php
                                        $count = 1;
                                        $num = 1;
                                        $unqId = Carbon::now()->valueOf();
                                        $order = 1;
                                   ?>
                                   <div class="add_contents mySortableDiv">
                                        @if(isset($documentRight) && $documentRight != null)
                                             @foreach($documentRight as $data)
                                                  @if($data->type == 'content_heading')
                                                  <div class="append_content_heading" id="content_heading{{ $data->id ?? '' }}" data-id="{{ $data->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                       <!-- <hr> -->
                                                       <div class="card card-bordered card-preview">
                                                            <div class="card-inner">
                                                                 <div class="row">
                                                                      {{-- <div class="col-md-1">
                                                                           <input type="checkbox" class="copy-checkbox" data-id="{{ $data->id ?? '' }}" data-field="content_heading">
                                                                      </div> --}}
                                                                      <div class="col-md-5 div_hding" style="display: flex;">
                                                                           <div class="cnt_count">
                                                                                <p><b>
                                                                                     <!-- {{ $num++ ?? '' }} -->
                                                                                     TID : {{ $data->id ?? '' }}
                                                                                </b></p>
                                                                           </div>
                                                                           |
                                                                           <div class="cnt_heding">
                                                                                <p class="drop_options"><b>Content Heading
                                                                                     <!-- <em class="icon ni ni-edit drop_options"></em> -->
                                                                                     </b>
                                                                                </p>
                                                                           </div>
                                                                           {{-- <div class="form-group drop_box_option" style="display:none;">
                                                                                <div class="text-end cut_btn">
                                                                                     <div class="form-group">
                                                                                          <span onclick="removeDropbox(this)">
                                                                                               <i class="fa fa-times"></i>
                                                                                          </span>
                                                                                     </div>
                                                                                </div>
                                                                                <div class="slct_optns">
                                                                                     <select class="form-select js-select2 " data-content-id="{{ $data->id ?? '' }}" data-change-from="content_heading" onchange="changeContentType(this)">
                                                                                          <option value="content_heading" @selected($data->type == 'content_heading')>Headline</option>
                                                                                          <option value="content" @selected($data->type == 'content')>Content</option>
                                                                                          <option value="signature_field" @selected($data->type == 'signature_field')>Signature</option>
                                                                                     </select>
                                                                                </div>
                                                                           </div> --}}
                                                                      </div>
                                                                      <div class="col-md-6 prnt_icon_cls">
                                                                           <div class="input_icons d-flex">
                                                                                {{-- <span class="step_copy_icon blue_hover remove_icon" onclick="copyLayout(this)" data-id="{{ $data->id ?? '' }}" data-field="content_heading" data-toggle="tooltip" data-placement="top" title="Copy"><i class="fa-solid fa-copy"></i></span>
                                                                                <span class="step_paste_icon remove_icon blue_hover" onclick="pasteAtCursor(this)" data-id="{{ $data->id ?? '' }}" data-field="content_heading" data-toggle="tooltip" data-placement="top" title="Paste"><i class="fa-solid fa-paste"></i></span>
                                                                                <span class="step_duplicate_icon blue_hover remove_icon" onclick="duplicateLayout(this)" data-id="{{ $data->id ?? '' }}" data-field="content_heading" data-toggle="tooltip" data-placement="top" title="Duplicate"><i class="fa-solid fa-clone"></i></span> --}}
                                                                                <span class="remove_icon red_hover" onclick="removeContent(this)" data-id="{{ $data->id ?? '' }}" data-field="content_heading"><i class="fa fa-trash" data-toggle="tooltip" data-placement="top" title="Remove"></i></span>
                                                                                {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                     <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $data->id ?? '' }}" data-field="content_heading"><i class="fa-solid fa-plus"></i></span>
                                                                                     <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                          <a onclick="addContent('content_heading','{{ $data->id ?? '' }}','third',this)">Heading</a>
                                                                                          <a onclick="addContent('content','{{ $data->id ?? '' }}','third',this)">Content</a>
                                                                                          <a onclick="addContent('signature_field','{{ $data->id ?? '' }}','third',this)">Signature field</a>
                                                                                     </div>
                                                                                </div>
                                                                                <div class="col-md-1 drag_drop_svg">
                                                                                     <img src="https://www.svgrepo.com/show/374858/drag-and-drop.svg" class="drag-handle" style="width: 20px; height: 20px; margin-right: 2px; cursor: grab;">
                                                                                </div> --}}
                                                                           </div>
                                                                      </div>
                                                                 </div>
                                                                 <hr>
                                                                 <div class="col-md-12 custom_box_{{ $data->id ?? '' }}">
                                                                      <div class="form-group">
                                                                           <div class="row add_content_heading">
                                                                                <div class="col-md-6">
                                                                                <!-- <label class="form-label active" for="content_heading_html{{ $data->id ?? '' }}">Text</label>-->
                                                                                </div>
                                                                                <div class="col-md-6 hide_box_{{ $data->id ?? '' }} text_align_div">
                                                                                     @if(isset($data->text_align) && $data->text_align != null)
                                                                                          <div class="form-group">
                                                                                               <div class="form-control-wrap">
                                                                                                    <select class="form-select js-select2" name="text_align-{{ $data->id ?? '' }}" id="text_align{{ $data->id ?? '' }}">
                                                                                                         <option value="" selected disabled>Select</option>
                                                                                                         @if(isset($data->text_align) && $data->text_align != null)
                                                                                                              @if($data->text_alignment == 'left')
                                                                                                              <option value="left" selected>Align Left</option>
                                                                                                              @else
                                                                                                              <option value="left">Align Left</option>
                                                                                                              @endif

                                                                                                              @if($data->text_align == 'right')
                                                                                                              <option value="right" selected>Align Right</option>
                                                                                                              @else
                                                                                                              <option value="right">Align Right</option>
                                                                                                              @endif

                                                                                                              @if($data->text_align == 'center')
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
                                                                                     <!-- <div class="start_append_section{{ $data->id ?? '' }}"> -->
                                                                                          <div class="form-group">
                                                                                               <div class="form-control-wrap">
                                                                                                    <select class="form-select js-select2 text_align" name="text_align-{{ $data->id ?? '' }}" id="text_align{{ $data->id ?? '' }}">
                                                                                                         <!-- <option value="" selected disabled>Select</option> -->
                                                                                                         <option value="left" selected>Align Left</option>
                                                                                                         <option value="right">Align Right</option>
                                                                                                         <option value="center">Align Center</option>
                                                                                                    </select>
                                                                                               </div>
                                                                                          </div>
                                                                                     <!-- </div> -->
                                                                                     @endif
                                                                                </div>
                                                                           </div>
                                                                           <x-text-document-input-field
                                                                                class="form-control content_heading_html mt-2"
                                                                                type="text"
                                                                                name="content_heading_html-{{ $data->id ?? '' }}"
                                                                                id="content_heading_html{{ $data->id ?? '' }}"
                                                                                label="Text"
                                                                                :value="$data->content ?? ''"
                                                                           />
                                                                      <!-- <input type="text" class="form-control content_heading_html mt-2" name="content_heading_html-{{ $data->id ?? '' }}" id="content_heading_html{{ $data->id ?? '' }}" value="{{ $data->content ?? '' }}">-->
                                                                      </div>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                       <br>
                                                  </div>
                                                  @elseif($data->type == 'content')
                                                  <div class="append_content" id="content{{ $data->id ?? '' }}" data-id="{{ $data->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                       <!-- <hr> -->
                                                       <div class="card card-bordered card-preview">
                                                            <div class="card-inner">
                                                                 <div class="row">
                                                                      {{-- <div class="col-md-1">
                                                                           <input type="checkbox" class="copy-checkbox" data-id="{{ $data->id ?? '' }}" data-field="content">
                                                                      </div> --}}
                                                                      <div class="col-md-5 div_hding" style="display: flex;">
                                                                           <div class="cnt_count">
                                                                                <p><b>
                                                                                     <!-- {{ $num++ ?? '' }} -->
                                                                                     TID : {{ $data->id ?? '' }}
                                                                                </b></p>
                                                                           </div>
                                                                           |
                                                                           <div class="cnt_heding">
                                                                                <p class="drop_options"><b>Content
                                                                                     <!-- <em class="icon ni ni-edit drop_options"></em> -->
                                                                                     </b>
                                                                                </p>
                                                                           </div>
                                                                           {{-- <div class="form-group drop_box_option" style="display:none;">
                                                                                <div class="text-end cut_btn">
                                                                                     <div class="form-group">
                                                                                          <span onclick="removeDropbox(this)">
                                                                                               <i class="fa fa-times"></i>
                                                                                          </span>
                                                                                     </div>
                                                                                </div>
                                                                                <div class="slct_optns">
                                                                                     <select class="form-select js-select2 " data-content-id="{{ $data->id ?? '' }}" data-change-from="content" onchange="changeContentType(this)">
                                                                                          <option value="content_heading" @selected($data->type == 'content_heading')>Headline</option>
                                                                                          <option value="content" @selected($data->type == 'content')>Content</option>
                                                                                          <option value="signature_field" @selected($data->type == 'signature_field')>Signature</option>
                                                                                     </select>
                                                                                </div>
                                                                           </div> --}}
                                                                      </div>
                                                                      <div class="col-md-6 prnt_icon_cls">
                                                                           <div class="input_icons d-flex">
                                                                                {{-- <span class="step_copy_icon blue_hover remove_icon" onclick="copyLayout(this)" data-id="{{ $data->id ?? '' }}" data-field="content" data-toggle="tooltip" data-placement="top" title="Copy"><i class="fa-solid fa-copy"></i></span>
                                                                                <span class="step_paste_icon remove_icon blue_hover" onclick="pasteAtCursor(this)" data-id="{{ $data->id ?? '' }}" data-field="content" data-toggle="tooltip" data-placement="top" title="Paste"><i class="fa-solid fa-paste"></i></span>
                                                                                <span class="step_duplicate_icon blue_hover remove_icon" onclick="duplicateLayout(this)" data-id="{{ $data->id ?? '' }}" data-field="content" data-toggle="tooltip" data-placement="top" title="Duplicate"><i class="fa-solid fa-clone"></i></span> --}}
                                                                                <span class="remove_icon red_hover" onclick="removeContent(this)" data-id="{{ $data->id ?? '' }}" data-field="content" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                     <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $data->id ?? '' }}" data-field="content"><i class="fa-solid fa-plus"></i></span>
                                                                                     <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                          <a onclick="addContent('content_heading','{{ $data->id ?? '' }}','third',this)">Heading</a>
                                                                                          <a onclick="addContent('content','{{ $data->id ?? '' }}','third',this)">Content</a>
                                                                                          <a onclick="addContent('signature_field','{{ $data->id ?? '' }}','third',this)">Signature field</a>
                                                                                     </div>
                                                                                </div>
                                                                                <div class="col-md-1 drag_drop_svg">
                                                                                     <img src="https://www.svgrepo.com/show/374858/drag-and-drop.svg" class="drag-handle" style="width: 20px; height: 20px; margin-right: 8px; cursor: grab;">
                                                                                </div> --}}
                                                                           </div>
                                                                      </div>
                                                                 </div>
                                                                 <hr>
                                                                 <div class="col-md-12 custom_box_{{ $data->id ?? '' }}">
                                                                 <div class="col-md-12">
                                                                      <div class="form-group">
                                                                           <div class="row add_content_heading">
                                                                                <div class="col-md-6">
                                                                                     <!--<label class="form-label" for="content_content_html{{ $data->id ?? '' }}">Text</label>-->
                                                                                </div>
                                                                                <div class="col-md-6 text_align_div">
                                                                                     @if(isset($data->text_align) && $data->text_align != null)
                                                                                          <div class="form-group">
                                                                                               <div class="form-control-wrap">
                                                                                                    <select class="form-select js-select2" name="text_align-{{ $data->id ?? '' }}" id="text_align{{ $data->id ?? '' }}">
                                                                                                         <option value="" selected disabled>Select</option>
                                                                                                         @if(isset($data->text_align) && $data->text_align != null)
                                                                                                              @if($data->text_alignment == 'left')
                                                                                                              <option value="left" selected>Align Left</option>
                                                                                                              @else
                                                                                                              <option value="left">Align Left</option>
                                                                                                              @endif

                                                                                                              @if($data->text_align == 'right')
                                                                                                              <option value="right" selected>Align Right</option>
                                                                                                              @else
                                                                                                              <option value="right">Align Right</option>
                                                                                                              @endif

                                                                                                              @if($data->text_align == 'center')
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
                                                                                                    <select class="form-select js-select2 text_align" name="text_align-{{ $data->id ?? '' }}" id="text_align{{ $data->id ?? '' }}">
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
                                                                                name="content_content_html-{{ $data->id ?? '' }}"
                                                                                id="content_content_html{{ $data->id ?? '' }}"
                                                                                label="Text"
                                                                                :value="$data->content ?? ''"
                                                                           />
                                                                           <!--<textarea class="form-control content_content_html mt-2" name="content_content_html-{{ $data->id ?? '' }}" id="content_content_html{{ $data->id ?? '' }}">{{ $data->content ?? '' }}</textarea>-->
                                                                      </div>
                                                                 </div>
                                                                 <hr>
                                                                 @if(isset($data->is_condition) && $data->is_condition != null)
                                                                 <div class="ad_cnd_div" id="ad_cnd_div{{ $data->id ?? '' }}">
                                                                      <div class="append_condition" id="append_condition{{ $data->id ?? '' }}">
                                                                           @if(isset($data->conditions) && $data->conditions != null)
                                                                                @foreach($data->conditions as $qu_conditions)
                                                                                     @if($qu_conditions->condition_type == 'content_condition')
                                                                                     <div class="condition-section" id="condition-section{{ $qu_conditions->id ?? '' }}" data-id="{{ $qu_conditions->id ?? '' }}" data-is_new=false>
                                                                                          <!-- <hr> -->

                                                                                          <div class="row">
                                                                                               <div class="col-md-3">
                                                                                                    <div class="form-group">
                                                                                                         <!--<label class="form-label" for="condition_question_id-{{ $qu_conditions->id ?? '' }}">Question ID</label>-->
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
                                                                                                              <!--<select class="form-select js-select2" data-search="on" name="condition_question_id-{{ $qu_conditions->id ?? '' }}[]" id="condition_question_id-{{ $qu_conditions->id ?? '' }}">
                                                                                                                   @if(isset($questions) && $questions != null)
                                                                                                                        @foreach($questions as $question)
                                                                                                                             <option value="{{ $question->getName() }}"
                                                                                                                                  {{ isset($qu_conditions->conditional_question_id) && $qu_conditions->conditional_question_id == $question->getName() ? 'selected' : '' }}>
                                                                                                                                  {{ $question->getName() }}
                                                                                                                             </option>
                                                                                                                        @endforeach
                                                                                                                   @endif
                                                                                                              </select>-->
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>
                                                                                               <div class="col-md-4">
                                                                                                    <div class="form-group">
                                                                                                         <!--<label class="form-label" for="conditions-{{ $qu_conditions->id ?? '' }}">Condition</label>-->
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
                                                                                                              <!--<select class="form-select js-select2" name="conditions-{{ $qu_conditions->id ?? '' }}[]" id="conditions-{{ $qu_conditions->id ?? '' }}">
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
                                                                                                              </select>-->
                                                                                                         </div>
                                                                                                    </div>
                                                                                               </div>
                                                                                               <div class="col-md-3">
                                                                                                    <div class="form-group">
                                                                                                         <!--<label class="form-label" for="condition_question_value-{{ $qu_conditions->id ?? '' }}">Question Value</label>
                                                                                                    <input type="text" class="form-control" id="condition_question_value-{{ $qu_conditions->id ?? '' }}" name="condition_question_value-{{ $qu_conditions->id ?? '' }}[]" value="{{ $qu_conditions->conditional_question_value ?? '' }}">-->
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

                                                                                               <div class="col-md-1 form-group prnt_add_cls">
                                                                                                    <span class="remove_icon red_hover" onclick="removeCondition(this,'content')" data-id="{{ $qu_conditions->id ?? '' }}">
                                                                                                         <i class="fa fa-trash"></i>
                                                                                                    </span>
                                                                                               </div>

                                                                                               <div class="col-md-1 form-group prnt_add_cls">
                                                                                                    <span class="remove_icon add_icon" onclick="addCondition('{{ $data->id ?? '' }}','content')"><i class="fa-solid fa-add"></i></span>
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
                                                                 <div class="ad_cnd_div" id="ad_cnd_div{{ $data->id ?? '' }}">
                                                                      <div class="grey_btn_div">
                                                                           <button type="button" class="btn btn-sm btn-primary add_btn{{ $data->id ?? '' }} grey-btn" onclick="addCondition('{{ $data->id ?? '' }}','content')">Add Condition</button>
                                                                      </div>
                                                                      <div class="append_condition" id="append_condition{{ $data->id ?? '' }}"></div>
                                                                 </div>
                                                                 @endif
                                                                 <hr>
                                                                 <div class="row">
                                                                      <div class="col-md-6">
                                                                           <!-- <p class="p_label">Blur Content</p> -->
                                                                           <div class="custom-control custom-checkbox">
                                                                           @if(isset($data->secure_blur_content) && $data->secure_blur_content != null)
                                                                                @if($data->secure_blur_content == '1')
                                                                                <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $data->id ?? '' }}" name="secure_blurr_content{{ $data->id ?? '' }}" checked>
                                                                                <label class="custom-control-label" for="secure_blurr_content{{ $data->id ?? '' }}">Blur Content</label>
                                                                                @else
                                                                                <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $data->id ?? '' }}" name="secure_blurr_content{{ $data->id ?? '' }}">
                                                                                <label class="custom-control-label" for="secure_blurr_content{{ $data->id ?? '' }}">Blur Content</label>
                                                                                @endif
                                                                           @else
                                                                           <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $data->id ?? '' }}" name="secure_blurr_content{{ $data->id ?? '' }}">
                                                                           <label class="custom-control-label" for="secure_blurr_content{{ $data->id ?? '' }}">Blur Content</label>
                                                                           @endif
                                                                           </div>
                                                                      </div>
                                                                 </div>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                       <br>
                                                  </div>
                                                  @elseif($data->type == 'signature_field')
                                                  <div class="append_signature_field" id="signature{{ $data->id ?? '' }}" data-id="{{ $data->id ?? '' }}" data-is_new=false data-order_id="{{ $order++ ?? '' }}">
                                                       <!-- <hr> -->
                                                       <div class="card card-bordered card-preview">
                                                            <div class="card-inner">
                                                                 <div class="row">
                                                                      {{-- <div class="col-md-1">
                                                                           <input type="checkbox" class="copy-checkbox" data-id="{{  $data->id ?? '' }}" data-field="signature_field">
                                                                      </div> --}}
                                                                      <div class="col-md-5 div_hding" style="display: flex;">
                                                                           <div class="cnt_count">
                                                                                <p><b>
                                                                                     <!-- {{ $num++ ?? '' }} -->
                                                                                     TID : {{ $data->id ?? '' }}
                                                                                </b></p>
                                                                           </div>
                                                                           |
                                                                           <div class="cnt_heding">
                                                                                <p class="drop_options"><b>Signature Field
                                                                                     <!-- <em class="icon ni ni-edit drop_options"></em> -->
                                                                                     </b>
                                                                                </p>
                                                                           </div>
                                                                           {{-- <div class="form-group drop_box_option" style="display:none;">
                                                                                <div class="text-end cut_btn">
                                                                                     <div class="form-group">
                                                                                          <span onclick="removeDropbox(this)">
                                                                                               <i class="fa fa-times"></i>
                                                                                          </span>
                                                                                     </div>
                                                                                </div>
                                                                                <div class="slct_optns">
                                                                                     <select class="form-select js-select2 " data-content-id="{{ $data->id ?? '' }}" data-change-from="signature_field" onchange="changeContentType(this)">
                                                                                          <option value="content_heading" @selected($data->type == 'content_heading')>Headline</option>
                                                                                          <option value="content" @selected($data->type == 'content')>Content</option>
                                                                                          <option value="signature_field" @selected($data->type == 'signature_field')>Signature</option>
                                                                                     </select>
                                                                                </div>
                                                                           </div> --}}
                                                                      </div>
                                                                      <div class="col-md-6 prnt_icon_cls">
                                                                           <div class="input_icons d-flex">
                                                                                {{-- <span class="step_copy_icon blue_hover remove_icon" onclick="copyLayout(this)" data-id="{{ $data->id ?? '' }}" data-field="signature" data-toggle="tooltip" data-placement="top" title="Copy"><i class="fa-solid fa-copy"></i></span>
                                                                                <span class="step_paste_icon remove_icon blue_hover" onclick="pasteAtCursor(this)" data-id="{{ $data->id ?? '' }}" data-field="signature" data-toggle="tooltip" data-placement="top" title="Paste"><i class="fa-solid fa-paste"></i></span>
                                                                                <span class="step_duplicate_icon blue_hover remove_icon" onclick="duplicateLayout(this)" data-id="{{ $data->id ?? '' }}" data-field="signature" data-toggle="tooltip" data-placement="top" title="Duplicate"><i class="fa-solid fa-clone"></i></span> --}}
                                                                                <span class="remove_icon red_hover" onclick="removeContent(this)" data-id="{{ $data->id ?? '' }}" data-field="signature" data-toggle="tooltip" data-placement="top" title="Remove"><i class="fa fa-trash"></i></span>
                                                                                {{-- <div class="dropdown" data-toggle="tooltip" data-placement="top" title="Add">
                                                                                     <span class="step_add_icon blue_hover remove_icon dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{ $data->id ?? '' }}" data-field="signature"><i class="fa-solid fa-plus"></i></span>
                                                                                     <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                                          <a onclick="addContent('content_heading','{{ $data->id ?? '' }}','third',this)">Heading</a>
                                                                                          <a onclick="addContent('content','{{ $data->id ?? '' }}','third',this)">Content</a>
                                                                                          <a onclick="addContent('signature_field','{{ $data->id ?? '' }}','third',this)">Signature field</a>
                                                                                     </div>
                                                                                </div>
                                                                                <div class="col-md-1 drag_drop_svg">
                                                                                     <img src="https://www.svgrepo.com/show/374858/drag-and-drop.svg" class="drag-handle" style="width: 20px; height: 20px; margin-right: 8px; cursor: grab;">
                                                                                </div> --}}
                                                                           </div>
                                                                      </div>
                                                                 </div>
                                                                 <hr>
                                                                 <div class="col-md-12 custom_box_{{ $data->id ?? '' }}">
                                                                      @php
                                                                           $filledInputs = [];
                                                                           if (!empty($data->content)) $filledInputs[] = $data->content;
                                                                           if (!empty($data->content2)) $filledInputs[] = $data->content2;
                                                                           if (!empty($data->content3)) $filledInputs[] = $data->content3;
                                                                           $filledCount = count($filledInputs);
                                                                           $maxInputs = 3;
                                                                           $remainingInputs = $maxInputs - $filledCount;
                                                                      @endphp

                                                                      <div class="append_textBox" id="append_textBox{{ $data->id ?? '' }}">

                                                                      @if($filledCount > 0)
                                                                           @if (!empty($data->content))
                                                                                <div class="row textbox_section" id="textbox_section{{ $data->id ?? '' }}">
                                                                                     <div class="col-md-10">
                                                                                          <div class="form-group">
                                                                                               <div class="row add_content_heading">
                                                                                                    <div class="col-md-6">
                                                                                                         <!--<label class="form-label" for="sign_content_1">Text</label>-->
                                                                                                    </div>
                                                                                                    <div class="col-md-6 text_align_div">
                                                                                                         @if(isset($data->text_align) && $data->text_align != null)
                                                                                                              <div class="form-group">
                                                                                                                   <div class="form-control-wrap">
                                                                                                                        <select class="form-select js-select2" name="text_align-{{ $data->id ?? '' }}" id="text_align{{ $data->id ?? '' }}">
                                                                                                                             <option value="" selected disabled>Select</option>
                                                                                                                             @if(isset($data->text_align) && $data->text_align != null)
                                                                                                                                  @if($data->text_alignment == 'left')
                                                                                                                                  <option value="left" selected>Align Left</option>
                                                                                                                                  @else
                                                                                                                                  <option value="left">Align Left</option>
                                                                                                                                  @endif

                                                                                                                                  @if($data->text_align == 'right')
                                                                                                                                  <option value="right" selected>Align Right</option>
                                                                                                                                  @else
                                                                                                                                  <option value="right">Align Right</option>
                                                                                                                                  @endif

                                                                                                                                  @if($data->text_align == 'center')
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
                                                                                                                        <select class="form-select js-select2 text_align" name="text_align-{{ $data->id ?? '' }}" id="text_align{{ $data->id ?? '' }}">
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
                                                                                                    <input type="text" class="form-control signature_text mt-2" name="sign_content_1" id="sign_content_1" value="{{ $data->content ?? '' }}">
                                                                                               </div>
                                                                                          </div>
                                                                                     </div>
                                                                                     <div class="col-md-2 form-group prnt_add_cls">
                                                                                     @if($remainingInputs > 0)
                                                                                          <span class="remove_icon add_icon" id="textbox_add_btn{{ $data->id ?? '' }}" onclick="addTextbox('{{ $data->id ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                     @endif
                                                                                     </div>
                                                                                </div>

                                                                           @endif

                                                                           @if (!empty($data->content2))
                                                                                <div class="row textbox_section" id="textbox_section{{ $data->id ?? '' }}">
                                                                                     <div class="col-md-10">
                                                                                          <div class="form-group input-box active">
                                                                                               <label class="form-label" for="sign_content2">Text</label>
                                                                                               <input type="text" class="form-control signature_text" name="sign_content2" id="sign_content2" value="{{ $data->content2 }}">
                                                                                          </div>
                                                                                     </div>
                                                                                </div>

                                                                           @endif

                                                                           @if (!empty($data->content3))
                                                                                <div class="row textbox_section" id="textbox_section{{ $data->id ?? '' }}">
                                                                                     <div class="col-md-10">
                                                                                          <div class="form-group input-box active">
                                                                                               <label class="form-label" for="sign_content3">Text</label>
                                                                                               <input type="text" class="form-control signature_text" name="sign_content3" id="sign_content3" value="{{ $data->content3 }}">
                                                                                          </div>
                                                                                     </div>
                                                                                </div>
                                                                           @endif
                                                                      @else
                                                                           <div class="row textbox_section" id="textbox_section{{ $data->id ?? '' }}">
                                                                                <div class="col-md-10">
                                                                                     <div class="form-group">
                                                                                          <div class="row add_content_heading">
                                                                                               <div class="col-md-6">
                                                                                               <!-- <label class="form-label" for="sign_content">Text</label>-->
                                                                                               </div>
                                                                                               <div class="col-md-6 text_align_div">
                                                                                                    @if(isset($data->text_align) && $data->text_align != null)
                                                                                                         <div class="form-group">
                                                                                                              <div class="form-control-wrap">
                                                                                                                   <select class="form-select js-select2" name="text_align-{{ $data->id ?? '' }}" id="text_align{{ $data->id ?? '' }}">
                                                                                                                        <option value="" selected disabled>Select</option>
                                                                                                                        @if(isset($data->text_align) && $data->text_align != null)
                                                                                                                             @if($data->text_alignment == 'left')
                                                                                                                             <option value="left" selected>Align Left</option>
                                                                                                                             @else
                                                                                                                             <option value="left">Align Left</option>
                                                                                                                             @endif

                                                                                                                             @if($data->text_align == 'right')
                                                                                                                             <option value="right" selected>Align Right</option>
                                                                                                                             @else
                                                                                                                             <option value="right">Align Right</option>
                                                                                                                             @endif

                                                                                                                             @if($data->text_align == 'center')
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
                                                                                                                   <select class="form-select js-select2 text_align" name="text_align-{{ $data->id ?? '' }}" id="text_align{{ $data->id ?? '' }}">
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
                                                                                     <span class="remove_icon add_icon" id="textbox_add_btn{{ $data->id ?? '' }}" onclick="addTextbox('{{ $data->id ?? '' }}')"><i class="fa-solid fa-add"></i></span>
                                                                                </div>
                                                                           </div>
                                                                      @endif

                                                                      </div>
                                                                      <hr>
                                                                      @if(isset($data->is_condition) && $data->is_condition != null)
                                                                      <div class="ad_cnd_div" id="ad_cnd_div{{ $data->id ?? '' }}">
                                                                           <div class="append_signature_condition" id="append_signature_condition{{ $data->id ?? '' }}">
                                                                                @if(isset($data->conditions) && $data->conditions != null)
                                                                                     @foreach($data->conditions as $qu_conditions)
                                                                                          @if($qu_conditions->condition_type == 'signature_field')
                                                                                          <div class="condition-section" id="condition-section{{ $qu_conditions->id ?? '' }}" data-id="{{ $qu_conditions->id ?? '' }}" data-is_new=false>
                                                                                               <!-- <hr> -->
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
                                                                                                    <div class="col-md-1 form-group prnt_add_cls">
                                                                                                         <span class="remove_icon red_hover" onclick="removeCondition(this,'signature_field')" data-id="{{ $qu_conditions->id ?? '' }}">
                                                                                                              <i class="fa fa-trash"></i>
                                                                                                         </span>
                                                                                                    </div>
                                                                                                    <div class="col-md-1 form-group prnt_add_cls">
                                                                                                         <span class="remove_icon add_icon" onclick="addCondition('{{ $data->id ?? '' }}','signature_field')"><i class="fa-solid fa-add"></i></span>
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
                                                                      <div class="ad_cnd_div" id="ad_cnd_div{{ $data->id ?? '' }}">
                                                                           <div class="grey_btn_div">
                                                                                <button type="button" class="btn btn-sm btn-primary add_btn{{ $data->id ?? '' }} grey-btn" onclick="addCondition('{{ $data->id ?? '' }}','signature_field')">Add Condition</button>
                                                                           </div>
                                                                           <div class="append_signature_condition" id="append_signature_condition{{ $data->id ?? '' }}"></div>
                                                                      </div>
                                                                      @endif
                                                                      <hr>
                                                                      <div class="row">
                                                                           <div class="col-md-6">
                                                                                <!-- <p class="p_label">Blur Content</p> -->
                                                                                <div class="custom-control custom-checkbox">
                                                                                @if(isset($data->secure_blur_content) && $data->secure_blur_content != null)
                                                                                     @if($data->secure_blur_content == '1')
                                                                                     <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $data->id ?? '' }}" name="secure_blurr_content{{ $data->id ?? '' }}" checked>
                                                                                     <label class="custom-control-label" for="secure_blurr_content{{ $data->id ?? '' }}">Blur Content</label>
                                                                                     @else
                                                                                     <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $data->id ?? '' }}" name="secure_blurr_content{{ $data->id ?? '' }}">
                                                                                     <label class="custom-control-label" for="secure_blurr_content{{ $data->id ?? '' }}">Blur Content</label>
                                                                                     @endif
                                                                                @else
                                                                                <input type="checkbox" class="custom-control-input" id="secure_blurr_content{{ $data->id ?? '' }}" name="secure_blurr_content{{ $data->id ?? '' }}">
                                                                                <label class="custom-control-label" for="secure_blurr_content{{ $data->id ?? '' }}">Blur Content</label>
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
                                             @endforeach
                                        @endif
                                   </div>
                                   <br>
                                   <div class="text-end">
                                        <div class="dropdown">
                                             <button type="button" class="btn btn-primary question_dropbtn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add Content</button>
                                             <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                  <a onclick="addContent('content_heading','{{ $unqId }}','first')">Heading</a>
                                                  <a onclick="addContent('content','{{ $unqId }}','first')">Content</a>
                                                  <a onclick="addContent('signature_field','{{ $unqId }}','first')">Signature field</a>
                                             </div>
                                        </div>
                                   </div> 
                              </div>
                         </div>
                    </div>
                    <div class="col col-md-4 right-content">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="d-flex justify-content-between align-items-center">
                                        {{-- <div class="nk-block-head-content butn-cls">
                                             <div class="mbsc-form-group view_btn">
                                                  @if(isset($document) && $document->published == '1')
                                                  <a href="{{ url('/contracts/'.$slug) }}" class="view_page" target="_blank">View Page</a>
                                                  @else
                                                  <a href="javascript:void(0);" class="view_page" onclick="isNotView()">View Page</a>
                                                  @endif
                                             </div>
                                        </div> --}}
                                        <div class="nk-block-head-content">
                                             <div class="up-btn mbsc-form-group">
                                                  <button class="btn btn-sm btn-primary" type="button" id="saveFormdata">Save</button>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </form>
     </div>
</div>

<!-- jQuery and Popper.js (Required for Bootstrap) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
     $(document).ready(function(){
          $('.mySortableDiv').sortable({
               handle: '.drag-handle',
               update: function(event, ui) {
                    // Call this function when the user drops and the order changes
                    updateOrderIds();
               }
          });
     });
</script>


<script>
     const contentClassMap = {
          content_heading: "append_content_heading",
          content: "append_content",
          signature_field: "append_signature_field",
     };

     const contentTemplates = {
          "content_heading": (content_id) => `<div class="form-group">
                                             <div class="row">
                                                  <div class="col-md-6">
                                                      <!-- <label class="form-label" for="content_heading_html${content_id}">Text</label>-->
                                                  </div>
                                                  <div class="col-md-6 text_align_div">
                                                       <div class="form-group">
                                                            <div class="form-control-wrap">
                                                                 <select class="form-select js-select2 text_align" name="text_align-${content_id}" id="text_align${content_id}">
                                                                      <option value="left" selected>Align Left</option>
                                                                      <option value="right">Align Right</option>
                                                                      <option value="center">Align Center</option>
                                                                 </select>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="form-group input-box active">
                                                <label class="form-label" for="content_heading_html${content_id}">Text</label>
                                                <input type="text" class="form-control new_heading_html mt-2" name="content_heading_html-${content_id}" id="content_heading_html${content_id}" value="">
                                                 </div>
                                             </div>`,
          "content": (content_id) => `<div class="col-md-12">
                                        <div class="form-group">
                                             <div class="row">
                                                  <div class="col-md-6">
                                                       <!--<label class="form-label" for="content_content_html">Text</label>-->
                                                  </div>
                                                  <div class="col-md-6 text_align_div">
                                                       <div class="form-group">
                                                            <div class="form-control-wrap">
                                                                 <select class="form-select js-select2 text_align" name="text_align-${content_id}" id="text_align${content_id}">
                                                                      <option value="left" selected>Align Left</option>
                                                                      <option value="right">Align Right</option>
                                                                      <option value="center">Align Center</option>
                                                                 </select>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="form-group input-box active">
                                                 <label class="form-label" for="content_content_html">Text</label>

                                             <textarea class="form-control content_content_html mt-2" name="content_content_html-${content_id}" id="content_content_html${content_id}"></textarea>
                                            </div>
                                             </div>
                                   </div>
                                   <hr>
                                   <div class="ad_cnd_div" id="ad_cnd_div${content_id}">
                                        <div class="grey_btn_div">
                                             <button type="button" class="btn btn-sm btn-primary add_btn${content_id} grey-btn" onclick="addCondition('${content_id}','content')">Add Condition</button>
                                        </div>
                                        <div class="append_condition" id="append_condition${content_id}"></div>
                                   </div>
                                   <hr>
                                   <div class="row">
                                        <div class="col-md-6">
                                             <!-- <p class="p_label">Blur Content</p> -->
                                             <div class="custom-control custom-checkbox">
                                                  <input type="checkbox" class="custom-control-input" id="secure_blurr_content${content_id}" name="secure_blurr_content${content_id}">
                                                  <label class="custom-control-label" for="secure_blurr_content${content_id}">Blur Content</label>
                                             </div>
                                        </div>
                                   </div>`,
          'signature_field':(content_id) =>
                                        `<div class="append_textBox" id="append_textBox${content_id}">
                                   <div class="row textbox_section" id="textbox_section${content_id}">
                                        <div class="col-md-10">
                                             <div class="form-group">
                                                  <div class="row">
                                                       <div class="col-md-6">
                                                            <!--<label class="form-label" for="sign_content">Text</label>-->
                                                       </div>
                                                       <div class="col-md-6 text_align_div">
                                                            <div class="form-group">
                                                                 <div class="form-control-wrap">
                                                                      <select class="form-select js-select2 text_align" name="text_align-${content_id}" id="text_align${content_id}">
                                                                           <option value="left" selected>Align Left</option>
                                                                           <option value="right">Align Right</option>
                                                                           <option value="center">Align Center</option>
                                                                      </select>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                                <div class="form-group input-box active">
                                                     <label class="form-label" for="sign_content">Text</label>
                                                    <input type="text" class="form-control signature_text mt-2" name="sign_content" id="sign_content" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 form-group prnt_add_cls">
                                             <span class="remove_icon add_icon" id="textbox_add_btn${content_id}" onclick="addTextbox('${content_id}')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                   </div>
                              </div>
                              <hr>
                              <div class="ad_cnd_div" id="ad_cnd_div${content_id}">
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary add_btn${content_id} grey-btn" onclick="addCondition('${content_id}','signature_field')">Add Condition</button>
                                   </div>
                                   <div class="append_signature_condition" id="append_signature_condition${content_id}"></div>
                              </div>
                              <hr>
                              <div class="row">
                                   <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                             <input type="checkbox" class="custom-control-input" id="secure_blurr_content${content_id}" name="secure_blurr_content${content_id}">
                                             <label class="custom-control-label" for="secure_blurr_content${content_id}">Blur Content</label>
                                        </div>
                                   </div>
                              </div>`,
     }

     function changeContentType(e) {
          var content_id = $(e).data("content-id");
          var change_from = $(e).data("change-from");
          var change_to = $(e).val();
          var option_name = '';

          $(e).data("change-from", change_to);
          var hiddenInput = $("#changed_content_type");
          var existingChanges = JSON.parse(hiddenInput.val());

          let mainBoxID = `.custom_box_${content_id}`;
          let hideBoxID = `.hide_box_${content_id}`;
          let main = $(e).closest(`.${contentClassMap[change_from]}`);
          main.removeClass(`${contentClassMap[change_from]}`).addClass(`${contentClassMap[change_to]}`);

          if(change_from === "content_heading" && change_to === "content"){
               const value = $(mainBoxID).find('input[type="text"]').val();
               $(mainBoxID).html(contentTemplates[change_to](content_id));
               $(mainBoxID).find('textarea').val(value);

          }else if(change_from === "content" && change_to === "content_heading") {
               const value = $(mainBoxID).find('textarea').val();
               $(mainBoxID).html(contentTemplates[change_to](content_id));
               $(mainBoxID).find('input[type="text"]').val(value);
               $(hideBoxID).hide();
          }else{

               let value = '';
               if($(mainBoxID).find('textarea').length){
                    value = $(mainBoxID).find('textarea').val();
               }else if($(mainBoxID).find('input[type="text"]').length){
                    value = $(mainBoxID).find('input[type="text"]').val();
               }

               $(mainBoxID).html(contentTemplates[change_to](content_id));
               $(mainBoxID).find('input[type="text"]').val(value);
          }

          var foundIndex = existingChanges.findIndex((q) => q.content_id === content_id);
          if(foundIndex !== -1){
               existingChanges[foundIndex].change_to = change_to;
          }else{
               existingChanges.push({ content_id: content_id, change_from: change_from, change_to: change_to });
          }
          hiddenInput.val(JSON.stringify(existingChanges));

          if(change_to == "content"){
               option_name = 'Content';
          }else if(change_to == "content_heading"){
               option_name = 'Content Heading';
          }else if(change_to == "signature_field"){
               option_name = 'Signature Field';
          }

          $(e).closest('.col-md-5').find('.cnt_heding').show();
          $(e).closest('.col-md-5').find('.cnt_heding').html(`<p class="drop_options"><b>${option_name} <em class="icon ni ni-edit drop_options"></em></b></p>`);
          $(e).closest('.col-md-5').find('.drop_box_option').hide();

     }

     $(document).on('change', '.type_content', function() {
          const value = $(this).val();
          const id = $(this).attr('id').replace('content_type', '');
          addContent(value, id, 'second');
     });

     let heading_section_count = 0;
     let content_section_count = 0;
     let num = "{{ $order ?? '' }}";

     function addContent(name,id,key,element=null){
          const newUniqueId = Date.now();
          let html = ``;

          if(name === 'content_heading'){
               heading_section_count++ ;
               html = `<div class="append_content_heading new_cont_sec${newUniqueId}" id="content_heading${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                    <!-- <hr> -->
                    <div class="card card-bordered card-preview">
                         <div class="card-inner">
                              <div class="text-end">
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <span class="col-md-2 offset-md-10">
                                                  <span class="remove_icon red_hover" onclick="removeContent(this)" value="appended" data-field="content_heading"><i class="fa fa-trash"></i></span>
                                             </span>
                                        </div>
                                   </div>
                              </div>
                              <div class="row add_content_heading">
                                   <div class="col-md-6">
                                        <select class="form-select js-select2 type_content" name="content_type${newUniqueId}" id="content_type${newUniqueId}">
                                             <option value="content_heading" ${name === 'content_heading' ? 'selected' : ''}>Headline</option>
                                             <option value="content" ${name === 'content' ? 'selected' : ''}>Content</option>
                                             <option value="signature_field" ${name === 'signature_field' ? 'selected' : ''}>Signature</option>
                                        </select>
                                   </div>
                              </div>
                              <hr>
                              <div class="col-md-12">

                                        <div class="row">
                                             <div class="col-md-6">
                                                  <!--<label class="form-label" for="content_heading_html${newUniqueId}">Text</label>-->
                                             </div>
                                             <div class="col-md-6 text_align_div">
                                                  <div class="form-group">
                                                       <div class="form-control-wrap">
                                                            <select class="form-select js-select2 text_align" name="text_align-${newUniqueId}" id="text_align${newUniqueId}">
                                                                 <option value="left" selected>Align Left</option>
                                                                 <option value="right">Align Right</option>
                                                                 <option value="center">Align Center</option>
                                                            </select>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                    <div class="form-group input-box active">
                                         <label class="form-label" for="content_heading_html${newUniqueId}">Text</label>

                                        <input type="text" class="form-control new_heading_html mt-2" name="content_heading_html-${newUniqueId}" id="content_heading_html${newUniqueId}" value="">
                                   </div>
                              </div>
                         </div>
                    </div>
                    <br>
               </div>`
          }else if(name === 'content'){
               content_section_count++ ;

               html = `<div class="append_content new_cont_sec${newUniqueId}" id="content${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                    <!-- <hr> -->
                    <div class="card card-bordered card-preview">
                         <div class="card-inner">
                              <div class="text-end">
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <span class="col-md-2 offset-md-10">
                                                  <span class="remove_icon red_hover" onclick="removeContent(this)" value="appended" data-field="content"><i class="fa fa-trash"></i></span>
                                             </span>
                                        </div>
                                   </div>
                              </div>
                              <div class="row">
                                   <div class="col-md-6">
                                        <select class="form-select js-select2 type_content" name="content_type${newUniqueId}" id="content_type${newUniqueId}">
                                             <option value="content_heading" ${name === 'content_heading' ? 'selected' : ''}>Headline</option>
                                             <option value="content" ${name === 'content' ? 'selected' : ''}>Content</option>
                                             <option value="signature_field" ${name === 'signature_field' ? 'selected' : ''}>Signature</option>
                                        </select>
                                   </div>

                              </div>
                              <hr>
                              <div class="col-md-12">
                                        <div class="row">
                                             <div class="col-md-6">
                                                <!--<label class="form-label" for="content_content_html">Text</label>-->
                                                  </div>
                                             <div class="col-md-6 text_align_div">
                                                  <div class="form-group">
                                                       <div class="form-control-wrap">
                                                            <select class="form-select js-select2 text_align" name="text_align-${newUniqueId}" id="text_align${newUniqueId}">
                                                                 <option value="left" selected>Align Left</option>
                                                                 <option value="right">Align Right</option>
                                                                 <option value="center">Align Center</option>
                                                            </select>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                    <div class="form-group input-box active">
                                        <label class="form-label" for="content_content_html">Text</label>
                                        <textarea class="form-control content_content_html mt-2" name="content_content_html-${newUniqueId}" id="content_content_html${newUniqueId}"></textarea>
                                   </div>
                              </div>
                              <hr>
                              <div class="ad_cnd_div" id="ad_cnd_div${newUniqueId}">
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary add_btn${newUniqueId} grey-btn" onclick="addCondition('${newUniqueId}','content')">Add Condition</button>
                                   </div>
                                   <div class="append_condition" id="append_condition${newUniqueId}"></div>
                              </div>
                              <hr>
                              <div class="row">
                                   <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                             <input type="checkbox" class="custom-control-input" id="secure_blurr_content${newUniqueId}" name="secure_blurr_content${newUniqueId}">
                                             <label class="custom-control-label" for="secure_blurr_content${newUniqueId}">Blur Content</label>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <br>
               </div>`
          }else if(name === 'signature_field'){
               // $('#signature_field_hidden').val(1);
               html = `<div class="append_signature_field new_cont_sec${newUniqueId}" id="signature${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                    <!-- <hr> -->
                    <div class="card card-bordered card-preview">
                         <div class="card-inner">
                              <div class="text-end">
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <span class="col-md-2 offset-md-10">
                                                  <span class="remove_icon red_hover" onclick="removeContent(this)" value="appended" data-field="signature"><i class="fa fa-trash"></i></span>
                                             </span>
                                        </div>
                                   </div>
                              </div>
                              <hr>
                              <div class="row">
                                   <div class="col-md-6">
                                        <select class="form-select js-select2 type_content" name="content_type${newUniqueId}" id="content_type${newUniqueId}">
                                             <option value="content_heading" ${name === 'content_heading' ? 'selected' : ''}>Headline</option>
                                             <option value="content" ${name === 'content' ? 'selected' : ''}>Content</option>
                                             <option value="signature_field" ${name === 'signature_field' ? 'selected' : ''}>Signature</option>
                                        </select>
                                   </div>

                              </div>
                              <hr>
                              <!-- <div class="col-md-12">
                                  <p class="p_label">This is signature field</p>
                                   <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="signature_field${newUniqueId}" name="signature_field-${newUniqueId}">
                                        <label class="custom-control-label" for="signature_field${newUniqueId}"></label>
                                   </div>
                              </div> -->
                              <div class="append_textBox" id="append_textBox${newUniqueId}">
                                   <div class="row textbox_section" id="textbox_section${newUniqueId}">
                                        <div class="col-md-10">
                                             <!--<div class="form-group">-->
                                                  <div class="row">
                                                       <div class="col-md-6">
                                                            <!--<label class="form-label" for="sign_content">Text</label>-->
                                                       </div>
                                                       <div class="col-md-6 text_align_div">
                                                            <div class="form-group">
                                                                 <div class="form-control-wrap">
                                                                      <select class="form-select js-select2 text_align" name="text_align-${newUniqueId}" id="text_align${newUniqueId}">
                                                                           <option value="left" selected>Align Left</option>
                                                                           <option value="right">Align Right</option>
                                                                           <option value="center">Align Center</option>
                                                                      </select>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                                <div class="form-group input-box active">
                                                    <label class="form-label" for="sign_content">Text</label>
                                                  <input type="text" class="form-control signature_text mt-2" name="sign_content" id="sign_content" value="">
                                             </div>
                                        </div>
                                        <div class="col-md-2 form-group prnt_add_cls">
                                             <span class="remove_icon add_icon" id="textbox_add_btn${newUniqueId}" onclick="addTextbox('${newUniqueId}')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                   </div>
                              </div>
                              <hr>
                              <div class="ad_cnd_div" id="ad_cnd_div${newUniqueId}">
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary add_btn${newUniqueId} grey-btn" onclick="addCondition('${newUniqueId}','signature_field')">Add Condition</button>
                                   </div>
                                   <div class="append_signature_condition" id="append_signature_condition${newUniqueId}"></div>
                              </div>
                              <hr>
                              <div class="row">
                                   <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                             <input type="checkbox" class="custom-control-input" id="secure_blurr_content${newUniqueId}" name="secure_blurr_content${newUniqueId}">
                                             <label class="custom-control-label" for="secure_blurr_content${newUniqueId}">Blur Content</label>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <br>
               </div>`;
          }

          if(key === 'first'){
               $('.add_contents').append(html);
               $('.question_dropbtn').hide();
          }else if(key === 'second'){
               $('.new_cont_sec'+id).replaceWith(html);
               $('.question_dropbtn').hide();
          }else if(key === 'third'){
               if(!element) {
                    console.error("No element provided for 'third' layout insertion.");
                    return;
               }

               let $clickedBtn = $(element);
               let $nearestSection = $clickedBtn.closest(".add_contents > div");
               let $insertedElement;

               if($nearestSection.length){
                    alert("Layout has been added");
                    $nearestSection.before(html);
                    // $insertedElement = $nearestSection.next();
               }else{
                    $(".add_contents").append(html);
                    // $insertedElement = $(".add_qu_sec").children().last();
               }

               // if($insertedElement.length){
               //      $insertedElement[0].scrollIntoView({ behavior: "smooth", block: "center" });
               // }

               updateOrderIds();

          }

          num++ ;
     }


     function removeContent(e){
          $('.question_dropbtn').show();
          if($(e).attr('data-field') === 'content_heading'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_content_heading').remove();
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).attr('data-id');
                              let deleteIds = $('#remove_content_heading').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_content_heading').val(deleteIds);
                              $('#content_heading'+id).hide();
                         }
                    });
               }
          }else if($(e).attr('data-field') === 'content'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_content').remove();
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).attr('data-id');
                              let deleteIds = $('#remove_content').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_content').val(deleteIds);
                              $('#content'+id).hide();
                         }
                    });
               }
          }else if($(e).attr('data-field') === 'signature'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_signature_field').remove();
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).attr('data-id');
                              let deleteIds = $('#remove_signature').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_signature').val(deleteIds);
                              $('#signature'+id).hide();
                         }
                    });
               }
          }
     }

     let textCount = 0;
     function addTextbox(id){
          console.log(id);
          textCount++ ;
          const parentDiv = $('#append_textBox' + id);
          if(parentDiv.find('.textbox_section').length === 2){
               $('#textbox_add_btn'+id).hide();
          }

          let html = `<div class="row textbox_section" id="textbox_section${id}">
                    <div class="col-md-10">
                         <div class="form-group input-box active">
                              <label class="form-label" for="new_sign_content${textCount}">Text</label>
                              <input type="text" class="form-control signature_text" name="new_sign_content-${textCount}[]" id="new_sign_content${textCount}" value="">
                         </div>
                    </div>
               </div>`;


          console.log(html);
          $('#append_textBox'+id).append(html);

     }

     function removeTextbox(e){
          if($(e).attr('value') === 'appended'){
               Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
               }).then((result) => {
                    if(result.isConfirmed){
                         $(e).closest('.add_textbox').remove();
                    }
               });
          }
     }

     let condition_count = 0;
     function addCondition(id,type){
          condition_count++ ;

          if(type == 'content'){
               const html = `<div class="condition-section" id="condition-section${id}" value="appended" data-is_new=true>
                         <div class="row">
                              <div class="col-md-3">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="condition_question_id-${condition_count}">Question ID</label>
                                        <div class="form-control-wrap question">
                                             <select class="form-select js-select2 condition_question_id" data-search="on" name="condition_question_id-${condition_count}[]" id="condition_question_id-${condition_count}">
                                                  @if(isset($questions) && $questions != null)
                                                       @foreach($questions as $question)
                                                            <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                       @endforeach
                                                  @endif
                                             </select>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-md-4">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="conditions-${condition_count}">Condition</label>
                                        <div class="form-control-wrap">
                                             <select class="form-select js-select2 conditions" name="conditions-${condition_count}[]" id="conditions-${condition_count}">
                                                  <option value="" selected disabled>Select</option>
                                                  <option value="is_equal_to">is equal to</option>
                                                  <option value="is_greater_than">is greater than</option>
                                                  <option value="is_less_than">is less than</option>
                                                  <option value="not_equal_to">not equal to</option>
                                             </select>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-md-3">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="condition_question_value-${condition_count}">Question Value</label>
                                        <input type="text" class="form-control new_condition_question_value" id="condition_question_value-${condition_count}" name="condition_question_value-${condition_count}[]" value="">
                                   </div>
                              </div>
                              <div class="col-md-1 form-group prnt_add_cls">
                                    <span class="remove_icon red_hover" onclick="removeCondition(this,'content')" value="appended">
                                        <i class="fa fa-trash"></i>
                                   </span>
                              </div>
                              <div class="col-md-1 form-group prnt_add_cls">
                                   <span class="remove_icon add_icon" onclick="addCondition('${id}','content')"><i class="fa-solid fa-add"></i></span>
                              </div>
                         </div>
                         <br>
                    </div> `
               $('#append_condition'+id).append(html);
               $('.add_btn'+id).hide();
          }else if(type == 'signature_field'){
               const html = `<div class="condition-section" id="condition-section${id}" value="appended" data-is_new=true>
                         <div class="row">
                              <div class="col-md-3">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="condition_question_id-${condition_count}">Question ID</label>
                                        <div class="form-control-wrap question">
                                             <select class="form-select js-select2 condition_question_id" data-search="on" name="condition_question_id-${condition_count}[]" id="condition_question_id-${condition_count}">
                                                  @if(isset($questions) && $questions != null)
                                                       @foreach($questions as $question)
                                                            <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                       @endforeach
                                                  @endif
                                             </select>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-md-4">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="conditions-${condition_count}">Condition</label>
                                        <div class="form-control-wrap">
                                             <select class="form-select js-select2 conditions" name="conditions-${condition_count}[]" id="conditions-${condition_count}">
                                                  <option value="" selected disabled>Select</option>
                                                  <option value="is_equal_to">is equal to</option>
                                                  <option value="is_greater_than">is greater than</option>
                                                  <option value="is_less_than">is less than</option>
                                                  <option value="not_equal_to">not equal to</option>
                                             </select>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-md-3">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="condition_question_value-${condition_count}">Question Value</label>
                                        <input type="text" class="form-control condition_question_value" id="condition_question_value-${condition_count}" name="condition_question_value-${condition_count}[]" value="">
                                   </div>
                              </div>
                              <div class="col-md-1 form-group prnt_add_cls">
                                   <span class="remove_icon red_hover" onclick="removeCondition(this,'signature_field')" value="appended">
                                        <i class="fa fa-trash"></i>
                                   </span>
                              </div>
                              <div class="col-md-1 form-group prnt_add_cls">
                                   <span class="remove_icon add_icon" onclick="addCondition('${id}','signature_field')"><i class="fa-solid fa-add"></i></span>
                              </div>
                         </div>
                         <br>
                    </div> `
               $('#append_signature_condition'+id).append(html);
               $('.add_btn'+id).hide();
          }


     }

     function removeCondition(e,type){
          if(type == 'content'){
               if($(e).attr('value') === 'appended'){
                    const uniqueId = $(e).closest('.condition-section').attr('id').replace('condition-section', '');
                    console.log(uniqueId);

                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.condition-section').remove();
                              const parentDiv = $('#append_condition' + uniqueId);
                              if(parentDiv.find('.condition-section').length === 0){
                                   $('.add_btn'+uniqueId).show();
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              console.log(id);
                              let deleteIds = $('#remove_condition').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              } else {
                                   deleteIds = id;
                              }
                              $('#remove_condition').val(deleteIds);
                              $('#condition-section' + id).hide();

                              const container = $(e).closest('.append_condition');
                              const uniqueId = $(container).attr('id').replace('append_condition', '');

                              if(container.find('.condition-section:visible').length === 0){
                                   console.log('if');
                                   const html = `<div class="grey_btn_div"><button type="button" class="btn btn-sm btn-primary add_btn${uniqueId} grey-btn" onclick="addCondition('${uniqueId}','content')">Add Condition</button></div>
                                        <div class="append_condition" id="append_condition${uniqueId}"></div>`;

                                   $('#ad_cnd_div' + uniqueId).html(html);
                              }

                         }
                    });
               }
          }else if(type == 'signature_field'){
               if($(e).attr('value') === 'appended'){
                    const uniqueId = $(e).closest('.condition-section').attr('id').replace('condition-section', '');
                    console.log(uniqueId);

                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.condition-section').remove();
                              const parentDiv = $('#append_signature_condition' + uniqueId);
                              if(parentDiv.find('.condition-section').length === 0){
                                   $('.add_btn'+uniqueId).show();
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#remove_condition').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              } else {
                                   deleteIds = id;
                              }
                              $('#remove_condition').val(deleteIds);
                              $('#condition-section' + id).hide();

                              const container = $(e).closest('.append_signature_condition');
                              const uniqueId = $(container).attr('id').replace('append_signature_condition', '');

                              if(container.find('.condition-section:visible').length === 0){
                                   console.log('if');
                                   const html = `<div class="grey_btn_div"><button type="button" class="btn btn-sm btn-primary add_btn${uniqueId} grey-btn" onclick="addCondition('${uniqueId}','content')">Add Condition</button></div>
                                        <div class="append_signature_condition" id="append_signature_condition${uniqueId}"></div>`;

                                   $('#ad_cnd_div' + uniqueId).html(html);
                              }

                         }
                    });
               }
          }

     }

     $(document).ready(function() {
          $(document).on('change', '[id^="add_condition"]', function() {
               const id = $(this).attr('id').replace('add_condition', '');
               conditionalOptions(id);
          });

          $(document).on('change', '[id^="start_new_section"]', function() {
               const id = $(this).attr('id').replace('start_new_section', '');
               startNewSection(id);
          });

          $(document).on('change', '[id^="signature_field"]', function() {
               const id = $(this).attr('id').replace('signature_field', '');
               toggleCheckboxValue($(this), 'signature_field' + id);
          });

          $(document).on('change', '[id^="secure_blurr_content"]', function() {
               const id = $(this).attr('id').replace('secure_blurr_content', '');
               goToSteps(id);
          });

          $(document).on('change', '[id^="blurr_content"]', function() {
               const id = $(this).attr('id').replace('blurr_content', '');
               toggleCheckboxValue($(this), 'blurr_content' + id);
          });
     });

     function conditionalOptions(id) {
          if ($('#add_condition' + id).is(':checked')) {
               $('.add_condition_section' + id).show();
               $('#add_condition' + id).val(1);
          } else {
               $('.add_condition_section' + id).hide();
               $('#add_condition' + id).val(0);
          }
     }

     function goToSteps(id) {
          if($('#secure_blurr_content' + id).is(':checked')) {
               $('#secure_blurr_content' + id).val(1);
          }else {
               $('#secure_blurr_content' + id).val(0);
          }
     }

     function toggleCheckboxValue(element, id) {
          if(element.is(':checked')) {
               $('#' + id).val(1);
          } else {
               $('#' + id).val(0);
          }
     }
</script>

<script>

     function getAllContents() {
          var contents = [];

          $('.add_contents .append_content_heading').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');
               var headingInputValue = $(this).find('input[type="text"]').val();
               var textAlign = $(this).find('select[name^="text_align"]').val() || '';

               contents.push({
                    section: 'content_heading',
                    id: id,
                    is_new: is_new,
                    heading_html: headingInputValue,
                    text_align: textAlign,
                    order_id: order_id,
               });
          });

          $('.add_contents .append_content').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');
               var textAlign = $(this).find('select[name^="text_align"]').val() || '';
               var contentHtml = $(this).find('textarea[name^="content_content_html"]').val();
               var contentClass = $(this).find('input[name^="content_class"]').val() || '';
               var secureBlurrContent = $(this).find('input[name^="secure_blurr_content"]').is(':checked') ? 1 : 0;

               var contentData = {
                    section: 'content',
                    is_new: is_new,
                    id: id,
                    text_align: textAlign,
                    content_html: contentHtml,
                    content_class: contentClass,
                    add_condition: 0,
                    secure_blurr_content: secureBlurrContent,
                    conditions: [],
                    new_conditions: [],
                    order_id: order_id,
               };

               if($(this).find('.append_condition .condition-section').length > 0){
                    $(this).find('.append_condition .condition-section').each(function () {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var new_condition = {
                                   question_id: $(this).find('select[name^="condition_question_id"]').val() || '',
                                   condition: $(this).find('select[name^="conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(new_condition.question_id && new_condition.condition || new_condition.question_value) {
                                   contentData.new_conditions.push(new_condition);
                                   contentData.add_condition = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   question_id: $(this).find('select[name^="condition_question_id"]').val() || '',
                                   condition: $(this).find('select[name^="conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId
                              };

                              if(condition.question_id && condition.condition || condition.question_value) {
                                   contentData.conditions.push(condition);
                                   contentData.add_condition = 1;
                              }
                         }
                    });
               }
               contents.push(contentData);
          });

          $('.add_contents .append_signature_field').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');
               var textAlign = $(this).find('select[name^="text_align"]').val() || '';
               var content =  $(this).find('input[name^="sign_content_1"]').val() || '';
               var content2 =  $(this).find('input[name^="sign_content2"]').val() || '';
               var content3 =  $(this).find('input[name^="sign_content3"]').val() || '';
               var sign_content =  $(this).find('input[name="sign_content"]').val() || '';
               var new_sign_content = [];
               $(this).find('input[name^="new_sign_content"]').each(function(){
                    new_sign_content.push($(this).val());
               });

               var secureBlurrContent = $(this).find('input[name^="secure_blurr_content"]').is(':checked') ? 1 : 0;

               var contentData = {
                    section: 'signature_field',
                    id: id,
                    is_new: is_new,
                    text_align: textAlign,
                    content: content,
                    content2: content2,
                    content3: content3,
                    sign_content: sign_content,
                    new_sign_content: new_sign_content,
                    secure_blurr_content: secureBlurrContent,
                    order_id: order_id,
                    add_condition: 0,
                    conditions: [],
                    new_conditions: [],
               };

               if($(this).find('.append_signature_condition .condition-section').length > 0){
                    $(this).find('.append_signature_condition .condition-section').each(function () {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var new_condition = {
                                   question_id: $(this).find('select[name^="condition_question_id"]').val() || '',
                                   condition: $(this).find('select[name^="conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(new_condition.question_id && new_condition.condition || new_condition.question_value) {
                                   contentData.new_conditions.push(new_condition);
                                   contentData.add_condition = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   question_id: $(this).find('select[name^="condition_question_id"]').val() || '',
                                   condition: $(this).find('select[name^="conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId
                              };

                              if(condition.question_id && condition.condition || condition.question_value) {
                                   contentData.conditions.push(condition);
                                   contentData.add_condition = 1;
                              }
                         }
                    });
               }
               contents.push(contentData);
          });

          // console.log(contents);
          return contents;
     }

     $(document).ready(function () {
          $('#saveFormdata').click(function (e) {
               var data = getAllContents();
               console.log(data);
               $('#formdata').val(JSON.stringify(data));

               var documentName = $('#document_id').val();
               let hasError = false;

               $(".new_heading_html").each(function(){
                    if (!hasError && !$(this).val()) {
                         NioApp.Toast('Please fill the heading HTML field', 'error', { position: 'top-right' });
                         hasError = true;
                         return false;
                    }
               });


               $(".content_content_html").each(function(){
                    const uniqueId = $(this).attr('id').replace('content_content_html', '');

                    if (!hasError && !$(this).val().trim()) {
                         NioApp.Toast('Please fill the Text field', 'error', { position: 'top-right' });
                         hasError = true;
                         return false;
                    }
               });

               $('.ad_cnd_div').each(function (){
                    const uniqueId = $(this).attr('id').replace('ad_cnd_div', '');
                    if(!hasError){
                         const appendSection = $('#append_condition' + uniqueId);
                         const conditionSections = appendSection.find('.condition-section');

                         let conditionInvalid = false;
                         conditionSections.find('select').each(function(){
                              if (!$(this).val()) {
                                   conditionInvalid = true;
                                   return false; // Breaks the .each loop
                              }
                         });

                         if(conditionInvalid){
                              NioApp.Toast('Please fill in all required condition fields.', 'error', { position: 'top-right' });
                              hasError = true;
                              return false;
                         }
                    }
               });

               if(!hasError){
                    $('#updatecontentForm').submit();
               }

          });
     });

     $(document).ready(function(){
          $('body').delegate('.drop_options','click', function(){
               $(this).closest('.col-md-5').find('.cnt_heding').hide();
               $(this).closest('.col-md-5').find('.drop_box_option').show();
          });
     });

     function removeDropbox(e){
          $(e).closest('.col-md-5').find('.drop_box_option').hide();
          $(e).closest('.col-md-5').find('.cnt_heding').show();
     }

     function duplicateLayout(element) {
          let $element = $(element);
          let questionType = $element.data("field");
          let dataId = $element.data("id");
          let $elementToCopy = null;

          const typeMappings = {
               'content_heading': `#content_heading${dataId}`,
               'content': `#content${dataId}`,
               'signature': `#signature${dataId}`,
          };

          if(typeMappings[questionType]){
               $elementToCopy = $(typeMappings[questionType]);
          }

          if($elementToCopy && $elementToCopy.length){
               let copiedHTML = $elementToCopy.prop("outerHTML");

               copiedHTML = copiedHTML
                    .replace(/data-is_new=".*?"/g, 'data-is_new="true"')
                    .replace(/data-id=".*?"/g, 'value="appended"');

               let $copiedElement = $($.parseHTML(copiedHTML));
               if (!$copiedElement.length) return;


               let $section = $element.closest(".add_contents > div");
               if ($section.length) {
                    $section.after($copiedElement);
               } else {
                    $(".add_contents").append($copiedElement);
               }

               $copiedElement[0].scrollIntoView({ behavior: "smooth", block: "start" });
               alert("Layout has been duplicated.");

               updateOrderIds();
          }
     }

     function copyLayout(element) {
          console.log('jhgdjfhjdf');

          let contentType = $(element).data('field');
          let dataId = $(element).data('id');
          let elementToCopy = null;

          const typeMappings = {
               'content_heading': `#content_heading${dataId}`,
               'content': `#content${dataId}`,
               'signature': `#signature${dataId}`,
          };

          if (typeMappings[contentType]) {
               elementToCopy = $(typeMappings[contentType])[0]; // Get the DOM element
          }

          console.log(typeMappings[contentType]);

          if (elementToCopy) {
               let copiedHTML = elementToCopy.outerHTML;

               copiedHTML = copiedHTML
                    .replace(/data-is_new=".*?"/g, 'data-is_new="true"')
                    .replace(/data-id=".*?"/g, 'value="appended"');

               localStorage.setItem("copiedContent", copiedHTML);

               alert("Layout has been copied to your clipboard. You can now paste it anywhere.");
          }
     }

     function pasteAtCursor(element){
          let copiedHTML = localStorage.getItem("copiedContent");
          let copiedContentLayout = localStorage.getItem("copiedContentLayout");

          if(copiedHTML){
               if(!element) {
                    return;
               }

               let $clickedBtn = $(element);
               let $nearestSection = $clickedBtn.closest(".add_contents > div");
               let $insertedElement;

               if($nearestSection.length){
                    alert("Layout has been pasted.");
                    $nearestSection.before(copiedHTML);
               }else{
                    $(".add_contents").append(copiedHTML);
               }

               updateOrderIds();
               localStorage.removeItem("copiedContent");
          }else if(copiedContentLayout){
               let layouts = JSON.parse(copiedContentLayout);

               if (!element) {
                    console.error("No element provided for layout insertion.");
                    return;
               }

               let $clickedBtn = $(element);
               let $nearestSection = $clickedBtn.closest(".add_contents > div");

               if ($nearestSection.length) {
                    layouts.forEach(function (layoutHTML) {
                         $nearestSection.before(layoutHTML);
                    });
               } else {
                    layouts.forEach(function (layoutHTML) {
                         $(".add_contents").append(layoutHTML);
                    });
               }

               alert("Copied layouts have been pasted.");
               updateOrderIds();
               localStorage.removeItem("copiedContentLayout");

          }
          else{
               alert("Please copy a layout first.");
          }
     }

     function updateOrderIds() {
          $(".add_contents [data-order_id]").each(function(index) {
               $(this).attr("data-order_id", index + 1);
          });

          // let $parent = $(".add_contents");
          // let $elements = $parent.children().detach(); // Detach to preserve order

          // $elements.each(function(index) {
          //      let $countElement = $(this).find(".cnt_count p b");
          //      if ($countElement.length) {
          //           $countElement.html(index + 1);
          //      }
          //      $parent.append($(this));
          // });
     }

     function copySelectedLayout(element){
          let copiedLayouts = [];

          $('.copy-checkbox:checked').each(function () {
               let $checkbox = $(this);
               let dataId = $checkbox.data("id");
               let contentType = $checkbox.data('field');

               const typeMappings = {
                    'content_heading': `#content_heading${dataId}`,
                    'content': `#content${dataId}`,
                    'signature': `#signature${dataId}`,
               };

               if (typeMappings[contentType]) {
                    elementToCopy = $(typeMappings[contentType])[0]; // Get the DOM element
               }
               let $element = $(typeMappings[$checkbox.data("field")]);

               if ($element.length) {
                    let html = $element.prop("outerHTML")
                         .replace(/data-is_new=".*?"/g, 'data-is_new="true"')
                         .replace(/data-id=".*?"/g, 'value="appended"');
                    copiedLayouts.push(html);
               }
          });

          if (copiedLayouts.length > 0) {
               localStorage.setItem("copiedContentLayout", JSON.stringify(copiedLayouts));
               //alert("Selected layouts have been copied.");
          } else {
               //alert("No layouts selected.");
          }
     }


     $(document).on('change', '.copy-checkbox',function() {
          copySelectedLayout();

     });


</script>


@endsection
