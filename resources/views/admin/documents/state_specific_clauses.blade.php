@extends('admin_layout.master')
@section('content')

<div class="st-content">
    <div class="container-fluid px-4 py-4" style="margin-top:60px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 text-gray-800">State-Specific Clauses</h1>
                <p class="text-muted mb-0">Manage state-specific contract clauses for AI integration</p>
            </div>
            <a href="{{ route('admin.state-clauses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Clause
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="state" class="form-label">Filter by State</label>
                        <select name="state" id="state" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $state === 'all' ? 'selected' : '' }}>All States</option>
                            @foreach($states as $stateName)
                            <option value="{{ $stateName }}" {{ $state === $stateName ? 'selected' : '' }}>
                                {{ $stateName }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="clause_type_filter" class="form-label">Filter by Clause Type</label>
                        <select name="clause_type" id="clause_type_filter" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ ($clauseType ?? 'all') === 'all' ? 'selected' : '' }}>All Types</option>
                            <option value="national" {{ ($clauseType ?? '') === 'national' ? 'selected' : '' }}>National</option>
                            <option value="state_specific" {{ ($clauseType ?? '') === 'state_specific' ? 'selected' : '' }}>State-Specific</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        @if(!$clauses->isEmpty())
        <form action="{{ route('state-clauses.destroyAll') }}" method="POST" onsubmit="return confirm('This will delete ALL clauses. Continue?');">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-secondary mb-2" style="margin-left:840px;">
                Delete All Clauses
            </button>
        </form>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                @if($clauses->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">No state-specific clauses found.</p>
                    <a href="{{ route('admin.state-clauses.create') }}" class="btn btn-primary">
                        Create Your First Clause
                    </a>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>State(s)</th>
                                <th>Description</th>
                                <th>Placeholder</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clauses as $clause)
                            <tr>
                                <td class="text-muted">
                                    <small> {{ $clauses->firstItem() + $loop->index }} </small>
                                </td>
                                <td class="text-secondary">
                                    {{$clause->title}}
                                    @if($clause->questions)
                                    <span class="" title="Has questions">
                                        <i class="fa fa-question-circle" style="font-size:12px"></i>
                                    </span>
                                    @endif 
                                </td>
                                {{--  Display clause type badge --}}
                                <td>
                                    @if($clause->clause_type === 'national')
                                        <span class="badge bg-primary">National</span>
                                    @else
                                        <span class="badge bg-info text-dark">State-Specific</span>
                                    @endif
                                </td>
                                {{-- Display states — shows 'All States' for national, or state badges for state-specific --}}
                                <td>
                                    @if($clause->clause_type === 'national')
                                        <span class="text-muted">All State</span>
                                    @else
                                        @if(is_array($clause->states) && count($clause->states))
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($clause->states as $st)
                                                    <span class="badge bg-light text-dark border">{{ $st }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span>{{ $clause->state }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-secondary">
                                    {{ Str::limit($clause->description, 60) }}
                                </td>
                                <td>
                                    <small> <code class="bg-light px-2 py-1 rounded">
                                            {{ $clause->getPlaceholder() }}
                                        </code><small>
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-dark p-0" style="border: none; background:none;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v" style="margin-left:20px;"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <form method="POST" action="{{ route('toggle', $clause->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fas fa-ban me-2"></i>
                                                        {{ $clause->is_active ? 'Unpublish' : 'Publish' }}
                                                    </button>
                                                </form>
                                            </li>

                                            <li>
                                                <a class="dropdown-item" href="{{ route('edit', $clause->id) }}">
                                                    <i class="fas fa-edit me-2"></i> Edit
                                                </a>
                                            </li>



                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>

                                            <li>
                                                <form action="{{ route('destroy', $clause->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this clause?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $clauses->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
    }

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    code {
        font-size: 0.85rem;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        color: white;
    }

    /*  State badges styling */
    .badge.bg-light {
        font-size: 0.75rem;
    }
</style>
@endpush
@endsection