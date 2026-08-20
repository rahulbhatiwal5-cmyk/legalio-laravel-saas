@extends('admin_layout.master')
@section('content')
<div class="nk-content">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">AI config</h4>
        </div>

    </div>
    <div class="container-fluid">
        <form action="{{ route('ai.config.update') }}" method="post" enctype="multipart/form-data"  onsubmit="submitModelForm(event)" >
            @csrf


            {{-- Show Table of AI Models --}}
            <div class="card card-bordered">
                <div class="card-inner">
                    <h5>AI Models</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Location ID</th>
                                <th>Model ID</th>
                                <th>Endpoint</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="model-table-body">
                            @php
                            // dd($settings);
                        
                        $models = $settings->unique('model_ref');
                        $models = $settings->groupBy('model_ref')->map(function ($group) {
                            $mapped = [
                                'model_ref' => $group->first()->model_ref,
                            ];

                            foreach ($group as $setting) {
                                // Normalize key: lowercase, replace spaces with underscores, trim trailing underscores
                                $key = rtrim(strtolower(str_replace(' ', '_', trim($setting->name))), '_');
                                $mapped[$key] = $setting->value;
                            }

                            return $mapped;
                        });

                        // Reindex models as a numerically indexed array
                        $models = $models->values();

                        // dd($models);
                        @endphp
                        @foreach ($models as $index => $model)
                            {{-- <tr onclick="loadModel('{{ $model['model_id'] }}')" style="cursor:pointer"> --}}
                            <tr onclick="loadModel({{ $index }})" style="cursor:pointer">

                                <td>{{ $model['location_id'] ?? '' }}</td>
                                <td>{{ $model['model_id'] ?? '' }}</td>
                                <td>{{ $model['api_endpoint'] ?? '' }}</td>
                                <td>
                                    {{-- <button type="button" class="btn btn-sm btn-danger" onclick="deleteModel(event, '{{ $model }}')">Delete</button> --}}
                                  {{-- @if (!empty($model['model_ref']))
                                        <a href="{{ route('ai.config.delete', ['modelRef' => $model['model_ref']]) }}" 
                                        class="btn btn-sm btn-danger" 
                                        onclick="deleteModel('{{ $model['model_ref'] }}')">
                                            Delete
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td> --}}
                                <td>
                                @if (!empty($model['model_ref']))
                                    <button type="button" class="btn btn-sm btn-warning me-1" 
                                        onclick="event.stopPropagation(); loadModel({{ $index }})">
                                        Edit
                                    </button>
                                    <a href="{{ route('ai.config.delete', ['modelRef' => $model['model_ref']]) }}" 
                                    class="btn btn-sm btn-danger" 
                                    onclick="deleteModel('{{ $model['model_ref'] }}')">
                                        Delete
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            </tr>
                        @endforeach
                        
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-primary mt-2" onclick="addNewModel()">+ Add New AI Model</button>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="modelModal" tabindex="-1" aria-labelledby="modelModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                <div class="modal-content">
                    <form id="modelForm" onsubmit="submitModelForm(event)">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modelModalLabel">Add/Edit AI Model</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="model_index" id="modal_model_index">
                    
                            <div class="mb-2">
                                <label class="form-label">Model Reference</label>
                                <input type="text" class="form-control" id="modal_model_ref" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Model ID</label>
                                <input type="text" class="form-control" id="modal_model_id">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">API Endpoint</label>
                                <input type="text" class="form-control" id="modal_api_endpoint">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Project ID</label>
                                <input type="text" class="form-control" id="modal_project_id">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Service Account File Path</label>
                                <input type="text" class="form-control" id="modal_service_account_file_path">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Generate Content API</label>
                                <input type="text" class="form-control" id="modal_generate_content_api">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Location ID</label>
                                <input type="text" class="form-control" id="modal_location_id">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Model</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>                    
                </div>
                </div>
            </div>
            


        </form>
    </div>
</div>
@endsection


    <script>
      const models = @json($models);

        function addNewModel() {
            document.getElementById('modelModalLabel').innerText = 'Add New AI Model';
            document.getElementById('modal_model_index').value = '';
            document.getElementById('modal_model_ref').value = '';
            document.getElementById('modal_model_id').value = '';
            document.getElementById('modal_api_endpoint').value = '';
            document.getElementById('modal_project_id').value = '';
            document.getElementById('modal_service_account_file_path').value = '';
            document.getElementById('modal_generate_content_api').value = '';
            document.getElementById('modal_location_id').value = '';
            new bootstrap.Modal(document.getElementById('modelModal')).show();
        }

        function loadModel(index) {
            const model = models[index];
            document.getElementById('modelModalLabel').innerText = 'Edit Model: ' + (model.model_ref ?? 'No Name');
            document.getElementById('modal_model_index').value = index;
            document.getElementById('modal_model_ref').value = model.model_ref ?? '';
            document.getElementById('modal_model_id').value = model.model_id ?? '';
            document.getElementById('modal_api_endpoint').value = model.api_endpoint ?? '';
            document.getElementById('modal_project_id').value = model.project_id ?? '';
            document.getElementById('modal_service_account_file_path').value = model.service_account_file_path ?? '';
            document.getElementById('modal_generate_content_api').value = model.generate_content_api ?? '';
            document.getElementById('modal_location_id').value = model.location_id ?? '';
            new bootstrap.Modal(document.getElementById('modelModal')).show();
        }

        function submitModelForm(event) {
            event.preventDefault();

            // Get the numeric index based on the number of existing models in the form
            const index = document.querySelectorAll('form input[name^="models["]').length;

            // Get the fields from the modal
            const fields = {
                model_ref: document.getElementById('modal_model_ref').value,
                model_id: document.getElementById('modal_model_id').value,
                api_endpoint: document.getElementById('modal_api_endpoint').value,
                project_id: document.getElementById('modal_project_id').value,
                service_account_file_path: document.getElementById('modal_service_account_file_path').value,
                generate_content_api: document.getElementById('modal_generate_content_api').value,
                location_id: document.getElementById('modal_location_id').value
            };

            // Get the outer form
            const outerForm = document.querySelector('form[action="{{ route('ai.config.update') }}"]');

            // Remove old inputs with the same index (if any)
            Object.keys(fields).forEach(key => {
                const existing = outerForm.querySelector(`input[name="models[${index}][${key}]"]`);
                if (existing) existing.remove();
            });

            // Add hidden inputs for each field to the outer form with a numeric index
            Object.entries(fields).forEach(([key, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `models[${index}][${key}]`; // Numeric index used here
                input.value = value;
                outerForm.appendChild(input);
            });

            // Submit the outer form
            outerForm.submit();

            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modelModal'));
            modal.hide();
        }

        function deleteModel(modelRef) {
        // Ask for confirmation before deleting
        if (confirm('Are you sure you want to delete this model?')) {
            fetch("{{ url('ai/config/delete') }}/" + modelRef, {
                method: 'DELETE',  // Specify the method as DELETE
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Model deleted successfully!');
                    location.reload();  // Reload the page to reflect changes
                } else {
                    alert('Error deleting the model.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting the model.');
            });
        }
        }

    </script>

