@extends('admin_layout.master')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Document Generating Prompts Management</h4>
                </div>
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4 mt-5">
                            <h5 class="mb-0">Existing Prompts</h5>
                            <button type="button" class="btn btn-success" id="addPromptBtn">
                                <i class="fas fa-plus"></i> Add New Prompt
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80">Step No.</th>
                                        <th width="120">Contract Type</th>
                                        <th width="300">Prompt Text</th>
                                        <th width="120" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="promptsTableBody">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted mb-5">
                                            <i class="fas fa-spinner fa-spin"></i> Loading prompts...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="promptModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="promptModalTitle">Add New Prompt</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="promptForm">
                    @csrf
                    <input type="hidden" id="prompt_id" name="prompt_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="steps_no" class="form-label">
                                Step Number <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="steps_no" name="steps_no" required>
                                <option value="">Select Step</option>
                                <option value="1">Step 1</option>
                                <option value="2">Step 2</option>
                                <option value="3">Step 3</option>
                                <option value="4">Step 4</option>
                                <option value="5">Step 5</option>
                            </select>
                            <small class="form-text text-muted">Select which step this prompt belongs to</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="contract_type" class="form-label">
                                Prompts Type <span class="text-danger">*</span>
                            </label>
                            <select name="contract_type" id="contract_type" class="form-select" required>
                                <option value="">-- Select Type --</option>
                                <option value="question">Question</option>
                                <option value="contract">Contract</option>
                            </select>
                            <small class="form-text text-muted">Select the type of contract</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="prompts" class="form-label">
                            Prompt Text <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="prompts" name="prompts" rows="6" required placeholder="Enter the prompt text for this step..."></textarea>
                        <small class="form-text text-muted">Enter the AI prompt that will be used for this step</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-success" id="saveBtn">
                     Save Prompt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this prompt?</p>
                <p class="text-muted mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let isEditMode = false;
        let deletePromptId = null;
        loadAllPrompts();

        $('#addPromptBtn').on('click', function() {
            resetForm();
            $('#promptModal').modal('show');
        });

        // Save button click handler
        $('#saveBtn').on('click', function() {
            $('#promptForm').submit();
        });

        $('#promptForm').on('submit', function(e) {
            e.preventDefault();

            const promptId = $('#prompt_id').val();
            const stepsNo = $('#steps_no').val();
            const contractType = $('#contract_type').val();
            const promptText = $('#prompts').val();

            // Updated validation
            if (!stepsNo || !contractType || !promptText.trim()) {
                showNotification('Please fill all required fields', 'error');
                return;
            }

            //  Added contract_type to data object
            const data = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                steps_no: parseInt(stepsNo),
                contract_type: contractType,
                prompts: promptText.trim()
            };

            let url, method;

            if (isEditMode && promptId) {
                url = "{{ route('update.document.prompts') }}";
                method = 'PUT';
                data.prompt_id = parseInt(promptId);
            } else {
                url = "{{ route('store.document.prompts') }}";
                method = 'POST';
            }

            $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: url,
                method: method,
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        $('#promptModal').modal('hide');
                        resetForm();
                        loadAllPrompts();
                    } else {
                        showNotification(response.message || 'Operation failed', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr);
                    let errorMessage = 'An error occurred';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }
                    }

                    showNotification(errorMessage, 'error');
                },
                complete: function() {
                    $('#saveBtn').prop('disabled', false).html('Save Prompt');
                }
            });
        });

        function loadAllPrompts() {
            $.ajax({
                url: "{{ route('get.document.prompts') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        renderPromptsTable(response.data);
                    } else {
                        renderPromptsTable([]);
                    }
                },
                error: function(xhr) {
                    console.error('Load Error:', xhr);
                    $('#promptsTableBody').html(`
                        <tr>
                            <td colspan="4" class="text-center text-danger">
                                <i class="fas fa-exclamation-triangle"></i> Failed to load prompts
                            </td>
                        </tr>
                    `);
                }
            });
        }

        //  Updated to show contract_type
        function renderPromptsTable(prompts) {
            if (prompts.length === 0) {
                $('#promptsTableBody').html(`
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            <i class="fas fa-inbox"></i> No prompts found.
                        </td>
                    </tr>
                `);
                return;
            }

            let html = '';
            prompts.forEach(function(prompt) {

                const truncatedText = prompt.prompts.length > 100
                    ? prompt.prompts.substring(0, 100) + '...'
                    : prompt.prompts;

                //  Added contract type badge
                const contractTypeBadge = prompt.contract_type === 'question' 
                    ? '<span class="badge bg-info">Question</span>' 
                    : '<span class="badge bg-success">Contract</span>';

                html += `
                    <tr>
                        <td class="text-center">Step ${prompt.steps_no}</td>
                        <td class="text-center">${contractTypeBadge}</td>
                        <td>${truncatedText}</td>

                        <td class="text-center">
                            <div class="dropdown">
                                <span 
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </span>

                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li>
                                        <a class="dropdown-item edit-btn" href="javascript:void(0)"
                                           data-id="${prompt.id}">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item view-btn" href="javascript:void(0)"
                                           data-id="${prompt.id}">
                                            <i class="fas fa-eye me-2"></i> View
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger delete-btn" href="javascript:void(0)"
                                           data-id="${prompt.id}">
                                            <i class="fas fa-trash me-2"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                `;
            });

            $('#promptsTableBody').html(html);
        }

        $(document).on('click', '.view-btn', function() {
            const promptId = $(this).data('id');

            $.ajax({
                url: "{{ route('get.document.prompts') }}",
                method: 'GET',
                success: function(response) {
                    const prompt = response.data.find(p => p.id == promptId);
                    if (prompt) {
                        const promptText = prompt.prompts;
                        // const contractType = prompt.contract_type === 'question' ? 'Question' : 'Contract';
                        const contractType = prompt.contract_type;


                        Swal.fire({
                            title: `Step ${prompt.steps_no} Prompt (${contractType})`,
                            html: `<div style="text-align: left; max-height: 400px; overflow-y: auto;">
                                <pre style="white-space: pre-wrap; word-wrap: break-word;">${promptText}</pre>
                            </div>`,
                            width: '800px',
                            showCloseButton: true,
                            confirmButtonText: 'Close'
                        });
                    }
                }
            });
        });

        $(document).on('click', '.edit-btn', function() {
            const promptId = $(this).data('id');

            $.ajax({
                url: "{{ route('get.document.prompts') }}",
                method: 'GET',
                success: function(response) {
                    const prompt = response.data.find(p => p.id == promptId);
                    if (prompt) {
                        isEditMode = true;
                        const promptText = prompt.prompts;

                        $('#prompt_id').val(prompt.id);
                        $('#steps_no').val(prompt.steps_no);
                        $('#contract_type').val(prompt.contract_type);
                        $('#prompts').val(promptText);

                        $('#promptModalTitle').text('Edit Prompt');
                        $('#saveBtn').html('<i class="fas fa-save"></i> Update Prompt');

                        $('#promptModal').modal('show');
                    }
                }
            });
        });

        $(document).on('click', '.delete-btn', function() {
            deletePromptId = $(this).data('id');
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').on('click', function() {
            if (!deletePromptId) return;

            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

            $.ajax({
                url: "{{ route('delete.document.prompts') }}",
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: deletePromptId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        loadAllPrompts();
                        $('#deleteModal').modal('hide');
                        if (isEditMode && $('#prompt_id').val() == deletePromptId) {
                            resetForm();
                        }
                    } else {
                        showNotification(response.message || 'Deletion failed', 'error');
                    }
                },
                error: function(xhr) {
                    showNotification('Failed to delete prompt', 'error');
                },
                complete: function() {
                    $('#confirmDeleteBtn').prop('disabled', false).html('Delete');
                    deletePromptId = null;
                }
            });
        });

        function resetForm() {
            isEditMode = false;
            $('#promptForm')[0].reset();
            $('#prompt_id').val('');
            $('#promptModalTitle').text('Add New Prompt');
            $('#saveBtn').html('Save Prompt');
        }

        function showNotification(message, type) {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Success!' : 'Error!',
                html: message,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        $('#steps_no, #contract_type').on('change', function() {
            const stepNo = $('#steps_no').val();
            const contractType = $('#contract_type').val();
            if (!stepNo || !contractType || isEditMode) return;

            $.ajax({
                url: "{{ route('get.document.prompts') }}",
                method: 'GET',
                data: {
                    step: stepNo,
                      contract_type: contractType 
                },
                success: function(response) {
                    if (response.success && response.data) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Step Already Has Prompt',
                            text: 'This step already has a prompt. You can edit it from the table below.',
                            showCancelButton: true,
                            confirmButtonText: 'Edit Existing',
                            cancelButtonText: 'Create New Anyway'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#promptModal').modal('hide');
                                const editBtn = $(`.edit-btn[data-id="${response.data.id}"]`);
                                if (editBtn.length) {
                                    editBtn.click();
                                }
                            }
                        });
                    }
                }
            });
        });
    });
</script>

<style>
    .prompt-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .btn-group-sm>.btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .card {
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .form-label {
        font-weight: 600;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .badge {
        font-size: 0.85em;
        padding: 0.15em 0.45em;
        margin-top: 10px;
    }

    .btn-close-white {
        filter: brightness(0) invert(1);
    }
</style>
@endsection