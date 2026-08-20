@extends('admin_layout.master')
@section('content')
<style>
    .placeholders-inps {
        position: relative;
    }

    .placeholders-inps label {
        position: absolute;
        top: -8px;
        background: #fff;
        font-size: 11px;
        left: 18px;
        color: #80868b;
        padding-inline: 6px;
        margin: 0;
        font-weight: 400 !important;
    }

    .placeholders-inps input,
    .placeholders-inps select {
        padding: 10px;
    }

    .row.g-2 .placeholders-inps {
        margin-top: 25px;
    }

    .form-delete-btn button {
        display: inline-flex;
        height: 26px;
        width: 26px;
        background-color: #80808036;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        margin: 4px;
        border-radius: 50% !important;
        font-size: 14px;
        border: 0;
    }

    .form-delete-btn button i {
        font-size: 10px;
        color: #526484;
    }

    /* ── Contract Editor split layout ── */
    .ce-wrap {
        display: flex;
        /* grid-template-columns: 1fr 1fr; */
        gap: 0;
        height: calc(100vh - 260px);
        min-height: 500px;
        border-radius: 12px;
        overflow: hidden;
    }

    .ce-pane {
        border: 1px solid #e0e0e0;
    }

    div#questionRows {
        max-height: 460px;
        overflow-y: scroll;
        scrollbar-width: thin;
        scrollbar-color: #c1c7d0 #f1f3f5;
    }

    .ce-pane {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .ce-pane-left {
        border-right: 1px solid #e0e0e0;
    }

    .ce-pane-header {
        padding: 12px 16px;
        background: #fafafa;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
        color: #1a1a2e;
    }
.ce-pane-right .ce-pane-header,
    .ce-pane-left .ce-pane-header {
        flex-direction: column;
        align-items: unset;
    }

    .ce-pane-header .top-head-wrp,
    .ce-pane-header .head-inpt-btn {
        display: flex;
        gap: 10px;
    }


    .ce-pane-header .ce-count {
        background: #FF6B35;
        color: #fff;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 12px;
        font-weight: 600;
    }

    .ce-pane-body {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        scrollbar-width: thin;
        scrollbar-color: #c1c7d0 #f1f3f5;
    }

    .ce-add-btn {
        background: #FF6B35;
        border: none;
        color: #fff;
        border-radius: 6px;
        padding: 5px 14px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .ce-search {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 13px;
    }

    /* Question card */
    .q-card {
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        background: #fff;
        margin-bottom: 10px;
        overflow: hidden;
    }

    .q-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: #f9f9f9;
        border-bottom: 1px solid #eee;
    }

    .q-card-body {
        padding: 10px 12px;
    }

    .q-badge-pill {
        background: #FF6B35;
        color: #fff;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 10px;
        font-weight: 700;
    }

    .q-action-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #999;
        font-size: 14px;
        padding: 2px 5px;
        border-radius: 4px;
        transition: color .2s;
    }

    .q-action-btn:hover {
        color: #dc3545;
    }

    .q-field {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 7px 10px;
        font-size: 13px;
        height: 37px;
        background-color: transparent;
    }

    .q-field:focus {
        outline: none;
        border-color: #FF6B35;
    }

    /* Text block card */
    .tb-card {
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        background: #fff;
        margin-bottom: 10px;
        overflow: hidden;
    }

    .tb-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: #f9f9f9;
        border-bottom: 1px solid #eee;
    }

    .tb-card-body {
        padding: 10px 12px;
    }

    .tb-badge-pill {
        background: #526484;
        color: #fff;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 10px;
        font-weight: 700;
    }

    .tb-textarea {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 8px 10px;
        font-size: 13px;
        resize: vertical;
        min-height: 100px;
    }

    .tb-textarea:focus {
        outline: none;
        border-color: #FF6B35;
    }

    /* Card action icon buttons */
    .card-action-group {
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .card-icon-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #999;
        font-size: 13px;
        padding: 3px 6px;
        border-radius: 4px;
        transition: color .2s, background .2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .card-icon-btn:hover {
        color: #FF6B35;
        background: #fff3ee;
    }

    .card-icon-btn.danger:hover {
        color: #dc3545;
        background: #fff0f0;
    }

    /* .card-icon-btn.move-btn:hover { color: #526484; background: #f0f3f7; } */
    .drag-handle {
        cursor: grab;
        color: #bbb;
        padding: 3px 6px;
        display: inline-flex;
        align-items: center;
        font-size: 14px;
    }

    .drag-handle:active {
        cursor: grabbing;
    }

    /*  Copy flash toast */
    .copy-flash {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: #1a1a2e;
        color: #fff;
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s, transform .25s;
        z-index: 9999;
    }

    .copy-flash.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .sortable-ghost {
        opacity: 0.4;
        background: #fff3ee;
        border: 1px dashed #FF6B35;
    }

    #panel_editor .ce-pane-left {
        max-width: 450px;
        width: 100%;
    }

    #panel_editor .ce-pane-right {
        flex: 1;
    }

    #panel_editor .card-action-group .card-icon-btn {
        height: 22px;
        width: 22px;
        background-color: #e9ecef;
        border-radius: 50%;
        color: #6b7280;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    #panel_editor .card-action-group .card-icon-btn:hover {
        color: #e85d2f;
        background-color: #ffe4db;
    }

    #panel_config {
        margin-left: 20px;
    }

    #panel_config .row {
        margin-inline: unset !important;
    }


    @media screen and (max-width:991px) {
        #panel_editor .ce-pane-left {
            max-width: 340px;
            width: 100%;
        }

        .ce-pane-header {
            flex-wrap: wrap;
        }

        #panel_config {
            margin-left: 0;
        }

    }

    @media screen and (max-width: 767px) {
        .ce-wrap {
            flex-Direction: column;
            gap: 20px;
        }


        #panel_editor .ce-pane-left {
            max-width: 100%;
            width: 100%;
        }

        #panel_config .col-md-8 .card {
            margin-bottom: 20px
        }

        .ce-pane-header {
            flex-wrap: wrap;
        }

        div#questionRows {
            max-height: 260px;
            overflow-y: scroll;
            scrollbar-width: none;
        }

        .ce-wrap {
            height: 100%;
        }
