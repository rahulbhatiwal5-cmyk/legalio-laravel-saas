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
                                        <h4 class="nk-block-title">Knowledge Base Article</h4>
                                   </div>
                                   <div class="d-flex justify-content-end p-2">
                                        <div class="nk-block-head-content">
                                             <div class="mbsc-form-group">
                                                  <a href="{{ route('knowledge.base.add.article') }}" class="btn btn-light">Add Article</a>
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
                                                       <th scope="col">Category name</th>
                                                       <th scope="col">Description</th>
                                                       {{-- <th scope="col">Slug</th> --}}
                                                       <th scope="col">Action</th>
                                                       {{-- <th scope="col"></th> --}}



                                                  </tr>
                                             </thead>
                                             <tbody>
                                                  @if(isset($articles) && $articles->isNotEmpty())
                                                      <?php $count = 1; ?>
                                                      @foreach($articles as $data)
                                                      <tr>
                                                          <th scope="row">{{ $count }}</th>
                                                          <td><a href="{{ route('knowledge.base.edit.article', $data->id) }}">{{ $data->title ?? '' }}</a></td>
                                                          <td>{{ $data->category->name ?? '' }}</td>
                                                          <td>{{ $data->heading ?? '-' }}</td>
                                                          <td>
                                                              <form action="{{ route('knowledge.base.delete.article') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                                                  @csrf
                                                                  <input type="hidden" name="article_id" value="{{ $data->id }}">
                                                                  <button type="submit" class="btn btn-primary">Delete</button>
                                                              </form>
                                                          </td>
                                                      </tr>
                                                      <?php $count++; ?>
                                                      @endforeach
                                                  @else
                                                      <tr>
                                                          <td colspan="5" class="text-center">No data found.</td>
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