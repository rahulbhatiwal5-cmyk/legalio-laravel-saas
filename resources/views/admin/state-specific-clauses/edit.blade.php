@extends('admin_layout.master')
@section('title', 'Edit State-Specific Clause')

@section('content')
<div class="container-fluid px-4 py-4" style="margin-top:60px;">
    <div class="mb-4">
        <div class="d-flex align-items-center mb-2">
            <a href="{{ route('index') }}" class="btn btn-sm btn-outline-secondary me-3" style="color:white;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h3 mb-0 text-gray-800">Edit State-Specific Clause</h1>
        </div>
        <p class="text-muted">Modify existing state-specific clause</p>
    </div>
    
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('update', $clause->id) }}" id="clauseForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Clause Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Clause Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="{{ old('title', $clause->title) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Short Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description', $clause->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="text" class="form-label">Clause Text <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="text" name="text" rows="10" required>{{ old('text', $clause->text) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Questions (Optional)</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" style="color:white;" onclick="addQuestion()">
                            <i class="fas fa-plus me-1"></i>Add Question
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="questionsContainer">
                            @if($clause->questions && count($clause->questions) > 0)
                                @foreach($clause->questions as $index => $question)
                                <div class="question-item" id="question_{{ $index }}">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>Question {{ $index + 1 }}</strong>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion({{ $index }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Question Text</label>
                                        <input type="text" class="form-control" name="questions[{{ $index }}][question]" 
                                               value="{{ $question['question'] ?? '' }}" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Answer Type</label>
                                        <select class="form-select" name="questions[{{ $index }}][type]" required>
                                            <option value="text" {{ ($question['type'] ?? '') === 'text' ? 'selected' : '' }}>Text</option>
                                            <option value="textarea" {{ ($question['type'] ?? '') === 'textarea' ? 'selected' : '' }}>Long Text</option>
                                            <option value="number" {{ ($question['type'] ?? '') === 'number' ? 'selected' : '' }}>Number</option>
                                            <option value="date" {{ ($question['type'] ?? '') === 'date' ? 'selected' : '' }}>Date</option>
                                            <option value="select" {{ ($question['type'] ?? '') === 'select' ? 'selected' : '' }}>Dropdown</option>
                                            <option value="checkbox" {{ ($question['type'] ?? '') === 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                        </select>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center py-3" id="noQuestionsMsg">
                                    No questions added yet. Click "Add Question" to add custom questions.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4 pb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Clause Type</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Classify this clause as <span class="text-danger">*</span></label>
                            <select class="form-select" name="clause_type" id="clauseTypeSelect" required onchange="toggleStateSelector()">
                                <option value="national" {{ old('clause_type', $clause->clause_type ?? 'national') === 'national' ? 'selected' : '' }}>
                                    National
                                </option>
                                <option value="state_specific" {{ old('clause_type', $clause->clause_type ?? '') === 'state_specific' ? 'selected' : '' }}>
                                    State-Specific
                                </option>
                            </select>
                        </div>

                        {{-- State selector — only shown when state_specific is selected --}}
                        <div id="statesSelectorWrapper" style="{{ old('clause_type', $clause->clause_type ?? 'national') === 'state_specific' ? '' : 'display:none;' }}">
                            <label class="form-label">Select State(s) <span class="text-danger">*</span></label>
                            <select class="form-select" name="states[]" id="statesSelect" multiple size="6">
                                @foreach($states as $state)
                                    <option value="{{ $state }}"
                                        {{ in_array($state, old('states', is_array($clause->states) ? $clause->states : [$clause->state])) ? 'selected' : '' }}>
                                        {{ $state }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Hold Ctrl / Cmd to select multiple states.</small>

                            {{-- Display currently assigned states as badges --}}
                            @php
                                $assignedStates = is_array($clause->states) ? $clause->states : (isset($clause->state) ? [$clause->state] : []);
                            @endphp
                            @if($clause->clause_type === 'state_specific' && count($assignedStates))
                            <div class="mt-2">
                                <small class="text-muted d-block mb-1">Currently assigned:</small>
                                <div class="d-flex flex-wrap gap-1" id="assignedStatesBadges">
                                    @foreach($assignedStates as $st)
                                        <span class="badge bg-light text-dark border">{{ $st }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Message shown when national is selected --}}
                        <div id="nationalNote" style="{{ old('clause_type', $clause->clause_type ?? 'national') === 'national' ? '' : 'display:none;' }}">
                            <p class="text-muted mb-0"><i class="fas fa-globe me-1"></i> This clause applies to <strong>all states</strong> nationally.</p>
                        </div>
                    </div>
                </div>

                <!-- Other State Versions -->
                @if($clauseVersions->count() > 1)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Other State Versions</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">This clause exists for multiple states:</p>
                        <div class="list-group list-group-flush">
                            @foreach($clauseVersions as $version)
                                @if($version->id !== $clause->id)
                                <a href="{{ route('edit', $version->id) }}" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    {{ $version->state }}
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Submit Actions -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-outline-secondary w-100 mb-2" style="color:white;">
                            <i class="fas fa-save me-2"></i>Update Clause
                        </button>
                        <a href="{{ route('index') }}" class="btn btn-outline-secondary w-100" style="color:white;">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.25rem;
    }
    
    .question-item {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        background-color: #f8f9fc;
    }
    
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 0.75rem 0;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }

    .badge.bg-light {
        font-size: 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script>
let questionCount = {{ $clause->questions ? count($clause->questions) : 0 }};

function addQuestion() {
    questionCount++;
    const container = document.getElementById('questionsContainer');
    const noQuestionsMsg = document.getElementById('noQuestionsMsg');
    
    if (noQuestionsMsg) {
        noQuestionsMsg.remove();
    }
    
    const questionHtml = `
        <div class="question-item" id="question_${questionCount}">
            <div class="d-flex justify-content-between mb-2">
                <strong>Question ${questionCount + 1}</strong>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion(${questionCount})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-2">
                <label class="form-label">Question Text</label>
                <input type="text" class="form-control" name="questions[${questionCount}][question]" 
                       placeholder="Enter your question" required>
            </div>
            <div>
                <label class="form-label">Answer Type</label>
                <select class="form-select" name="questions[${questionCount}][type]" required>
                    <option value="text">Text</option>
                    <option value="textarea">Long Text</option>
                    <option value="number">Number</option>
                    <option value="date">Date</option>
                    <option value="select">Dropdown</option>
                    <option value="checkbox">Checkbox</option>
                </select>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', questionHtml);
}

function removeQuestion(id) {
    const element = document.getElementById(`question_${id}`);
    if (element) {
        element.remove();
    }
    
    const container = document.getElementById('questionsContainer');
    if (container.children.length === 0) {
        container.innerHTML = '<p class="text-muted text-center py-3" id="noQuestionsMsg">No questions added yet.</p>';
    }
}

// Toggle state selector visibility based on clause type
function toggleStateSelector() {
    const clauseType = document.getElementById('clauseTypeSelect').value;
    const statesWrapper = document.getElementById('statesSelectorWrapper');
    const nationalNote = document.getElementById('nationalNote');
    const statesSelect = document.getElementById('statesSelect');

    if (clauseType === 'state_specific') {
        statesWrapper.style.display = '';
        nationalNote.style.display = 'none';
        statesSelect.setAttribute('required', 'required');
    } else {
        statesWrapper.style.display = 'none';
        nationalNote.style.display = '';
        statesSelect.removeAttribute('required');
    }
}
</script>

<script>
function validateAndRunAiAutofill(){
        const title = $('#title').val().trim();
        if(!title){
            Swal.fire('Title Required', 'Please fill in the Document Title before using AI Autofill.', 'warning');
            return;
        }   

        // First AJAX request: Generate keywords
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
                
                if(data){
                    if(data.success == true){
                        let secondaryKeywords = data.secondary_keywords;

                        if(typeof secondaryKeywords === 'string'){
                            try{
                                // Replace single quotes with double quotes for valid JSON
                                const jsonFixed = secondaryKeywords.replace(/'/g, '"');
                                const parsed = JSON.parse(jsonFixed);

                                // Use parsed if it's an array
                                if(Array.isArray(parsed)){
                                    secondaryKeywords = parsed;
                                }else{
                                    // If not array, fallback by splitting manually
                                    secondaryKeywords = secondaryKeywords
                                        .replace(/\[|\]/g, '')
                                        .split(',')
                                        .map(s => s.replace(/['"]+/g, '').trim())
                                        .filter(Boolean);
                                }
                            }catch (e){
                                // If JSON.parse fails, fallback
                                secondaryKeywords = secondaryKeywords
                                    .replace(/\[|\]/g, '')
                                    .split(',')
                                    .map(s => s.replace(/['"]+/g, '').trim())
                                    .filter(Boolean);
                            }
                        }else if(!Array.isArray(secondaryKeywords)){
                            secondaryKeywords = [];
                        }

                            Swal.fire({
                                title: 'Confirm Keywords',
                                html: `
                                    <strong>Primary Keyword:</strong> ${data.primary_keyword || ''}<br><br>
                                    <strong>Secondary Keywords:</strong> ${secondaryKeywords.join(', ')}
                                `,
                                showCancelButton: true,
                                cancelButtonText: 'Cancel',
                                confirmButtonText: 'Continue with AI Autofill',
                                reverseButtons: true,
                            }).then(result => {
                                if (result.isConfirmed) {
                                    runFullAiAutofill(title, data.primary_keyword, secondaryKeywords);
                                }
                            });
                       
                    } 
                }
            }
        });
    }
    </script>
@endpush