.ce-pane-header .top-head-wrp, .ce-pane-header .head-inpt-btn {
   flex-wrap: wrap;
}
       
    }

</style>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4" style="margin-top:80px; margin-left:20px;">
        <h4>
            <i class="fas fa-users"></i>
            {{ isset($partiesTemplate) ? 'Edit' : 'Create' }} Parties Section Template
        </h4>
        <a href="{{ route('admin.parties-templates') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div style="
        background:#ebebeb; border-radius:50px; padding:6px 8px;
        display:inline-flex; gap:4px; margin-bottom:24px;
        border:1.5px solid #d0d0d0; margin-left:20px;">
        <button type="button" id="tab_config" onclick="switchTab('config')" style="border-radius:50px;border:none;padding:10px 24px;font-weight:600;
                   font-size:14px;cursor:pointer;transition:all .2s;
                   background:#FF6B35;color:#fff;">
            Configuration
        </button>
        <button type="button" id="tab_editor" onclick="switchTab('editor')" style="border-radius:50px;border:none;padding:10px 24px;font-weight:600;
                   font-size:14px;cursor:pointer;transition:all .2s;
                   background:transparent;color:#444;">
            Contract Editor
        </button>
    </div>

    <form action="{{ isset($partiesTemplate)
        ? route('admin.parties-templates.update', $partiesTemplate)
        : route('admin.parties-templates.store') }}" method="POST" id="partiesForm">
        @csrf
        @if(isset($partiesTemplate)) @method('PUT') @endif


        <div id="panel_config">
            <div class="row">
                <div class="col-md-8">
                    <div class="card" style="border:1px solid #e0e0e0;border-radius:12px;">
                        <div class="card-body p-4">

                            <div class="mb-4">
                                <label class="form-label fw-bold" style="font-size:15px;">Title</label>
                                <input type="text" name="name" class="form-control mt-1" placeholder="e.g. One Client, One Service Provider" value="{{ old('name', $partiesTemplate->name ?? '') }}" required>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold" style="font-size:13px;">
                                        Parties Type <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="parties_type" class="form-control form-control-sm" placeholder="e.g. 1-1, 1-3" value="{{ old('parties_type', $partiesTemplate->parties_type ?? '') }}" required>
                                    <small class="text-muted" style="font-size:11px;">[Side A]-[Side B]</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold" style="font-size:13px;">
                                        Side A Count <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="party_a_count" class="form-control form-control-sm" min="1" value="{{ old('party_a_count', $partiesTemplate->party_a_count ?? 1) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold" style="font-size:13px;">
                                        Side B Count <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="party_b_count" class="form-control form-control-sm" min="1" value="{{ old('party_b_count', $partiesTemplate->party_b_count ?? 1) }}" required>
                                </div>
                            </div>

                            <div class="mt-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $partiesTemplate->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card" style="border:1px solid #e0e0e0;border-radius:12px;">
                        <div class="card-body p-3">
                            <button type="submit" style="background:#FF6B35;border:none;color:#fff;
                                       border-radius:30px;padding:10px 28px;
                                       font-weight:600;font-size:14px;cursor:pointer;">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="panel_editor" style="display:none; margin-left:20px; margin-right:20px;">

            {{-- Top save card --}}
            <div class="d-flex justify-content-end mb-3">
                <button type="submit" style="background:#FF6B35;border:none;color:#fff;
                                   border-radius:30px;padding:10px 28px;
                                   font-weight:600;font-size:14px;cursor:pointer;">
                    Save
                </button>
            </div>

            <div class="ce-wrap ">

                <div class="ce-pane ce-pane-left">
                    <div class="ce-pane-header">

                        <div class="top-head-wrp">
                            <i class="fas fa-question-circle" style="color:#FF6B35;"></i>
                            Questionnaire
                            <span class="ce-count" id="qCount"></span>
                        </div>
                        <div class="head-inpt-btn">
                            <input class="ce-search" type="text" id="qSearch" placeholder="Search questions..." oninput="filterQuestions()">
                            <button type="button" class="ce-add-btn" onclick="addQuestion()">
                                + Add
                            </button>
                        </div>
                    </div>
                    <div class="ce-pane-body" id="questionRows">
                        @php
                        $existingQuestions = isset($partiesTemplate) && $partiesTemplate->questions
                        ? (is_array($partiesTemplate->questions)
                        ? $partiesTemplate->questions
                        : json_decode($partiesTemplate->questions, true))
                        : [];
                        @endphp
                        @foreach($existingQuestions as $i => $q)
                        <div class="q-card" data-search="{{ strtolower($q['question'] ?? '') }}">
                            <div class="q-card-header">
                                <span class="q-badge-pill q-badge">Q{{ $i + 1 }}</span>
                                <div class="card-action-group">
                                    <button type="button" class="card-icon-btn" onclick="copyQuestion(this)" title="Copy">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button type="button" class="card-icon-btn" onclick="pasteQuestion(this)" title="Paste below">
                                        <i class="fas fa-paste"></i>
                                    </button>
                                    <button type="button" class="card-icon-btn edit-toggle-q" onclick="toggleQEdit(this)" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button type="button" class="card-icon-btn danger" onclick="removeQuestion(this)" title="Remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    {{-- <button type="button" class="card-icon-btn move-btn" onclick="moveQuestion(this,-1)" title="Move up">
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                    <button type="button" class="card-icon-btn move-btn" onclick="moveQuestion(this,1)" title="Move down">
                                        <i class="fas fa-arrow-down"></i>
                                    </button> --}}
                                    <span class="drag-handle" title="Drag to reorder">
                                        <i class="fas fa-grip-vertical"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="q-card-body">
                                <div class="mb-2">
                                    <label style="font-size:11px;color:#80868b;font-weight:600;">Question Text</label>
                                    <input type="text" class="q-field q-text" readonly placeholder="e.g. Full legal name of [A1_SINGULAR]?" value="{{ $q['question'] ?? '' }}">
                                </div>
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <label style="font-size:11px;color:#80868b;font-weight:600;">Type</label>
                                        <select class="q-field q-type" disabled style="padding:7px 10px;">
                                            <option value="text" {{ ($q['type'] ?? 'text') === 'text'   ? 'selected':'' }}>Text</option>
                                            <option value="date" {{ ($q['type'] ?? '') === 'date'       ? 'selected':'' }}>Date</option>
                                            <option value="number" {{ ($q['type'] ?? '') === 'number'   ? 'selected':'' }}>Number</option>
                                            <option value="email" {{ ($q['type'] ?? '') === 'email'     ? 'selected':'' }}>Email</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label style="font-size:11px;color:#80868b;font-weight:600;">Placeholder Key</label>
                                        <input type="text" class="q-field q-placeholder" readonly placeholder="e.g. A1_NAME" value="{{ $q['placeholder'] ?? '' }}">
                                        <small style="font-size:10px;color:#aaa;">Used as [KEY] in contract text</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="ce-pane ce-pane-right">
                    <div class="ce-pane-header">
                     <div class="top-head-wrp">
                        <i class="fas fa-file-contract" style="color:#FF6B35;"></i>
                        Contract Preview
                        <span class="ce-count" id="tbCount">0</span>
                           </div>
                                 <div class="head-inpt-btn">
                                <input class="ce-search" type="text" id="tbSearch" placeholder="Search sections..." oninput="filterTextBlocks()">
                                <button type="button" class="ce-add-btn" onclick="addTextBlock()">
                                    + Add
                        </button>
                        </div>
                    </div>
                    <div class="ce-pane-body" id="textBlocks">
                        @php
                        $rawTextBlocks = old('text_blocks');
                        $existingTextBlocks = $rawTextBlocks !== null
                        ? (is_array($rawTextBlocks) ? $rawTextBlocks : json_decode($rawTextBlocks, true))
                        : [];
                        if (empty($existingTextBlocks) && isset($partiesTemplate)) {
                        $tb = $partiesTemplate->text_blocks;
                        if ($tb) {
                        $existingTextBlocks = is_array($tb) ? $tb : json_decode($tb, true);
                        }
                        }
                        if (empty($existingTextBlocks) && !empty($partiesTemplate->parties_section_text)) {
                        $existingTextBlocks = [[
                        'block_type' => 'content',
                        'align' => 'left',
                        'text' => $partiesTemplate->parties_section_text,
                        'blur' => false,
                        ]];
                        }
                        @endphp
                        @foreach($existingTextBlocks as $bi => $tb)
                        @php $tbText = $tb['text'] ?? $tb['content'] ?? ''; @endphp
                        <div class="tb-card" data-search="{{ strtolower($tbText) }}">
                            <div class="tb-card-header">
                                <span class="tb-badge-pill tb-badge">S{{ $bi + 1 }}</span>
                                <div class="card-action-group">
                                    <button type="button" class="card-icon-btn" onclick="copyTextBlock(this)" title="Copy">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button type="button" class="card-icon-btn" onclick="pasteTextBlock(this)" title="Paste below">
                                        <i class="fas fa-paste"></i>
                                    </button>
                                    <button type="button" class="card-icon-btn edit-toggle-tb" onclick="toggleTbEdit(this)" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button type="button" class="card-icon-btn danger" onclick="removeTextBlock(this)" title="Remove">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    {{-- <button type="button" class="card-icon-btn move-btn" onclick="moveQuestion(this,-1)" title="Move up">
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                    <button type="button" class="card-icon-btn move-btn" onclick="moveQuestion(this,1)" title="Move down">
                                        <i class="fas fa-arrow-down"></i>
                                    </button> --}}
                                    <span class="drag-handle" title="Drag to reorder">
                                        <i class="fas fa-grip-vertical"></i>
                                    </span>

                                </div>
                            </div>
                            <div class="tb-card-body">
                                <label style="font-size:11px;color:#80868b;font-weight:600;">Text</label>
                                <textarea class="tb-textarea tb-content" rows="6" readonly placeholder="This Agreement is entered into between [A1_NAME] (the &quot;[A1_SINGULAR]&quot;) and [B1_NAME] (the &quot;[B1_SINGULAR]&quot;)...">{{ $tbText }}</textarea>
                                <input type="hidden" class="tb-hidden" value="{{ $tbText }}">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

        <textarea name="questions" id="questionsJson" style="display:none;">{{ old('questions',   isset($partiesTemplate) ? json_encode($partiesTemplate->questions,   JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '[]') }}</textarea>

        <textarea name="parties_section_text" id="textBlocksJson" style="display:none;">
        {{ old('parties_section_text', isset($partiesTemplate)
                ? json_encode($partiesTemplate->parties_section_text, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                : '[]') }}
        </textarea>

    </form>
</div>

{{-- Copy flash toast --}}
<div class="copy-flash" id="copyFlash"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>

<script>
    /* ── Tab switch (unchanged) ── */
    function switchTab(tab) {
        ['config', 'editor'].forEach(function(t) {
            var panel = document.getElementById('panel_' + t);
            var btn = document.getElementById('tab_' + t);
            if (!panel || !btn) return;
            var active = t === tab;
            panel.style.display = active ? '' : 'none';
            btn.style.background = active ? '#FF6B35' : 'transparent';
            btn.style.color = active ? '#fff' : '#444';
        });
        updateCounts();
    }

    function updateCounts() {
        var qc = document.getElementById('qCount');
        var tc = document.getElementById('tbCount');
        if (qc) qc.textContent = document.querySelectorAll('#questionRows .q-card').length;
        if (tc) tc.textContent = document.querySelectorAll('#textBlocks .tb-card').length;
    }

    /* ── Flash toast ── */
    function showCopyFlash(msg) {
        var el = document.getElementById('copyFlash');
        el.textContent = msg || 'Copied!';
        el.classList.add('show');
        setTimeout(function() {
            el.classList.remove('show');
        }, 1800);
    }

    /* Clipboards */
    var _clipboardQ = null;
    var _clipboardTb = null;

    function toggleQEdit(btn) {
        var card = btn.closest('.q-card');
        var fields = card.querySelectorAll('.q-field');
        var isReadonly = fields[0].hasAttribute('readonly');
        fields.forEach(function(f) {
            if (isReadonly) {
                f.removeAttribute('readonly');
                f.removeAttribute('disabled');
            } else {
                f.setAttribute('readonly', true);
                if (f.tagName === 'SELECT') f.setAttribute('disabled', true);
            }
        });
        var icon = btn.querySelector('i');
        if (isReadonly) {
            icon.className = 'fas fa-check';
            btn.style.color = '#FF6B35';
            btn.title = 'Done';
        } else {
            icon.className = 'fas fa-pencil-alt';
            btn.style.color = '';
            btn.title = 'Edit';
        }
    }

    function copyQuestion(btn) {
        var card = btn.closest('.q-card');
        _clipboardQ = {
            question: card.querySelector('.q-text').value
            , type: card.querySelector('.q-type').value
            , placeholder: card.querySelector('.q-placeholder').value
        };
        // showCopyFlash('Question copied!');
    }

    function pasteQuestion(btn) {
        if (!_clipboardQ) {
            showCopyFlash('Nothing to paste!');
            return;
        }
        var card = btn.closest('.q-card');
        var container = document.getElementById('questionRows');
        var newCard = buildQCard(_clipboardQ);
        if (card.nextSibling) container.insertBefore(newCard, card.nextSibling);
        else container.appendChild(newCard);
        renumberQuestions();
        updateCounts();
        // showCopyFlash('Question pasted!');
    }

    function moveQuestion(btn, dir) {
        var card = btn.closest('.q-card');
        var container = document.getElementById('questionRows');
        var cards = Array.from(container.querySelectorAll('.q-card'));
        var idx = cards.indexOf(card);
        var target = cards[idx + dir];
        if (!target) return;
        if (dir === -1) container.insertBefore(card, target);
        else container.insertBefore(target, card);
        renumberQuestions();
    }

    function removeQuestion(btn) {
        btn.closest('.q-card').remove();
        renumberQuestions();
        updateCounts();
    }

    function addQuestion() {
        var container = document.getElementById('questionRows');
        var card = buildQCard({
            question: ''
            , type: 'text'
            , placeholder: ''
        }, true);
        container.appendChild(card);
        renumberQuestions();
        updateCounts();
    }

    function buildQCard(q, startEditable) {
        var card = document.createElement('div');
        card.className = 'q-card';
        card.setAttribute('data-search', (q.question || '').toLowerCase());

        var typeOpts = ['text', 'date', 'number', 'email'].map(function(t) {
            return '<option value="' + t + '"' + (q.type === t ? ' selected' : '') + '>' +
                t.charAt(0).toUpperCase() + t.slice(1) + '</option>';
        }).join('');

        card.innerHTML =
            '<div class="q-card-header">' +
            '<span class="q-badge-pill q-badge">Q</span>' +
            '<div class="card-action-group">' +
            '<button type="button" class="card-icon-btn" onclick="copyQuestion(this)" title="Copy">' +
            '<i class="fas fa-copy fa-xs"></i>' +
            '</button>' +
            '<button type="button" class="card-icon-btn" onclick="pasteQuestion(this)" title="Paste below">' +
            '<i class="fas fa-paste fa-xs"></i>' +
            '</button>' +
            '<button type="button" class="card-icon-btn edit-toggle-q" onclick="toggleQEdit(this)" title="' + (startEditable ? 'Done' : 'Edit') + '">' +
            '<i class="fas ' + (startEditable ? 'fa-check fa-xs' : 'fa-pencil-alt fa-xs') + '"></i>' +
            '</button>' +
            '<button type="button" class="card-icon-btn danger" onclick="removeQuestion(this)" title="Remove">' +
            '<i class="fa fa-trash fa-xs"></i>' +
            '</button>' +
            '<span class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical fa-xs"></i></span>' +
            // '<button type="button" class="card-icon-btn move-btn" onclick="moveQuestion(this,-1)" title="Move up">' +
            //     '<i class="fas fa-arrow-up"></i>' +
            // '</button>' +
            // '<button type="button" class="card-icon-btn move-btn" onclick="moveQuestion(this,1)" title="Move down">' +
            //     '<i class="fas fa-arrow-down"></i>' +
            // '</button>' 

            '</div>' +
            '</div>' +
            '<div class="q-card-body">' +
            '<div class="mb-2">' +
            '<label style="font-size:11px;color:#80868b;font-weight:600;">Question Text</label>' +
            '<input type="text" class="q-field q-text" ' + (startEditable ? '' : 'readonly ') + 'placeholder="e.g. Full legal name of [A1_SINGULAR]?" value="' + escAttr(q.question || '') + '">' +
            '</div>' +
            '<div class="row g-2">' +
            '<div class="col-sm-6">' +
            '<label style="font-size:11px;color:#80868b;font-weight:600;">Type</label>' +
            '<select class="q-field q-type" ' + (startEditable ? '' : 'disabled ') + 'style="padding:7px 10px;">' + typeOpts + '</select>' +
            '</div>' +
            '<div class="col-sm-6">' +
            '<label style="font-size:11px;color:#80868b;font-weight:600;">Placeholder Key</label>' +
            '<input type="text" class="q-field q-placeholder" ' + (startEditable ? '' : 'readonly ') + 'placeholder="e.g. A1_NAME" value="' + escAttr(q.placeholder || '') + '">' +
            '<small style="font-size:10px;color:#aaa;">Used as [KEY] in contract text</small>' +
            '</div>' +
            '</div>' +
            '</div>';

        if (startEditable) {
            var editBtn = card.querySelector('.edit-toggle-q');
            if (editBtn) editBtn.style.color = '#FF6B35';
        }
        return card;
    }

    function renumberQuestions() {
        document.querySelectorAll('#questionRows .q-card').forEach(function(card, i) {
            var badge = card.querySelector('.q-badge');
            if (badge) badge.textContent = 'Q' + (i + 1);
        });
    }

    function filterQuestions() {
        var term = document.getElementById('qSearch').value.toLowerCase();
        document.querySelectorAll('#questionRows .q-card').forEach(function(card) {
            var txt = (card.getAttribute('data-search') || '') +
                (card.querySelector('.q-text') ? card.querySelector('.q-text').value.toLowerCase() : '');
            card.style.display = txt.includes(term) ? '' : 'none';
        });
    }

    /* 
       TEXT BLOCK ACTIONS
     */

    function toggleTbEdit(btn) {
        var card = btn.closest('.tb-card');
        var ta = card.querySelector('.tb-content');
        var isReadonly = ta.hasAttribute('readonly');
        if (isReadonly) ta.removeAttribute('readonly');
        else ta.setAttribute('readonly', true);

        var icon = btn.querySelector('i');
        if (isReadonly) {
            icon.className = 'fas fa-check';
            btn.style.color = '#FF6B35';
            btn.title = 'Done';
            ta.focus();
        } else {
            icon.className = 'fas fa-pencil-alt';
            btn.style.color = '';
            btn.title = 'Edit';
        }
    }

    function copyTextBlock(btn) {
        var card = btn.closest('.tb-card');
        _clipboardTb = {
            block_type: 'content'
            , align: 'left'
            , text: card.querySelector('.tb-content').value
            , blur: false
        };
        // showCopyFlash('Section copied!');
    }

    function pasteTextBlock(btn) {
        if (!_clipboardTb) {
            showCopyFlash('Nothing to paste!');
            return;
        }
        var card = btn.closest('.tb-card');
        var container = document.getElementById('textBlocks');
        var newCard = buildTbCard(_clipboardTb);
        if (card.nextSibling) container.insertBefore(newCard, card.nextSibling);
        else container.appendChild(newCard);
        renumberTextBlocks();
        updateCounts();
        // showCopyFlash('Section pasted!');
    }

    function moveTextBlock(btn, dir) {
        var card = btn.closest('.tb-card');
        var container = document.getElementById('textBlocks');
        var cards = Array.from(container.querySelectorAll('.tb-card'));
        var idx = cards.indexOf(card);
        var target = cards[idx + dir];
        if (!target) return;
        if (dir === -1) container.insertBefore(card, target);
        else container.insertBefore(target, card);
        renumberTextBlocks();
    }

    function removeTextBlock(btn) {
        btn.closest('.tb-card').remove();
        renumberTextBlocks();
        updateCounts();
    }

    function addTextBlock() {
        var container = document.getElementById('textBlocks');
        var card = buildTbCard({
            text: ''
        }, true);
        container.appendChild(card);
        renumberTextBlocks();
        updateCounts();
    }

    function buildTbCard(tb, startEditable) {
        var text = tb.text || tb.content || '';
        var card = document.createElement('div');
        card.className = 'tb-card';
        card.setAttribute('data-search', text.toLowerCase());

        card.innerHTML =
            '<div class="tb-card-header">' +
            '<span class="tb-badge-pill tb-badge">S</span>' +
            '<div class="card-action-group">' +
            '<button type="button" class="card-icon-btn" onclick="copyTextBlock(this)" title="Copy">' +
            '<i class="fas fa-copy fa-xs"></i>' +
            '</button>' +
            '<button type="button" class="card-icon-btn" onclick="pasteTextBlock(this)" title="Paste below">' +
            '<i class="fas fa-paste fa-xs"></i>' +
            '</button>' +
            '<button type="button" class="card-icon-btn edit-toggle-tb" onclick="toggleTbEdit(this)" title="' + (startEditable ? 'Done' : 'Edit') + '">' +
            '<i class="fas ' + (startEditable ? 'fa-check fa-xs' : 'fa-pencil-alt fa-xs') + '"></i>' +
            '</button>' +
            '<button type="button" class="card-icon-btn danger" onclick="removeTextBlock(this)" title="Remove">' +
            '<i class="fa fa-trash fa-xs"></i>' +
            '</button>' +
            '<span class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical fa-xs"></i></span>' +

            // '<button type="button" class="card-icon-btn move-btn" onclick="moveTextBlock(this,-1)" title="Move up">' +
            //     '<i class="fas fa-arrow-up"></i>' +
            // '</button>' +
            // '<button type="button" class="card-icon-btn move-btn" onclick="moveTextBlock(this,1)" title="Move down">' +
            //     '<i class="fas fa-arrow-down"></i>' +
            // '</button>' +                 
            '</div>' +
            '</div>' +
            '<div class="tb-card-body">' +
            '<label style="font-size:11px;color:#80868b;font-weight:600;">Text</label>' +
            '<textarea class="tb-textarea tb-content" rows="6" ' + (startEditable ? '' : 'readonly ') +
            'placeholder="This Agreement is entered into between [A1_NAME] (the &quot;[A1_SINGULAR]&quot;) and [B1_NAME] (the &quot;[B1_SINGULAR]&quot;)...">' +
            escText(text) +
            '</textarea>' +
            '<input type="hidden" class="tb-hidden" value="' + escAttr(text) + '">' +
            '</div>';

        if (startEditable) {
            var editBtn = card.querySelector('.edit-toggle-tb');
            if (editBtn) editBtn.style.color = '#FF6B35';
        }
        return card;
    }

    function renumberTextBlocks() {
        document.querySelectorAll('#textBlocks .tb-card').forEach(function(card, i) {
            var badge = card.querySelector('.tb-badge');
            if (badge) badge.textContent = 'S' + (i + 1);
        });
    }

    function filterTextBlocks() {
        var term = document.getElementById('tbSearch').value.toLowerCase();
        document.querySelectorAll('#textBlocks .tb-card').forEach(function(card) {
            var txt = (card.getAttribute('data-search') || '') +
                (card.querySelector('.tb-content') ? card.querySelector('.tb-content').value.toLowerCase() : '');
            card.style.display = txt.includes(term) ? '' : 'none';
        });
    }

    /* ── Form submit (unchanged logic) ── */
    document.getElementById('partiesForm').addEventListener('submit', function() {
        var questions = [];
        document.querySelectorAll('#questionRows .q-card').forEach(function(card) {
            var question = (card.querySelector('.q-text') ? card.querySelector('.q-text').value.trim() : '');
            var type = (card.querySelector('.q-type') ? card.querySelector('.q-type').value : 'text');
            var placeholder = (card.querySelector('.q-placeholder') ? card.querySelector('.q-placeholder').value.trim() : '');
            if (question || placeholder) {
                questions.push({
                    question: question
                    , type: type
                    , placeholder: placeholder
                });
            }
        });
        document.getElementById('questionsJson').value = JSON.stringify(questions);

        var textBlocks = [];
        document.querySelectorAll('#textBlocks .tb-card').forEach(function(card) {
            var contentEl = card.querySelector('.tb-content');
            var hiddenEl = card.querySelector('.tb-hidden');
            var text = contentEl ? contentEl.value : '';
            if (hiddenEl) hiddenEl.value = text;
            if (text.trim()) {
                textBlocks.push({
                    block_type: 'content'
                    , align: 'left'
                    , text: text
                    , blur: false
                });
            }
        });
        document.getElementById('textBlocksJson').value = JSON.stringify(textBlocks);
    });



    /* Sortable drag-and-drop */
    document.addEventListener('DOMContentLoaded', function() {
    updateCounts();

    if (document.querySelectorAll('#questionRows .q-card').length === 0) addQuestion();
    if (document.querySelectorAll('#textBlocks .tb-card').length === 0) addTextBlock();

    Sortable.create(document.getElementById('questionRows'), {
        animation: 150
        , handle: '.drag-handle'
        , ghostClass: 'sortable-ghost'
        , onEnd: function() {}
    });

    Sortable.create(document.getElementById('textBlocks'), {
        animation: 150
        , handle: '.drag-handle'
        , ghostClass: 'sortable-ghost'
        , onEnd: function() {}
    });
});

    function escAttr(str) {
        return String(str)
            .replace(/&/g, '&amp;').replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function escText(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

</script>
@endsection
