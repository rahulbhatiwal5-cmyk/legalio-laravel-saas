@extends('admin_layout.master')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<link rel="stylesheet" href="{{ asset('public/admin-theme/assets/css/dashlite.css') }}">
<link rel="stylesheet" href="{{ asset('assets/admin/css/document_generator_beta.css') }}">




</style>

<div class="container-beta">
    <div class="stepper">
        <button class="step active" data-step="1"><i class="fas fa-file-alt"></i> Questionnaire</button>
        <button class="step" data-step="2"><i class="fas fa-edit"></i> Edit Questions</button>
        <button class="step" data-step="3"><i class="fas fa-file-contract"></i> Contract</button>
        <button class="step" data-step="4"><i class="fas fa-edit"></i> Edit Contract</button>
        <button class="step" data-step="5"><i class="fas fa-check-circle"></i> Final</button>
        <button class="step" data-step="6"><i class="fa fa-file-text" aria-hidden="true"></i> FrontPage</button>
    </div>

    <input type="hidden" name="steps_count" id="steps_count" value="{{ $DocumentGenratingPrompts->count() }}">
    @foreach($DocumentGenratingPrompts as $key => $prompt)
    <input type="hidden" name="qPrompt" id="qPrompt{{ $key + 1 }}" data-prompt="{{ $prompt->prompts }}" data-step_no="{{ $prompt->steps_no }}" data-contract_type="{{ $prompt->contract_type }}">
    @endforeach

    {{-- STEP 1: QUESTIONNAIRE GENERATION --}}
    <div id="step-1" class="step-content active">
        <div class="card">
            <div class="card-body">
                <h4><i class="fas fa-question-circle"></i> Step 1: Generate Questionnaire</h4>
                <p class="text-muted">Create questions for your document using AI</p>

                <div class="mb-4">
                    <label class="form-label"><strong>Technical Specifications</strong></label>
                    <div class="tech-spec-box">
                        {{ $technicalSpecifications->original_prompt ?? 'No technical specifications found' }}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Document Name</strong> <span class="text-danger">*</span></label>
                        <input type="text" id="contractName" class="form-control" placeholder="e.g., Loan Agreement">
                        <div id="contractNameError" class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Parties Type</strong></label>
                        <select id="partiesTypeSelect" name="parties_type" class="form-select" onchange="onPartiesTypeChange(this)">
                            <option value="">— Select Parties Type —</option>
                            @foreach($partiesTemplates as $tpl)
                            <option value="{{ $tpl->parties_type }}" data-name="{{ $tpl->name }}" data-a="{{ $tpl->party_a_count }}" data-b="{{ $tpl->party_b_count }}" data-id="{{ $tpl->id }}">
                                {{ $tpl->parties_type }} — {{ $tpl->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Party Label Inputs (rendered dynamically) --}}
                <div id="partyLabelsContainer" class="mb-4"></div>

                {{-- NEW: AI-Powered Define Standard Clauses             --}}
                <div class="mb-4" id="aiStandardClausesSection">
                    <label class="form-label d-block mb-2"><strong>Standard Clauses</strong></label>

                    <button type="button" class="define-clauses-btn" id="defineStandardClausesBtn" onclick="toggleAIStandardClausesPanel()">
                        <span class="dc-icon"><i class="fas fa-file-contract"></i></span>
                        <span class="dc-label">
                            Define standard clauses
                            <small class="d-block text-muted" style="font-size:11px;font-weight:400;">
                                AI will analyse your contract and pre-select the best matching clauses
                            </small>
                        </span>
                        <span class="dc-chevron" id="aiClausesChevron"><i class="fas fa-chevron-down"></i></span>
                    </button>

                    <div id="aiClausesPanel">

                        {{-- ── SCANNING STATE ── --}}
                        <div class="ai-clauses-scanning" id="aiClausesScanningState">
                            <div class="ai-scan-spinner">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="ai-scan-title">AI is reviewing your clauses…</div>
                            <div class="ai-scan-sub" id="aiScanSubText">
                                Matching standard clauses to your contract type
                            </div>
                            <div class="ai-progress-bar">
                                <div class="ai-progress-fill" id="aiClausesProgressFill"></div>
                            </div>
                        </div>

                        <div id="aiClausesReadyState" style="display:none;">

                            {{-- Header row --}}
                            <div class="ai-clauses-header">
                                <div class="ai-clauses-header-left">
                                    <span class="ai-clauses-title">Standard clauses</span>
                                    <span class="ai-selected-badge" id="aiClausesSelectedCount">0 selected</span>
                                </div>
                                <div class="ai-clauses-header-right">
                                    <input type="text" class="ai-search-input" placeholder="Search clauses…" oninput="aiClausesSearch(this.value)" id="aiClausesSearchInput">
                                    {{-- <select class="ai-type-filter" onchange="aiClausesFilterType(this.value)">
                                        <option value="all">All types</option>
                                        <option value="national">National</option>
                                        <option value="state_specific">State-specific</option>
                                    </select> --}}
                                </div>
                            </div>

                            <div class="ai-note-bar">
                                <i class="fas fa-magic"></i>
                                <span>
                                    AI has pre-selected clauses that best fit your contract.
                                    Unchecked clauses were flagged as unlikely to apply — you can still include them manually.
                                </span>
                            </div>

                            <div class="ai-clauses-list" id="aiClausesList"></div>

                            {{-- Empty search result --}}
                            <div class="ai-clauses-empty" id="aiClausesEmptyState" style="display:none;">
                                <i class="fas fa-search"></i>
                                No clauses match your search.
                            </div>

                            {{-- Apply bar --}}
                            <div class="ai-clauses-apply-bar">
                                <div>
                                    <div class="ai-apply-count-text">
                                        <strong id="aiClausesCheckedNum">0</strong>
                                        of <span id="aiClausesTotalNum">0</span> clauses selected
                                    </div>
                                    <div class="ai-bulk-actions">
                                        <button class="ai-bulk-btn ai-select-all-btn" onclick="aiClausesSelectAll(true)">Select all</button>
                                        <button class="ai-bulk-btn ai-deselect-all-btn" onclick="aiClausesSelectAll(false)">Deselect all</button>
                                    </div>
                                </div>
                                <button class="ai-clauses-apply-btn" id="aiClausesApplyBtn" disabled onclick="applyAISelectedClauses()">
                                    <i class="fas fa-check-circle me-1"></i> Apply to contract
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div id="qStepIndicator" class="step-indicator" style="display:none;"></div>
            <div id="qStepOutput" class="mb-3"></div>
            <div id="qNextAction" class="mt-3"></div>

            {{-- <div class="d-flex gap-2 mt-3" style="margin: 40px 20px;">
                <button id="qSubmitBtn" class="btn btn-primary" onclick="submitQuestionnaireStep()">
                    <i class="fas fa-paper-plane"></i> Start Generation
                </button>
                <button id="qBackBtn" class="btn btn-secondary d-none" onclick="goPrevQuestionnaireStep()">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button class="btn btn-secondary" onclick="resetQuestionnaireGeneration()">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div> --}}
            <div class="d-flex gap-2 mt-3" id="qActionButtons" style="margin: 40px 20px; display:none !important;">
    
    <button id="qBackBtn" class="btn btn-secondary d-none" onclick="goPrevQuestionnaireStep()">
        <i class="fas fa-arrow-left"></i> Previous
    </button>
    <button class="btn btn-secondary" onclick="resetQuestionnaireGeneration()">
        <i class="fas fa-redo"></i> Reset
    </button>
    <buottn id="qSubmitBtn" class="btn btn-primary" onclick="submitQuestionnaireStep()">
        <i class="fas fa-paper-plane"></i> Start Generation
    </buottn>
