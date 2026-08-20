@extends('admin_layout.master')
<style>
        /* 17 march */
@media screen and (max-width:575px) {
    .mbsc-form-group  .btn.btn-primary {
    white-space: nowrap;
} 
} 
</style>
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
                                             <h4 class="nk-block-title">Standard Clauses</h4>
                                        </div>
                                        <div class="nk-block-head-content">
                                             <div class="mbsc-form-group orange-btn">
                                                  <a href="{{ route('admin.document.standard_document') }}" class="btn btn-primary">Add Standard Clause</a>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <table id="data_order_table" class="datatable-init table">
                                             <thead>
                                                  <tr>
                                                       <th scope="col">Title</th>
                                                       <th scope="col">Description</th>
                                                       {{-- <th scope="col">Type</th>
                                                       <th scope="col" style="width: 200px">States</th> --}}
                                                       <th scope="col">Status</th>
                                                       <th scope="col">Action</th>
                                                       <th></th>
                                                  </tr>
                                             </thead>
                                             <tbody>
                                                  @if(isset($standard_documents) && count($standard_documents) > 0)
                                                       @foreach($standard_documents as $document)
                                                            <tr>
                                                                 <td>
                                                                      <a href="{{ route('admin.document.edit_standard_document', ['slug' => $document->slug]) }}">{{ $document->title ?? '' }}</a>
                                                                 </td>
                                                                 <td>
                                                                      @php 
                                                                           $description = Str::limit($document->description, 25, '...');
                                                                           print_r($description); 
                                                                      @endphp
                                                                 </td>
                                                                 {{--  Clause Type Badge --}}
                                                                 {{-- <td>
                                                                      @if(($document->clause_type ?? 'national') === 'national')
                                                                           <span 
                                                                           class="badge bg-primary"
                                                                           >National</span>
                                                                      @else
                                                                           <span class="badge bg-warning text-dark">State-Specific</span>
                                                                      @endif
                                                                 </td> --}}
                                                                 {{--  States Display --}}
                                                                 {{-- <td>
                                                                      @if(($document->clause_type ?? 'national') === 'national')
                                                                           <span class="text-muted small">All States</span>
                                                                      @else
                                                                           @php $states = $document->states ?? []; @endphp
                                                                           @if(count($states))
                                                                                <div class="d-flex flex-wrap gap-1">
                                                                                     @foreach(array_slice($states, 0, 1) as $st)
                                                                                          <span class="badge bg-light text-dark border" style="font-size:10px;">{{ $st }}</span>
                                                                                     @endforeach
                                                                                     @if(count($states) > 1)
                                                                                          <span class="badge bg-secondary" title="{{ implode(', ', array_slice($states, 1)) }}">+{{ count($states) - 3 }} more</span>
                                                                                     @endif
                                                                                </div>
                                                                           @else
                                                                                <span class="text-muted small">—</span>
                                                                           @endif
                                                                      @endif
                                                                 </td> --}}
                                                                 {{-- <td>
                                                                 @if($document->status == '1')
                                                                      <span class="badge bg-success">Active</span>
                                                                 @else
                                                                      <span class="badge bg-danger">Inactive</span>
                                                                 @endif
                                                                 </td> --}}


                                                                 {{-- AFTER --}}
                                                                 <td>
                                                                 <a href="{{ route('admin.document.toggle_standard_document_status', ['slug' => $document->slug]) }}"
                                                                      title="Click to toggle status"
                                                                      onclick="return confirm('Are you sure you want to {{ $document->status == '1' ? 'deactivate' : 'activate' }} this document?')">
                                                                      @if($document->status == '1')
                                                                           <span class="badge bg-success" style="cursor:pointer;">Active</span>
                                                                      @else
                                                                           <span class="badge bg-danger" style="cursor:pointer;">Inactive</span>
                                                                      @endif
                                                                 </a>
                                                                 </td>        
                                                         {{-- {{ $document->status ?? '' }}</td> --}}
                                                                 <td class="tb-tnx-action text-center">
                                                                      <div class="dropdown">
                                                                           <a class="text-soft dropdown-toggle" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                                           <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                                                {{-- <ul class="link-list-plain">
                                                                                     <li><a href="{{ route('admin.document.edit_standard_document', ['slug' => $document->slug]) }}">Edit</a></li>
                                                                                     <li><a href="{{ route('admin.document.delete_standard_document', ['slug' => $document->slug]) }}">Delete</a></li>
                                                                                </ul> --}}
                                                                                <ul class="link-list-plain">
    <li><a href="{{ route('admin.document.edit_standard_document', ['slug' => $document->slug]) }}">Edit</a></li>
    <li>
        <a href="{{ route('admin.document.toggle_standard_document_status', ['slug' => $document->slug]) }}"
           onclick="return confirm('Are you sure you want to {{ $document->status == '1' ? 'deactivate' : 'activate' }} this document?')">
            {{ $document->status == '1' ? 'Deactivate' : 'Activate' }}
        </a>
    </li>
    <li><a href="{{ route('admin.document.delete_standard_document', ['slug' => $document->slug]) }}">Delete</a></li>
</ul>
                                                                           </div>
                                                                      </div>
                                                                 </td>
                                                                 <td></td>
                                                            </tr>
                                                       @endforeach
                                                  @else
                                                       <tr>
                                                            <td colspan="7" class="text-center">No Standard Documents Found</td>
                                                       </tr>
                                                  @endif
                                             </tbody>
                                        </table>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>

@endsection