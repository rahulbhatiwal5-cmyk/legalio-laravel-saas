@extends('admin_layout.master')
@section('content')

<div class="nk-content">

     <div class="nk-block-head">
          <div class="nk-block-head-content">
              <h4 class="nk-block-title">Home Page</h4>
          </div>
      </div>


     <div class="container-fluid">

          <div class="row">
               <div class="col-md-8">
                    <div class="card card-bordered card-preview">
                         <div class="card-inner">
               <form action="{{ url('admin-dashboard/add/home-content') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="category_sec" name="category_sec" value="">
                    <input type="hidden" id="template_sec" name="template_sec" value="">
                    <input type="hidden" id="bg_image_id" name="bg_image_id" value="">
                    <input type="hidden" id="baner_image_id" name="baner_image_id" value="">
                    <input type="hidden" id="btom_banner_id" name="btom_banner_id" value="">
                    <input type="hidden" id="rght_id" name="rght_id" value="">
                    <input type="hidden" id="lft_id" name="lft_id" value="">
                    <input type="hidden" id="category_img_id" name="category_img_id" value="">
                    <div class="row main_section">
                         <div class="col-md-12 left-content">
                              <div class="col-md-12 pb-2">
                                   <div class="form-group">
                                        <label class="form-label" for="title"><b><h5>Page Title</b></h5></label>
                                        <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ $data['title'] ?? '' }}">
                                   </div>
                              </div>
                              <hr>
                              <h6>Banner Section</h6>
                              <hr>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="background_image">Background Image</label>
                                        <input type="file" class="form-control" id="background_image" name="background_image" value="">
                                   </div>
                                   @if($data['background_image'] != null)
                                   <div class="bg_image_div" id="bg_image{{ $data['background_image_id'] ?? '' }}">
                                        <div class="form-group">
                                             <span class="col-md-9 offset-md-3 remove_background_image" data-id="{{ $data['background_image_id'] ?? '' }}">
                                                  <i class="fa fa-times"></i>
                                             </span>
                                        </div>
                                        <div class="form-group">
                                             <img src="{{ asset('storage/'.$data['background_image'] ?? '' ) }}" height="140px" width="170px">
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
                                        <input type="text" class="form-control" id="banner_description" name="banner_description" value="{{ $data['banner_description'] ?? '' }}">
                                   </div>
                              </div>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="banner_image">Banner Image</label>
                                        <input type="file" class="form-control" id="banner_image" name="banner_image" value="">
                                   </div>
                                   @if($data['banner_image'] != null)
                                   <div class="banner_div" id="banner_div{{ $data['banner_image_id'] ?? '' }}">
                                        <div class="form-group">
                                             <span class="col-md-10 offset-md-2 remove_banner_image" data-id="{{ $data['banner_image_id'] ?? '' }}">
                                                  <i class="fa fa-times"></i>
                                             </span>
                                        </div>
                                        <div class="form-group">
                                             <img src="{{ asset('storage/'.$data['banner_image'] ?? '' ) }}" height="140px" width="160px">
                                        </div>
                                   </div>
                                   @endif
                              </div>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="banner_placeholder">Placeholder</label>
                                        <input type="text" class="form-control" id="banner_placeholder" name="banner_placeholder" value="{{ $data['banner_placeholder'] ?? '' }}">
                                   </div>
                              </div>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="button_name">Button Name</label>
                                        <input type="text" class="form-control" id="button_name" name="button_name" value="{{ $data['button_name'] ?? '' }}">
                                   </div>
                              </div>
                              <hr>
                              <h6>Most popular Documents</h6>
                              <hr>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="main_title">Main Title</label>
                                        <input type="text" class="form-control" id="main_title" name="main_title" value="{{ $data['most_popular_title'] ?? '' }}">
                                   </div>
                              </div>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="popular_documents">Popular Documents</label>
                                        <div class="form-control-wrap">
                                             <select class="form-select js-select2" multiple="multiple" name="popular_documents[]" id="popular_documents">
                                             @if(isset($document_category) && $document_category != null)
                                             @foreach($document_category as $document)
                                                  @if(isset($data['popular']) && $data['popular'] != null)
                                                  <?php
                                                       $documentsId = json_decode($data['popular']);
                                                  ?>
                                                       @if(in_array($document->id,$documentsId))
                                                       <option value="{{ $document->id ?? '' }}" selected>{{ $document->name ?? '' }}</option>
                                                       @else
                                                       <option value="{{ $document->id ?? '' }}">{{ $document->name ?? '' }}</option>
                                                       @endif
                                                  @else
                                                  <option value="{{ $document->id ?? '' }}">{{ $document->name ?? '' }}</option>
                                                  @endif
                                             @endforeach
                                             @endif
                                             </select>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="most_popular_btn_text">Button Text</label>
                                        <input type="text" class="form-control" id="most_popular_btn_text" name="most_popular_btn_text" value="{{ $data['most_popular_btn_text'] ?? '' }}">
                                   </div>
                              </div>
                              <hr>
                              <h6>Bottom Banner</h6>
                              <hr>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="bottom_heading">Heading</label>
                                        <input type="text" class="form-control" id="bottom_heading" name="bottom_heading" value="{{ $data['bottom_heading'] ?? '' }}">
                                   </div>
                              </div>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="bottom_sub_heading">Sub Heading</label>
                                        <textarea class="form-control" id="bottom_sub_heading" name="bottom_sub_heading">{{ $data['bottom_subheading'] ?? '' }}</textarea>
                                   </div>
                              </div>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="bottom_button_label">Button Label</label>
                                        <input type="text" class="form-control" id="bottom_button_label" name="bottom_button_label" value="{{ $data['bottom_button_label'] ?? '' }}">
                                   </div>
                              </div>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="bottom_button_link">Button Link</label>
                                        <input type="text" class="form-control" id="bottom_button_link" name="bottom_button_link" value="{{ $data['bottom_button_link'] ?? '' }}">
                                   </div>
                              </div>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="bottom_banner_img">Banner Image</label>
                                        <input type="file" class="form-control" id="bottom_banner_img" name="bottom_banner_img" value="">
                                   </div>
                                   @if($data['bottom_banner_image'] != null)
                                   <div class="bottom_bnnr" id="bottom_bnnr{{ $data['bottom_banner_id'] ?? '' }}">
                                        <div class="form-group">
                                             <span class="col-md-9 offset-md-3 remove_bottom_banner" data-id="{{ $data['bottom_banner_id'] ?? '' }}">
                                                  <i class="fa fa-times"></i>
                                             </span>
                                        </div>
                                        <div class="form-group">
                                             <img src="{{ asset('storage/'.$data['bottom_banner_image'] ?? '' ) }}" height="140px" width="160px">
                                        </div>
                                   </div>
                                   @endif
                              </div>
                              <hr>
                              <h6>Categories Section</h6>
                              <hr>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="category_main_title">Main Title</label>
                                        <input type="text" class="form-control" id="category_main_title" name="category_main_title" value="{{ $data['category_title'] ?? '' }}">
                                   </div>
                              </div>
                              <br>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="">Categories</label>
                                   </div>
                              </div>
                              @if(isset($home) && $home != null)
                              @foreach($home as $index=>$value)
                              <?php
                                   // $path = getStorageFilepath($value->media->file_path);
                                   $path = isset($value->media->file_path) ? getStorageFilepath($value->media->file_path) : null;
                              ?>
                              <div class="category-sec{{ $value->id ?? '' }}">
                                   <hr>
                                   <div class="text-end">
                                        <div class="form-group">
                                             <span class="remove_category" data-id="{{ $value->id ?? '' }}">
                                                  <i class="fa fa-times"></i>
                                             </span>
                                        </div>
                                   </div>
                                   <div class="row gy-12">
                                        <div class="col-md-2">
                                             <div class="form-group">
                                                  <button class="btn-sm update_category_img" type="button" data-id="{{ $value->id ?? '' }}">Add New</button>
                                                  <input type="file" name="category_up_img" class="up_img" data-id="{{ $value->id ?? '' }}" id="category_up_img{{ $value->id ?? '' }}" style="display:none;">
                                             </div>
                                             <div class="catg_img_div" id="catg_img_div{{ $value->id ?? '' }}">
                                                  <div class="form-group">
                                                       <span class="col-md-7 offset-md-5 remove_category_img" data-id="{{ $value->id ?? '' }}">
                                                            <i class="fa fa-times"></i>
                                                       </span>
                                                  </div>
                                                  <div class="form-group">
                                                       {{-- <img src="{{ asset('storage/'.$path ?? '' ) }}" alt="Category image"> --}}
                                                       @if($path)
                                                       <img src="{{ asset('storage/'.$path) }}" alt="Category image">
                                                       @endif
                                                  </div>
                                             </div>
                                        </div>
                                        <div class="col-md-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="cat_heading">Heading</label>
                                                  <input type="text" class="form-control" id="cat_heading" name="cat_heading[{{ $value->id ?? '' }}]" value="{{ $value->heading ?? '' }}">
                                             </div>
                                        </div>
                                        <div class="col-md-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="category{{ $value->id ?? '' }}">Category</label>
                                                  <div class="form-control-wrap">
                                                       <select class="form-select js-select2" name="category[{{ $value->id ?? '' }}]" id="category{{ $value->id ?? '' }}">
                                                            <option value="" selected disabled>Select</option>
                                                            @if(isset($document_category) && count($document_category) > 0)
                                                                 @foreach($document_category as $catg)
                                                                      @php
                                                                      $isSelected = isset($value->category_id) && $value->category_id == $catg->id;
                                                                      @endphp
                                                                      <option value="{{ $catg->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                                      {{ $catg->name ?? '' }}
                                                                      </option>
                                                                 @endforeach
                                                            @else
                                                                 <option value="" disabled>No categories available</option>
                                                            @endif
                                                       </select>
                                                  </div>
                                             </div>
                                        </div>
                                        <div class="col-md-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="category_btn_text">Button Text</label>
                                                  <input type="text" class="form-control" id="category_btn_text" name="category_btn_text[{{ $value->id ?? '' }}]" value="{{ $value->btn_text ?? '' }}">
                                             </div>
                                        </div>
                                        <div class="col-md-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="category_btn_link">Button Link</label>
                                                  <input type="text" class="form-control" id="category_btn_link" name="category_btn_link[{{ $value->id ?? '' }}]" value="{{ $value->btn_link ?? '' }}">
                                             </div>
                                        </div>
                                        <div class="col-md-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="category_description">Description</label>
                                                  <textarea class="form-control" id="category_description" name="category_description[{{ $value->id ?? '' }}]">{{ $value->category_description ?? '' }}</textarea>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                              @endforeach
                              @endif
                              <br>
                              <div class="text-end">
                                   <div class="form-group">
                                        <button type="button" class="btn btn-sm btn-primary" id="add_catgory">Add Row</button>
                                   </div>
                              </div>
                              <div id="catg_sec"></div>
                              <hr>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="join_us_text">Join Us Text</label>
                                        <input type="text" class="form-control" id="join_us_text" name="join_us_text" value="{{ $data['join_us_text'] ?? '' }}">
                                   </div>
                              </div>
                              <hr>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="reviews_heading">Reviews Heading</label>
                                        <input type="text" class="form-control" id="reviews_heading" name="reviews_heading" value="{{ $data['reviews_heading'] ?? '' }}">
                                   </div>
                              </div>
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="reviews_sub_heading">Reviews Sub Heading</label>
                                        <input type="text" class="form-control" id="reviews_sub_heading" name="reviews_sub_heading" value="{{ $data['reviews_sub_heading'] ?? '' }}">
                                   </div>
                              </div>
                                 <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="home_text_register">Home Register Text</label>
                                        <input type="text" class="form-control" id="home_text_register" name="home_text_register" value="{{ $data['home_text_register'] ?? '' }}">
                                   </div>
                              </div>
                                 <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="home_text_email">Home Email Text</label>
                                        <input type="text" class="form-control" id="home_text_email" name="home_text_email" value="{{ $data['home_text_email'] ?? '' }}">
                                   </div>
                              </div>
                                 <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="home_text_facebook">Home Facebook Text</label>
                                        <input type="text" class="form-control" id="home_text_facebook" name="home_text_facebook" value="{{ $data['home_text_facebook'] ?? '' }}">
                                   </div>
                              </div>
                                 <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="home_text_google">Home Google Text</label>
                                        <input type="text" class="form-control" id="home_text_google" name="home_text_google" value="{{ $data['home_text_google'] ?? '' }}">
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
                                                       <a href="{{ url('/') }}" class="view_page" target="_blank">View Page</a>
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
                                        <div class="col-md-12">
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
     $('.js-select2').select2();

     $('.update_category_img').click(function(){
          var id = $(this).data('id');
          $('#category_up_img' + id).trigger('click');
     });

     $('.up_img').change(function() {
          var id = $(this).data('id');
          var file = this.files[0];
          var formData = new FormData();
          formData.append('cat_image', file);
          formData.append('_token', "{{ csrf_token() }}");
          formData.append('category_id', id);

          $.ajax({
               url: "{{ url('/update/homecategory/image') }}",
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
                    alert('Error uploading image');
               }
          });
     });

     $('.remove_background_image').click(function(){
          id = $(this).data('id');
          console.log(id);
          // $('#bg_image_id').val(id);
          $('#bg_image'+id).hide();
     });

     $('.remove_banner_image').click(function(){
          id = $(this).data('id');
          // $('#baner_image_id').val(id);
          $('#banner_div'+id).hide();
     });

     $('.remove_bottom_banner').click(function(){
          id = $(this).data('id');
          // $('#btom_banner_id').val(id);
          $('#bottom_bnnr'+id).hide();
     });

     $('.remove_left_arrow').click(function(){
          id = $(this).data('id');
          $('#lft_id').val(id);
          $('#left_arrow_div'+id).hide();
     });

     $('.remove_right_arrow').click(function(){
          id = $(this).data('id');
          $('#rght_id').val(id);
          $('#right_arrow_div'+id).hide();
     });

     $('.remove_category_img').click(function(){
          id = $(this).data('id');
          let removeIds = $('#category_img_id').val();

          if(removeIds) {
               removeIds += ',' + id;
          }else{
               removeIds = id;
          }
          $('#category_img_id').val(removeIds);

          $('#catg_img_div'+id).hide();
     })

})

