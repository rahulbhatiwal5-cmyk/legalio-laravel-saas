@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="nk-block-head">
          <div class="nk-block-head-content">
               <h4 class="nk-block-title">Price Page</h4>
          </div>
     </div>
     <div class="container-fluid">
          <form action="{{ route('admin.dashboard.add_price') }}" method="POST" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="delete_content_ids" id="delete_content_ids" value="">
               <input type="hidden" name="bg_img_id" id="bg_img_id" value="">
               <input type="hidden" name="baner_image_id" id="baner_image_id" value="">
               <input type="hidden" name="faq_id" id="faq_id" value="">
               <div class="row main_section">
                    <div class="col-md-8 left-content">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="col-md-12 pb-2">
                                        <div class="form-group">
                                             <label class="form-label" for="title"><b><h5>Page Title</b></h5></label>
                                             <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ $data['title'] ?? '' }}">
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
                                             <input type="file" class="form-control" id="banner_image" name="banner_image">
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
                                   <h5>Document Description Price</h5>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="document_heading">Document heading</label>
                                             <input type="text" class="form-control" id="document_heading" name="document_heading" value="{{ $data['document_heading'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="description_heading">Description heading</label>
                                             <input type="text" class="form-control" id="description_heading" name="description_heading" value="{{ $data['description_heading'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="price_heading">Price heading</label>
                                             <input type="text" class="form-control" id="price_heading" name="price_heading" value="{{ $data['price_heading'] ?? '' }}">
                                        </div>
                                   </div>
                                   <br>
                                   <h6>Price Section</h6>
                                   <div class="price-append-sec">
                                        <hr>
                                        <div class="row gy-12">
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="subscription_title">Subscription Title</label>
                                                       <input type="text" class="form-control" id="subscription_title" name="subscription_title" value="{{ $data['subscription_title'] ?? '' }}">
                                                  </div>    
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="subscription_heading">Subscription Heading</label>
                                                       <input type="text" class="form-control" id="subscription_heading" name="subscription_heading" value="{{ $data['subscription_heading'] ?? '' }}">
                                                  </div>    
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="recommended_text">Recommended Text</label>
                                                       <input type="text" class="form-control" id="recommended_text" name="recommended_text" value="{{ $data['recommended_text'] ?? '' }}">
                                                  </div>    
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="subscription_description">Subscription Description</label>
                                                       <input type="text" class="form-control" id="subscription_description" name="subscription_description" value="{{ $data['subscription_description'] ?? '' }}">
                                                  </div>
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="monthly_text">Monthly Text</label>
                                                       <input type="text" class="form-control" id="monthly_text" name="monthly_text" value="{{ $data['monthly_text'] ?? '' }}">
                                                  </div>    
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="yearly_text">Yearly Text</label>
                                                       <input type="text" class="form-control" id="yearly_text" name="yearly_text" value="{{ $data['yearly_text'] ?? '' }}">
                                                  </div>    
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="ahorra_text">Ahorra Text</label>
                                                       <input type="text" class="form-control" id="ahorra_text" name="ahorra_text" value="{{ $data['ahorra_text'] ?? '' }}">
                                                  </div>    
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="subscription_note">Subscription Note</label>
                                                       <input type="text" class="form-control" id="subscription_note" name="subscription_note" value="{{ $data['subscription_note'] ?? '' }}">
                                                  </div>
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="per_month_text">Per Month Text</label>
                                                       <input type="text" class="form-control" id="per_month_text" name="per_month_text" value="{{ $data['per_month_text'] ?? '' }}">
                                                  </div>    
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="per_year_text">Per Year Text</label>
                                                       <input type="text" class="form-control" id="per_year_text" name="per_year_text" value="{{ $data['per_year_text'] ?? '' }}">
                                                  </div>    
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="subscription_btn_text">Subscription Button Text</label>
                                                       <input type="text" class="form-control" id="subscription_btn_text" name="subscription_btn_text" value="{{ $data['subscription_btn_text'] ?? '' }}">
                                                  </div>    
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="one_time_heading">One Time Heading</label>
                                                       <input type="text" class="form-control" id="one_time_heading" name="one_time_heading" value="{{ $data['one_time_heading'] ?? '' }}">
                                                  </div>
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="one_time_description">One Time Description</label>
                                                       <input type="text" class="form-control" id="one_time_description" name="one_time_description" value="{{ $data['one_time_description'] ?? '' }}">
                                                  </div>    
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="one_time_price_note">One Time Price Note</label>
                                                       <input type="text" class="form-control" id="one_time_price_note" name="one_time_price_note" value="{{ $data['one_time_price_note'] ?? '' }}">
                                                  </div>
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="one_time_btn_text">One Time Button Text</label>
                                                       <input type="text" class="form-control" id="one_time_btn_text" name="one_time_btn_text" value="{{ $data['one_time_btn_text'] ?? '' }}">
                                                  </div>
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="button_text">Button text</label>
                                                       <input type="text" class="form-control" id="button_text" name="button_text" value="{{ $data['btn_text'] ?? '' }}">
                                                  </div>
                                             </div>
                                        </div>

                                   </div>

                                   <br>
                                   <h6>FAQ Section</h6>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="faq_heading">Heading</label>
                                             <input class="form-control" name="faq_heading" id="faq_heading" value="{{ $data['faq_heading'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="faq_description">Description</label>
                                             <input class="form-control" name="faq_description" id="faq_description" value="{{ $data['faq_description'] ?? '' }}">
                                        </div>
                                   </div>
                                   @if(isset($price_faq) && count($price_faq) > 0)
                                        @foreach($price_faq as $faq)
                                             <div class="row gy-12 faq-append-sec{{ $faq->id ?? ''}}" id="faq-append-sec{{ $faq->id ?? ''}}">
                                                  @if(!$loop->first)
                                                  <div class="text-end">
                                                       <div class="form-group">
                                                            <div><span class="remove-faq-sec" onclick="removeFaq(this)" data-id="{{ $faq->id ?? '' }}"><i class="fa fa-times"></i></span></div>
                                                       </div>
                                                  </div>
                                                  @endif
                                                  <div class="col-md-6">
                                                       <div class="form-group">
                                                            <label class="form-label" for="question">Question</label>
                                                            <input class="form-control" name="question[{{ $faq->id ?? '' }}]" id="question" value="{{ $faq->question }}">
                                                       </div>
                                                  </div>
                                                  <div class="col-md-6">
                                                       <div class="form-group">
                                                            <label class="form-label" for="answer">Answer</label>
                                                            <textarea class="form-control answer_editor" name="answer[{{ $faq->id ?? '' }}]" id="answer">{{ $faq->answer }}</textarea>
                                                       </div>
                                                  </div>
                                             </div>
                                        @endforeach
                                   @else               
                                        <div class="row gy-12">
                                             <div class="col-md-6">
                                                  <div class="form-group">
                                                       <label class="form-label" for="first_question">Question</label>
                                                       <input class="form-control" name="first_question" id="first_question" value="">
                                                  </div>
                                             </div>
                                             <div class="col-md-6">
                                                  <div class="form-group">
                                                       <label class="form-label" for="first_answer">Answer</label>
                                                       <textarea class="form-control answer_editor" name="first_answer" id="first_answer"></textarea>
                                                  </div>
                                             </div>
                                        </div>
                                   @endif
                                   <div id="new-faq-container"></div>
                                   <br>
                                   <div class="text-end">
                                        <div class="form-group">
                                             <button type="button" class="btn btn-sm btn-primary" id="add-faq-sec" onclick="addFaq()">Add</button>
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
                                                  {{-- <a href="{{ url('/precios') }}" class="view_page" target="_blank">View Page</a> --}}
                                                  <a href="{{ url('/pricing') }}" class="view_page" target="_blank">View Page</a>
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
          var documentData = {!! json_encode($document) !!};
          $('#add-price-sec').click(function(){
               var documentOptions = [];
               $.each(documentData, function(key, val){
                    var documentHtml = `<option value="${val.id}">${val.title}</option>`;
                    documentOptions.push(documentHtml);
               });
               var documentOptionsHtml = documentOptions.join('');

               var html = `<div class="price-append-sec">
                              <hr>
                              <div class="row gy-12">
                                   <div class="text-end"><span class="remove-price-sec" value="appended"><i class="fa fa-times"></i></span></div>
                                   <div class="col-md-3">
                                        <div class="form-group">
                                             <label class="form-label" for="new_document">Document</label>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2" name="new_document[]" id="new_document">
                                                       <option value="" selected disabled>Select</option>
                                                       ${documentOptionsHtml}
                                                  </select>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-3">
                                        <div class="form-group">
                                             <label class="form-label" for="new_button_text">Button text</label>
                                             <input type="text" class="form-control" id="new_button_text" name="new_button_text[]" value="">
                                        </div>
                                   </div>
                                   <div class="col-md-2">
                                        <div class="form-group">
                                             <label class="form-label" for="new_price">Price</label>
                                             <input type="text" class="form-control" id="new_price" name="new_price[]" value="">
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="new_description">Description</label>
                                             <textarea class="form-control" id="new_description" name="new_description[]"></textarea>
                                        </div>
                                   </div>
                              </div>
                         </div><br>`;

               $('#price-section').append(html);
          });


          $('body').delegate('.remove-price-sec', 'click', function () {
               var id = $(this).data('id');
               if($(this).attr('value') === 'appended'){
                    $(this).closest('.price-append-sec').remove();
                    return false;
               }

               let deleteIds = $('#delete_content_ids').val();
               if(deleteIds) {
                    deleteIds += ',' + id;
               }else {
                    deleteIds = id;
               }
               $('#delete_content_ids').val(deleteIds);

               $('.price-append-sec'+id).hide();
          });


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


     });
