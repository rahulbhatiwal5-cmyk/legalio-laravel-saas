@extends('admin_layout.master')
@section('content')
<div class="nk-content">

     <div class="nk-block-head">
          <div class="nk-block-head-content">
              <h4 class="nk-block-title">How It Work Page</h4>
          </div>
      </div>

     <div class="container-fluid">
          <div class="row">
               <div class="col-md-8">
                    <div class="card card-bordered card-preview">
                         <div class="card-inner">
          <form action="{{ url('admin-dashboard/add-how-it-works') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="delete_work_ids" id="delete_work_ids" value="">
               <input type="hidden" name="work_img_id" id="work_img_id" value="">
               <input type="hidden" name="bg_img_id" id="bg_img_id" value="">
               <input type="hidden" name="baner_image_id" id="baner_image_id" value="">
               <input type="hidden" name="second_id" id="second_id" value="">

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
                         <h4>How it Works</h4>
                         <hr>
                         <h6>Work Section</h6>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="main_heading">Main Heading</label>
                                   <input type="text" class="form-control form-control" id="main_heading" name="main_heading" value="{{ $data['main_heading'] ?? '' }}">
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="short_description">Short Description</label>
                                   <textarea class="form-control" id="short_description" name="short_description">{{ $data['short_description'] ?? '' }}</textarea>
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="">Works</label>
                              </div>
                         </div>
                         @if(isset($works) && $works->isNotEmpty())
                         @foreach($works as $work)
                         <?php
                              $path = getStorageFilepath($work->media->file_path);
                         ?>
                         <div class="work-append-sec{{ $work->id ?? '' }}">
                              <hr>
                              <div class="row gy-12">
                                   <div class="text-end"><span class="remove-work-sec" data-id="{{ $work->id ?? '' }}"><i class="fa fa-times"></i></span></div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <button class="btn-sm btn btn-primary" type="button" data-id="{{ $work->id ?? '' }}">Add New</button>
                                             <input type="file" name="work_up_img" class="up_img" data-id="{{ $work->id ?? '' }}" id="work_up_img{{ $work->id ?? '' }}" style="display:none;">
                                        </div>
                                        @if(isset($work->media) && $work->media != null)
                                        <div class="work_img_div" id="work_img_div{{ $work->id ?? '' }}">
                                             <div class="form-group">
                                                  <span class="col-md-7 offset-md-5 remove_work_img" data-id="{{ $work->id ?? '' }}">
                                                       <i class="fa fa-times"></i>
                                                  </span>
                                             </div>
                                             <div class="form-group">
                                                  <img src="{{ asset('storage/'.$path) }}" alt="">
                                             </div>
                                        </div>
                                        @endif
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="work_heading">Heading</label>
                                             <input type="text" class="form-control" id="work_heading" name="work_heading[{{$work->id}}]" value="{{ $work->heading ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="work_description">Description</label>
                                             <textarea class="form-control" id="work_description" name="work_description[{{$work->id}}]">{{ $work->description ?? '' }}</textarea>
                                        </div>
                                   </div>
                              </div>
                         </div>
                         @endforeach
                         @endif
                         <div id="work-section"></div>
                         <br>
                         <div class="text-end">
                              <div class="form-group">
                                   <button type="button" class="btn btn-sm btn-primary" id="add-works-sec">Add Row</button>
                              </div>
                         </div>
                         <hr>
                         <h6>Second Banner</h6>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="second_banner_img">Banner Image</label>
                                   <input type="file" class="form-control" id="second_banner_img" name="second_banner_img">
                              </div>
                              @if(isset($data['second_banner_img']) && $data['second_banner_img'] != null)
                              <div class="second_banner_div" id="second_banner_div{{ $data['second_banner_id'] ?? '' }}">
                                   <div class="form-group">
                                        <span class="col-md-9 offset-md-3 remove_second_banner" data-id="{{ $data['second_banner_id'] ?? '' }}">
                                             <i class="fa fa-times"></i>
                                        </span>
                                   </div>
                                   <div class="form-group">
                                        <img src="{{ asset('storage/'.$data['second_banner_img']) }}" alt="banner_img" height="140px" width="160px">
                                   </div>
                              </div>
                              @endif
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="banner_heading">Heading</label>
                                   <input type="text" class="form-control" id="banner_heading" name="banner_heading" value="{{ $data['second_banner_heading'] ?? '' }}">
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="sub_heading">Sub Heading</label>
                                   <textarea class="form-control" id="sub_heading" name="sub_heading">{{ $data['second_banner_sub_heading'] ?? '' }}</textarea>
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="button_label">Button Label</label>
                                   <input type="text" class="form-control" id="button_label" name="button_label" value="{{ $data['button_label'] ?? '' }}">
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="button_link">Button Link</label>
                                   <input type="text" class="form-control form-control" id="button_link" name="button_link" value="{{ $data['button_link'] ?? '' }}">
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
                                                  {{-- <a href="{{ url('/asi-funciona') }}" class="view_page" target="_blank">View Page</a> --}}
                                                  <a href="{{ url('/how-it-works') }}" class="view_page" target="_blank">View Page</a>
                                             </div>
                                        </div>
                                        <div class="nk-block-head-content">
                                             <div class="up-btn mbsc-form-group">
                                                  <button class="btn btn-primary" type="submit">Update</button>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="meta_title">Meta Title</label>
                                             <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ $data['meta_title'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="meta_description">Meta Description</label>
                                             <textarea class="form-control" id="meta_description" name="meta_description">{{ $data['meta_description'] ?? '' }}</textarea>
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
          $('.update_work_img').click(function(){
               var id = $(this).data('id');
               $('#work_up_img' + id).trigger('click');
          });

          $('.up_img').change(function() {
               var id = $(this).data('id');
               var file = this.files[0];
               var formData = new FormData();
               formData.append('image', file);
               formData.append('_token', "{{ csrf_token() }}");
               formData.append('work_id', id);

               $.ajax({
                    url: "{{ url('/update/work/image') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function(response){
                         NioApp.Toast('New image is updated', 'info', {position: 'top-right'});
                         setTimeout(() => {
                              location.reload();
                         },1000);
                    },
                    error: function(response) {
                         console.log(response.responseText);
                         NioApp.Toast('Error uploading image','error', {position: 'top-right'});
                    }
               });
          });

          $('.remove_work_img').click(function(){
               id = $(this).data('id');
               let removeIds = $('#work_img_id').val();

               if(removeIds) {
                    removeIds += ',' + id;
               }else{
                    removeIds = id;
               }
               $('#work_img_id').val(removeIds);

               $('#work_img_div'+id).hide();
          })

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

          $('.remove_second_banner').click(function(){
               id = $(this).data('id');
               // $('#second_id').val(id);
               $('#second_banner_div'+id).hide();
          });
     })

