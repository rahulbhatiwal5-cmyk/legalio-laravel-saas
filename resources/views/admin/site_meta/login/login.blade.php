@extends('admin_layout.master')
@section('content')

<div class="nk-content">

    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">Login Page</h4>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                 <div class="card card-bordered card-preview">
                      <div class="card-inner">
        <form action="{{ url('/admin-dashboard/add-login') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ $login->id ?? '' }}">
            <input type="hidden" name="bg_img_id" id="bg_img_id" value="">
            <div class="row main_section">
                <div class="col-md-12 left-content">
                    <div class="col-md-12 pb-2">
                        <div class="form-group">
                            <label class="form-label" for="title"><b><h5>Page Title</b></h5></label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ $login->title ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" for="background_image">Background Image</label>
                            <input type="file" class="form-control" id="background_image" name="background_image">
                        </div>
                        @if($login->background_image != null)
                        <?php $path = getStorageFilepath($login->file_path); ?>
                        <div class="bg_image_div" id="bg_image{{ $login->id ?? '' }}">
                            <div class="form-group">
                                <span class="col-md-9 offset-md-3 remove_background_image" data-id="{{ $login->id ?? '' }}">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div>
                            <div class="form-group">
                                <img src="{{ asset('storage/'.$path) }}" height="140px" width="160px">
                            </div>
                        </div>
                        @endif
                    </div>
                    <hr>
                    <h5>Login Page</h5>
                    <hr>
                    <h6>Login Section</h6>
                    <hr>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" for="main_heading">Main Heading</label>
                            <input type="text" class="form-control form-control" id="main_heading" name="main_heading" value="{{ $login->main_heading ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="main_heading">Main Sub Heading</label>
                            <input type="text" class="form-control form-control" id="main_sub_heading" name="main_sub_heading" value="{{ $login->main_sub_heading ?? '' }}">
                        </div>
                    </div>
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
                                        <a href="{{ route('login.user') }}" class="view_page" target="_blank">View Page</a>
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
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="50" value="{{ $login->meta_title ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="meta_description">Meta Description</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" maxlength="155">{{ $login->meta_description ?? '' }}</textarea>
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
          });
    })
</script>

@endsection
