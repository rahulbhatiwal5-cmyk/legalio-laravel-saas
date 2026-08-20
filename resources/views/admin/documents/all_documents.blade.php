@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="components-preview wide-md">
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head">
                            <div class="d-flex justify-content-between p-2">
                                <div class="nk-block-head-content">
                                    <h4 class="nk-block-title">Documents</h4>
                                </div>
                                <div class="nk-block-head-content">
                                    <div class="mbsc-form-group orange-btn">
                                        <a href="{{ route('admin.generate.document') }}" class="btn btn-primary">Add Document</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table id="data_order_table" class="datatable-init table">
                                    @if(isset($documents) && $documents->isNotEmpty())
                                    <thead>
                                        <tr>
                                            {{-- <th scope="col">#</th> --}}
                                            <th scope="col">Title</th>
                                            <th scope="col">Document Categories</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $count = 1; @endphp
                                        @foreach($documents as $data)
                                            <tr>
                                                {{-- <th scope="row">{{ $count }}</th> --}}
                                                <td>
                                                    @if(!empty($data->slug))
                                                        <a href="{{ route('admin.dashboard.edit_documents', ['slug' => $data->slug]) }}">
                                                            {{ $data->title ?? '' }}
                                                        </a>
                                                    @else
                                                        <span>{{ $data->title ?? '' }}</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    @foreach($data->categories as $category)
                                                        {{ $category->name }}@if(!$loop->last),@endif
                                                    @endforeach
                                                </td>

                                                <td>
                                                    {{ $data->published == '1' ? 'published' : 'draft' }}
                                                </td>

                                                <td class="tb-tnx-action text-center">
                                                    <div class="dropdown">
                                                        <a class="text-soft dropdown-toggle" data-bs-toggle="dropdown">
                                                            <em class="icon ni ni-more-h"></em>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                            <ul class="link-list-plain">
                                                                <li>
                                                                    @if(!empty($data->slug))
                                                                        <a href="{{ route('admin.dashboard.edit_documents', ['slug' => $data->slug]) }}">
                                                                            <i class="icon ni ni-edit"></i> Edit
                                                                        </a>
                                                                    @else
                                                                        <span class="text-muted">
                                                                            <i class="icon ni ni-edit"></i> Edit (unavailable)
                                                                        </span>
                                                                    @endif
                                                                </li>

                                                                <li>
                                                                    @if($data->published == '1' && !empty($data->slug))
                                                                        <a href="{{ url('document/'.$data->slug) }}" target="_blank">
                                                                            <i class="icon ni ni-eye"></i> View
                                                                        </a>
                                                                    @else
                                                                        <a href="javascript:void(0);" onclick="isNotView()">
                                                                            <i class="icon ni ni-eye"></i> View
                                                                        </a>
                                                                    @endif
                                                                </li>

                                                                <li>
                                                                    <a href="{{ route('delete.document', ['id' => $data->id ?? '' ]) }}">
                                                                        <i class="icon ni ni-trash"></i> Delete
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                            @php $count++; @endphp
                                        @endforeach
                                    </tbody>

                                    @else
                                    No Documents found.
                                    @endif
                                </table>

                            </div>
                        </div><!-- .card-preview -->
                    </div><!-- .nk-block -->
                </div><!-- .components-preview -->
            </div>
        </div>
    </div>
</div>



{{-- pagination
@if ($documents->hasPages())
<nav>
    <ul class="pagination justify-content-center">
        <!-- Previous Page Button -->
        @if ($documents->onFirstPage())
        <li class="page-item disabled">
            <span class="page-link">Previous</span>
        </li>
        @else
        <li class="page-item">
            <a class="page-link" href="{{ $documents->previousPageUrl() }}">Previous</a>
        </li>
        @endif

        <!-- Page Number Links -->
        @foreach ($documents->getUrlRange(1, $documents->lastPage()) as $page => $url)
        <li class="page-item {{ $page == $documents->currentPage() ? 'active' : '' }}">
            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
        </li>
        @endforeach

        <!-- Next Page Button -->
        @if ($documents->hasMorePages())
        <li class="page-item">
            <a class="page-link" href="{{ $documents->nextPageUrl() }}">Next</a>
        </li>
        @else
        <li class="page-item disabled">
            <span class="page-link">Next</span>
        </li>
        @endif
    </ul>
</nav>
@endif --}}

@endsection
{{-- <script>
    $('#data_order_table').DataTable({
    responsive: false
});

</script> --}}