</div>
        </div>
    </div>
</div>

{{-- STEP 2: EDIT QUESTIONS --}}
<div id="step-2" class="step-content">
    <div class="card">
        <div class="card-body">
            <h4><i class="fas fa-edit"></i> Step 2: Edit Questions</h4>
            <p class="text-muted">Click to edit, drag to reorder</p>
            <div id="questionEditor"></div>
            <div class="d-flex gap-2 mt-4 heig_rem_auto ">
                <button class="btn btn-secondary" onclick="goBack()"><i class="fas fa-arrow-left"></i> Back</button>
                <button class="btn btn-primary" onclick="goToStep(3)">Next: Contract <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="steps_count" id="steps_count" value="{{ $DocumentGeneratingContract->count() }}">
@foreach($DocumentGeneratingContract as $key => $prompt)
<input type="hidden" name="cPrompt" id="cPrompt{{ $key + 1 }}" data-prompt="{{ $prompt->prompts }}" data-step_no="{{ $prompt->steps_no }}" data-contract_type="{{ $prompt->contract_type }}">
@endforeach

{{-- STEP 3: CONTRACT GENERATION --}}
<div id="step-3" class="step-content">
    <div class="card">
        <div class="card-body">
            <h4><i class="fas fa-file-contract"></i> Step 3: Generate Contract</h4>
            <p class="text-muted">Create contract text using AI</p>
            <div class="mb-3">
                <label class="form-label"><strong>Document Name</strong></label>
                <input type="text" id="contractNamePreview" class="form-control" disabled>
            </div>
            <div id="cStepIndicator" class="step-indicator" style="display:none;"></div>
            <div id="cStepOutput" class="mb-3"></div>
            <div id="cNextAction" class="mt-3"></div>
            <div class="d-flex gap-2 mt-3 inner-contract-new heig_rem_auto " style="margin: 30px 20px;">
                <button id="cSubmitBtn" class="btn btn-primary" onclick="submitContractStep()">
                    <i class="fas fa-paper-plane"></i> Start Generation
                </button>
                <button id="cBackBtn" class="btn btn-secondary d-none" onclick="goPrevContractStep()">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <button class="btn btn-secondary" onclick="goBack()"><i class="fas fa-arrow-left"></i> Back</button>
                <button class="btn btn-warning" onclick="resetContractGeneration()"><i class="fas fa-redo"></i> Reset</button>
            </div>
        </div>
    </div>
