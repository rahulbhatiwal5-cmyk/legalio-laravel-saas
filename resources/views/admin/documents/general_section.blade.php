@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="container-fluid">
          <form action="{{ url('/admin-dashboard/add/general-section') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" id="removelegal" name="removelegal" value="">
               <div class="col-md-12 pb-4">
                    <h2>General Section</h2>
               </div>
               <div class="row">
                    <div class="col-md-8">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <h3>Agreement Steps</h3>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="agreement_headline">Headline</label>
                                             <input type="text" class="form-control" id="agreement_headline" name="agreement_headline" value="{{ $data['agreement_headline'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="agreement_short_description">Description</label>
                                             <textarea class="form-control" id="agreement_short_description" name="agreement_short_description">{{ $data['agreement_short_description'] ?? '' }}</textarea>
                                        </div>
                                   </div>
                                   <hr>
                                   @if(isset($agreements) && $agreements != null)
                                   @foreach($agreements as $agrmnt)
                                   <?php
                                        $path = getStorageFilepath($agrmnt->media->file_path); 
                                   ?>
                                   <div class="faq-append-sec{{ $agrmnt->id ?? '' }}">
                                        <div class="row gy-12">
                                             <div class="col-md-2">
                                                  <div class="form-group">
                                                       <button class="ad_btn btn-sm update_agreement_img btn-primary" type="button" data-id="{{ $agrmnt->id ?? '' }}">Add New</button>
                                                       <input type="file" name="agreement_up_img" class="update_img" data-id="{{ $agrmnt->id ?? '' }}" id="agreement_up_img{{ $agrmnt->id ?? '' }}" style="display:none;">
                                                  </div>
                                                  <div class="img_div" id="img_div{{ $agrmnt->id ?? '' }}">
                                                       <div class="form-group">
                                                            <img src="{{ asset('storage/'.$path ?? '' ) }}" alt="{{ asset('storage/'.$path ?? '' ) }}">
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="col-md-5">
                                                  <div class="form-group">
                                                       <label class="form-label" for="new_agreement_heading">Heading</label>
                                                       <input type="text" class="form-control" id="new_agreement_heading" name="new_agreement_heading[{{ $agrmnt->id ?? '' }}]" value="{{ $agrmnt->heading ?? '' }}">
                                                  </div>
                                             </div>
                                             <div class="col-md-5">
                                                  <div class="form-group">
                                                       <label class="form-label" for="new_agreement_description">Description</label>
                                                       <textarea class="form-control" id="new_agreement_description" name="new_agreement_description[{{ $agrmnt->id ?? '' }}]">{{ $agrmnt->description ?? '' }}</textarea>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   @endforeach
                                   @else
                                   @php $num=4; @endphp
                                   @for($i=1; $i<=$num; $i++)
                                   <div class="faq-append-sec{{ $i ?? '' }}">
                                        <div class="row gy-12">
                                             <div class="col-md-2">
                                                  <div class="form-group">
                                                       <label class="form-label" for="agreement_image">Image</label>
                                                       <input type="file" class="form-control" name="agreement_image[]">
                                                  </div>
                                             </div>
                                             <div class="col-md-5">
                                                  <div class="form-group">
                                                       <label class="form-label" for="agreement_heading">Heading</label>
                                                       <input type="text" class="form-control" id="agreement_heading" name="agreement_heading[]" value="">
                                                  </div>
                                             </div>
                                             <div class="col-md-5">
                                                  <div class="form-group">
                                                       <label class="form-label" for="agreement_description">Description</label>
                                                       <textarea class="form-control" id="agreement_description" name="agreement_description[]"></textarea>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   @endfor
                                   @endif
                                   <hr>
                                   <h3 class="mt-2">Guide Section</h3>
                                   <hr>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="guide_heading">Guide Section Main Heading</label>
                                             <input type="text" class="form-control form-control" id="guide_heading" name="guide_heading" value="{{ $data['guide_heading'] ?? '' }}">
                                        </div>
                                   </div>
                                   <br>
                                   <div class="col-md-12 mt-2">
                                        <h6> Steps</h6>
                                   </div>
                                   @if(isset($guides) && $guides != null)
                                        @foreach($guides as $guide)
                                             <div class="guide-append-sec{{ $guide->id ?? '' }}">
                                                  <hr>
                                                  <div class="row gy-12">
                                                       <div class="col-md-6">
                                                            <div class="form-group">
                                                                 <label class="form-label" for="new_step_title">Step Title</label>
                                                                 <input type="text" class="form-control form-control" id="new_step_title" name="new_step_title[{{ $guide->id ?? '' }}]" value="{{ $guide->heading ?? '' }}">
                                                            </div>
                                                       </div>
                                                       <div class="col-md-6">
                                                            <div class="form-group">
                                                                 <label class="form-label" for="new_step_description">Step Description</label>
                                                                 <textarea class="form-control" id="new_step_description" name="new_step_description[{{ $guide->id ?? '' }}]">{{ $guide->description ?? '' }}</textarea>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        @endforeach
                                   @else
                                        @php $count=2; @endphp
                                        @for($i=1; $i<=$count; $i++)
                                        <div class="guide-append-sec{{ $i ?? '' }}">
                                             <hr>
                                             <div class="row gy-12">
                                                  <div class="col-md-6">
                                                       <div class="form-group">
                                                            <label class="form-label" for="step_title">Step Title</label>
                                                            <input type="text" class="form-control form-control" id="step_title" name="step_title[]" value="">
                                                       </div>
                                                  </div>
                                                  <div class="col-md-6">
                                                       <div class="form-group">
                                                            <label class="form-label" for="step_description">Step Description</label>
                                                            <textarea class="form-control" id="step_description" name="step_description[]"></textarea>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        @endfor
                                   @endif
                                   <hr>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="document_faq_heading">FAQ Heading</label>
                                             <input type="text" class="form-control form-control" id="document_faq_heading" name="document_faq_heading" value="{{ $data['document_faq_heading'] ?? '' }}">
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="guide_button">Guide Button</label>
                                             <input type="text" class="form-control form-control" id="guide_button" name="guide_button" value="{{ $data['guide_button'] ?? '' }}">
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="rating_text">Rating Text</label>
                                             <input type="text" class="form-control form-control" id="rating_text" name="rating_text" value="{{ $data['rating_text'] ?? '' }}">
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="valid_in">Valid in</label>
                                             <input type="text" class="form-control form-control" id="valid_in" name="valid_in" value="{{ $data['valid_in'] ?? '' }}">
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="applicable_in">Applicable in</label>
                                             <input type="text" class="form-control form-control" id="applicable_in" name="applicable_in" value="{{ $data['applicable_in'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="ultima_revision_text">Última revisión Text</label>
                                             <input type="text" class="form-control form-control" id="ultima_revision_text" name="ultima_revision_text" value="{{ $data['ultima_revision_text'] ?? '' }}">
                                        </div>
                                   </div> 
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="formatos_disponibles_text">Formatos disponibles Text</label>
                                             <input type="text" class="form-control form-control" id="formatos_disponibles_text" name="formatos_disponibles_text" value="{{ $data['formatos_disponibles_text'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="formatos_disponibles_data_text">Formatos Disponibles Data Text</label>
                                             <input type="text" class="form-control form-control" id="formatos_disponibles_data_text" name="formatos_disponibles_data_text" value="{{ $data['formatos_disponibles_data_text'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="aplicable_en_text">Aplicable en Text</label>
                                             <input type="text" class="form-control form-control" id="aplicable_en_text" name="aplicable_en_text" value="{{ $data['aplicable_en_text'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="descargas_text">Descargas Text</label>
                                             <input type="text" class="form-control form-control" id="descargas_text" name="descargas_text" value="{{ $data['descargas_text'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="descargas_data_text">Descargas Data Text</label>
                                             <input type="text" class="form-control form-control" id="descargas_data_text" name="descargas_data_text" value="{{ $data['descargas_data_text'] ?? '' }}">
                                        </div>
                                   </div>
                                   <hr>
                                   <h3 class="mt-4">Review Modal</h3>
                                   <hr>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="open_review_modal_button_text">Review Modal Button Text</label>
                                             <input type="text" class="form-control" id="open_review_modal_button_text" name="open_review_modal_button_text" value="{{ $data['open_review_modal_button_text'] ?? '' }}">

                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="review_modal_publicamente_text">Review Modal Se mostrará públicamente Text</label>
                                             <input type="text" class="form-control" id="review_modal_publicamente_text" name="review_modal_publicamente_text" value="{{ $data['review_modal_publicamente_text'] ?? '' }}">

                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="review_modal_nombre_publico_placeholder">Review Modal Nombre público Placeholder Text</label>
                                             <input type="text" class="form-control" id="review_modal_nombre_publico_placeholder" name="review_modal_nombre_publico_placeholder" value="{{ $data['review_modal_nombre_publico_placeholder'] ?? '' }}">

                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="review_modal_description_placeholder">Review Modal Description Placeholder Text</label>
                                             <input type="text" class="form-control" id="review_modal_description_placeholder" name="review_modal_description_placeholder" value="{{ $data['review_modal_description_placeholder'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="review_modal_not_login_message_text">Review Modal Not Login Message</label>
                                             <input type="text" class="form-control" id="review_modal_not_login_message_text" name="review_modal_not_login_message_text" value="{{ $data['review_modal_not_login_message_text'] ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="review_modal_hace_text">Review Modal Hace Text</label>
                                             <input type="text" class="form-control" id="review_modal_hace_text" name="review_modal_hace_text" value="{{ $data['review_modal_hace_text'] ?? '' }}">
                                        </div>
                                   </div>
                                   <hr>
                                   <h3 class="mt-4">Related Document Section</h3>
                                   <hr>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="related_heading">Related Document Heading</label>
                                             <input type="text" class="form-control" id="related_heading" name="related_heading" value="{{ $data['related_heading'] ?? '' }}">
                                             @error('related_heading')
                                                  <span class="text-danger">{{ $message }}</span>
                                             @enderror
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="related_description">Related Document Short Description</label>
                                             <textarea class="form-control" id="related_description" name="related_description">{{ $data['related_description'] ?? '' }}</textarea>
                                             @error('related_description')
                                                  <span class="text-danger">{{ $message }}</span>
                                             @enderror
                                        </div>
                                   </div>
                                   <hr>
                                   <h3 class="mt-4">Legal Document Section </h3>
                                   <hr>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="legal_section_heading">Title</label>
                                             <input type="text" class="form-control" id="legal_section_heading" name="legal_section_heading" value="{{ $data['legal_section_heading'] ?? '' }}">
                                             @error('legal_section_heading')
                                                  <span class="text-danger">{{ $message }}</span>
                                             @enderror
                                        </div>
                                   </div>
                                   @if(isset($legal_sections) && $legal_sections != null)
                                   @foreach($legal_sections as $key => $value)
                                   <?php
                                        $image_path = getStorageFilepath($value->media->file_path); 
                                   ?>
                                   <div class="legal-append-sec{{ $value->id ?? '' }}">
                                        <div class="row gy-12">
                                             <div class="col-md-6">
                                                  <div class="form-group">
                                                       <button class="ad_btn btn-sm update_legal_img btn-primary" type="button" data-id="{{ $value->id ?? '' }}">Add New</button>
                                                       <input type="file" name="addnew_legal_img" class="legal_up_img" data-id="{{ $value->id ?? '' }}" id="addnew_legal_img{{ $value->id ?? '' }}" style="display:none;">
                                                  </div>
                                                  <div class="legal_img_div" id="legal_img_div">
                                                       <div class="form-group">
                                                            <img src="{{ asset('storage/'.$image_path ?? '' ) }}" alt="{{ asset('storage/'.$image_path ?? '' ) }}">
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="col-md-6">
                                                  <div class="form-group">
                                                       <label class="form-label" for="legal_heading{{ $value->id ?? '' }}">Heading</label>
                                                       <input type="text" class="form-control" id="legal_heading{{ $value->id ?? '' }}" name="legal_heading[{{ $value->id ?? '' }}]" value="{{ $value->heading ?? '' }}">
                                                  </div>
                                             </div>
                                        </div>
                                        <div class="row gy-12">
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="legal_description{{ $value->id ?? '' }}">Description</label>
                                                       <textarea class="form-control" id="legal_description{{ $value->id ?? '' }}" name="legal_description[{{ $value->id ?? '' }}]">{{ $value->description ?? '' }}</textarea>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   @endforeach
                                   @else
                                   <div class="legal-append-sec">
                                        <div class="row gy-12">
                                             <div class="col-md-2">
                                                  <div class="form-group">
                                                       <label class="form-label" for="new_legal_img">Image</label>
                                                       <input type="file" class="form-control" name="new_legal_img[]" id="new_legal_img">
                                                  </div>
                                             </div>
                                             <div class="col-md-5">
                                                  <div class="form-group">
                                                       <label class="form-label" for="new_legal_heading">Heading</label>
                                                       <input type="text" class="form-control" id="new_legal_heading" name="new_legal_heading[]" value="">
                                                  </div>
                                             </div>
                                             <div class="col-md-5">
                                                  <div class="form-group">
                                                       <label class="form-label" for="new_legal_description">Description</label>
                                                       <textarea class="form-control" id="new_legal_description" name="new_legal_description[]"></textarea>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   @endif
                                   <div id="new-legal-section-container"></div>
                                   <br>
                                   <div class="text-end">
                                        <div class="form-group">
                                             <button type="button" class="btn btn-sm btn-primary" id="add-legal-sec" onclick="addMoreSection()">Add</button>
                                        </div>
                                   </div>
                                   <!-- <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="legal_section_description">Description</label>
                                             <textarea class="form-control" id="legal_section_description" name="legal_section_description">{{ $data['legal_section_description'] ?? '' }}</textarea>
                                             @error('legal_section_description')
                                                  <span class="text-danger">{{ $message }}</span>
                                             @enderror
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="legal_section_image">Image</label>
                                             <input type="file" class="form-control" name="legal_section_image" id="legal_section_image">
                                             @error('legal_section_image')
                                                  <span class="text-danger">{{ $message }}</span>
                                             @enderror
                                        </div>
                                   </div> -->
                                   <h3 class="mt-4">Buttons on Frontpage</h3>
                                   <hr>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="detail_page_letter_now_btn">Checkout Button</label>
                                             <input type="text" class="form-control" id="detail_page_letter_now_btn" name="detail_page_letter_now_btn" value="{{ $data['detail_page_letter_now_btn'] ?? '' }}">
                                             @error('detail_page_letter_now_btn')
                                                  <span class="text-danger">{{ $message }}</span>
                                             @enderror
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="detail_page_job_recommend_btn">Sub button</label>
                                             <input type="text" class="form-control" id="detail_page_job_recommend_btn" name="detail_page_job_recommend_btn" value="{{ $data['detail_page_job_recommend_btn'] ?? '' }}">
                                             @error('detail_page_job_recommend_btn')
                                                  <span class="text-danger">{{ $message }}</span>
                                             @enderror
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <div class="col-md-4">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="d-flex justify-content-end">
                                        <div class="nk-block-head-content">
                                             <div class="up-btn mbsc-form-group">
                                                  <button class="btn btn-sm btn-primary" type="submit">Update</button>
                                             </div>
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
     // Update Agreement Image //
     $('.update_agreement_img').click(function(){
          var id = $(this).data('id');
          $('#agreement_up_img' + id).trigger('click');
     });

     $('.update_img').change(function() {
          var id = $(this).data('id');
          var file = this.files[0];
          var formData = new FormData();
          formData.append('image', file);
          formData.append('_token', "{{ csrf_token() }}");
          formData.append('id', id);
          formData.append('type', 'agreement');

          $.ajax({
               url: "{{ url('/update/agreement/image') }}",
               type: 'POST',
               data: formData,
               processData: false,
               contentType: false,
               dataType: "json",
               success: function(response){
                    console.log(response);
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


     ClassicEditor
          .create(document.querySelector('#legal_section_description'), {
               toolbar: {
                    items: [
                         'heading',
                         'bold',
                         'bulletedList',
                         'numberedList',
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
               console.error('error:', error);
          });


     function addMoreSection(){
          let html = `<div class="legal-append-sec">
                         <div class="row gy-12">
                              <div class="text-end">
                                   <div class="form-group">
                                        <div><span class="remove-legal-sec" value="appended"><i class="fa fa-times"></i></span></div>
                                   </div>
                              </div>
                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="form-label" for="new_legal_img">Image</label>
                                        <input type="file" name="new_legal_img[]" class="form-control" id="new_legal_img">
                                   </div>
                              </div>
                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="form-label" for="new_legal_heading">Heading</label>
                                        <input type="text" class="form-control" id="new_legal_heading" name="new_legal_heading[]" value="">
                                   </div>
                              </div>
                         </div>
                         <div class="row gy-12">
                              <div class="col-md-12">
                                   <div class="form-group">
                                        <label class="form-label" for="new_legal_description">Description</label>
                                        <textarea class="form-control" id="new_legal_description" name="new_legal_description[]"></textarea>
                                   </div>
                              </div>
                         </div>
                    </div>`;
          
               $('#new-legal-section-container').append(html);
     }

     $('body').delegate('.remove-legal-sec', 'click', function(){
          var id = $(this).data('id');
          if($(this).attr('value') === 'appended'){
               $(this).closest('.legal-append-sec').remove();
               return false;
          }

          let deleteIds = $('#removelegal').val();

          if(deleteIds){
               deleteIds += ',' + id;
          }else{
               deleteIds = id;
          }

          $('#removelegal').val(deleteIds);

          $('.legal-append-sec'+id).hide();
     })
   

     $('.update_legal_img').click(function(){
          var id = $(this).data('id');
          $('#addnew_legal_img' + id).trigger('click');
     });

     $('.legal_up_img').change(function() {
          var id = $(this).data('id');
          var file = this.files[0];
          var formData = new FormData();
          formData.append('image', file);
          formData.append('_token', "{{ csrf_token() }}");
          formData.append('id', id);
          formData.append('type', 'legal');

          $.ajax({
               url: "{{ url('/update/agreement/image') }}",
               type: 'POST',
               data: formData,
               processData: false,
               contentType: false,
               dataType: "json",
               success: function(response){
                    console.log(response);
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
     
</script>

@endsection