</script>

<script>
     function initializeCKEditor(element) {
          ClassicEditor
               .create(element, {
                    toolbar: [
                         'heading', '|',
                         'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                         'imageUpload', 'blockQuote', 'undo', 'redo'
                    ]
               })
          .catch(error => {
               console.error(error);
          });
     }


     $('.answer_editor').each(function() {
          initializeCKEditor(this);
     });

     function addFaq() {
          let faqHtml = `<div class="faq-append-sec">
                         <hr>
                         <div class="row gy-12">
                              <div class="text-end">
                                   <div class="form-group">
                                        <div><span class="remove-faq-sec" onclick="removeFaq(this)" value="appended"><i class="fa fa-times"></i></span></div>
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
          $('#new-faq-container').append(faqHtml);


          const newAnswerEditor = $('#new-faq-container').find('textarea.answer_editor').last()[0];
          if (newAnswerEditor) {
               ClassicEditor
               .create(newAnswerEditor, {
                    toolbar: {
                         items: [
                              'heading',   
                              'bold',      
                              'bulletedList',  
                              'numberedList'   
                         ]
                    },
                    heading: {
                         options: [
                              { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                              { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                              { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                         ]
                    },
                    removePlugins: [
                         'Table', 'MediaEmbed', 'BlockQuote',
                    ]
               })
               .catch(error => {
                    console.error(error);
               });
          }
     }

     function removeFaq(e){
          var value = $(e).attr('value');
          var id = $(e).attr('data-id');
          
          if(value == 'appended'){
               $(e).closest('.faq-append-sec').remove();
          }else{
               let deleteIds = $('#faq_id').val();
               if(deleteIds){
                    deleteIds += ',' + id;
               }else{
                    deleteIds = id;
               }

               $('#faq_id').val(deleteIds);
               $('#faq-append-sec' + id).hide();
          }

     }

</script>
@endsection
