@extends('admin_layout.master')
@section('content')
<div class="nk-content">

     <div class="nk-block-head">
          <div class="nk-block-head-content">
              <h4 class="nk-block-title">Privacy Policy Page</h4>
          </div>
      </div>

     <div class="container-fluid">
          <div class="row">
               <div class="col-md-8">
                    <div class="card card-bordered card-preview">
                         <div class="card-inner">
          <form action="{{ url('/admin-dashboard/privacy-policy-process') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="remove_ids" id="remove_ids" value="">
               <input type="hidden" name="bg_img_id" id="bg_img_id" value="">
               <input type="hidden" name="baner_image_id" id="baner_image_id" value="">
               <div class="row main_section">
                    <div class="col-md-12 left-content">
                         <div class="col-md-12 pb-2">
                              <div class="form-group">
                                   <label class="form-label" for="title"><b><h5>Page Title</b></h5></label>
                                   <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ $data['title_name'] ?? '' }}">
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
                              @if(isset($data['background_image']) && $data['background_image'] != null)
                              <div class="bg_image_div" id="bg_image{{ $data['bg_image_id'] ?? '' }}">
                                   <div class="form-group">
                                        <span class="col-md-9 offset-md-3 remove_background_image" data-id="{{ $data['bg_image_id'] ?? '' }}">
                                             <i class="fa fa-times"></i>
                                        </span>
                                   </div>
                                   <div class="form-group">
                                        <img src="{{ asset('storage/'.$data['background_image']) }}" alt="background_img" height="100px" width="160px">
                                   </div>
                              </div>
                              @endif
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="banner_title">Banner Title</label>
                                   <input type="text" class="form-control" id="banner_title" name="banner_title" value="{{ $data['banner_title'] ?? '' }}">
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="banner_description">Banner Description</label>
                                   <textarea class="form-control" id="banner_description" name="banner_description">{{ $data['banner_description'] ?? '' }}</textarea>
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="banner_image">Banner Image</label>
                                   <input type="file" class="form-control" id="banner_image" name="banner_image" value="">
                              </div>
                              @if(isset($data['banner_image']) && $data['banner_image'] != null)
                              <div class="banner_div" id="banner_div{{ $data['banner_image_id'] ?? '' }}">
                                   <div class="form-group">
                                        <span class="col-md-9 offset-md-3 remove_banner_image" data-id="{{ $data['banner_image_id'] ?? '' }}">
                                             <i class="fa fa-times"></i>
                                        </span>
                                   </div>
                                   <div class="form-group">
                                        <img src="{{ asset('storage/'.$data['banner_image']) }}" alt="banner_img" height="140px" width="160px">
                                   </div>
                              </div>
                              @endif
                         </div>
                         <hr>
                         <!--<h5>Privacy Policy</h5>
                         <hr>-->
                         <h6>Privacy & Policy Section</h6>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="main_heading">Main Heading</label>
                                   <input type="text" class="form-control" id="main_heading" name="main_heading" value="{{ $data['main_heading'] ?? '' }}">
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group" id="terms_section">
                                   <label class="form-label" for="main_heading">Terms & condition</label>
                              </div>
                         </div>
                         @if(isset($privacy_policy) && $privacy_policy->isNotEmpty())
                         @foreach($privacy_policy as $index=>$value)
                         <div class="append-sec{{ $value->id ?? '' }}">
                              <hr>
                              <div class="text-end">
                                   <div class="form-group">
                                        <div><span class="removeTerms" data-id="{{ $value->id ?? '' }}"><i class="fa fa-times"></i></span></div>
                                   </div>
                              </div>
                              <div class="row gy-4">
                                   <div class="col-md-6">
                                             <div class="form-group">
                                             <label class="form-label" for="terms">Terms</label>
                                             <input type="text" class="form-control form-control" id="terms" name="terms[{{ $value->id ?? '' }}]" value="{{ $value->terms ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-6">
                                        <div class="form-group">
                                             <label class="form-label" for="condition">Condition</label>
                                             <textarea class="form-control" id="condition{{ $index ?? '' }}" name="condition[{{ $value->id ?? '' }}]">{{ $value->condition ?? '' }}</textarea>
                                        </div>
                                   </div>
                              </div>
                         </div>
                         <script>  ClassicEditor.create( document.querySelector('#condition{{ $index }}')); </script>
                         @endforeach
                         @endif
                         <div id="termCondition-section"></div>
                         <br>
                         <div class="col-md-3 offset-md-9">
                              <div class="form-group">
                                   <button type="button" class="btn btn-sm btn-primary" id="addnewrow">Add Row</button>
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
                                                  {{-- <a href="{{ url('/aviso-de-privacidad') }}" class="view_page" target="_blank">View Page</a> --}}
                                                  <a href="{{ url('/privacy-policy') }}" class="view_page" target="_blank">View Page</a>
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
                                             <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="50" value="{{ $data['meta_title'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="meta_description">Meta Description</label>
                                             <textarea class="form-control" id="meta_description" name="meta_description" maxlength="155">{{ $data['meta_description'] ?? '' }}</textarea>
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


          $('#addnewrow').click(function(){
               var html = `<div class="append-sec">
                              <hr>
                              <div class="text-end">
                                   <div class="form-group">
                                        <div class=""><span class="removeTerms" value="appended"><i class="fa fa-times"></i></span></div>
                                   </div>
                              </div>
                              <div class="row gy-4">
                                   <div class="col-md-6">
                                             <div class="form-group">
                                             <label class="form-label" for="new_terms">Terms</label>
                                             <input type="text" class="form-control form-control" id="new_terms" name="new_terms[]" value="">
                                        </div>
                                   </div>
                                   <div class="col-md-6">
                                        <div class="form-group">
                                             <label class="form-label" for="new_condition">Condition</label>
                                             <textarea class="add-editor" id="new_condition" name="new_condition[]"></textarea>
                                        </div>
                                   </div>
                              </div>
                         </div>`;
               $('#termCondition-section').append(html);

               $('.add-editor').each(function() {
                    if (!$(this).data('ckeditor-initialized')) {
                         ClassicEditor
                              .create(this)
                              .catch(error => {
                                   console.error(error);
                              });
                         $(this).data('ckeditor-initialized', true);
                    }
               });
          })

          $('body').delegate('.removeTerms', 'click', function () {
               var id = $(this).data('id');
               if($(this).attr('value') === 'appended'){
                    $(this).closest('.append-sec').remove();
                    return false;
               }

               let deleteIds = $('#remove_ids').val();

               if(deleteIds) {
                    deleteIds += ',' + id;
               }else{
                    deleteIds = id;
               }
               $('#remove_ids').val(deleteIds);

               $('.append-sec'+id).hide();
          });
     })
</script>


@endsection
