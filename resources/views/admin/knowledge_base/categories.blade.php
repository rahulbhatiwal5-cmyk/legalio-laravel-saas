@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="container-fluid">
          <div class="nk-content-inner">
               <div class="nk-content-body">
                    <div class="components-preview wide-md mx-auto">
                         <div class="nk-block nk-block-lg">
                              <div class="nk-block-head">
                                   <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Knowledge Base Categories</h4>
                                   </div>
                                   <div class="d-flex justify-content-end p-2">
                                        <div class="nk-block-head-content">
                                             <div class="mbsc-form-group">
                                                  <a href="{{ route('knowledge.base.add.category') }}" class="btn btn-light">Add Category</a>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <table class="table" id="catg_table">
                                             <thead>
                                                  <tr>
                                                       <th scope="col">#</th>
                                                       <th scope="col">Name</th>
                                                       <th scope="col">Description</th>
                                                       {{-- <th scope="col">Slug</th> --}}
                                             
                                                       <th scope="col">Action</th>
                                                       {{-- <th scope="col"></th> --}}


                                                  </tr>
                                             </thead>
                                             <tbody>
                                                  @if(isset($categories) && $categories->isNotEmpty())
                                                      <?php $count = 1; ?>
                                                      @foreach($categories as $data)
                                                      <tr>
                                                          <th scope="row">{{ $count }}</th>
                                                          <td><a href="{{ route('knowledge.base.edit.category', $data->id) }}">{{ $data->name ?? '' }}</a></td>
                                                          <td>{{ $data->description ?? '-' }}</td>
                                                          <td>
                                                              <form action="{{ route('knowledge.base.delete.category') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                                  @csrf
                                                                  <input type="hidden" name="category_id" value="{{ $data->id }}">
                                                                  <button type="submit" class="btn btn-primary">Delete</button>
                                                              </form>
                                                          </td>
                                                      </tr>
                                                      <?php $count++; ?>
                                                      @endforeach
                                                  @else
                                                      <tr>
                                                          <td colspan="4" class="text-center">No data found.</td>
                                                      </tr>
                                                  @endif
                                             </tbody>
                                              
                                        </table>
                                   </div>
                              </div><!-- .card-preview -->
                         </div><!-- .nk-block -->
                    </div><!-- .components-preview -->
               </div>
          </div>
     </div>
</div>

@endsection