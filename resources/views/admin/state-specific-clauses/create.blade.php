@extends('admin_layout.master')
@section('title', 'Create State-Specific Clause')

@section('content')
<div class="container-fluid px-4 py-4" style="margin-top:60px;">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex align-items-center mb-2">
            <a href="{{ route('index') }}" class="btn btn-sm btn-outline-secondary me-3" style="color:white;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h3 mb-0 text-gray-800">Create State-Specific Clause</h1>
        </div>
        <p class="text-muted">Add a new state-specific clause for contracts</p>
    </div>

    <!-- Errors -->
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

    {{--AI Auto-fill Section --}}
    <div class="card shadow-sm mb-4">
        <div class="m-3">
            <h5 class="mb-0">
                <i class="fas fa-robot me-2"></i>AI Auto-Fill Clause
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="ai_clause_type" class="form-label">Describe the clause you need</label>
                        <input type="text" class="form-control" id="ai_clause_type" 
                               placeholder="e.g., Force Majeure, Confidentiality, Non-Compete, etc.">
                        <small class="text-muted">Enter the type or purpose of the clause you want to create</small>
                    </div>
                    <div class="mb-3">
                        <label for="ai_context" class="form-label">Additional Context (Optional)</label>
                        <textarea class="form-control" id="ai_context" rows="2"
                          placeholder="Add any specific requirements or details..."></textarea>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100" id="aiGenerateBtn">
                        <i class="fas fa-magic me-2"></i>Generate with AI
                    </button>
                </div>
            </div>
            {{--  AI Loading indicator --}}
            <div id="aiLoadingIndicator" class="mt-3" style="display: none;">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Please wait...</p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.state-clauses.store') }}" id="clauseForm">
        @csrf
        
        <div class="row">

            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0" style="margin-top:15px;">Clause Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Clause Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="{{ old('title') }}" required
                                   placeholder="e.g., Force Majeure Clause">
                            <small class="text-muted">This title will be used to group clauses across states</small>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Short Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required
                                      placeholder="Brief description for AI to understand when to use this clause">{{ old('description') }}</textarea>
                            <small class="text-muted">This helps the AI decide when to include this clause</small>
                        </div>

                        <!-- Clause Text -->
                        <div class="mb-3">
                            <label for="text" class="form-label">Clause Text <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="text" name="text" rows="10" required
                                      placeholder="Enter the full legal text of the clause">{{ old('text') }}</textarea>
                            <small class="text-muted">The complete legal text that will appear in the contract</small>
                        </div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Questions (Optional)</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-black" style="color:white;" id="addQuestionBtn">
                            <i class="fas fa-plus me-1"></i>Add Question
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="questionsContainer">   
                            <p class="text-muted text-center py-3" id="noQuestionsMsg">
                                No questions added yet. Click "Add Question" to add custom questions for this clause.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">

                {{-- Clause Type card — added to capture national vs state-specific --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Clause Type <span class="text-danger">*</span></h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select" name="clause_type" id="clauseTypeSelect" required>
                            <option value="national" {{ old('clause_type') === 'national' ? 'selected' : '' }}>
                                National (applies to all states)
                            </option>
                            <option value="state_specific" {{ old('clause_type', 'state_specific') === 'state_specific' ? 'selected' : '' }}>
                                State-Specific
                            </option>
                        </select>
                        <small class="text-muted d-block mt-2">
                            Choose <strong>National</strong> if this clause applies everywhere, or <strong>State-Specific</strong> to assign it to particular states below.
                        </small>
                    </div>
                </div>
                {{--  End Clause Type card --}}

                <!-- State Selection -->
                <div class="card shadow-sm mb-4" id="stateSelectionCard">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">State Selection <span class="text-danger">*</span></h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary w-100 mb-3" style="color:white;" id="selectAllStatesBtn">
                                <i class="fas fa-check-double me-2"></i>Select All States
                            </button>
                            <button type="button" class="btn btn-outline-secondary w-100 mb-3" style="color:white;" id="clearAllStatesBtn">
                                <i class="fas fa-times me-2"></i>Clear Selection
                            </button>
                        </div>

                        <div class="state-checkboxes" style="max-height: 400px; overflow-y: auto;">
                            @foreach($states as $state)
                            <div class="form-check mb-2">
                                <input class="form-check-input state-checkbox" type="checkbox" 
                                       name="states[]" value="{{ $state }}" id="state_{{ $loop->index }}"
                                       {{ in_array($state, old('states', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="state_{{ $loop->index }}">
                                    {{ $state }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-2">
                            Select one or more states for this clause
                        </small>
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-outline-secondary w-100 mb-2" style="color:white;"> 
                            <i class="fas fa-save me-2"></i>Create Clause
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
    
    .state-checkboxes {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 1rem;
    }
    
    .form-check-input:checked {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .border-primary {
        border: 2px solid #4e73df !important;
    }

    #aiLoadingIndicator {
        background-color: #f8f9fc;
        border-radius: 8px;
        padding: 2rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let questionCount = 0;

    //  Show/hide state selection card based on clause type
    function toggleStateSelection() {
        const clauseType = document.getElementById('clauseTypeSelect').value;
        const stateCard = document.getElementById('stateSelectionCard');
        if (clauseType === 'national') {
            stateCard.style.display = 'none';
            // Uncheck all states so they don't get submitted for national
            document.querySelectorAll('.state-checkbox').forEach(cb => cb.checked = false);
        } else {
            stateCard.style.display = '';
        }
    }

    //  Attach change listener to clause type select
    document.getElementById('clauseTypeSelect').addEventListener('change', toggleStateSelection);

    //  Run once on load to set correct initial state
    toggleStateSelection();

    // AI Auto-fill function
    async function generateWithAI() {
        const clauseType = document.getElementById('ai_clause_type').value.trim();
        const context = document.getElementById('ai_context').value.trim();
        
        if (!clauseType) {
            alert('Please describe the type of clause you need.');
            return;
        }

        const selectedStates = Array.from(document.querySelectorAll('.state-checkbox:checked'))
            .map(cb => cb.value);

        document.getElementById('aiLoadingIndicator').style.display = 'block';
        document.getElementById('aiGenerateBtn').disabled = true;

        try {
            const response = await fetch('{{ route("state-clauses.ai-auto-fill") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    clause_type: clauseType,
                    states: selectedStates,
                    context: context
                })
            });

            const result = await response.json();

            if (result.success) {
                document.getElementById('title').value = result.data.title || '';
                document.getElementById('description').value = result.data.description || '';
                document.getElementById('text').value = result.data.text || '';

                if (result.data.questions && result.data.questions.length > 0) {
                    const container = document.getElementById('questionsContainer');
                    container.innerHTML = '';
                    questionCount = 0;

                    result.data.questions.forEach(q => {
                        addQuestion(q.question, q.type);
                    });
                }

                showNotification('Success! AI has generated the clause content.', 'success');
            } else {
                showNotification(result.message || 'Failed to generate content. Please try again.', 'error');
            }
        } catch (error) {
            console.error('AI generation error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        } finally {
            document.getElementById('aiLoadingIndicator').style.display = 'none';
            document.getElementById('aiGenerateBtn').disabled = false;
        }
    }

    function addQuestion(questionText = '', questionType = 'text') {
        questionCount++;
        const container = document.getElementById('questionsContainer');
        const noQuestionsMsg = document.getElementById('noQuestionsMsg');
        
        if (noQuestionsMsg) {
            noQuestionsMsg.remove();
        }
        
        const questionHtml = `
            <div class="question-item" id="question_${questionCount}">
                <div class="d-flex justify-content-between mb-2">
                    <strong>Question ${questionCount}</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger" style="color:white;" onclick="window.removeQuestion(${questionCount})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mb-2">
                    <label class="form-label">Question Text</label>
                    <input type="text" class="form-control" name="questions[${questionCount}][question]" 
                           placeholder="Enter your question" value="${questionText}" required>
                </div>
                <div>
                    <label class="form-label">Answer Type</label>
                    <select class="form-select" name="questions[${questionCount}][type]" required>
                        <option value="text" ${questionType === 'text' ? 'selected' : ''}>Text</option>
                        <option value="textarea" ${questionType === 'textarea' ? 'selected' : ''}>Long Text</option>
                        <option value="number" ${questionType === 'number' ? 'selected' : ''}>Number</option>
                        <option value="date" ${questionType === 'date' ? 'selected' : ''}>Date</option>
                        <option value="select" ${questionType === 'select' ? 'selected' : ''}>Dropdown</option>
                        <option value="checkbox" ${questionType === 'checkbox' ? 'selected' : ''}>Checkbox</option>
                    </select>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', questionHtml);
    }

    window.removeQuestion = function(id) {
        const element = document.getElementById(`question_${id}`);
        if (element) {
            element.remove();
        }
        
        const container = document.getElementById('questionsContainer');
        if (container.children.length === 0) {
            container.innerHTML = '<p class="text-muted text-center py-3" id="noQuestionsMsg">No questions added yet.</p>';
        }
    }

    function selectAllStates() {
        document.querySelectorAll('.state-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function clearAllStates() {
        document.querySelectorAll('.state-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    function showNotification(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
        const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
        
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="fas fa-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    document.getElementById('aiGenerateBtn').addEventListener('click', generateWithAI);
    document.getElementById('addQuestionBtn').addEventListener('click', () => addQuestion());
    document.getElementById('selectAllStatesBtn').addEventListener('click', selectAllStates);
    document.getElementById('clearAllStatesBtn').addEventListener('click', clearAllStates);

    //  Updated form validation — skip state check when clause type is national
    document.getElementById('clauseForm').addEventListener('submit', function(e) {
        const clauseType = document.getElementById('clauseTypeSelect').value;
        if (clauseType === 'state_specific') {
            const checkedStates = document.querySelectorAll('.state-checkbox:checked');
            if (checkedStates.length === 0) {
                e.preventDefault();
                alert('Please select at least one state for this clause.');
            }
        }
    });
});
</script>
@endsection