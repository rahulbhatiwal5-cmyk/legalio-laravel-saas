@extends('admin_layout.master')

<style>
       /* This css is for add categories */
        .col-md-12 .select2-container {
            width: 100% !important;
        }

        .select2-selection--multiple {
            max-height: 120px;
            overflow-y: auto !important;
        }

        .select2-selection__rendered {
            white-space: normal !important;
            word-wrap: break-word;
        }
        
    /* 17 march */
                @media screen and (max-width:575px) {
            .new-inner-doc-outr .nk-block-head-content {
                flex-direction: column !important;
                gap: 20px !important;
            }


            #short_des_Ai {
                white-space: nowrap;
            }
        }

        /* 19 march */
                  .inner_new_side-crd {
                    display: flex;
                    gap: 20px;
                }

                .inner_new_side-crd .new-card-preview {
                    margin: 0 !important;
                }

                .inner_new_side-crd .card-preview {
                    width: 100%;
                }

                .inner_new_side-crd .new-card-preview {
                    flex: 0 0 30%;
                }

                #iner-frm-wrp {
                    display: flex;
                    flex-direction: column;
                    margin: 0;
                }
</style>
@section('content')

<div class="nk-content">
    <div class="container-fluid">
        @if(isset($document) && $document != null)
        <form action="{{ url('/admin-dashboard/update-document') }}" id="documentForm" method="post"
            enctype="multipart/form-data">
        @else
        <form action="{{ route('admin.dashboard.add_documents') }}" id="documentForm" method="post"
            enctype="multipart/form-data">
        @endif
            @csrf
            <input type="hidden" name="id" value="{{ $document->id ?? '' }}">
            <input type="hidden" name="img_sec_ids" id="img_sec_ids" value="">
            <input type="hidden" name="slug" id="slug" value="{{ $document->slug ?? '' }}">
            <input type="hidden" name="published" id="published" value="{{ $document->published ?? '' }}">
            <input type="hidden" name="field_img_id" id="field_img_id" value="">
            <input type="hidden" name="ag_img_id" id="ag_img_id" value="">
            <input type="hidden" name="faq_ids" id="faq_ids" value="">

            @if(isset($document) && $document != null)
            <div class="col-md-12 doc-title mt-4 pb-4">
                <h3>{{ $document->title ?? '' }}</h3>
            </div>
            @endif
            <div class="nk-block-head doc-outer-div new-inner-doc-outr">
                <div class="nk-block-head-content wrapper ">
                    <div class="tab">
                        {{-- @if(isset($document) && $document != null)
                        <a href="{{ url('admin-dashboard/document-generator/?id='.$document->id) }}" class="btn tab_btn" target="_blank">Document Generator</a>
                        @else
                        <a href="{{ url('admin-dashboard/document-generator') }}" class="btn tab_btn" target="_blank">Document Generator</a>
                        @endif --}}

                         @if(isset($document) && $document != null)
                        <a href="{{ route('admin.dashboard.edit_documents',['slug' => $document->slug]) }}"
                            class="btn tab_btn active">Frontpage</a>
                        @else
                        <a href="{{ route('admin.dashboard.documents') }}"
                            class="btn tab_btn active">Document</a>
                        @endif

                        @if(isset($document) && $document != null)
                        <a href="{{ url('admin-dashboard/document-questions/?id='.$document->id) }}"
                            class="btn tab_btn" target="_blank">Document Questions</a>
                        @else
                        <a href="javascript:void(0);" class="btn tab_btn">Document Questions</a>
                        @endif

                        @if(isset($document) && $document != null)
                        <a href="{{ url('admin-dashboard/document-right-content/?id='.$document->id) }}"
                            class="btn tab_btn" target="_blank">Document Text</a>
                        @else
                        <a href="javascript:void(0);" class="btn tab_btn">Document Text</a>
                        @endif

                        @if(isset($document) && $document != null)
                        <a href="{{ url('/admin-dashboard/document-contract-edit?id='.$document->id) }}" target="_blank" class="btn tab_btn">Contract Editor</a>
                        @else
                        <a href="javascript:void(0);" class="btn tab_btn" style="opacity:.5; cursor:not-allowed;">Contract Editor</a>
                        @endif
                    </div>
                    @if(isset($document) && $document != null)
                    <div class="doc-top-butns2">
                        <div class="form-group">
                            <button type="button" class="btn btn-light" onclick="validateAndRunAiAutofill()">
                            AI Autofill
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="row main_section_div">
                <div class="col col-md-8 doc-left-content">
                    <div class="inner_new_side-crd">
                        <div class="card card-bordered card-preview  new-card-preview-one" data-box-id="1000">
                            <div class="card-inner">
                                @if(isset($document) && $document != null)
                                <div class="col-md-12 doc-title">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="form-label mb-2" for="title">
                                                <b>
                                                    <h4>
                                                        Document Title
                                                        <small><b>{Document_Title}</b></small>
                                                    </h4>
                                                </b>
                                            </label>
                                        </div>
                                        <input type="text" class="form-control form-control-lg" id="title" name="title"
                                            placeholder="Add title" value="{{ $document->title ?? '' }}">
                                    </div>
                                </div>
                                @else
                                <div class="col-md-12 doc-title">
                                    <div class="form-group">
                                        <label class="form-label" for="title"><b>
                                                <h3>
                                                    Document Title <small><b>{Document_Title}</b></small>
                                                </h3>
                                            </b></label>
                                        <input type="text" class="form-control form-control-lg" id="title" name="title"
                                            placeholder="Add title" value="{{ old('title') }}">
                                        <span id="title-error" style="color:red; display:none;"></span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- box-2 --}}
                        @if(isset($document) && $document != null)
                        <div class="card card-bordered card-preview new-card-preview">
                            <div class="card-inner">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-2">Document Image
                                    </h5>
                                    {{-- <button type="button" class="btn btn-light m-1 ai-autofill-box-btn"
                                        onclick="window.dispatchEvent(new CustomEvent('openAiModal', { detail: { title: 'AI Modal', id: 1002 ,document_id:{{ $document->id ?? '' }}} }))">
                                        AI Autofill
                                    </button> --}}
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group" style="width:140px; height:140px;">
                                        <img src="{{ $document->document_image }}"
                                            alt="{{ $document->document_image }}">
                                    </div>
                                    <br>
                                <button type="button" class="MYBTN btn-sm btn-primary"  data-bs-toggle="modal" data-bs-target="#generateSVG">Edit</button>
                                </div>
                                <div class="modal fade" tabindex="-1" id="generateSVG">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit SVG</h5>
                                                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                    <em class="icon ni ni-cross"></em>
                                                </a>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control image_name_input" id="image_name"
                                                            name="image_name[]" placeholder="Line-1" value="{{ old('image_name.0', explode('@', $document->name_on_image ?? '')[0] ?? '') }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control image_name_input" id="image_name"
                                                            name="image_name[]" placeholder="Line-2" value="{{ old('image_name.1', explode('@', $document->name_on_image ?? '')[1] ?? '') }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control image_name_input" id="image_name"
                                                            name="image_name[]" placeholder="Line-3" value="{{ old('image_name.2', explode('@', $document->name_on_image ?? '')[2] ?? '') }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control image_name_input" id="image_name"
                                                            name="image_name[]" placeholder="Line-4" value="{{ old('image_name.3', explode('@', $document->name_on_image ?? '')[3] ?? '') }}">
                                                        </div>
                                                    </div>
                                                    <div id="error-message" style="color: red; display: none;"></div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <div class="nk-block-head-content">
                                                    <div class="up-btn mbsc-form-group">
                                                        <button class="btn btn-sm btn-primary" type="button" onclick="generateSVG()">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="row gy-12 mt-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <!-- <label class="form-label" for="">Name on Image<span
                                                    class="required_field">*</span></label> -->
                                            <div class="row">
                                                <div class="col">
                                                    <input type="text" class="form-control" id="image_name"
                                                    name="image_name[]" placeholder="Line-1" value="{{ old('image_name.0', explode('@', $document->name_on_image ?? '')[0] ?? '') }}">
                                                </div>

                                                <div class="col">
                                                    <input type="text" class="form-control" id="image_name"
                                                    name="image_name[]" placeholder="Line-2" value="{{ old('image_name.1', explode('@', $document->name_on_image ?? '')[1] ?? '') }}">
                                                </div>

                                                <div class="col">
                                                    <input type="text" class="form-control" id="image_name"
                                                    name="image_name[]" placeholder="Line-3" value="{{ old('image_name.2', explode('@', $document->name_on_image ?? '')[2] ?? '') }}">
                                                </div>

                                                <div class="col">
                                                    <input type="text" class="form-control" id="image_name"
                                                    name="image_name[]" placeholder="Line-4" value="{{ old('image_name.3', explode('@', $document->name_on_image ?? '')[3] ?? '') }}">
                                                </div>
                                            </div>

                                            @error('image_name')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- box-3 --}}
                    <div class="card card-bordered card-preview mt-4">
                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mt-2">
                                    Short Description <small><b>{Short_Description}</b></small>

                                </h5>
                                <button type="button" class="btn btn-light m-1 ai-autofill-box-btn" id="short_des_Ai"
                                    onclick="setFieldIdAndOpenModal('short_description',1002,{{ $document->id ?? '' }})">
                                    <!-- onclick="window.dispatchEvent(new CustomEvent('openAiModal', { detail: { title: 'AI Modal', id: 1002 ,document_id:{{ $document->id ?? '' }}} }))"> -->
                                    AI Autofill
                                </button>
                                <!-- 
                                <button type="button" class="btn btn-light m-1 ai-autofill-box-btn" id="related_doc"
                                    onclick="setFieldIdAndOpenModal('realted_documents',1005,{{ $document->id ?? '' }})">
                                    
                                    AI Autofill
                                </button> -->
                            </div>
                            <div class="row gy-12 mt-2">
                                <div class="col-md-12 doc-short-des">
                                    <div class="form-group">
                                        <!-- <label class="form-label" for="short_description">Short Description

                                        </label> -->
                                        <textarea class="form-control" id="short_description"
                                            name="short_description">{{ old('short_description', $document->short_description ?? '') }}</textarea>
                                        @error('short_description')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- box-4 --}}
                    <!-- <div class="card card-bordered card-preview mt-4">
                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mt-4">
                                    Long Description <small><b>{Long_Description}</b></small>

                                </h5>
                                <button type="button" class="btn btn-light m-1 ai-autofill-box-btn" id="agreement_Ai"
                                onclick="setFieldIdAndOpenModal('long_description',1003,{{ $document->id ?? '' }})">
                                AI Autofill</button>
                            </div>


                            <div class=" col-md-12 mt-2">
                                <div class="form-group">
                                    <textarea class="form-control" id="long_description"
                                        name="long_description">{{ old('long_description', $document->long_description ?? '') }}</textarea>
                                    @error('long_description')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div> -->


                    {{-- box-5 --}}
                    @if(isset($document->documentField) && $document->documentField != null)
                        <?php $sec=1; $sec2=1; ?>
                        <script>let editors = {};</script>
                        @foreach($document->documentField as $index=>$field)
                            <div class="card card-bordered card-preview mt-4">
                                <div class="card-inner">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6>
                                            <!-- Image and text -->
                                            Article Section {{ $sec++ }} <small><b>{Article_Section_{{ $sec2++ }}}</b></small>
                                        </h6>
                                        <button type="button" class="btn btn-light m-1 ai-autofill-box-btn" id="doc_fields_Ai"
                                            onclick="setFieldIdAndOpenModal(['img_heading{{ $index ?? '' }}','img_description{{ $index ?? '' }}','img_description_second{{ $index ?? '' }}'],1004,{{ $document->id ?? '' }})">
                                        <!-- onclick="window.dispatchEvent(new CustomEvent('openAiModal', { detail: { title: 'AI Modal', id: 1004 ,document_id:{{ $document->id ?? '' }}} }))"> -->
                                        AI Autofill</button>
                                    </div>

                                    <?php
                                        $path = getStorageFilepath($field->media->file_path ?? '');
                                    ?>
                                    <div class=" img-txt-section{{ $field->id ?? '' }}">
                                        <hr>

                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label class="form-label" for="img_heading">
                                                    <!-- Heading -->
                                                    Article Title
                                                    <!-- <span class="required_field">*</span> -->
                                                </label>
                                                <input type="text" class="form-control" id="img_heading{{ $index ?? '' }}"
                                                    name="img_heading[{{ $field->id ?? '' }}]"
                                                    value="{{ $field->heading ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label class="form-label" for="img_description">
                                                    <!-- Description Here -->
                                                    Article Text
                                                    <!-- <span class="required_field">*</span> -->
                                                </label>
                                                <textarea class="form-control" id="img_description{{ $index ?? '' }}"
                                                    name="img_description[{{ $field->id ?? '' }}]">{{ $field->description ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <div class="row">
                                                <div class="col-md-6 mt-2">
                                                    <div class="field_img_div" id="field_img_div{{ $field->id ?? '' }}">
                                                        <div class="form-group doc_img">
                                                            <img src="{{ asset('storage/'.$path) }}"
                                                                alt="{{ asset('storage/'.$path) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mt-2">
                                                    <div class="form-group">
                                                        <button class="btn-sm btn btn-primary update_field_img" type="button"
                                                            data-id="{{ $field->id ?? '' }}">Replace Image</button>
                                                        <input type="file" name="field_up_img" class="up_img"
                                                            data-id="{{ $field->id ?? '' }}"
                                                            id="field_up_img{{ $field->id ?? '' }}" style="display:none;">
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="button" class="btn-sm btn btn-primary ai-autofill-box-btn" id="article_img_Ai"
                                                            onclick="generateAiImageModel('{{ $field->id ?? '' }}',1009,{{ $document->id ?? '' }})">Generate Image</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label class="form-label" for="img_description_second">
                                                    <!-- Description Here -->
                                                    Article Text
                                                    <!-- <span class="required_field">*</span> -->
                                                </label>
                                                <textarea class="form-control"
                                                    id="img_description_second{{ $index ?? '' }}"
                                                    name="img_description_second[{{ $field->id ?? '' }}]">{{ $field->description2 ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <script>


                                        ClassicEditor.create(document.querySelector("#img_description{{ $index }}"), {
                                            toolbar: {
                                                items: [
                                                    'heading',
                                                    'bold',
                                                    'bulletedList',
                                                    'numberedList',
                                                ]
                                            },
                                            heading: {
                                                options: [
                                                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                                                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                                                ]
                                            },
                                            removePlugins: [
                                                'Table','MediaEmbed', 'BlockQuote',
                                            ]
                                        })
                                        .then(editor => {
                                            editors['img_description{{ $index }}'] = editor;
                                        })
                                        .catch( error => {
                                            console.error( error );
                                        });

                                        ClassicEditor.create(document.querySelector('#img_description_second{{ $index }}'),{
                                            toolbar: {
                                                items: [
                                                    'heading',
                                                    'bold',
                                                    'bulletedList',
                                                    'numberedList',
                                                ]
                                            },
                                            heading: {
                                                options: [
                                                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                                                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                                                ]
                                            },
                                            removePlugins: [
                                                'Table','MediaEmbed', 'BlockQuote',
                                            ]
                                        })
                                        .then(editor => {
                                            editors['img_description_second{{ $index }}'] = editor;
                                        })
                                        .catch( error => {
                                            console.error( error );
                                        });

                                    </script>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card card-bordered card-preview mt-4">
                            <div class="card-inner">
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php $sec=1; $sec2=1;?>
                                    <h6>
                                        <!-- Image and text -->
                                        Article Section {{ $sec++ }} <small><b>{Article_Section_{{ $sec2++ }}}</b></small>

                                    </h6>
                                    <button type="button" class="btn btn-light m-1 ai-autofill-box-btn" id="doc_fields_Ai"
                                        onclick="setFieldIdAndOpenModal(['img_heading','img_description','img_description_second'],1004,{{ $document->id ?? '' }})">AI Autofill</button>
                                </div>
                                <div class="img-txt-section">
                                    <hr>
                                    @php
                                    $imgHeading = old('img_heading');
                                    $imgDescription1 = old('img_description');
                                    $imgDescription2 = old('img_description_second');
                                    @endphp
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                            <label class="form-label" for="img_heading">
                                                <!-- Heading -->
                                                Article Title
                                                <!-- <span class="required_field">*</span> -->
                                            </label>
                                            @if($imgHeading)
                                            @foreach($imgHeading as $key => $heading)
                                            <input type="text" class="form-control" id="img_heading"
                                                name="img_heading[]" value="{{ $heading }}">
                                            @endforeach
                                            @else
                                            <input type="text" class="form-control" id="img_heading"
                                                name="img_heading[]" value="">
                                            @endif
                                            @error('img_heading.*')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                            <label class="form-label" for="img_description">
                                                <!-- Description Here -->
                                                Article Text
                                                <!-- <span class="required_field">*</span> -->
                                            </label>
                                            @if($imgDescription1)
                                            @foreach($imgDescription1 as $key => $description)
                                            <textarea class="form-control" id="img_description"
                                                name="img_description[]">{{ $description }}</textarea>
                                            @endforeach
                                            @else
                                            <textarea class="form-control" id="img_description"
                                                name="img_description[]"></textarea>
                                            @endif
                                            @error('img_description.*')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                            <label class="form-label" for="field_image">Image
                                                <!-- <span class="required_field">*</span> -->
                                            </label>
                                            <input type="file" class="form-control" id="field_image"
                                                name="field_image[]">
                                            @error('field_image.*')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                            <label class="form-label" for="img_description_second">
                                                <!-- Description Here -->
                                                Article Text
                                                <!-- <span class="required_field">*</span> -->
                                            </label>
                                            @if($imgDescription2)
                                            @foreach($imgDescription2 as $key => $description2)
                                            <textarea class="form-control" id="img_description_second"
                                                name="img_description_second[]">{{ $description2 }}</textarea>
                                            @endforeach
                                            @else
                                            <textarea class="form-control" id="img_description_second"
                                                name="img_description_second[]"></textarea>
                                            @endif
                                            @error('img_description_second.*')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div id="document_field_container"></div>
                    <br>
                    <div class="text-end">
                        <div class="form-group">
                            <button type="button" class="btn btn-sm btn-primary" id="second-section-add">Add
                                Article</button>
                        </div>
                    </div>
                    <div class="card card-bordered card-preview mt-4">
                    <div class="card-inner">
                        <h5 class="mt-4">
                            FAQ Section <small><b>{FAQ_Section}</b></small>
                        </h5>

                        @if(isset($document->documentFaq) && $document->documentFaq->isNotEmpty())
                            <?php $faq_idx = 0; ?>
                            @foreach($document->documentFaq as $index => $faq)
                                <?php $faq_idx = $index; ?>
                                <div class="faq-append-section{{ $faq->id ?? '' }}">
                                    <div class="text-end">
                                        <div class="form-group">
                                            <div>
                                                <span class="remove-faq-sec" 
                                                    data-id="{{ $faq->id ?? '' }}" 
                                                    onclick="removeFAQSection(this)">
                                                    <i class="fa fa-times"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" 
                                                class="btn btn-light m-1 ai-autofill-box-btn" 
                                                id="faq_section"
                                                onclick="setFieldIdAndOpenModal(
                                                    ['question{{ $index+1 ?? '' }}','answer{{ $index+1 ?? '' }}'],
                                                    1008,
                                                    {{ $document->id ?? '' }}
                                                )">
                                            AI Autofill
                                        </button>
                                    </div>
                                    <div class="row gy-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="question">Question</label>
                                                <input class="form-control" 
                                                    name="question[{{ $faq->id ?? '' }}]" 
                                                    id="question{{ $index+1 ?? '' }}" 
                                                    value="{{ $faq->question ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="answer">Answer</label>
                                                <textarea class="form-control" 
                                                        name="answer[{{ $faq->id ?? '' }}]" 
                                                        id="answer{{ $index+1 ?? '' }}">{{ $faq->answer ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" 
                                        name="is_ai[{{ $faq->id ?? '' }}]" 
                                        value="{{ $faq->is_ai ?? 0 }}">

                                </div>

                                <script>
                                    ClassicEditor.create(document.querySelector("#answer{{ $index+1 ?? '' }}"), {
                                        toolbar: {
                                            items: ['heading', 'bold', 'bulletedList', 'numberedList']
                                        },
                                        heading: {
                                            options: [
                                                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                                                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                                            ]
                                        },
                                        removePlugins: ['Table', 'MediaEmbed', 'BlockQuote']
                                    })
                                    .then(editor => {
                                        editors['answer{{ $index+1 ?? '' }}'] = editor;
                                    })
                                    .catch(error => {
                                        console.error(error);
                                    });
                                </script>

                            @endforeach

                        @else

                            {{-- No existing FAQs — show one empty manual FAQ row --}}
                            <div class="faq-append-section">
                                <hr>
                                <div class="text-end">
                                    <button type="button" 
                                            class="btn btn-light m-1 ai-autofill-box-btn" 
                                            id="faq_section"
                                            onclick="setFieldIdAndOpenModal(
                                                ['new_question','new_answer'],
                                                1008,
                                                {{ $document->id ?? '' }}
                                            )">
                                        AI Autofill
                                    </button>
                                </div>
                                <div class="row gy-12">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="new_question">Question</label>
                                            <input class="form-control" 
                                                name="new_question[]" 
                                                id="new_question" 
                                                value="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="new_answer">Answer</label>
                                            <textarea class="form-control" 
                                                    name="new_answer[]" 
                                                    id="new_answer"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="new_is_ai[]" value="0">

                            </div>

                            <script>
                                ClassicEditor.create(document.querySelector("#new_answer"), {
                                    toolbar: {
                                        items: ['heading', 'bold', 'bulletedList', 'numberedList']
                                    },
                                    heading: {
                                        options: [
                                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                                            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                                        ]
                                    },
                                    removePlugins: ['Table', 'MediaEmbed', 'BlockQuote']
                                })
                                .then(editor => {
                                    editors['new_answer'] = editor;
                                })
                                .catch(error => {
                                    console.error(error);
                                });
                            </script>

                        @endif

                        <div id="new-faq-container"></div>
                        <br>
                        <div class="text-end">
                            <div class="form-group">
                                <button type="button" 
                                        class="btn btn-sm btn-primary" 
                                        onclick="addFaqSection()">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <div class="col col-md-4 doc-right-content">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="nk-block-head-content butn-cls">
                                    <div class="mbsc-form-group view_btn">
                                        @if(isset($document))
                                            @if($document->published == '1')
                                                <a href="{{ url('document/' . ($document->slug ?? '')) }}" target="_blank" class="view_page">View Page</a>
                                            @else
                                                <a href="javascript:void(0);" class="view_page" onclick="isNotView()">View Page</a>
                                            @endif
                                        @else
                                            <a href="javascript:void(0);" class="view_page d-none">View Page</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="nk-block-head-content">
                                    <div class="up-btn mbsc-form-group">
                                        @if(isset($document) && $document != null)
                                        <button class="btn btn-sm btn-primary" type="submit">Update</button>
                                        @else
                                        <button class="btn btn-sm btn-primary" type="submit">Save</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p>Published</p>
                                    <div class="custom-control custom-switch">
                                        @if(isset($document->published) && $document->published != null)
                                        @if($document->published == '1')
                                        <input type="checkbox" class="custom-control-input publish" id="publish1"
                                            checked>
                                        <label class="custom-control-label" for="publish1"></label>
                                        @else
                                        <input type="checkbox" class="custom-control-input publish" id="publish1">
                                        <label class="custom-control-label" for="publish1"></label>
                                        @endif
                                        @else
                                        <input type="checkbox" class="custom-control-input publish" id="publish1">
                                        <label class="custom-control-label" for="publish1"></label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <h5>
                                       Categories
                                    </h5>
                                    <div class="form-control-wrap">
                                        <select class="form-select js-select2" multiple name="category_id[]" id="category_id">
                                            @foreach($categories ?? [] as $category)
                                                <option value="{{ $category->id }}"
                                                    @selected(in_array($category->id, old('category_id', $selectedCategoryIds ?? [])))>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="doc_price">Price
                                        <!-- <span class="required_field">*</span> -->
                                    </label>
                                    <input type="number" class="form-control" id="doc_price" name="doc_price"
                                        value="{{ old('doc_price', $document->doc_price ?? '') }}">

                                        <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        
                                        <input type="number" class="form-control" id="doc_price" name="doc_price" 
                                        value="{{ old('doc_price', $document_price->value ?? '') }}"
                                            readonly>
                                    </div>
                                    <a href="" class="btn btn-warning">Edit Price</a>
                                </div>
                            </div> --}}
                            <div class="col-md-12 mt-2">
                            <div class="form-group">
                                <label class="form-label" for="doc_price">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="doc_price" name="doc_price" 
                                        {{-- value="{{ old('doc_price', $document_price->value ?? '') }}" --}}
                                        value="{{ old('doc_price', $document->doc_price ?? $document_price->value ?? '') }}"

                                        readonly>
                                    <button type="button" class="btn btn-outline-secondary" id="editPriceBtn" 
                                        onclick="togglePriceEdit()" title="Edit price">
                                        <i class="fa-solid fa-pen-to-square" style="color:#fff;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    @if(isset($document))
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <div class="col-md-12 mt-2">
                                <div class="form-group" id="iner-frm-wrp">
                                    <label class="form-label">Permalink <span id="edit-slug-btn" style="cursor:pointer; margin-left:5px;"><i class="fa-solid fa-pen-to-square"></i></span></label>
                                    <span id="permalink-display">
                                        <a href="{{ url('document/'.$document->slug ?? '') }}" target="_blank";>
                                            {{ url('document/') }}/<span id="editable-post-name" style="font-weight:600;">{{ $document->slug ?? '' }}</span>/
                                        </a>
                                        <!-- <button id="edit-slug-btn" type="button" class="btn ad_btn btn-sm btn-primary"><i class="fa-solid fa-pen-to-square"></i></button> -->
                                           {{-- <span id="edit-slug-btn" style="cursor:pointer;"><i class="fa-solid fa-pen-to-square"></i></span> --}}
                                    </span>

                                    <span id="permalink-edit" style="display:none;">
                                        {{ url('document/') }}/
                                        <input type="text" id="slug-input" class="form-control d-inline" style="width:auto;" value="{{ $document->slug ?? '' }}">
                                        <small id="slug-error" class="error-message"></small>
                                        /
                                        <button id="accept-slug" type="button" class="ad_btn btn-sm btn-primary">Aceptar</button>
                                        <button id="cancel-slug" type="button" class="ad_btn btn-sm btn-primary">Cancelar</button>
                                        
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            {{-- meta-title --}}
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex justify-content-between meta_fields">
                                            <label class="form-label" for="meta_title">
                                                Meta Title <small><b>{Meta_Title}</b></small>
                                            </label>
                                            <button type="button" class="btn btn-light ai-autofill-box-btn m-1" id="meta_Ai"
                                                onclick="setFieldIdAndOpenModal(['meta_title','meta_description'],1006,{{ $document->id ?? '' }})">
                                                AI Autofill
                                            </button>
                                        </div>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                                            maxlength="50" value="{{ old('meta_title', $document->meta_title ?? '') }}">
                                        @error('title_tag')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="meta_description">Meta Description <small><b>{Meta_Description}</b></small>
                                    </label>
                                    <textarea class="form-control" id="meta_description" name="meta_description"
                                        maxlength="155">{{ old('meta_description', $document->meta_description ?? '') }}</textarea>
                                    @error('title_description')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mt-4">Keywords <small><b>{Keywords}</b></small></h6>
                                <button type="button" class="btn btn-light m-2 ai-autofill-box-btn" id="keyword_Ai" onclick="setFieldIdAndOpenModal(['primary_keywords','secondary_keywords'],1007,{{ $document->id ?? '' }})">AI Autofill</button>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="primary_keywords">Primary Keyword</label>
                                    <input type="text" class="form-control" id="primary_keywords" name="primary_keywords"
                                        value="{{ old('primary_keywords', $document->primary_keywords ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="secondary_keywords">Secondary Keywords</label>
                                    <textarea class="form-control" id="secondary_keywords" name="secondary_keywords">{{ old('secondary_keywords', $document->secondary_keywords ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <livewire:front-document-ai-modal id="aiModalComponent" />
    <livewire:front-ai-image-model id="aiImageComponent" />
</div>




@section('js')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
@endsection

<script>
    // $('#title').on('keyup',function(){
    //     const name = $(this).val();
    //     const url = name.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g, '');
    //     $('#slug').val(url);
    // })


    $(document).ready(function() {
        $('#title').on('keyup', function() {
            const name = $(this).val();
            const url = name.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g, '');
            $('#slug').val(url);
            const title = name.trim();
            const errorSpan = $('#title-error');

            if(!title){
                errorSpan.text('Title is required').show();
                return;
            }

            $.ajax({
                url: "{{ route('admin.check.document.title') }}",
                type: "POST",
                data: {
                    title: title,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.exists){
                        errorSpan.text('This title already exists').show();
                    }else{
                        errorSpan.hide();
                    }
                }
            });
        });
    });

</script>

<script>
    $(document).ready(function(){
        var switchStatus = false;
        $(".publish").on('change', function() {
            if($(this).is(':checked')) {
                switchStatus = $(this).is(':checked');
                // $('#published').val(1);
                $('#published').val(1);
            }
            else{
                switchStatus = $(this).is(':checked');
                $('#published').val(0);
            }
        })

        // Update Document Field Image //
        $('.update_field_img').click(function(){
            var id = $(this).data('id');
            $('#field_up_img' + id).trigger('click');
        });

        $('.up_img').change(function() {
            var id = $(this).data('id');
            var file = this.files[0];
            var formData = new FormData();
            formData.append('field_image', file);
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('id', id);

            $.ajax({
                url: "{{ url('/update/documentField/image') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(response){
                        console.log(response);
                        NioApp.Toast('New image is updated', 'info', {position: 'top-right'});
                        setTimeout(() => {
                            location.reload();
                        },1000);
                },
                error: function(response) {
                        console.log(response.responseText);
                        alert('Error uploading image');
                }
            });
        });

          // Delete Field Image //
        $('.remove_field_img').click(function(){
            id = $(this).data('id');
            let removeIds = $('#field_img_id').val();

            if(removeIds) {
                removeIds += ',' + id;
            }else{
                removeIds = id;
            }
            $('#field_img_id').val(removeIds);

            $('#field_img_div'+id).hide();
        })

    });

</script>

<script>

    function initializeCKEditor(element,id) {
        ClassicEditor
        .create(element, {
            toolbar: {
                items: [
                        'heading',   // For headings (h2, h3, h4)
                        'bold',      // For bold text
                        'bulletedList',  // For unordered list
                        'numberedList'   // For ordered list
                ]
            },
            heading: {
                options: [
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                ]
            },
            removePlugins: [
                'Table', 'MediaEmbed', 'BlockQuote',
            ]
        })
        .then(editor => {
            editors[id] = editor;
        })
        .catch(error => {
            console.error(error);
        });
    }

    let sectionIndex = 0;
    let sec = "{{ $sec ?? '' }}";
    let sec2 = "{{ $sec2 ?? '' }}";
    $(document).ready(function(){
        $('#second-section-add').on('click',function(){
            sectionIndex++ ;

            var html = `<div class="card card-bordered card-preview mt-4">
                        <div class="card-inner">
                            <div class="img-txt-section">
                                <div class="text-end">
                                    <div class="form-group">
                                        <div><span class="remove-second-sec" value="appended"><i class="fa fa-times"></i></span></div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6>
                                    <!-- Image and text -->
                                        Article Section ${sec} {Article_Section_${sec2++ }}
                                    </h6>
                                    <button type="button" class="btn btn-light m-1 ai-autofill-box-btn" id="doc_fields_Ai"
                                        onclick="setFieldIdAndOpenModal(['new_img_heading${sectionIndex}','new_img_description${sectionIndex}','new_img_description_second${sectionIndex}'],1004,{{ $document->id ?? '' }})">
                                    <!-- onclick="window.dispatchEvent(new CustomEvent('openAiModal', { detail: { title: 'AI Modal', id: 1004 ,document_id:{{ $document->id ?? '' }}} }))"> -->
                                    AI Autofill</button>
                                </div>
                                <div class="second-append-sec" id="second-append-sec">
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                                <label class="form-label" for="new_img_heading">Article Title</label>
                                                <input type="text" class="form-control" id="new_img_heading${sectionIndex}" name="new_img_heading[]" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                                <label class="form-label" for="new_img_description">Article Text</label>
                                            <textarea class="description-editor" name="new_img_description[]" id="new_img_description${sectionIndex}"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                                <label class="form-label" for="new_field_image">Image</label>
                                                <input type="file" class="form-control" id="new_field_image" name="new_field_image[]">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                                <label class="form-label" for="new_img_description_second">Article Text</label>
                                                <textarea class="description-editor2" id="new_img_description_second${sectionIndex}" name="new_img_description_second[]"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>`

            $('#document_field_container').append(html);


            const firstTextarea = document.getElementById(`new_img_description${sectionIndex}`);
            if(firstTextarea && !$(firstTextarea).data('ckeditor-initialized')){
                initializeCKEditor(firstTextarea,`new_img_description${sectionIndex}`);
                $(firstTextarea).data('ckeditor-initialized', true);
            }

            const secondTextarea = document.getElementById(`new_img_description_second${sectionIndex}`);
            if(secondTextarea && !$(secondTextarea).data('ckeditor-initialized')){
                initializeCKEditor(secondTextarea,`new_img_description_second${sectionIndex}`);
                $(secondTextarea).data('ckeditor-initialized', true);
            }

            sec++ ;
        });


        // Remove second section //
        $('body').delegate('.remove-second-sec', 'click', function () {
            console.log('kjhgjhghfdgkjfd');
            if ($(this).attr('value') === 'appended') {
                $(this).closest('.card-bordered').remove();
            } else {
                var id = $(this).data('id');
                let deleteIds = $('#img_sec_ids').val();
                if(deleteIds){
                    deleteIds += ',' + id;
                } else {
                    deleteIds = id;
                }
                $('#img_sec_ids').val(deleteIds);
                $('.img-txt-section'+id).hide();
            }
        });

    });

    function faqCKEditor(element,id) {
        ClassicEditor
        .create(element, {
            toolbar: {
                items: [
                        'heading',   // For headings (h2, h3, h4)
                        'bold',      // For bold text
                        'bulletedList',  // For unordered list
                        'numberedList'   // For ordered list
                ]
            },
            heading: {
                options: [
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                ]
            },
            removePlugins: [
                'Table', 'MediaEmbed', 'BlockQuote',
            ]
        })
        .then(editor => {
            editors[id] = editor;
        })
        .catch(error => {
            console.error(error);
        });
    }

    $('.answer_editor').each(function() {
        faqCKEditor(this);
    });

    // let faq_idx = "{{ $faq_idx ?? '' }}";
    // function addFaqSection(){
    //     faq_idx++;
    //     var html = `<div class="faq-append-section">
    //                     <hr>
    //                     <div class="text-end">
    //                         <div class="form-group">
    //                             <div><span class="remove-faq-sec" value="appended" onclick="removeFAQSection(this)"><i class="fa fa-times"></i></span></div>
    //                         </div>
    //                     </div>
    //                     <div class="text-end">
    //                         <button type="button" class="btn btn-light m-1 ai-autofill-box-btn" id="faq_section"
    //                             onclick="setFieldIdAndOpenModal(['new_question${faq_idx}','new_answer${faq_idx}'],1008,{{ $document->id ?? '' }})">
    //                             AI Autofill
    //                         </button>
    //                     </div>
    //                     <div class="row gy-12">
    //                         <div class="col-md-6">
    //                             <div class="form-group">
    //                                 <label class="form-label" for="new_question">Question</label>
    //                                 <input class="form-control" name="new_question[]" id="new_question${faq_idx}" value="">
    //                             </div>
    //                         </div>
    //                         <div class="col-md-6">
    //                             <div class="form-group">
    //                                 <label class="form-label" for="new_answer">Answer</label>
    //                                 <textarea class="form-control answer_editor" name="new_answer[]" id="new_answer${faq_idx}"></textarea>
    //                             </div>
    //                         </div>
    //                     </div>
    //                 </div>`;
    //     $('#new-faq-container').append(html);

    //     const newTextarea = $('.answer_editor').last()[0];
    //     if (newTextarea && !$(newTextarea).data('ckeditor-initialized')) {
    //         faqCKEditor(newTextarea, 'new_answer'+faq_idx);
    //         $(newTextarea).data('ckeditor-initialized', true);
    //     }
    // }

    let faq_idx = "{{ $faq_idx ?? '' }}";

function addFaqSection() {
    faq_idx++;

    var html = `<div class="faq-append-section">
                    <hr>
                    <div class="text-end">
                        <div class="form-group">
                            <div>
                                <span class="remove-faq-sec" value="appended" onclick="removeFAQSection(this)">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" 
                                class="btn btn-light m-1 ai-autofill-box-btn" 
                                onclick="setFieldIdAndOpenModal(
                                    ['new_question${faq_idx}','new_answer${faq_idx}'],
                                    1008,
                                    {{ $document->id ?? '' }}
                                )">
                            AI Autofill
                        </button>
                    </div>
                    <div class="row gy-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Question</label>
                                <input class="form-control" 
                                       name="new_question[]" 
                                       id="new_question${faq_idx}" 
                                       value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Answer</label>
                                <textarea class="form-control answer_editor" 
                                          name="new_answer[]" 
                                          id="new_answer${faq_idx}"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- hidden is_ai = 0 for manually added FAQs -->
                    <input type="hidden" 
                           name="new_is_ai[]" 
                           id="new_is_ai${faq_idx}" 
                           value="0">

                </div>`;

    $('#new-faq-container').append(html);

    // Initialize CKEditor for the new answer textarea
    const newTextarea = document.getElementById(`new_answer${faq_idx}`);
    if (newTextarea && !$(newTextarea).data('ckeditor-initialized')) {
        faqCKEditor(newTextarea, 'new_answer' + faq_idx);
        $(newTextarea).data('ckeditor-initialized', true);
    }
}

    function removeFAQSection(element) {
        if($(element).attr('value') === 'appended'){
            $(element).closest('.faq-append-section').remove();
        }else {
            var id = $(element).attr('data-id');
            let deleteIds = $('#faq_ids').val();
            if(deleteIds){
                deleteIds += ',' + id;
            } else {
                deleteIds = id;
            }
            $('#faq_ids').val(deleteIds);
            $('.faq-append-section'+id).hide();
        }
    }

    let output = '';
    let selectedFieldIds = null;
    let shortDescriptionEditor;
    let longDescriptionEditor;
    let imageDescriptionEditor;
    let faqAnswerEditor;
    let documentId = null;
    let record_id = null;

    // let editors = [];
    // let imageDescriptionSecondEditor;

    function setFieldIdAndOpenModal(field_ids, recordId, document_id) {
        // selectedFieldId = field_id;
        selectedFieldIds = Array.isArray(field_ids) ? field_ids : [field_ids];
        documentId = document_id;
        record_id = recordId;
    
        const titleInput = document.getElementById('title');
        const title = titleInput?.value?.trim();
        const slug = document.getElementById('slug');

        if(!title){
            Swal.fire({
                icon: 'warning',
                title: 'Title Required',
                text: 'Please fill in the Document Title before using AI Autofill.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    titleInput?.focus();
                }
            });
            return;
        }

        window.dispatchEvent(new CustomEvent('openAiModal', {
            detail: {
                title: 'AI Modal',
                id: recordId,
                document_id: document_id,
                field_ids: selectedFieldIds
            }
        }));
    }

    ClassicEditor
        .create( document.querySelector('#short_description'),{
        toolbar: {
            items: [
                    'heading',
                    'bold',
                    'bulletedList',
                    'numberedList',
            ]
        },
        heading: {
            options: [
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
            ]
        },
        removePlugins: [
            'Table','MediaEmbed', 'BlockQuote',
        ]
    })
    .then(editor => {
        shortDescriptionEditor = editor;
    })
    .catch( error => {
        console.error( error );
    });

    ClassicEditor
        .create( document.querySelector('#long_description'),{
        toolbar: {
            items: [
                    'heading',
                    'bold',
                    'bulletedList',
                    'numberedList',
            ]
        },
        heading: {
            options: [
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
            ]
        },
        removePlugins: [
            'Table','MediaEmbed', 'BlockQuote',
        ]
    })
    .then(editor => {
        longDescriptionEditor = editor;
    })
    .catch( error => {
        console.error( error );
    });

    ClassicEditor
        .create( document.querySelector('#img_description'),{
        toolbar: {
            items: [
                    'heading',
                    'bold',
                    'bulletedList',
                    'numberedList',
            ]
        },
        heading: {
            options: [
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
            ]
        },
        removePlugins: [
            'Table','MediaEmbed', 'BlockQuote',
        ]
    })
    .then(editor => {
        imageDescriptionEditor = editor;
    })
    .catch( error => {
        console.error( error );
    });


    ClassicEditor
        .create(document.querySelector('#img_description_second'), {
        toolbar: {
            items: [
                'heading',
                'bold',
                'bulletedList',
                'numberedList',
            ]
        },
        heading: {
            options: [
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
            ]
        },
        removePlugins: [
            'Table', 'MediaEmbed', 'BlockQuote',
        ]
    })
    .then(editor => {
        imageDescriptionSecondEditor = editor;
    })
    .catch(error => {
        console.error('Image description second editor error:', error);
    });


    function cleanAndParseOutput(rawOutput) {
        let output = rawOutput.trim();
        output = output.replace(/^(json|```json|```)\s*/i, '');
        output = output.replace(/```$/, '');

        try {
            return JSON.parse(output);
        } catch (e) {
            console.error("Invalid JSON:", e);
            return null;
        }
    }

    // Helper to set editor content
    function setEditorData(fieldId, key, decoded) {
        if (decoded && typeof decoded === 'object') {
            editors[fieldId].setData(decoded[key] || '');
        }
    }

    // Helper to set input/textarea value
    function setInputValue(targetEl, key, decoded) {
        if (decoded && typeof decoded === 'object') {
            targetEl.value = decoded[key] || '';
        }
    }

    /* Store the output into input fields */
    function getOutput() {
        let output = document.getElementById('ai-output').innerHTML;
        const decoded = cleanAndParseOutput(output);

        selectedFieldIds.forEach((fieldId) => {
            const targetEl = document.getElementById(fieldId);
            console.log(targetEl);

            if (!targetEl) {
                console.error("Element not found:", fieldId);
                return;
            }

            //  FAQ AI Autofill — set is_ai = 1
        if (/^new_answer\d*$/.test(fieldId)) {
            const parentDiv = targetEl.closest('.faq-append-section');
            if (parentDiv) {
                const isAiInput = parentDiv.querySelector('input[name="new_is_ai[]"]');
                if (isAiInput) {
                    isAiInput.value = 1; 
                }
            }
        }

        if (/^answer\d+$/.test(fieldId)) {
            const parentDiv = targetEl.closest('.faq-append-section' + targetEl.closest('[class*="faq-append-section"]')?.className?.match(/\d+/)?.[0] ?? '');
            if (parentDiv) {
                const hiddenIsAi = parentDiv.querySelector('input[name^="is_ai["]');
                if (hiddenIsAi) {
                    hiddenIsAi.value = 1;
                }
            }
        }

            if (fieldId === 'short_description' && shortDescriptionEditor) {
                shortDescriptionEditor.setData(output);

            } else if (fieldId === 'long_description' && longDescriptionEditor) {
                longDescriptionEditor.setData(output);

            } else if (fieldId === 'img_description' && imageDescriptionEditor) {
                imageDescriptionEditor.setData(output);

            } else if (fieldId === 'img_description_second' && imageDescriptionSecondEditor) {
                imageDescriptionSecondEditor.setData(output);
            } else if (fieldId === 'img_heading'){
                setInputValue(targetEl, 'title', decoded);


            } else if (/^img_description_second\d*$/.test(fieldId) && editors[fieldId]) {
                setEditorData(fieldId, 'follow_up_text', decoded);

            } else if (/^img_description\d*$/.test(fieldId) && editors[fieldId]) {
                setEditorData(fieldId, 'short_description', decoded);

            } else if (/^img_heading\d*$/.test(fieldId)) {
                setInputValue(targetEl, 'title', decoded);

            } else if (/^new_img_description_second\d*$/.test(fieldId) && editors[fieldId]) {
                setEditorData(fieldId, 'follow_up_text', decoded);

            } else if (/^new_img_description\d*$/.test(fieldId) && editors[fieldId]) {
                setEditorData(fieldId, 'short_description', decoded);

            } else if (/^new_img_heading\d*$/.test(fieldId)) {
                setInputValue(targetEl, 'title', decoded);

            // FAQ questions/answers
            } else if (fieldId === 'new_question') {
                setInputValue(targetEl, 'faq_question', decoded);

            } else if (fieldId === 'new_answer' && editors[fieldId]) {
                setEditorData(fieldId, 'faq_answer', decoded);

            } else if (/^question\d+$/.test(fieldId)) {
                setInputValue(targetEl, 'faq_question', decoded);

            } else if (/^answer\d+$/.test(fieldId) && editors[fieldId]) {
                setEditorData(fieldId, 'faq_answer', decoded);

            } else if (/^new_question\d+$/.test(fieldId)) {
                setInputValue(targetEl, 'faq_question', decoded);

            } else if (/^new_answer\d+$/.test(fieldId) && editors[fieldId]) {
                setEditorData(fieldId, 'faq_answer', decoded);

            // SEO-related fields
            } else if (fieldId === 'meta_title') {
                setInputValue(targetEl, 'meta_title', decoded);

            } else if (fieldId === 'meta_description') {
                setInputValue(targetEl, 'meta_description', decoded);

            } else if (fieldId === 'primary_keywords') {
                setInputValue(targetEl, 'primary_keyword', decoded);

            } else if (fieldId === 'secondary_keywords') {
                setInputValue(targetEl, 'secondary_keywords', decoded);

            // } else if (fieldId === 'longtail_keywords') {
            //     setInputValue(targetEl, 'longtail_keywords', decoded);

            // } else if (fieldId === 'high_intent_keywords') {
            //     setInputValue(targetEl, 'high_intent_keywords', decoded);

            // Fallback for normal inputs or other content blocks
            } else if (targetEl.tagName === "INPUT" || targetEl.tagName === "TEXTAREA") {
                targetEl.value = output;

            } else {
                targetEl.innerHTML = output;
            }
        });
    }

</script>
<script>
    // function validateAndRunAiAutofill(){
    //     const title = $('#title').val().trim();
    //     if(!title){
    //         Swal.fire('Title Required', 'Please fill in the Document Title before using AI Autofill.', 'warning');
    //         return;
    //     }   

    //     $.ajax({
    //         url: '/ai/autofill/generate-keywords',
    //         type: 'POST',
    //         data: {
    //             title: title,
    //             _token: $('meta[name="csrf-token"]').attr('content')
    //         },
    //         beforeSend: function(){
    //             Swal.fire({
    //                 title: 'Generating Keywords...',
    //                 text: 'Please wait while AI suggests the best keywords.',
    //                 allowOutsideClick: false,
    //                 didOpen: () => Swal.showLoading()
    //             });
    //         },
    //         success: function(data){
    //             Swal.close();
                
    //             if(data){
    //                 if(data.success == true){
    //                     let secondaryKeywords = data.secondary_keywords;

    //                     if(typeof secondaryKeywords === 'string'){
    //                         try{
    //                             const jsonFixed = secondaryKeywords.replace(/'/g, '"');
    //                             const parsed = JSON.parse(jsonFixed);

    //                             if(Array.isArray(parsed)){
    //                                 secondaryKeywords = parsed;
    //                             }else{
    //                                 secondaryKeywords = secondaryKeywords
    //                                     .replace(/\[|\]/g, '')
    //                                     .split(',')
    //                                     .map(s => s.replace(/['"]+/g, '').trim())
    //                                     .filter(Boolean);
    //                             }
    //                         }catch (e){
    //                             secondaryKeywords = secondaryKeywords
    //                                 .replace(/\[|\]/g, '')
    //                                 .split(',')
    //                                 .map(s => s.replace(/['"]+/g, '').trim())
    //                                 .filter(Boolean);
    //                         }
    //                     }else if(!Array.isArray(secondaryKeywords)){
    //                         secondaryKeywords = [];
    //                     }

    //                     Swal.fire({
    //                         title: 'Confirm Keywords',
    //                         html: `
    //                             <strong>Primary Keyword:</strong> ${data.primary_keyword || ''}<br><br>
    //                             <strong>Secondary Keywords:</strong> ${secondaryKeywords.join('; ')}
    //                         `,
    //                         showCancelButton: true,
    //                         confirmButtonText: 'Continue with AI Autofill',
    //                         cancelButtonText: 'Cancel',
    //                         reverseButtons: true
    //                     }).then(result => {
    //                         if (result.isConfirmed) {
    //                             runFullAiAutofill(title, data.primary_keyword, secondaryKeywords);
    //                         }
    //                     });
    //                 } 
    //             }
    //         }
    //     });
    // }

    // function validateAndRunAiAutofill(){
    //     const title = $('#title').val().trim();
    //     if(!title){
    //         Swal.fire('Title Required', 'Please fill in the Document Title before using AI Autofill.', 'warning');
    //         return;
    //     }   

    //     $.ajax({
    //         url: '/ai/autofill/generate-keywords',
    //         type: 'POST',
    //         data: {
    //             title: title,
    //             _token: $('meta[name="csrf-token"]').attr('content')
    //         },
    //         beforeSend: function(){
    //             Swal.fire({
    //                 title: 'Generating Keywords...',
    //                 text: 'Please wait while AI suggests the best keywords.',
    //                 allowOutsideClick: false,
    //                 didOpen: () => Swal.showLoading()
    //             });
    //         },
    //         success: function(data){
    //             Swal.close();
                
    //             if(data){
    //                 if(data.success == true){
    //                     let secondaryKeywords = data.secondary_keywords;

    //                     if(typeof secondaryKeywords === 'string'){
    //                         try{
    //                             const jsonFixed = secondaryKeywords.replace(/'/g, '"');
    //                             const parsed = JSON.parse(jsonFixed);

    //                             if(Array.isArray(parsed)){
    //                                 secondaryKeywords = parsed;
    //                             }else{
    //                                 secondaryKeywords = secondaryKeywords
    //                                     .replace(/\[|\]/g, '')
    //                                     .split(';')
    //                                     .map(s => s.replace(/['"]+/g, '').trim())
    //                                     .filter(Boolean);
    //                             }
    //                         }catch (e){
    //                             secondaryKeywords = secondaryKeywords
    //                                 .replace(/\[|\]/g, '')
    //                                 .split(',')
    //                                 .map(s => s.replace(/['"]+/g, '').trim())
    //                                 .filter(Boolean);
    //                         }
    //                     }else if(!Array.isArray(secondaryKeywords)){
    //                         secondaryKeywords = [];
    //                     }

    //                     Swal.fire({
    //                         title: 'Confirm Keywords',
    //                         html: `
    //                             <div style="text-align:left; margin-bottom: 12px;">
    //                                 <label style="font-weight:600; display:block; margin-bottom:4px;">Primary Keyword:</label>
    //                                 <input 
    //                                     id="swal-primary-keyword" 
    //                                     type="text" 
    //                                     class="swal2-input" 
    //                                     style="margin: 0; width: 100%;" 
    //                                     value="${data.primary_keyword || ''}"
    //                                 >
    //                             </div>
    //                             <div style="text-align:left;">
    //                                 <label style="font-weight:600; display:block; margin-bottom:4px;">Secondary Keywords: <small style="font-weight:400; color:#888;"></small></label>
    //                                 <textarea 
    //                                     id="swal-secondary-keywords" 
    //                                     class="swal2-textarea" 
    //                                     style="margin: 0; width: 100%; height: 90px; resize: vertical;"
    //                                 >${secondaryKeywords.join('; ')}</textarea>
    //                             </div>
    //                         `,
    //                         showCancelButton: true,
    //                         confirmButtonText: 'Continue with AI Autofill',
    //                         cancelButtonText: 'Cancel',
    //                         reverseButtons: true,
    //                         preConfirm: () => {
    //                             const editedPrimary = document.getElementById('swal-primary-keyword').value.trim();
    //                             const editedSecondaryRaw = document.getElementById('swal-secondary-keywords').value.trim();

    //                             if(!editedPrimary){
    //                                 Swal.showValidationMessage('Primary keyword is required.');
    //                                 return false;
    //                             }

    //                             const editedSecondary = editedSecondaryRaw
    //                                 .split(';')
    //                                 .map(s => s.trim())
    //                                 .filter(Boolean);

    //                             return { primary: editedPrimary, secondary: editedSecondary };
    //                         }
    //                     }).then(result => {
    //                         if (result.isConfirmed) {
    //                             runFullAiAutofill(title, result.value.primary, result.value.secondary);
    //                         }
    //                     });
    //                 } 
    //             }
    //         }
    //     }); 
    // }

function validateAndRunAiAutofill(){
    const title = $('#title').val().trim();
    if(!title){
        Swal.fire('Title Required', 'Please fill in the Document Title before using AI Autofill.', 'warning');
        return;
    }   
    $.ajax({
        url: '/ai/autofill/generate-keywords',
        type: 'POST',
        data: {
            title: title,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function(){
            Swal.fire({
                title: 'Generating Keywords...',
                text: 'Please wait while AI suggests the best keywords.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function(data){
            Swal.close();
            
            if(data && data.success == true){
                let secondaryKeywords = data.secondary_keywords;

                if(typeof secondaryKeywords === 'string'){
                    try{
                        const jsonFixed = secondaryKeywords.replace(/'/g, '"');
                        const parsed = JSON.parse(jsonFixed);
                        if(Array.isArray(parsed)){
                            secondaryKeywords = parsed;
                        } else {
                            secondaryKeywords = secondaryKeywords
                                .replace(/\[|\]/g, '')
                                .split(';')
                                .map(s => s.replace(/['"]+/g, '').trim())
                                .filter(Boolean);
                        }
                    } catch(e){
                        secondaryKeywords = secondaryKeywords
                            .replace(/\[|\]/g, '')
                            .split(',')
                            .map(s => s.replace(/['"]+/g, '').trim())
                            .filter(Boolean);
                    }
                } else if(!Array.isArray(secondaryKeywords)){
                    secondaryKeywords = [];
                }

                Swal.fire({
                    title: 'Confirm Keywords',
                    html: `
                        <div style="text-align:left; margin-bottom: 12px;">
                            <label style="font-weight:600; display:block; margin-bottom:4px;">Primary Keyword:</label>
                            <input 
                                id="swal-primary-keyword" 
                                type="text" 
                                class="swal2-input" 
                                style="margin: 0; width: 100%;" 
                                value="${data.primary_keyword || ''}"
                            >
                        </div>
                        <div style="text-align:left;">
                            <label style="font-weight:600; display:block; margin-bottom:4px;">Secondary Keywords:</label>
                            <textarea 
                                id="swal-secondary-keywords" 
                                class="swal2-textarea" 
                                style="margin: 0; width: 100%; height: 90px; resize: vertical;"
                            >${secondaryKeywords.join('; ')}</textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Continue with AI Autofill',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    preConfirm: () => {
                        const editedPrimary = document.getElementById('swal-primary-keyword').value.trim();
                        const editedSecondaryRaw = document.getElementById('swal-secondary-keywords').value.trim();

                        if(!editedPrimary){
                            Swal.showValidationMessage('Primary keyword is required.');
                            return false;
                        }

                        const editedSecondary = editedSecondaryRaw
                            .split(';')
                            .map(s => s.trim())
                            .filter(Boolean);

                        return { primary: editedPrimary, secondary: editedSecondary };
                    }
                }).then(result => {
                    if(result.isConfirmed){
                        runFullAiAutofill(title, result.value.primary, result.value.secondary);
                    }
                });
            }
        },
        error: function(){
            Swal.close();
            Swal.fire('Error', 'Server error occurred while generating keywords.', 'error');
        }
    });
}

function runFullAiAutofill(title, primary_keyword, secondary_keywords) {
    const documentId = "{{ $document->id ?? '' }}";

    $.ajax({
        url: '/ai/autofill/save-document',
        type: 'POST',
        data: {
            title: title,
            primary_keyword: primary_keyword,
            secondary_keywords: secondary_keywords,
            document_id: documentId,
            _token: "{{ csrf_token() }}"
        },
        beforeSend: function() {
            Swal.fire({
                title: 'Generating Content...',
                text: 'Please wait while AI creates your document details.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function(data){
            Swal.close();

            if(data.success == true){
                if(primary_keyword){
                    $('#primary_keywords').val(primary_keyword);
                }
                if(secondary_keywords){
                    let secValue = Array.isArray(secondary_keywords) 
                        ? secondary_keywords.join('; ') 
                        : secondary_keywords;
                    $('#secondary_keywords').val(secValue);
                }

                if(documentId){
                    fetchAndFillGeneratedContent(data.document_data || null, data);
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Document Created!',
                        text: 'AI Autofill completed successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = data.redirect_url;
                    });
                }
            } else {
                Swal.fire('Error', data.message || 'Something went wrong while saving the document.', 'error');
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error', 'Server error occurred.', 'error');
        }
    });
}

function fetchAndFillGeneratedContent(documentData, responseData) {
    if(responseData.short_description && shortDescriptionEditor){
        shortDescriptionEditor.setData(responseData.short_description);
    }
    if(responseData.meta_title){
        $('#meta_title').val(responseData.meta_title);
    }
    if(responseData.meta_description){
        $('#meta_description').val(responseData.meta_description);
    }

    Swal.fire({
        icon: 'success',
        title: 'AI Autofill Complete!',
        text: 'Fields have been filled. Please review and save.',
        timer: 2500,
        showConfirmButton: false
    });
}



    // function runFullAiAutofill(title, primary_keyword, secondary_keywords) {
    //     $.ajax({
    //         url: '/ai/autofill/save-document',
    //         type: 'POST',
    //         data: {
    //             title: title,
    //             primary_keyword: primary_keyword,
    //             secondary_keywords: secondary_keywords,
    //              document_id: "{{ $document->id ?? '' }}",
    //             _token: "{{ csrf_token() }}"
    //         },
    //         beforeSend: function() {
    //             Swal.fire({
    //                 title: 'Generating Content...',
    //                 text: 'Please wait while AI creates your document details.',
    //                 allowOutsideClick: false,
    //                 didOpen: () => Swal.showLoading()
    //             });
    //         },
    //         success: function(data){
    //             Swal.close();

    //             if(data.success == true){
    //                 Swal.fire({
    //                     icon: 'success',
    //                     title: 'Document Created!',
    //                     text: 'AI Autofill completed successfully!',
    //                     timer: 3000,
    //                     showConfirmButton: false
    //                 });
    //                 window.location.href = data.redirect_url;
    //             } else {
    //                 Swal.fire('Error', 'Something went wrong while saving the document.', 'error');
    //             }
    //         },
    //         error: function() {
    //             Swal.close();
    //             Swal.fire('Error', 'Server error occurred.', 'error');
    //         }
    //     });
    // }

//     function runFullAiAutofill(title, primary_keyword, secondary_keywords) {
//     $.ajax({
//         url: '/ai/autofill/save-document',
//         type: 'POST',
//         data: {
//             title: title,
//             primary_keyword: primary_keyword,
//             secondary_keywords: secondary_keywords,
//             document_id: "{{ $document->id ?? '' }}",
//             _token: "{{ csrf_token() }}"
//         },
//         beforeSend: function() {
//             Swal.fire({
//                 title: 'Generating Content...',
//                 text: 'Please wait while AI creates your document details.',
//                 allowOutsideClick: false,
//                 didOpen: () => Swal.showLoading()
//             });
//         },
//         success: function(data){
//             Swal.close();

//             if(primary_keyword) {
//                 $('#primary_keywords').val(primary_keyword);
//             }
//             if(secondary_keywords) {
//                 let secValue = Array.isArray(secondary_keywords) ? secondary_keywords.join('; ') : secondary_keywords;
//                 $('#secondary_keywords').val(secValue);
//             }

//             if(data.success == true){
//                 Swal.fire({
//                     icon: 'success',
//                     title: 'Document Created!',
//                     text: 'AI Autofill completed successfully!',
//                     timer: 2000,
//                     showConfirmButton: false
//                 }).then(() => {
//                     window.location.href = data.redirect_url;
//                 });
//             } else {
//                 Swal.fire('Error', 'Something went wrong while saving the document.', 'error');
//             }
//         },
//         error: function() {
//             Swal.close();
//             Swal.fire('Error', 'Server error occurred.', 'error');
//         }
//     });
// }


</script>

<script>
    $(document).ready(function(){
        let editBtn = $('#edit-slug-btn');
        let acceptBtn = $('#accept-slug');
        let cancelBtn = $('#cancel-slug');
        let originalSlug = $('#slug-input').val();

        $(editBtn).click(function(){
            $('#slug-input').focus();
            $('#permalink-display').css({'display':'none'});
            $('#permalink-edit').css({'display':'inline'});
        })

        $(cancelBtn).click(function(){
            $('#slug-input').val(originalSlug);
            $('#permalink-display').css({'display':'inline'});
            $('#permalink-edit').css({'display':'none'});
        })

        $(acceptBtn).on("click", function(){
            const $input = $('#slug-input');
            const $error = $('#slug-error');
            const newSlug = $input.val()?.trim();
            const slugRegex = /^[a-z0-9\-]+$/;

            $input.removeClass('input-error');
            $error.text('').hide();

            if (!newSlug) {
                $input.addClass('input-error').focus();
                $error.text('Please enter the slug.').show();
                return;
            }

            if (!slugRegex.test(newSlug)) {
                $input.addClass('input-error').focus();
                $error.text('Slug can only contain lowercase letters, numbers, and hyphens.').show();
                return;
            }

            $('#editable-post-name').text(newSlug);

            var data = {
                slug: newSlug,
                id: "{{ $document->id ?? '' }}",
                _token: "{{ csrf_token() }}"
            };

            $.ajax({
                url: "{{ route('update.document.slug') }}",
                type: "POST",
                data: data,
                success: function (response) {
                    if(response.success == false){
                        $input.addClass('input-error').focus();
                        $error.text(response.message).show();
                    } else {
                        $('#permalink-display').find("a").attr("href", "{{ url('document') }}/" + newSlug);
                        $('#permalink-display').css({'display':'inline'});
                        $('#permalink-edit').css({'display':'none'});

                        setTimeout(() => {
                            window.location.href = response.redirect_url;
                        }, 1000);
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });
        });

        $('#slug-input').on('input', function () {
            $(this).removeClass('input-error');
        });

    })


    function generateSVG() {
        let imageNames = [];
        let hasAtLeastOne = false;

        $(".image_name_input").each(function () {
            let val = $(this).val().trim();
            imageNames.push(val);

            if (val !== '') {
                hasAtLeastOne = true;
            }

        });

        if(!hasAtLeastOne){
            $("#error-message").text("Please fill at least one image name field.").show();
            $(this).toggleClass('input-error', val === '');
            return;
        }else{
            $("#error-message").hide();
        }

        let data = {
            id: "{{ $document->id ?? '' }}",
            image_name: imageNames,
            _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: "{{ route('update.document.image') }}",
            type: "POST",
            data: data,
            dataType: "json",
            success: function (response) {
                $('#generateSVG').hide();

                if (!response.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    timer: 1000,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    location.reload();
                }, 1000);

            },
            error: function (xhr) {
                const response = xhr.responseJSON;
                if (response?.message) {
                    $("#error-message").text(response.message).show();
                }
            }
        });
    }



</script>

<script>

    let field_Id = null;
    function generateAiImageModel(fieldId, record_id, document_id, key = null){
        field_Id = fieldId;
        window.dispatchEvent(new CustomEvent('openAiImageModel', {
            detail: {
                title: 'AI Image Model',
                id: record_id,
                document_id: document_id,
                field_ids: fieldId
            }
        }));
    }


    function saveGeneratedImage() {

        setTimeout(() => {
            let img = document.querySelector('#ai-output img');
            if (img) {
                media_id = img.id;
                let data = {
                    id: field_Id,
                    media_id: media_id,
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: "{{ route('admin.dashboard.update_documentField_image') }}",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function (response) {
                        if (response.status == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Image saved successfully!',
                                text: response.message
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function (xhr) {
                        const response = xhr.responseJSON;
                        if (response?.message) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    }
                });
            } else {
                console.log("Image not found.");
            }
        }, 100);
    }

function togglePriceEdit() {
    const input = document.getElementById('doc_price');
    const btn = document.getElementById('editPriceBtn');
    const isReadonly = input.hasAttribute('readonly');

    if (isReadonly) {
        input.removeAttribute('readonly');
        input.focus();
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-primary');
        btn.title = 'Lock price';
        btn.innerHTML = '<i class="fa-solid fa-lock-open"></i>';
    } else {
        input.setAttribute('readonly', true);
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-secondary');
        btn.title = 'Edit price';
        btn.innerHTML = '<i class="fa-solid fa-pen-to-square"></i>';
    }
}

</script>
@endsection
