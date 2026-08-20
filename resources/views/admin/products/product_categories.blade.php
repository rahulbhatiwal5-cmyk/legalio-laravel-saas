@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="container-fluid">
          @if(isset($categories))
          <form action="{{ url('/admin-dashboard/add-categories') }}" method="post" enctype="multipart/form-data">
          @else
          <form action="{{ url('/admin-dashboard/add-categories') }}" method="post" enctype="multipart/form-data">
          @endif
               @csrf
               <input type="hidden" name="id" value="{{ $categories->id ?? '' }}">
               <div class="card card-bordered card-preview">
                    <div class="card-inner">
                         <div class="col-md-8 pb-2">
                              <div class="form-group">
                                   <label class="form-label" for="title"><b><h4>Title</b></h4></label>
                                   <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ $categories->title ?? '' }}">
                              </div>
                         </div>
                         <div class="card card-bordered card-preview mt-2">
                              <div class="card-inner">
                                   <div class="row g-3">
                                        <div class="col-lg-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="slug">Slug</label>
                                             </div>
                                        </div>
                                        <div class="col-lg-7">
                                             <div class="form-group">
                                                  <input type="text" class="form-control" id="slug" name="slug" value="{{ $categories->slug ?? '' }}">
                                             </div>
                                        </div>
                                   </div>
                                   <div class="row g-3 mt-2">
                                        <div class="col-lg-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="parent_category">Parent Category</label>
                                             </div>
                                        </div>
                                        <div class="col-lg-7">
                                             <div class="form-group">
                                                  <select class="form-select" id="parent_category" name="parent_category" tabindex="-1" aria-hidden="true">
                                                       <option value="">None</option>
                                                       @if(isset($parent_category) && $parent_category != null)
                                                            @foreach($parent_category as $p_catg)
                                                                 @if(isset($categories->parent_category) && $categories->parent_category != null)
                                                                      @if($categories->parent_category == $p_catg->id)
                                                                           <option value="{{ $p_catg->id ?? '' }}" selected>{{ $p_catg->title ?? '' }}</option>
                                                                      @else
                                                                           <option value="{{ $p_catg->id ?? '' }}">{{ $p_catg->title ?? '' }}</option>
                                                                      @endif
                                                                 @else
                                                                      <option value="{{ $p_catg->id ?? '' }}">{{ $p_catg->title ?? '' }}</option>
                                                                 @endif 
                                                            @endforeach
                                                       @endif
                                                  </select>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="row g-3 mt-2">
                                        <div class="col-lg-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="description">Description</label>
                                             </div>
                                        </div>
                                        <div class="col-lg-7">
                                             <div class="form-group">
                                                  <textarea class="form-control" id="description" name="description">{{ $categories->description ?? '' }}</textarea>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="row g-3 mt-2">
                                        <div class="col-lg-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="display_type">Display type</label>
                                             </div>
                                        </div>
                                        <div class="col-lg-7">
                                             <div class="form-group">
                                                  <select class="form-select" id="display_type" name="display_type" tabindex="-1" aria-hidden="true">
                                                  @if(isset($categories->display_type) && $categories->display_type != null)
                                                       <option value="default" {{ $categories->display_type == 'default' ? 'selected' : '' }}>Default</option>
                                                       <option value="products" {{ $categories->display_type == 'products' ? 'selected' : '' }}>Products</option>
                                                       <option value="subcategories" {{ $categories->display_type == 'subcategories' ? 'selected' : '' }}>Subcategories</option>
                                                       <option value="both" {{ $categories->display_type == 'both' ? 'selected' : '' }}>Both</option>
                                                  @endif
                                                  </select>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="row g-3 mt-2">
                                        <div class="col-lg-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="thumbnail">Thumbnail</label>
                                             </div>
                                        </div>
                                        @if($categories->media ?? '')
                                        <div class="col-lg-1">
                                             <div class="form-group">
                                                  <img src="{{ asset('storage/'.$categories->media->file_name) }}" alt="img">
                                             </div>
                                        </div>
                                        @endif
                                        <div class="col-lg-6">
                                             <div class="form-group">
                                                  <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
               <div class="mt-3">
                    @if(isset($categories))
                    <button class="btn btn-primary" type="submit">Update</button>
                    @else
                    <button class="btn btn-primary" type="submit">Save</button>
                    @endif
               </div>
          </form>
     </div>
</div>

<script>

     ClassicEditor
     .create( document.querySelector('#description'))
     .catch( error => {
          console.error( error );
     });

     $('#title').on('keyup',function(){
          const name = $(this).val();
          const url = name.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g, '');
          $('#slug').val(url);
     })
</script>

@endsection