</script>

<script>
     $(document).ready(function(){
          $('#addnewrow').click(function(){
               var html = `<div class="append-sec">
                              <hr>
                              <div class="col-md-4 offset-md-8">
                                   <div class="form-group">
                                        <div><span class="removeTemplate" value="appended"><i class="fa fa-times"></i></span></div>
                                   </div>
                              </div>
                              <div class="row gy-5">
                                   <div class="col-md-2">
                                             <div class="form-group">
                                             <label class="form-label" for="temp_img">Image</label>
                                             <input type="file" class="form-control" id="temp_img" name="temp_img[]">
                                        </div>
                                   </div>
                                   <div class="col-md-2">
                                        <div class="form-group">
                                             <label class="form-label" for="temp_heading">Heading</label>
                                             <input type="text" class="form-control" id="temp_heading" name="temp_heading[]" value="">
                                        </div>
                                   </div>
                                   <div class="col-md-2">
                                             <div class="form-group">
                                             <label class="form-label" for="temp_subheading">Sub Heading</label>
                                             <input type="text" class="form-control" id="temp_subheading" name="temp_subheading[]" value="">
                                        </div>
                                   </div>
                                   <div class="col-md-2">
                                        <div class="form-group">
                                             <label class="form-label" for="temp_link">Link</label>
                                             <input type="text" class="form-control" id="temp_link" name="temp_link[]" value="">
                                        </div>
                                   </div>
                              </div>
                         </div>`;
               $('#temp_sec').append(html);
          })

          $('body').delegate('.removeTemplate', 'click', function () {
               var id = $(this).data('id');
               if($(this).attr('value') === 'appended'){
                    $(this).closest('.append-sec').remove();
                    return false;
               }

               let deleteIds = $('#template_sec').val();

               if(deleteIds) {
                    deleteIds += ',' + id;
               }else{
                    deleteIds = id;
               }
               $('#template_sec').val(deleteIds);

               $('.append-sec'+id).hide();
          });

          var document_category = {!! json_encode($document_category) !!};

          $('#add_catgory').click(function(){
               var category_option = [];
               $.each(document_category, function(key, val){
                    var category_html = `<option value="${val.id}">${val.name}</option>`;
                    category_option.push(category_html);
               });
               var category_options_html = category_option.join('');

               var html = `
                    <div class="category-sec">
                         <hr>
                         <div class="text-end">
                              <div class="form-group">
                                   <div>
                                   <span class="remove_category" value="appended">
                                        <i class="fa fa-times"></i>
                                   </span>
                                   </div>
                              </div>
                         </div>
                         <div class="row gy-12">
                              <div class="col-md-2">
                                   <div class="form-group">
                                        <label class="form-label" for="new_cat_img">Image</label>
                                        <input type="file" class="form-control" id="new_cat_img" name="new_cat_img[]">
                                   </div>
                              </div>
                              <div class="col-md-2">
                                   <div class="form-group">
                                   <label class="form-label" for="new_cat_heading">Heading</label>
                                   <input type="text" class="form-control" id="new_cat_heading" name="new_cat_heading[]" value="">
                                   </div>
                              </div>
                              <div class="col-md-2">
                                   <div class="form-group">
                                   <label class="form-label" for="new_category">Category</label>
                                   <div class="form-control-wrap">
                                        <select class="form-select js-select2" name="new_category[]" id="new_category">
                                             <option value="" selected disabled>Select</option>
                                             ${category_options_html}
                                        </select>
                                   </div>
                                   </div>
                              </div>
                              <div class="col-md-2">
                                   <div class="form-group">
                                   <label class="form-label" for="new_category_btn_text">Button Text</label>
                                   <input type="text" class="form-control" id="new_category_btn_text" name="new_category_btn_text[]" value="">
                                   </div>
                              </div>
                              <div class="col-md-2">
                                   <div class="form-group">
                                   <label class="form-label" for="new_category_btn_link">Button Link</label>
                                   <input type="text" class="form-control" id="new_category_btn_link" name="new_category_btn_link[]" value="">
                                   </div>
                              </div>
                              <div class="col-md-2">
                                   <div class="form-group">
                                   <label class="form-label" for="new_category_description">Description</label>
                                   <textarea class="form-control" id="new_category_description" name="new_category_description[]"></textarea>
                                   </div>
                              </div>
                         </div>
                    </div>`;

               $('#catg_sec').append(html);
               $('.js-select2').select2();
          });

          $('body').delegate('.remove_category', 'click', function(){
               var id = $(this).data('id');
               if($(this).attr('value') === 'appended'){
                    $(this).closest('.category-sec').remove();
                    return false;
               }

               let deleteIds = $('#category_sec').val();

               if(deleteIds) {
                    deleteIds += ',' + id;
               }else{
                    deleteIds = id;
               }
               $('#category_sec').val(deleteIds);

               $('.category-sec' + id).find('input, select, textarea').prop('disabled', true);


               $('.category-sec'+id).hide();
          });
     })
</script>


@endsection
