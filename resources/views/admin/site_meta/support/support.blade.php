@extends('admin_layout.master')
@section('content')

<div class="nk-content">

     <div class="nk-block-head">
          <div class="nk-block-head-content">
              <h4 class="nk-block-title">Help Center Page</h4>
          </div>
      </div>

     <div class="container-fluid">
          <div class="row">
               <div class="col-md-8">
                    <div class="card card-bordered card-preview">
                         <div class="card-inner">

          <form action="{{ url('/admin-dashboard/help-center-proccess') }}" method="POST" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="removefaq" id="removefaq" value="">
               <input type="hidden" id="removehelp" name="removehelp" value="">
               <input type="hidden" id="helpimageId" name="helpimageId" value="">
               <input type="hidden" id="bg_image_id" name="bg_image_id" value="">
               <input type="hidden" id="baner_image_id" name="baner_image_id" value="">
               <input type="hidden" id="btom_banner_id" name="btom_banner_id" value="">
               <div class="row main_section">
                    <div class="col-md-12 left-content">
                         <div class="col-md-12 pb-2">
                              <div class="form-group">
                                   <label class="form-label" for="title"><b><h5>Page Title</b></h5></label>
                                   <input class="form-control form-control-lg" type="text" id="title"  name="title" value="{{ $data['title'] ?? '' }}">
                              </div>
                         </div>
                         <hr>
                         <h5>Banner Section</h5>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="background_image">Background Image</label>
                                   <input type="file" class="form-control" id="background_image" name="background_image">
                              </div>
                              @if(isset($data['background_image']) && $data['background_image'] != null)
                              <div class="bg_image_div" id="bg_image{{ $data['background_image_id'] ?? '' }}">
                                   <div class="form-group">
                                        <span class="col-md-9 offset-md-3 remove_background_image" data-id="{{ $data['background_image_id'] ?? '' }}">
                                             <i class="fa fa-times"></i>
                                        </span>
                                   </div>
                                   <div class="form-group">
                                        <img src="{{ asset('storage/'.$data['background_image']) }}" alt="background_img" height="160px" width="160px">
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
                                   <label class="form-label" for="banner_placeholder">Banner Placeholder</label>
                                   <input type="text" class="form-control" id="banner_placeholder" name="banner_placeholder" value="{{ $data['banner_placeholder'] ?? '' }}">
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="banner_image">Banner Image</label>
                                   <input type="file" class="form-control" id="banner_image" name="banner_image">
                              </div>
                              @if(isset($data['banner_image']) && $data['banner_image'] != null)
                              <div class="second_banner_div" id="second_banner_div{{ $data['banner_image_id'] ?? '' }}">
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
                         <h5>Help Section</h5>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="main_title">Main Title</label>
                                   <input type="text" class="form-control" id="main_title" name="main_title" value="{{ $data['main_title'] ?? '' }}">
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="sub_title">Sub Title</label>
                                   <textarea class="form-control" id="sub_title" name="sub_title">{{ $data['sub_title'] ?? '' }}</textarea>
                              </div>
                         </div>
                         @if(isset($help_you) && $help_you != null)
                         @foreach($help_you as $help)
                         <?php
                              $path = getStorageFilepath($help->media->file_path);
                         ?>
                         <div class="help-append-sec{{ $help->id ?? '' }}">
                              <hr>
                              <div class="row gy-12">
                                   <div class="text-end">
                                        <div class="form-group">
                                             <div><span class="remove-help-sec" data-id="{{ $help->id ?? '' }}"><i class="fa fa-times"></i></span></div>
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <button type="button" class="btn-sm btn btn-primary" data-id="{{ $help->id ?? '' }}">Add New</button>
                                             <input type="file" name="help_up_img" class="help_img" data-id="{{ $help->id ?? '' }}" id="help_up_img{{ $help->id ?? '' }}" style="display:none;">
                                        </div>
                                        <div class="help_img_div" id="help_img_div{{ $help->id ?? '' }}">
                                             <div class="form-group">
                                                  <span class="col-md-7 offset-md-5 remove_help_img" data-id="{{ $help->id ?? '' }}">
                                                       <i class="fa fa-times"></i>
                                                  </span>
                                             </div>
                                             <div class="form-group">
                                                  <img src="{{ asset('storage/'.$path ?? '' ) }}" alt="Category image">
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="heading">Heading</label>
                                             <input class="form-control" name="heading[{{ $help->id ?? '' }}]" id="heading" value="{{ $help->heading ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="description">Description</label>
                                             <textarea class="form-control" name="description[{{ $help->id ?? '' }}]" id="description">{{ $help->description ?? '' }}</textarea>
                                        </div>
                                   </div>
                              </div>
                         </div>
                         @endforeach
                         @endif
                         <div id="help-container"></div>
                         <br>
                         <div class="text-end">
                              <div class="form-group">
                                   <button type="button" class="btn btn-sm btn-primary" id="add-help-sec">Add</button>
                              </div>
                         </div>
                         <hr>
                         <h5>FAQ Section</h5>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="faq_heading">FAQ Heading</label>
                                   <input type="text" class="form-control" id="faq_heading" name="faq_heading" value="{{ $data['faq_heading'] ?? '' }}">
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="faq_description">FAQ Description</label>
                                   <textarea class="form-control" id="faq_description" name="faq_description">{{ $data['faq_description'] ?? '' }}</textarea>
                              </div>
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="">FAQ</label>
                              </div>
                         </div>
                         @if(isset($faqs) && $faqs != null)
                         @foreach($faqs as $faq)
                         <div class="faq-append-sec{{ $faq->id ?? '' }}">
                              <hr>
                              <div class="row gy-12">
                                   <div class="text-end">
                                        <div class="form-group">
                                             <div><span class="remove-faq-sec" data-id="{{ $faq->id ?? '' }}"><i class="fa fa-times"></i></span></div>
                                        </div>
                                   </div>
                                   <div class="col-md-6">
                                        <div class="form-group">
                                             <label class="form-label" for="question">Question</label>
                                             <input class="form-control" name="question[{{ $faq->id ?? '' }}]" id="question" value="{{ $faq->question ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-6">
                                        <div class="form-group">
                                             <label class="form-label" for="answer">Answer</label>
                                             <textarea class="form-control answer_editor" name="answer[{{ $faq->id ?? '' }}]" id="answer">{{ $faq->answer ?? '' }}</textarea>
                                        </div>
                                   </div>
                              </div>
                         </div>
                         @endforeach
                         @endif
                         <div id="faq-container"></div>
                         <br>
                         <div class="text-end">
                              <div class="form-group">
                                   <button type="button" class="btn btn-sm btn-primary" id="add-faq-sec">Add</button>
                              </div>
                         </div>
                         <hr>
                         <h5>Bottom Banner Section</h5>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="bottom_banner_image">Banner Image</label>
                                   <input type="file" class="form-control" id="bottom_banner_image" name="bottom_banner_image">
                              </div>
                              @if(isset($data['bottom_banner_image']) && $data['bottom_banner_image'] != null)
                              <div class="bottom_bnnr_div" id="bottom_bnnr{{ $data['bottom_image_id'] ?? '' }}">
                                   <div class="form-group">
                                        <span class="col-md-10 offset-md-2 remove_bottom_banner" data-id="{{ $data['bottom_image_id'] ?? '' }}">
                                             <i class="fa fa-times"></i>
                                        </span>
                                   </div>
                                   <div class="form-group">
                                        <img src="{{ asset('storage/'.$data['bottom_banner_image']) }}" alt="banner_img" height="140px" width="160px">
                                   </div>
                              </div>
                              @endif
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="banner_heading">Banner Heading</label>
                                   <input type="text" class="form-control" id="banner_heading" name="banner_heading" value="{{ $data['banner_heading'] ?? '' }}">
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
                                   <label class="form-label" for="button_text">Button Text</label>
                                   <input type="text" class="form-control" id="button_text" name="button_text" value="{{ $data['button_text'] ?? '' }}">
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
                                                  {{-- <a href="{{ url('/centro-de-ayuda') }}" class="view_page" target="_blank">View Page</a> --}}
                                                  <a href="{{ url('/help') }}" class="view_page" target="_blank">View Page</a>
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
     $(document).ready(function(){

     $('.update_help_img').click(function(){
          var id = $(this).data('id');
          $('#help_up_img' + id).trigger('click');
     });

     $('.help_img').change(function() {
          var id = $(this).data('id');
          var file = this.files[0];
          var formData = new FormData();
          formData.append('help_image', file);
          formData.append('_token', "{{ csrf_token() }}");
          formData.append('image_id', id);

          $.ajax({
               url: "{{ url('/update/help/image') }}",
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

     $('.remove_help_img').click(function(){
          id = $(this).data('id');
          let removeIds = $('#helpimageId').val();

          if(removeIds) {
               removeIds += ',' + id;
          }else{
               removeIds = id;
          }
          $('#helpimageId').val(removeIds);

          $('#help_img_div'+id).hide();
     })


     $('.remove_background_image').click(function(){
          id = $(this).data('id');
          // $('#bg_image_id').val(id);
          $('#bg_image'+id).hide();
     });

     $('.remove_banner_image').click(function(){
          id = $(this).data('id');
          // $('#baner_image_id').val(id);
          $('#second_banner_div'+id).hide();
     });

     $('.remove_bottom_banner').click(function(){
          id = $(this).data('id');
          // $('#btom_banner_id').val(id);
          $('#bottom_bnnr'+id).hide();
     });
})
</script>

<script>
function initializeCKEditor(element) {
     ClassicEditor.create(element).catch(error => {
          console.error(error);
     });
}

$(document).ready(function() {
     $('.answer_editor').each(function() {
          initializeCKEditor(this);
     });

     $('#add-faq-sec').click(function() {
          let faqHtml = `
          <div class="faq-append-sec">
          <hr>
          <div class="row gy-12">
               <div class="text-end">
                    <div class="form-group">
                         <div><span class="remove-faq-sec" value="appended"><i class="fa fa-times"></i></span></div>
                    </div>
               </div>
               <div class="col-md-6">
                    <div class="form-group">
                         <label class="form-label" for="new_question">Question</label>
                         <input class="form-control" name="new_question[]" id="new_question" value="">
                    </div>
               </div>
               <div class="col-md-6">
                    <div class="form-group">
                         <label class="form-label" for="new_answer">Answer</label>
                         <textarea class="form-control answer_editor" name="new_answer[]" id="new_answer"></textarea>
                    </div>
               </div>
          </div>
          </div>`;

          $('#faq-container').append(faqHtml);

          const newTextarea = $('.answer_editor').last()[0];
          if (newTextarea && !$(newTextarea).data('ckeditor-initialized')) {
               initializeCKEditor(newTextarea);
               $(newTextarea).data('ckeditor-initialized', true);
          }

     });

     $('body').delegate('.remove-faq-sec', 'click', function(){
          var id = $(this).data('id');
          if($(this).attr('value') === 'appended'){
               $(this).closest('.faq-append-sec').remove();
               return false;
          }

          let deleteIds = $('#removefaq').val();

          if(deleteIds) {
               deleteIds += ',' + id;
          }else{
               deleteIds = id;
          }
          $('#removefaq').val(deleteIds);

          $('.faq-append-sec'+id).hide();
     });

     $('#add-help-sec').click(function() {
          let faqHtml = `<div class="help-append-sec">
          <hr>
               <div class="row gy-12">
                    <div class="text-end">
                         <div class="form-group">
                              <div><span class="remove-help-sec" value="appended"><i class="fa fa-times"></i></span></div>
                         </div>
                    </div>
                    <div class="col-md-2">
                         <div class="form-group">
                              <label class="form-label" for="new_icon">Icon</label>
                              <input type="file" class="form-control" id="new_icon" name="new_icon[]">
                         </div>
                    </div>
                    <div class="col-md-5">
                         <div class="form-group">
                              <label class="form-label" for="new_heading">Heading</label>
                              <input class="form-control" name="new_heading[]" id="new_heading" value="">
                         </div>
                    </div>
                    <div class="col-md-5">
                         <div class="form-group">
                              <label class="form-label" for="new_description">Description</label>
                              <textarea class="form-control" name="new_description[]" id="new_description"></textarea>
                         </div>
                    </div>
               </div>
          </div>`;

          $('#help-container').append(faqHtml);
     });

     $('body').delegate('.remove-help-sec', 'click', function(){
          var id = $(this).data('id');
          if($(this).attr('value') === 'appended'){
               $(this).closest('.help-append-sec').remove();
               return false;
          }

          let deleteIds = $('#removehelp').val();

          if(deleteIds) {
               deleteIds += ',' + id;
          }else{
               deleteIds = id;
          }
          $('#removehelp').val(deleteIds);

          $('.help-append-sec'+id).hide();
     });
});

</script>

@endsection
