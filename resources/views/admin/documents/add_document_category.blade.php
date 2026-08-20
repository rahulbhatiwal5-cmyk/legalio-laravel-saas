@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">
        @if(isset($category) && $category != null)
        <form action="{{ url('/admin-dashboard/category-process') }}" method="post" enctype="multipart/form-data">
        @else
        <form action="{{ url('/admin-dashboard/category-process') }}" method="post" enctype="multipart/form-data">
        @endif
            @csrf
            <input type="hidden" name="id" value="{{ $category->id ?? '' }}">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="col-md-8 pb-2">
                        <div class="form-group">
                            <label class="form-label" for="name"><b><h4>@if(isset($category) && $category != null) Edit Category @else Add Category @endif</b></h4></label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Add Category Name" value="{{ $category->name ?? '' }}">
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
                                        <input type="text" class="form-control" id="slug" name="slug" value="{{ $category->slug ?? '' }}" readonly>
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
                                                @if(isset($category->parent_category) && $category->parent_category != null)
                                                    @if($category->parent_category == $p_catg->id)
                                                        <option value="{{ $p_catg->id ?? '' }}" selected>{{ $p_catg->name ?? '' }}</option>
                                                    @else
                                                        <option value="{{ $p_catg->id ?? '' }}">{{ $p_catg->name ?? '' }}</option>
                                                    @endif
                                                @else
                                                    <option value="{{ $p_catg->id ?? '' }}">{{ $p_catg->name ?? '' }}</option>
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
                                        <textarea class="form-control" id="description" name="description">{{ $category->description ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
            @if(isset($category) && $category != null)
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

     $('#name').on('keyup',function(){
          const name = $(this).val();
          const url = name.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g, '');
          $('#slug').val(url);
     })
</script>

@endsection