</div>

{{-- STEP 4: EDIT CONTRACT --}}
<div id="step-4" class="step-content">
    <div class="card">
        <div class="card-body">
            <h4><i class="fas fa-edit"></i> Step 4: Edit Contract</h4>
            <p class="text-muted">Click to edit sections, drag to reorder</p>
            <div id="contractEditorContainer"></div>
            <div class="d-flex gap-2 mt-4 heig_rem_auto">
                <button class="btn btn-secondary" onclick="goBack()"><i class="fas fa-arrow-left"></i> Back</button>
                <button class="btn btn-success organge-hov" onclick="goToStep(5)"><i class="fas fa-check"></i> Finish</button>
            </div>
        </div>
    </div>
</div>

{{-- STEP 5: FINAL DOCUMENT --}}
<div id="step-5" class="step-content">
    <div class="card">
        <div class="card-body">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                <p class="mt-3 text-muted">Loading final document preview...</p>
            </div>
        </div>
    </div>
</div>

{{-- STEP 6: FRONTPAGE --}}
<div id="step-6" class="step-content">
    <div class="card">
        <div class="card-body">
            <h4><i class="fas fa-file-text"></i> Step 6: FrontPage</h4>
            <p class="text-muted">Review and edit your document's front page details</p>

            <form id="step6MainForm" onsubmit="event.preventDefault(); manualSaveDocument(event);">
                <input type="hidden" name="slug" id="document_slug_hidden" value="{{ $document->slug ?? '' }}">
                <input type="hidden" name="id" id="document_id">
                <input type="hidden" name="published" id="published" value="0">

                <div class="row main_section_div" id="finalReviewContainer">
                    <div class="col col-md-8 doc-left-content">
                    <div class="doc-title-des">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <div class="col-md-12 doc-title">
                                    <div class="form-group">
                                        <label class="form-label" for="title">
                                            <b>
                                                <h4>Document Title <small><b>{Document_Title}</b></small></h4>
                                            </b>
                                        </label>
                                        <input type="text" name="title" class="form-control form-control-lg" id="title" placeholder="Add title">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Document Image --}}
                        <div class="card card-bordered card-preview mt-4">
                            <div class="card-inner">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mt-2">Document Image</h5>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group doc_img" id="document_image_container" style="display:block; width:140px; height:140px;">
                                        <img src="" alt="Document Image" id="document_image" style="display:none;">
                                    </div>
                                    <br>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#generateSVG">Edit</button>
                                </div>
                                <div class="modal fade" tabindex="-1" id="generateSVG">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit SVG</h5>
                                                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close"><em class="icon ni ni-cross"></em></a>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="col-md-6 mb-2"><input type="text" class="form-control image_name_input" id="image_name_1" placeholder="Line-1"></div>
                                                    <div class="col-md-6 mb-2"><input type="text" class="form-control image_name_input" id="image_name_2" placeholder="Line-2"></div>
                                                    <div class="col-md-6 mb-2"><input type="text" class="form-control image_name_input" id="image_name_3" placeholder="Line-3"></div>
                                                    <div class="col-md-6 mb-2"><input type="text" class="form-control image_name_input" id="image_name_4" placeholder="Line-4"></div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button class="btn btn-sm btn-primary" type="button" onclick="generateSVG()">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        {{-- Short Description --}}
                        <div class="card card-bordered card-preview mt-4">
                            <div class="card-inner">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mt-2">Short Description <small><b>{Short_Description}</b></small></h5>
                                </div>
                                <div class="row gy-12 mt-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <textarea name="short_description" class="form-control" id="short_description" rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Article Sections --}}
                        <div id="articleSectionsContainer"></div>
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-sm btn-primary" onclick="addStep6ArticleSection()">Add Article Section</button>
                        </div>

                        {{-- FAQ Section --}}
                        <div class="card card-bordered card-preview mt-4">
                            <div class="card-inner">
                                <h5 class="mt-4">FAQ Section <small><b>{FAQ_Section}</b></small></h5>
                                <div id="faqSectionsContainer"></div>
                                <div class="text-end mt-3">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addStep6FaqSection()">Add FAQ</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col col-md-4 doc-right-content">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                {{-- Action Buttons --}}
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="nk-block-head-content butn-cls">
                                        <div class="mbsc-form-group view_btn">
                                            @if(isset($document) && $document->published == '1')
                                            <a href="{{ url('/document/' . ($document->slug ?? '')) }}" target="_blank" class="view_page">View Page</a>
                                            @else
                                            <a href="javascript:void(0);" class="view_page" onclick="isNotView()">View Page</a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="nk-block-head-content">
                                        <div class="up-btn mbsc-form-group">
                                            <button onclick="manualSaveDocument(event)" class="btn btn-sm btn-primary" type="submit">Save</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <p>Published</p>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="publish1" onchange="toggleStep6Publish()">
                                            <label class="custom-control-label" for="publish1"></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <div class="form-group">
                                        <h5>Document Categories <small><b>{Document_Categories}</b></small></h5>
                                        <div class="form-control-wrap">
                                            <select class="form-select js-select2" name="category_id[]" id="category_id" multiple>
                                                @foreach($categories ?? [] as $category)
                                                <option value="{{ $category->id }}" @selected(in_array($category->id, old('category_id', $selectedCategoryIds ?? [])))>
                                                    {{ $category->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <div class="form-group">
                                        <label class="form-label" for="doc_price">Price</label>
                                        {{-- <input type="number" class="form-control" id="doc_price" name="doc_price" value="{{ old('doc_price', $document->doc_price ?? '') }}"> --}}
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

                                {{-- Permalink --}}
                                <div class="card card-bordered card-preview mt-3">
                                    <div class="card-inner">
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label class="form-label">Permalink:</label>
                                                <span id="permalink_display">
                                                    <span id="editable_post_name" style="font-weight:600;">{{ $document->slug ?? '' }}</span>
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="editStep6Slug()">Edit</button>
                                                </span>
                                                <span id="permalink_edit" style="display:none;">
                                                    <input type="text" id="slug_input" class="form-control d-inline" style="width:auto;">
                                                    <small id="slug_error" class="error-message text-danger"></small>
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="saveStep6Slug()">Accept</button>
                                                    <button type="button" class="btn btn-sm btn-secondary" onclick="cancelStep6Slug()">Cancel</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Meta Information --}}
                                <div class="card card-bordered card-preview mt-3">
                                    <div class="card-inner">
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label class="form-label" for="meta_title">Meta Title <small><b>{Meta_Title}</b></small></label>
                                                <input type="text" name="meta_title" class="form-control" id="meta_title" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label class="form-label" for="meta_description">Meta Description <small><b>{Meta_Description}</b></small></label>
                                                <textarea name="meta_description" class="form-control" id="meta_description" maxlength="155"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Keywords --}}
                                <div class="card card-bordered card-preview mt-3">
                                    <div class="card-inner">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mt-4">Keywords <small><b>{Keywords}</b></small></h6>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label class="form-label" for="primary_keywords">Primary Keyword</label>
                                                <input type="text" name="primary_keywords" class="form-control" id="primary_keywords">
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <div class="form-group">
                                                <label class="form-label" for="secondary_keywords">Secondary Keywords</label>
                                                <textarea name="secondary_keywords" class="form-control" id="secondary_keywords"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="d-flex gap-2 mt-4 heig_rem_auto">
                <button class="btn btn-secondary" onclick="goBack()"><i class="fas fa-arrow-left"></i> Back</button>
            </div>
        </div>
    </div>
</div>

{{-- Save Indicator --}}
<div class="save-indicator" id="save_indicator" style="display: none;">
    <i class="fas fa-spinner fa-spin"></i> <span id="save_text">Saving...</span>
</div>

@section('js')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script src="{{ asset('assets/admin/document-generator-beta.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contractInput = document.getElementById('contractName');
        if (contractInput) {
            contractInput.addEventListener('input', function() {
                const subText = document.getElementById('aiScanSubText');
                if (subText) {
                    subText.textContent = this.value.trim() ?
                        `Matching clauses for "${this.value.trim()}"` :
                        'Matching standard clauses to your contract type';
                }
                if (standardClausesScanned && this.value.trim()) {
                    standardClausesScanned = false;
                    aiProcessedClauses = [];
                }
            });
        }
    });

</script>
@endsection
@endsection
