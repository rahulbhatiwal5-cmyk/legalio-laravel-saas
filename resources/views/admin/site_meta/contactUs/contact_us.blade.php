@extends('admin_layout.master')
@section('content')

<div class="nk-content">

     <div class="nk-block-head">
          <div class="nk-block-head-content">
              <h4 class="nk-block-title">Contact Us Page</h4>
          </div>
      </div>

     <div class="container-fluid">
          <div class="row">
               <div class="col-md-8">
                    <div class="card card-bordered card-preview">
                         <div class="card-inner">
          <form action="{{ url('/admin-dashboard/contact-us-procc') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="id" value="{{ $contact->id ?? '' }}">
               <input type="hidden" name="bg_img_id" id="bg_img_id" value="">
               <input type="hidden" name="baner_image_id" id="baner_image_id" value="">

               <div class="row main_section">
                    <div class="col-md-12 left-content">
                         <div class="col-md-12 pb-2">
                              <div class="form-group">
                                   <label class="form-label" for="title"><b><h5>Page Title</b></h5></label>
                                   <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ $contact->title ?? '' }}">
                              </div>
                         </div>
                         <hr>
                         <h5>Banner Section</h5>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="background_image">Background Image</label>
                                   <input type="file" class="form-control" id="background_image" name="background_image" value="">
                              </div>
                              @if(isset($contact->background_image) && $contact->background_image != null)
                              <?php
                                   $path = getStorageFilepath($contact->background_image_path);
                              ?>
                              <div class="bg_image_div" id="bg_image{{ $contact->id ?? '' }}">
                                   <div class="form-group">
                                        <span class="col-md-9 offset-md-3 remove_background_image" data-id="{{ $contact->id ?? '' }}">
                                             <i class="fa fa-times"></i>
                                        </span>
                                   </div>
                                   <div class="form-group">
                                        <img src="{{ asset('storage/'.$path) }}" alt="background_img" height="140px" width="160px">
                                   </div>
                              </div>
                              @endif
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="banner_title">Banner Title</label>
                                   <input type="text" class="form-control" id="banner_title" name="banner_title" value="{{ $contact->banner_title ?? '' }}">
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="banner_description">Banner Description</label>
                                   <textarea class="form-control" id="banner_description" name="banner_description">{{ $contact->banner_description ?? '' }}</textarea>
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="banner_image">Banner Image</label>
                                   <input type="file" class="form-control" id="banner_image" name="banner_image" value="">
                              </div>
                              @if(isset($contact->banner_image) && $contact->banner_image != null)
                              <?php
                                   $path = getStorageFilepath($contact->banner_image_path);
                              ?>
                              <div class="banner_div" id="banner_div{{ $contact->id ?? '' }}">
                                   <div class="form-group">
                                        <span class="col-md-8 offset-md-4 remove_banner_image" data-id="{{ $contact->id ?? '' }}">
                                             <i class="fa fa-times"></i>
                                        </span>
                                   </div>
                                   <div class="form-group">
                                        <img src="{{ asset('storage/'.$path) }}" alt="banner_img" height="160px" width="220px">
                                   </div>
                              </div>
                              @endif
                         </div>
                         <hr>
                         <h5>Contact Page</h5>
                         <hr>
                         <h6>Contact Section</h6>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="main_title">Main Title</label>
                                   <input type="text" class="form-control" id="main_title" name="main_title" value="{{ $contact->main_title ?? '' }}">
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
                                                  {{-- <a href="{{ url('/contacto') }}" class="view_page" target="_blank">View Page</a> --}}
                                                  <a href="{{ url('/contact') }}" class="view_page" target="_blank">View Page</a>
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
                                             <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="50" value="{{ $contact->meta_title ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="meta_description">Meta Description</label>
                                             <textarea class="form-control" id="meta_description" name="meta_description" maxlength="155">{{ $contact->meta_description ?? '' }}</textarea>
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

     ClassicEditor
     .create( document.querySelector('#description'))
     .catch( error => {
          console.error( error );
     });


$(document).ready(function(){
     $('.remove_background_image').click(function(){
          id = $(this).data('id');
          // $('#bg_img_id').val(id);
          $('#bg_image'+id).hide();
     });

     $('.remove_banner_image').click(function(){
          id = $(this).data('id');
          // $('#baner_image_id').val(id);
          $('#banner_div'+id).hide();
     });
})
</script>


@endsection