</script>

<script>
     $(document).ready(function(){
          // Append Work Section //
          $('#add-works-sec').click(function(){
               var html = `<div class="work-append-sec">
                              <hr>
                              <div class="row gy-12">
                                   <div class="text-end"><span class="remove-work-sec" value="appended"><i class="fa fa-times"></i></span></div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="new_work_image">Image</label>
                                             <input type="file" class="form-control" id="new_work_image" name="new_work_image[]" value="">
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="new_work_heading">Heading</label>
                                             <input type="text" class="form-control" id="new_work_heading" name="new_work_heading[]" value="">
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="new_work_description">Description</label>
                                             <textarea class="form-control" id="new_work_description" name="new_work_description[]"></textarea>
                                        </div>
                                   </div>
                              </div>
                         </div><br>`;

               $('#work-section').append(html);
          });


          $('body').delegate('.remove-work-sec', 'click', function () {
               var id = $(this).data('id');
               if($(this).attr('value') === 'appended'){
                    $(this).closest('.work-append-sec').remove();
                    return false;
               }

               let deleteIds = $('#delete_work_ids').val();
               if(deleteIds) {
                    deleteIds += ',' + id;
               }else {
                    deleteIds = id;
               }
               $('#delete_work_ids').val(deleteIds);

               $('.work-append-sec'+id).hide();
          });


     });
</script>

@endsection
