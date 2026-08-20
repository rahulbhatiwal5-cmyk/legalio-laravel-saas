@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <form action="{{route('knowledge.base.update.category')}}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $category->id ?? '' }}">
            <input type="hidden" name="bg_img_id" id="bg_img_id" value="">
            <div class="row main_section">
                <div class="col-md-8 left_content">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="name"><b><h4>Category Title</b></h4></label>
                                    <input type="text" class="form-control form-control-lg" id="name" name="name" value="{{ old('name', $category->name) }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="image">Image</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                    @if(! $category->image)
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    @endif
                                </div>
                                @if($mediaUrl)
                                {{-- <div class="bg_image_div" id="bg_image{{ $register->id ?? '' }}"> --}}
                                <div class="bg_image_div"  id="bg_image{{ $category->id ?? '' }}" >

                                    <div class="form-group">
                                        {{-- <span class="col-md-9 offset-md-3 remove_background_image" data-id="{{ $register->id ?? '' }}"> --}}
                                        <span class="col-md-9 offset-md-3 remove_background_image" data-id="{{ $category->id ?? '' }}" >

                                            <i class="fa fa-times"></i>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <img src="{{ $mediaUrl }}" height="140px" width="160px">
                                    </div>
                                </div>
                            @endif
                            </div>

                            {{-- <h5>Register Page</h5>   --}}
                           <hr>
                            <h6>Short Description</h6>
                            <hr>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="description">Description</label>
                                    <input type="text" class="form-control form-control" id="description" name="description" value="{{ old('description', $category->description) }}">
                                    @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 right-content">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <div class="d-flex justify-content-between align-items-center">



                                <div class="nk-block-head-content butn-cls">
                                    <div class="mbsc-form-group view_btn">

                                         {{-- <a href="{{ route('help.center') }}" target="_blank" class="view_page">View Page</a> --}}

                                    </div>
                               </div>
                               <div class="nk-block-head-content">
                                    <div class="up-btn mbsc-form-group">
                                        <button class="btn btn-primary" type="submit">Update</button>
                                    </div>
                               </div>

                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="meta_title">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="50"  value="{{ old('meta_title', $category->meta_title) }}">
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="meta_description">Meta Description</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" maxlength="155">{{ old('meta_description', $category->meta_description) }}</textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.remove_background_image').click(function(){
               id = $(this).data('id');
            //    $('#bg_img_id').val(id);
               $('#bg_image'+id).hide();

               $.ajax
            ({
                url: "{{ route('knowledge.base.delete.category.image') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: $('input[name="id"]').val(),
                        },
                        success: function(response) {
                            if (response.success) {
                                alert("Image deleted successfully!");
                            } else {
                                alert("Error deleting image.");
                            }
                        }
            });
          });



    });


</script>

@endsection
