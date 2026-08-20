@extends('admin_layout.master')
@section('content')

@php 
     use Carbon\Carbon;
@endphp
<div class="nk-content Carta_Volunt">
     <div class="container-fluid">
          <div class="nk-block-head doc-outer-div">
               <div class="nk-block-head-content wrapper">
                    <div class="tab">
                         {{-- @if(isset($document) && $document != null)
                         <a href="{{ url('admin-dashboard/document-generator/?id='.$document->id) }}" class="btn tab_btn active" target="_blank">Document Generator</a>
                         @else
                         <a href="{{ url('admin-dashboard/document-generator/') }}" class="btn tab_btn active" target="_blank">Document Generator</a>
                         @endif --}}

                         @if(isset($document) && $document != null)
                         <a href="{{ route('admin.dashboard.edit_documents',['slug' => $document->slug]) }}"
                              class="btn tab_btn">Frontpage</a>
                         @else
                         <a href="{{ route('admin.dashboard.addDocuments') }}"
                              class="btn tab_btn">Document</a>
                         @endif
                        
                         @if(isset($document) && $document != null)
                         <a href="{{ url('admin-dashboard/document-questions/?id='.$document->id) }}"
                              class="btn tab_btn" target="_blank">Document Questions</a>
                         @else
                         <a href="javascript:void(0);" class="btn tab_btn">Document Questions</a>
                         @endif
                         @if(isset($document) && $document != null)
                         <a href="{{ url('admin-dashboard/document-right-content/?id='.$document->id) }}"
                              class="btn tab_btn" target="_blank">Document Text</a>
                         @else
                         <a href="javascript:void(0);" class="btn tab_btn">Document Text</a>
                         @endif
                    </div>
                    {{-- @php 
                         $documentQuestion = App\Models\Question::where('document_id', $document->id)->get();
                         $documentText = App\Models\DocumentRightSection::where('document_id', $document->id)->get();
                    @endphp
                    @if($documentQuestion->isNotEmpty() && $documentText->isNotEmpty())
                    <div class="mbsc-form-group orange-btn" id="graphical_interface">
                         <a href="{{ route('admin.document.graphical_interface', ['id' => $document->id]) }}" class="btn btn-primary">
                              Graphical Interface
                         </a>
                    </div>
                    
                    @endif --}}

                    <div class="card card-bordered card-preview" id="step_3_butns" style="{{ isset($document_generator->ai_status) ? (($document_generator->ai_status == 2 || $document_generator->ai_status == 3) ? 'display:block;' : 'display:none;') : 'display:none;' }}">
                         <div class="card-inner">
                              <div class="d-flex justify-content-between align-items-center g-4">
                                   <div class="nk-block-head-content butn-cls">
                                        <div class="mbsc-form-group view_btn">
                                             @if(isset($document) && $document->published == '1')
                                             <a href="{{ url('/contracts/'.$slug) }}" class="view_page" target="_blank">View Page</a>
                                             @else
                                             <a href="javascript:void(0);" class="view_page" onclick="isNotView()">View Page</a>
                                             @endif
                                        </div>
                                        <div class="mbsc-form-group view_btn" id="view_json_btn">
                                             @if(isset($document) && $document->id != null)
                                             <a href="{{ url('admin-dashboard/ai-response/'.$document->id ?? '') }}" class="view_page" id="AiRespnsTag" target="_blank">View AI Output</a>
                                             @else
                                             <a href="javascript:void(0);" class="view_page">View AI Output</a>
                                             @endif
                                        </div>
                                   </div>
                                   <div class="nk-block-head-content">
                                        <div class="up-btn mbsc-form-group">
                                             <button class="btn btn-primary" type="button"  id="SaveStep3" data-documentid="{{ $document->id ?? '' }}" onclick="savetheFinalStep(3)">Update</button>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
               <input type="hidden" name="recomm_sections_ids" id="recomm_sections_ids" value="{{ $recommendedSectionIds ?? '' }}">
               <div class="step1" style="{{ ($document_generator?->ai_status === null || $document_generator?->ai_status == 1 || $document_generator?->ai_status == 2) ? 'display:block;' : (($document_generator?->ai_status == 1 || $document_generator?->ai_status == 2 || $document_generator?->ai_status == 3) ? 'display:none;' : '') }}">
                    <div class="row main_section mt-4">
                         <div class="col-md-8 left_content">
                              <form action="" id="documentGenerator" method="post"
                                   enctype="multipart/form-data">
                                   @csrf

                                   <input type="hidden" name="document_id" id="document_id" value="{{ $document->id ?? '' }}">
                                   <input type="hidden" id="id" value="{{ $document_generator->id ?? '' }}"> 

                                   <div class="card card-bordered card-preview">
                                        <div class="card-inner">
                                             <div class="col-md-12 mb-3">
                                                  <div class="form-group">
                                                       <label class="form-label" for="document_name"><h6><b>Contract Name</b></h6></label>
                                                       <input type="text" class="form-control" id="document_name" name="document_name" value="{{ $document->title ?? '' }}">
                                                       <span id="title-error" style="color:red; display:none;"></span>
                                                  </div>
                                             </div>
                                             <div class="col-md-12 mb-3">
                                                  <div class="form-group">
                                                       <label class="form-label" for="additional_information"><h6><b>Additional Information</b></h6></label>
                                                       <div class="mensaje_img">
                                                            <div class="inside_contac_fild textarea-wrapper">
                                                                 <div class="message_div">
                                                                      <textarea name="additional_information" class="form-control mine_input" id="additional_information" rows="6">{{ old('additional_information',$document_generator->additional_information ?? '' ) }}</textarea>
                                                                 </div>
                                                                 <div>
                                                                 </div>
                                                                 {{-- <div class="image-wrapper">
                                                                      <img id="contact_image" src="{{ asset('assets/img/Group1.svg') }}" alt="Upload Icon">
                                                                 </div> --}}
                                                            </div>
                                                            <input type="file" id="fileInput" class="form-control-file upload_input_file" name="fileInput" style="display:none;">
                                                            <span id="fileName" class="file-name-display"></span>
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <div class="custom-control custom-checkbox checked">
                                                            @if(!empty($document_generator->is_verified))
                                                                 @if($document_generator->is_verified == 1)
                                                                 <input type="checkbox" class="custom-control-input" id="is_verified" name="is_verified" id="is_verified" value="{{ $document_generator->is_verified ?? '' }}" checked>
                                                                 <label class="custom-control-label" for="is_verified"><h6><b>AI Verification</b></h6></label>
                                                                 @else
                                                                 <input type="checkbox" class="custom-control-input" id="is_verified" name="is_verified" id="is_verified" value="">
                                                                 <label class="custom-control-label" for="is_verified"><h6><b>AI Verification</b></h6></label>
                                                                 @endif
                                                            @else
                                                            <input type="checkbox" class="custom-control-input" id="is_verified" name="is_verified" id="is_verified" value="">
                                                            <label class="custom-control-label" for="is_verified"><h6><b>AI Verification</b></h6></label>
                                                            @endif
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="col-md-12 mt-3">
                                                  <button type="button" class="btn btn-primary btn-lg" id="step_2">Step 2</button>
                                             </div>
                                        </div>
                                   </div>
                              </form>
                         </div>
                    </div>
               </div>
               <div class="step2 mt-4" style="display:none;">
                    <div class="row step2_section d-flex justify-content-center align-items-start">
                         <div class="col-md-4">
                              <h5><b>Available Standard Sections</b></h5>
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <div class="available_list" id="available">
                                        @php
                                             $recommendedSectionIdsArray = json_decode($recommendedSectionIds) ?? [];
                                        @endphp

                                        @if($standardDocument && $standardDocument->isNotEmpty())
                                             @foreach($standardDocument as $standard)
                                                  @php 
                                                       $isDisabled = in_array($standard->id, $recommendedSectionIdsArray); 
                                                  @endphp

                                                  <div class="item card card-bordered card-preview section_inner draggable {{ $isDisabled ? 'disabled' : '' }}"
                                                       id="av_section_{{ $standard->id }}">
                                                       <div class="section_name text-center fw-bold" data-id="{{ $standard->id }}">
                                                            {{ $standard->title }}
                                                       </div>
                                                  </div>
                                             @endforeach
                                        @endif
                                        </div>
                                   </div>
                              </div>
                         </div>
                         <div class="col-md-2 arrow-btns">
                              <button type="button" class="btn btn-primary mb-2"
                                   onclick="moveItems('available','selected')">&gt;</button>
                              <button type="button" class="btn btn-primary"
                                   onclick="moveItems('selected','available')">&lt;</button>
                         </div>
                         <div class="col-md-4">
                              <h5><b>Selected Standard Sections</b></h5>
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <div class="selected_list" id="selected">
                                        @if($recommendedSection && $recommendedSection->count())
                                             @foreach($recommendedSection as $section)
                                                  @if($section->standard_section)
                                                  <div class="item card card-bordered card-preview"
                                                       id="section_{{ $section->standard_section->id ?? '' }}">
                                                       <div class="section_name text-center fw-bold"
                                                            data-id="{{ $section->standard_section->id ?? '' }}">
                                                       {{ $section->standard_section->title ?? '' }}
                                                       </div>
                                                  </div>
                                                  @endif
                                             @endforeach
                                        @else
                                        <p class="text-muted">No sections selected yet.</p>
                                        @endif
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <div class="row">
                         <div class="d-flex justify-content-center">
                              <button type="button" class="btn btn-primary" id="back">Back</button>
                              <button type="button" class="btn btn-primary" id="generateContract">Generate</button>
                         </div>
                    </div>
               </div>
               <div class="step3 mt-4" style="{{ $document_generator?->ai_status == 3 ? 'display:block;' : 'display:none;' }}" id="Step3Data">
                    @include('admin.documents.partial.step3', ['questions' => $questions,'resultSections' => $resultSections,'types' => $types,'standardDocuments' => $standardDocuments])
               </div>
          </div>
     </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  
     $(document).on('click', '.item', function () {
          if(!$(this).hasClass('disabled')){
               $(this).toggleClass('selected');
          }
     });

     function moveItems(fromId, toId){
          const from = $('#' + fromId);
          const to = $('#' + toId);

          from.find('.item.selected').each(function(){
               const sectionId = $(this).find('.section_name').data('id');
               console.log("Moving section:", sectionId);

               let type = (toId === 'available') ? 'remove' : 'add';

               var data = {
                    id: sectionId,
                    document_id: $('#document_id').val(),
                    _token: "{{ csrf_token() }}",
                    type: type,
               };

               $.ajax({
                    url: "{{ route('admin.update.recommended.section') }}",
                    type: "post",
                    data: data,
                    dataType: "json",
                    success: function(response){
                         if(response.status){
                              // Swal.fire({
                              //      icon: 'success',
                              //      title: type === 'add' ? 'Added' : 'Removed',
                              //      text: response.message
                              // });

                              if(type === 'remove'){                         
                                   $('#av_section_' + sectionId)
                                        .removeClass('disabled');

                                   $('#section_' + sectionId).remove();

                                   if($('#selected .item').length === 0){
                                        $('#selected').append('<p class="text-muted">No sections selected yet.</p>');
                                   }
                              }

                              if(type === 'add'){
                                   $('#selected .text-muted').remove();

                                   $('#av_section_' + sectionId)
                                        .addClass('disabled')
                                        .removeClass('selected');

                                   if($('#section_' + sectionId).length === 0){
                                        let clone = $('#av_section_' + sectionId).clone();
                                        clone.attr('id', 'section_' + sectionId)
                                             .removeClass('disabled selected');
                                        $('#selected').append(clone);
                                   }
                              }
                         }else{
                              console.error("Failed:", response.message);
                         }
                    },
                    error: function(xhr){
                         console.error("Error:", xhr.responseText);
                    }
               });
          });
     }


</script>

<script>
     $(document).ready(function() {
          $('#document_name').on('keyup', function() {
               const title = $(this).val().trim();
               const errorSpan = $('#title-error');

               if(!title){
                    errorSpan.text('Title is required').show();
                    return;
               }

              
               $.ajax({
                    url: "{{ route('admin.check.document.title') }}",
                    type: "POST",
                    data: {
                         title: title,
                         _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                         if(response.exists){
                              errorSpan.text('This title already exists').show();
                         }else{
                              errorSpan.hide();
                         }
                    }
               });
          });
     });


     $(document).ready(function() {
          // $('#step_2').click(function(e){
               //  $('.step1').hide();
               // $('.step2').show();

               // var data = {
               //      documentId: $('#document_id').val(),
               //      documentName: $('#document_name').val(),
               //      recomm_sections_ids: $('#recomm_sections_ids').val(),
               //      _token: "{{ csrf_token() }}",
               // }
               
               // $.ajax({
               //      url: "{{ route('admin.save.recommended.section') }}",
               //      type: "post",
               //      data: data,
               //      dataType: "json",
               //      beforeSend: function() {
               //           Swal.fire({
               //                title: 'Please wait...',
               //                text: 'Loading recommended sections',
               //                allowOutsideClick: false,
               //                allowEscapeKey: false,
               //                didOpen: () => {
               //                     Swal.showLoading();
               //                }
               //           });
               //      },
               //      success: function(response){
               //           Swal.close();
               //           console.log(response);

               //           if(response.status && response.sections) {
               //                $('.step1').hide();
               //                $('.step2').show();

               //                var sectionIDs = response.section_ids;  
               //                let currentVal = $('#recomm_sections_ids').val();
               //                let currentIDs = currentVal ? JSON.parse(currentVal) : [];

               //                let mergedIDs = [...new Set([...currentIDs, ...sectionIDs])]; 

               //                $('#recomm_sections_ids').val(JSON.stringify(mergedIDs));
               //                var container = $("#selected");
               //                container.empty();

               //                $.each(response.sections, function(index, section) {
               //                     if(section.standard_section) {
               //                          var html = `
               //                               <div class="item card card-bordered card-preview"
               //                                    id="section_${section.standard_section.id}">
               //                               <div class="section_name text-center fw-bold"
               //                                    data-id="${section.standard_section.id}">
               //                                    ${section.standard_section.title}
               //                               </div>
               //                               </div>
               //                          `;
               //                          container.append(html);
               //                     }
               //                });

               //                $.each(mergedIDs, function(index, id) {
               //                     $('#av_section_' + id).addClass('disabled');
               //                });
               //           }
               //      }
               // })
             

               
          // });

          $('#step_2').click(function(e){
               e.preventDefault();
               const titleInput = document.getElementById('document_name');
               const title = titleInput?.value?.trim();

               if(!title){
                    Swal.fire({
                         icon: 'warning',
                         title: 'Title Required',
                         text: 'Please fill in the Document Title',
                         confirmButtonText: 'OK'
                    }).then((result) => {
                         if (result.isConfirmed) {
                              titleInput?.focus();
                         }
                    });
                    return;
               }

               let sectionIds = [];
               let recommVal = $('#recomm_sections_ids').val();
               if(recommVal){
                    try{
                         sectionIds = JSON.parse(recommVal); 
                    }catch(e){
                         console.error("Invalid JSON in recomm_sections_ids:", e);
                    }
               }

               var isVerified = $('#is_verified').val() === "1" ? 1 : 0;

               const formData = new FormData();
               formData.append('id', $('#id').val());
               formData.append('document_id', $('#document_id').val());
               formData.append('document_name', $('#document_name').val());
               formData.append('additional_information', $('#additional_information').val());
               // formData.append('fileInput', $('#fileInput')[0]?.files[0]);
               formData.append('is_verified', isVerified);
               sectionIds.forEach(id => {
                    formData.append('section_ids[]', id);
               });

               formData.append('_token', "{{ csrf_token() }}");

               // Swal.fire({
               //      title: "Generating Contract",
               //      html: "Please wait while AI generates your Document Questions and Text...",
               //      timer: 100000,
               //      timerProgressBar: true,
               //      didOpen: () => Swal.showLoading()
               // });

               Swal.fire({
                    title: "Generating Contract",
                    html: "Please wait while AI generates your Document...",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
               });

               $.ajax({
                    // url: "{{ route('admin.document.generateProcc') }}",
                    url: "{{ url('api/document/start') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function(response){
                         if(response.status == true){
                              console.log(response);
                         
                              var docStatus = response.status;
                              var documentId = response.document_id;
                              var docGeneratorId = response.id;
                              var ai_model = response.ai_model;
                         
                              let intervalTime = 30000; // Run every 10 seconds
                                   let maxDuration = 10 * 60 * 1000; // 5 minutes in milliseconds
                                   let elapsedTime = 0;

                                   let interval = setInterval(function () {
                                   CheckDocStatus(documentId, docGeneratorId, ai_model).then(function (docStatus) {
                                        if (docStatus === true) {
                                             
                                             clearInterval(interval);
                                             console.log("Document generation completed!");
                                        } else {
                                             console.log("Still processing...");
                                        }
                                   });

                                   elapsedTime += intervalTime;

                                   if (elapsedTime >= maxDuration) {
                                        clearInterval(interval);
                                        console.log("Stopped checking after 3 minutes.");

                                        Swal.close(); // hide loader if still showing
                                        Swal.fire({
                                             icon: 'info',
                                             title: 'Still Processing',
                                             text: 'The document is taking longer than expected. Please check again later.'
                                        });
                                   }
                              }, intervalTime);
                              
                              // saveJsonResponse(documentId,document_generator_id,prompt_ai_model);

                         }else{
                              Swal.fire({
                                   icon: 'warning',
                                   title: 'Generation Failed',
                                   text: response.message || 'Something went wrong.'
                              });
                         }
                    },
                    error: function(xhr){
                         Swal.fire({
                              icon: 'error',
                              title: 'Error Occurred',
                              text: xhr.responseJSON?.message || 'Unexpected error.'
                         });
                         console.error('Error:', xhr.responseJSON || xhr.statusText);
                    }
               });
          });
     });

     function CheckDocStatus(documentId, docGeneratorId, ai_model){
          return new Promise(function(resolve, reject) {
               $.ajax({
                    url: "{{ route('admin.check.document.status') }}",
                    type: "POST",
                    data: {
                         document_id: documentId,
                         id: docGeneratorId,
                         _token: "{{ csrf_token() }}"
                    },
                    success: function(response){
                         if(response.status){
                              if(response.ai_status == 2){
                                   $('#SaveStep3').attr('data-documentid', documentId);
                                   if(documentId){
                                        let newUrl = window.location.origin + window.location.pathname + '?id=' + documentId;
                                        window.history.pushState({ path: newUrl }, '', newUrl);
                                   }

                                   $('.step1').hide();
                                   $('#step_3_butns').show();
                                   $("#Step3Data").html('');
                                   $("#Step3Data").append(response.html).show();
                                   $('#view_json_btn').show();

                                   let ailink = "{{ url('admin-dashboard/ai-response/{id}') }}";
                                   ailink = ailink.replace('{id}', documentId);
                                   console.log(ailink);
                                   $('#AiRespnsTag').attr('href', ailink);

                                   Swal.close(); 

                                   Swal.fire({
                                        icon: 'success',
                                        title: 'Contract Generated!',
                                        text: response.message
                                   });
                                   resolve(true); 
                              }else{
                                   resolve(false); 
                              }
                         }else{
                              resolve(false); 
                         }
                    },
                    error: function(xhr){
                         console.error('Error:', xhr.responseJSON || xhr.statusText);
                         resolve(false); 
                    }
               });
          });
     }

     // function saveJsonResponse(documentId,id,model){
     //      if(!documentId) return;

     //      $.ajax({
     //           url: "{{ route('admin.save.document.json') }}",
     //           type: "POST",
     //           data: {
     //                document_id: documentId,
     //                id: id,
     //                ai_model: model,
     //                _token: "{{ csrf_token() }}"
     //           },
     //           success: function(response){
     //                if(response.status){
     //                     $('#SaveStep3').attr('data-documentid', documentId);
     //                     if(documentId){
     //                          let newUrl = window.location.origin + window.location.pathname + '?id=' + documentId;
     //                          window.history.pushState({ path: newUrl }, '', newUrl);
     //                     }

     //                     $('.step1').hide();
     //                     $('#step_3_butns').show();
     //                     $("#Step3Data").html('');
     //                     $("#Step3Data").append(response.html).show();

     //                     Swal.close(); 

     //                     Swal.fire({
     //                          icon: 'success',
     //                          title: 'Contract Generated!',
     //                          text: response.message
     //                     });
     //                }else{
     //                     Swal.fire({
     //                          icon: 'warning',
     //                          title: 'Generation Failed',
     //                          text: response.message || 'Something went wrong.'
     //                     });
     //                }
     //           },
     //            error: function(xhr){
     //                Swal.fire({
     //                     icon: 'error',
     //                     title: 'Error Occurred',
     //                     text: xhr.responseJSON?.message || 'Unexpected error.'
     //                });
     //                console.error('Error:', xhr.responseJSON || xhr.statusText);
     //           }
     //      });

     // }

</script>

<script>
     $(document).ready(function(){
          $('#contact_image').on('click', function(){
               console.log('Image clicked'); 
               $('#fileInput').trigger('click');
          });

          $('#fileInput').on('change', function(){
               const file = this.files[0];
               if (file) {
                    $('#fileName').text("Selected file: " + file.name);
               } else {
                    $('#fileName').text('');
               }
          });


          $('#is_verified').change(function(){
               if($(this).is(":checked") == true){
                    $(this).val(1);
                    // $('.hidden_field').show();
               }else{
                    $(this).val(0);
                    // $('.hidden_field').hide();
               }
          })
     });

     $('#generateContract').on('click', function(e){
          e.preventDefault();

          let sectionIds = [];
          let recommVal = $('#recomm_sections_ids').val();

          if(recommVal){
               try{
                    sectionIds = JSON.parse(recommVal); 
               }catch(e){
                    console.error("Invalid JSON in recomm_sections_ids:", e);
               }
          }

          // console.log(sectionIds);
          // return;

          const formData = new FormData();
          formData.append('id', $('#id').val());
          formData.append('document_id', $('#document_id').val());
          formData.append('document_name', $('#document_name').val());
          // formData.append('language', $('#language').val());
          // formData.append('country', $('#country').val());
          formData.append('additional_information', $('#additional_information').val());
          formData.append('fileInput', $('#fileInput')[0]?.files[0]);
          formData.append('is_verified', $('#is_verified').val());
          // formData.append('verification_prompt', $('#verification_prompt').val());
          // formData.append('section_ids',sectionIds); 
          sectionIds.forEach(id => {
               formData.append('section_ids[]', id);
          });

          formData.append('_token', "{{ csrf_token() }}");

          Swal.fire({
               title: "Generating Contract",
               html: "Please wait while AI generates your Document Questions and Text...",
               timer: 60000,
               timerProgressBar: true,
               didOpen: () => Swal.showLoading()
          });

          $.ajax({
               url: "{{ route('admin.document.generateProcc') }}",
               type: "POST",
               data: formData,
               processData: false,
               contentType: false,
               dataType: "json",
               success: function(response){
                    // console.log(response);
                    // return;
                    Swal.close(); 
                    if(response.status == true){
                         var documentId = response.document_id;
                         $('#SaveStep3').attr('data-documentid', documentId);
                         if (documentId) {
                              let newUrl = window.location.origin + window.location.pathname + '?id=' + documentId;
                              window.history.pushState({ path: newUrl }, '', newUrl);
                         }

                         $('.step2').hide();
                         $('#step_3_butns').show();
                         $("#Step3Data").html('');
                         $("#Step3Data").append(response.html).show();

                         Swal.fire({
                              icon: 'success',
                              title: 'Contract Generated!',
                              text: response.message
                         });
                    }else{
                         Swal.fire({
                              icon: 'warning',
                              title: 'Generation Failed',
                              text: response.message || 'Something went wrong.'
                         });
                    }
               },
               error: function(xhr){
                    Swal.fire({
                         icon: 'error',
                         title: 'Error Occurred',
                         text: xhr.responseJSON?.message || 'Unexpected error.'
                    });
                    console.error('Error:', xhr.responseJSON || xhr.statusText);
               }
          });
     });

</script>

<script>
     $(document).ready(function(){
          $(".draggable").draggable({
               helper: "clone",
               revert: "invalid",
               zIndex: 9999,
               appendTo: "body",
               
               start: function(event, ui) {
                    if($(this).hasClass("selected")){
                         // alert("This item has already been selected and cannot be moved again.");
                         return false;
                    }
                    ui.helper.attr("data-original-id", $(this).attr("id"));

               },
               stop: function(event, ui) {
                    $(this).addClass("selected");
               }
          });

          $("#droppable").droppable({
               drop: function(event, ui) {
                    var originalId = ui.helper.attr("data-original-id"); 
                    var sectionId  = originalId.replace("section_", "");  
                    var document_id = $('#document_id').val();
                    var $droppable = $(this);

                    // prepare droppedItem but don't append yet
                    var droppedItem = ui.helper.clone();
                    droppedItem.css({
                         position: "relative",
                         left: "auto",
                         top: "auto",
                         margin: "10px 0"
                    });
                    droppedItem.removeClass("draggable ui-draggable ui-draggable-handle selected");

                    $.ajax({
                         url: "{{ route('admin.save.recommended.section') }}",   
                         type: "POST",
                         data: {
                              section_id: sectionId,
                              document_id: document_id,
                              _token: "{{ csrf_token() }}"
                         },
                         success: function(response){
                              if(response.status === true){
                                   $droppable.append(droppedItem);

                                   let currentVal = $('#recomm_sections_ids').val();
                                   let ids = [];

                                   if(currentVal){
                                        try{
                                             ids = JSON.parse(currentVal);
                                        }catch (e){
                                             ids = [];
                                        }
                                   }

                                   if(!ids.includes(sectionId)){
                                        ids.push(sectionId);
                                   }

                                   $('#recomm_sections_ids').val(JSON.stringify(ids));

                                   Swal.fire({
                                        icon: 'success',
                                        title: 'Section saved successfully!',
                                        text: response.message
                                   });
                              }else{
                                   Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message
                                   });
                              }

                         },
                         error: function(xhr) {
                              const response = xhr.responseJSON;
                              Swal.fire({
                                   icon: 'error',
                                   title: 'Error',
                                   text: response?.message ?? "Something went wrong!"
                              });
                         }
                    });
               }
          });

         
     });
</script>

<script>
     $(document).ready(function(){
          $('#last_step').on('change',function(){
               if($(this).is(':checked')){
               $('#is_end').val(1);
               }else{
                    $('#is_end').val(0);
               }
          });

          $('body').delegate('.remove_steps','click', function(){
               if($(this).attr('value') === 'appended'){
                    $(this).closest('.steps_section').remove();
               }else{
                    var id = $(this).data('id');
                    let deleteIds = $('#img_sec_ids').val();
                    if(deleteIds){
                         deleteIds += ',' + id;
                    }else{
                         deleteIds = id;
                    }
                    $('#img_sec_ids').val(deleteIds);
                    $('.steps_section'+id).hide();
               }
          })


          // To remove the questions
          $('body').delegate('.remove_questions','click', function(){
               if($(this).attr('value') === 'appended'){
                    $(this).closest('.append_textbox').remove();
               }else{
                    var id = $(this).data('id');
                    let deleteIds = $('#img_sec_ids').val();
                    if(deleteIds){
                         deleteIds += ',' + id;
                    }else{
                         deleteIds = id;
                    }
                    $('#img_sec_ids').val(deleteIds);
                    $('#append_textbox'+id).hide();
               }
          })
     });

      // To add the  Label
      let label_count = 0;
     function addLabel(id,text){
          label_count++ ;

          let questionLabel = $('#template-text').html()
               .replace(/__ID__/g, `condition_question_label-${label_count}`)
               .replace(/__CLASS__/g, 'form-control')
               .replace(/__NAME__/g, `condition_question_label-${label_count}[]`)
               .replace(/:value="__VALUE__"/g, '')
               .replace(/__LABEL__/g, 'Question Label');

          let questionIDHtml = $('#template-question_select').html()
               .replace(/__ID__/g, `label_qu_id-${label_count}`)
               .replace(/__CLASS__/g, 'js-select2 new_label_question_id')
               .replace(/__NAME__/g, `label_qu_id-${label_count}[]`)
               .replace(/__LABEL__/g, 'Question ID');

          let questionValue = $('#template-text').html()
               .replace(/__ID__/g, `condition_question_value-${label_count}`)
               .replace(/__CLASS__/g, 'form-control')
               .replace(/__NAME__/g, `condition_question_value-${label_count}[]`)
               .replace(/__LABEL__/g, 'Value');

          $('#append_label_condition' + id + ' .label-condition').last().find('.add_icon').hide();

          const html = `<div class="label-condition" id="label-condition${id}" value="appended" data-is_new=true>
                         <div class="inner-label">   
                              <div class="row">
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             {{-- <label class="form-label" for="condition_question_label-${label_count}">Question Label</label>
                                             <input type="text" class="form-control" id="condition_question_label-${label_count}" name="condition_question_label-${label_count}[]" value="${text}">  --}}
                                             ${questionLabel}
                                        </div>
                                   </div>
                                   <div class="col-md-3">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="label_qu_id-${label_count}">Question ID</label>
                                             <div class="form-control-wrap question">
                                                  <select class="form-select js-select2 new_label_question_id" data-search="on" name="label_qu_id-${label_count}[]" id="label_qu_id-${label_count}">
                                                       @if(isset($questions) && $questions != null)
                                                            @foreach($questions as $question)
                                                                 <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                            @endforeach
                                                       @endif
                                                  </select>
                                             </div> -->
                                             ${questionIDHtml}
                                        </div>
                                   </div>
                                   <div class="col-md-3">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="condition_question_value-${label_count}">Value</label>
                                             <input type="text" class="form-control" id="condition_question_value-${label_count}" name="condition_question_value-${label_count}[]" value=""> -->
                                             ${questionValue}
                                        </div>
                                   </div>
                                   <div class="col-md-2 add_rmv_icn20">
                                        <div class="form-group prnt_add_cls">
                                             <span class="remove_icon red_hover" onclick="removeLabel(this)" value="appended"><i class="fa fa-trash"></i></span>
                                        </div>
                                        <div class="form-group prnt_add_cls">
                                             <span class="remove_icon add_icon" onclick="addLabel(${id},'')"><i class="fa-solid fa-add"></i></span>
                                        </div>                                    
                                   </div>
                              </div>
                         </div>
                    </div>`
          $('#append_label_condition'+id).append(html);
          $('.qu_label_cls' + id).hide();
          $('.qu_label_btn' + id).hide();
          $('.qu_label_btn' + id).hide();
          $(`#condition_question_label-${label_count}`).val(text);
     }

     // To remove  the label

     function removeLabel(e){
          if($(e).attr('value') === 'appended'){
               console.log('if');
               // return;
               const uniqueId = $(e).closest('.label-condition').attr('id').replace('label-condition', '');
               Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
               }).then((result) => {
                    if (result.isConfirmed) {
                         $(e).closest('.label-condition').remove();
                         const parentDiv = $('#append_label_condition' + uniqueId);

                         if(parentDiv.find('.label-condition').length === 0) {

                              if ($('.qu_label_cls' + uniqueId).length && $('.qu_label_btn' + uniqueId).length) {
                                   $('.qu_label_cls' + uniqueId).show();
                                   $('.qu_label_btn' + uniqueId).show();
                              } else {
                                   let questionLabel = $('#template-text').html()
                                        .replace(/__ID__/g, `text_qu_label-${uniqueId}`)
                                        .replace(/__CLASS__/g, 'form-control question_labl')
                                        .replace(/__NAME__/g, `text_qu_label-${uniqueId}`)
                                        .replace(/__LABEL__/g, 'Question Label');

                                   const html = `<div class="col-md-10 form-group qu_label_cls${uniqueId} label_qu">
                                        <!-- <label class="form-label" for="text_qu_label-${uniqueId}">Question Label</label>
                                        <input type="text" class="form-control question_labl" id="text_qu_label-${uniqueId}" name="text_qu_label-${uniqueId}" value=""> -->
                                        ${questionLabel}
                                   </div>
                                   <div class="col-md-2 form-group prnt_add_cls qu_label_btn${uniqueId}">
                                        <span class="remove_icon add_icon" onclick="addLabel('${uniqueId}','')"><i class="fa-solid fa-add"></i></span>
                                   </div>
                                   <div class="append_label_condition" id="append_label_condition${uniqueId}"></div>
                                   `;

                                   $('#hide_question_label' + uniqueId).html(html);
                              }
                         }
                    }
               });
          }else{
               Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
               }).then((result) => {
                    if(result.isConfirmed){
                         var id = $(e).data('id');
                         let deleteIds = $('#condition_id').val();
                         if(deleteIds){
                              deleteIds += ',' + id;
                         } else {
                              deleteIds = id;
                         }
                         $('#condition_id').val(deleteIds);

                         $('#label-condition' + id).hide();

                         const container = $(e).closest('.append_label_condition');
                         const uniqueId = $(container).attr('id').replace('append_label_condition', '');


                         if(container.find('.label-condition:visible').length === 0){
                              console.log('if');
                              let questionLabel = $('#template-text').html()
                                   .replace(/__ID__/g, `text_qu_label-${uniqueId}`)
                                   .replace(/__CLASS__/g, 'form-control question_labl')
                                   .replace(/__NAME__/g, `text_qu_label-${uniqueId}`)
                                   .replace(/__LABEL__/g, 'Question Label');

                              const html = `<div class="col-md-10 form-group qu_label_cls${uniqueId} label_qu">
                              <!-- <label class="form-label" for="text_qu_label-${uniqueId}">Question Label</label>
                              <input type="text" class="form-control question_labl" id="text_qu_label-${uniqueId}" name="text_qu_label-${uniqueId}" value=""> -->
                              ${questionLabel}
                              </div>
                              <div class="col-md-2 form-group prnt_add_cls qu_label_btn${uniqueId}">
                              <span class="remove_icon add_icon" onclick="addLabel('${uniqueId}','')"><i class="fa-solid fa-add"></i></span>
                              </div>
                              <div class="append_label_condition" id="append_label_condition${uniqueId}"></div>`;

                              $('#hide_question_label' + uniqueId).html(html);
                              console.log('#hide_question_label' + uniqueId);
                         }else{
                              console.log('else');
                         }
                    }
               });
          }
     }

     function removeAnotherCondition(e, goToStepId, independentCondId,key) {
          if($(e).attr('value') === 'appended'){
               const $anotherCondition = $(e).closest('.another-condition');
               const anotherConditionId = $anotherCondition.attr('id');
               const ids = anotherConditionId.replace('another-condition-', '').split('-');
               const newUniqueId = ids[0];
               const stepId = ids[1];
               $anotherCondition.remove();

               const parentDiv = $('#another_page_condition_' + newUniqueId + '_' + independentCondId);
               if(parentDiv.find('.another-condition').length === 0) {
                    $('#secondCondBtn' + newUniqueId + '_' + independentCondId).show();
               }else{
                    $('#secondCondBtn' + goToStepId + '_' + independentCondId).show();
               }
          }else{
               var id = $(e).attr('data-id');
               var deleteIds = $('#sub_condition_id').val();

               if(deleteIds){
                    deleteIds += ',' + goToStepId;
               }else{
                    deleteIds = goToStepId;
               }
               $('#sub_condition_id').val(deleteIds);

               $('#another-condition-' + goToStepId + '-'+independentCondId + '-' +key).hide();

               const container = $(e).closest('.another_page_condition');
               const uniqueId = $(container).attr('id').replace('another_page_condition', '');
               const parentDiv = $('#another_page_condition_' + goToStepId + '_' + independentCondId);

               if(container.find('.another-condition:visible').length === 0){
                    $('#secondCondBtn' + id + '_' + independentCondId).show();
               }else{
                    console.log('id else');
               }


          }
     }


     function removeIndependentDiv(e, goToStepId,id){
          console.log(goToStepId);
          if($(e).attr('value') === 'appended'){
               const $anotherCondition = $(e).closest('.independent_cond_div');
               const anotherConditionId = $anotherCondition.attr('id');
               const ids = anotherConditionId.replace('independent_cond_div_', '').split('_');
               const newUniqueId = ids[0];
               const stepId = ids[1];
               $anotherCondition.remove();
          }else{
               var conditionId = $(e).attr('data-id');
               var deleteIds = $('#condition_id').val();

               if(deleteIds){
                    deleteIds += ',' + conditionId;
               }else{
                    deleteIds = conditionId;
               }
               $('#condition_id').val(deleteIds);

               $('#independent_cond_div_' + conditionId + '_' + id).hide();
          }
     }

     var independentCondCounter = {};
     var anotherGoToStepCounter = {};

     function addAnotherCondition(id){

          if (!independentCondCounter[id]) {
               independentCondCounter[id] = 0;
          }
          let step = 0;

          if (independentCondCounter[id] < 10) {
               independentCondCounter[id]++;
               step++ ;
               console.log(step);

               anotherGoToStepCounter[id + '_' + independentCondCounter[id]] = 0;

               let conditionQuestionIDHtml = $('#template-question_select').html()
                    .replace(/__ID__/g, `another_que_id-${id}-${independentCondCounter[id]}-${step}`)
                    .replace(/__CLASS__/g, 'js-select2')
                    .replace(/__NAME__/g, `another_que_id-${id}-${independentCondCounter[id]}-${step}[]`)
                    .replace(/__LABEL__/g, 'Question ID');

               let conditionOptionHtml = $('#template-condition-select').html()
                    .replace(/__ID__/g, `another_conditions_step-${id}-${independentCondCounter[id]}-${step}`)
                    .replace(/__CLASS__/g, 'js-select2')
                    .replace(/__NAME__/g, `another_conditions_step-${id}-${independentCondCounter[id]}-${step}[]`)
                    .replace(/__LABEL__/g, 'Condition');

               let conditionValue = $('#template-text').html()
                    .replace(/__ID__/g, `another_qu_val-${id}-${independentCondCounter[id]}-${step}`)
                    .replace(/__CLASS__/g, 'form-control')
                    .replace(/__NAME__/g, `another_qu_val-${id}-${independentCondCounter[id]}-${step}[]`)
                    .replace(/__LABEL__/g, 'Value');

               let conditionGoToHtml = $('#template-select').html()
                    .replace(/__ID__/g, `another_conditional_go_to_step-${id}_${independentCondCounter[id]}`)
                    .replace(/__CLASS__/g, 'js-select2')
                    .replace(/__NAME__/g, `another_conditional_go_to_step-${id}_${independentCondCounter[id]}`)
                    .replace(/__LABEL__/g, 'Conditional Go to Step');

               let html = `
               <div class="independent_cond_div" id="independent_cond_div_${id}_${independentCondCounter[id]}" value="appended" data-is_new="true">
                    <hr>
                    <div class="text-end">
                         <div class="form-group">
                              <span class="remove_icon red_hover" onclick="removeIndependentDiv(this,'${id}','${independentCondCounter[id]}')" value="appended">
                                   <i class="fa fa-trash"></i>
                              </span>
                         </div>
                    </div>
                    <div class="col-md-12">
                         <div class="form-group">
                              <label class="form-label">Add Conditions</label>
                         </div>
                    </div>

                    <div class="another_page_condition" id="another_page_condition_${id}_${independentCondCounter[id]}">
                         <div class="another-condition" id="another-condition-${id}-${independentCondCounter[id]}-${step}" value="appended" data-is_new="true">
                              <div class="row">
                                   <div class="col-md-3">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="another_que_id-${id}-${independentCondCounter[id]}-${step}">Question ID</label>
                                             <div class="form-control-wrap question">
                                             <select class="form-select js-select2" data-search="on" name="another_que_id-${id}-${independentCondCounter[id]}-${step}[]" id="another_que_id-${id}-${independentCondCounter[id]}-${step}">
                                                  @if(isset($questions) && $questions != null)
                                                       @foreach($questions as $question)
                                                            <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                       @endforeach
                                                  @endif
                                             </select>
                                             </div> -->
                                             ${conditionQuestionIDHtml}
                                        </div>
                                   </div>

                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="another_conditions_step-${id}-${independentCondCounter[id]}-${step}">Condition</label>
                                             <div class="form-control-wrap">
                                             <select class="form-select js-select2" name="another_conditions_step-${id}-${independentCondCounter[id]}-${step}[]" id="another_conditions_step-${id}-${independentCondCounter[id]}-${step}">
                                                  <option value="" selected disabled>Select</option>
                                                  <option value="is_equal_to">is equal to</option>
                                                  <option value="is_greater_than">is greater than</option>
                                                  <option value="is_less_than">is less than</option>
                                                  <option value="not_equal_to">not equal to</option>
                                             </select>
                                             </div> -->
                                             ${conditionOptionHtml}
                                        </div>
                                   </div>

                                   <div class="col-md-3">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="another_qu_val-${id}-${independentCondCounter[id]}-${step}">Value</label>
                                             <input type="text" class="form-control" id="another_qu_val-${id}-${independentCondCounter[id]}-${step}" name="another_qu_val-${id}-${independentCondCounter[id]}-${step}[]" value=""> -->
                                             ${conditionValue}
                                        </div>
                                   </div>

                                   <div class="col-md-2 form-group prnt_add_cls">
                                        <span class="remove_icon add_icon" onclick="anotherCondition(this, '${id}', '${independentCondCounter[id]}')">
                                             <i class="fa-solid fa-plus"></i>
                                        </span>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <br>
                    <div class="col-md-12">
                         <div class="form-group">
                              <!-- <label class="form-label" for="another_conditional_go_to_step-${id}_${independentCondCounter[id]}">Conditional Go to Step</label>
                              <div class="form-control-wrap">
                              <select class="form-select js-select2" data-search="on"
                                   name="another_conditional_go_to_step-${id}_${independentCondCounter[id]}"
                                   id="another_conditional_go_to_step-${id}_${independentCondCounter[id]}">
                                   <option value="0">Checkout</option>
                                   @if(isset($questions) && $questions != null)
                                        @foreach($questions as $question)
                                             <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                        @endforeach
                                   @endif
                              </select>
                              </div> -->
                              ${conditionGoToHtml}
                         </div>
                    </div>
               </div>`;

               $('#independent_cond_container' + id).append(html);
          } else {
               // alert("Maximum 10 conditions allowed!");
          }
     }

     // Add the condition
     let condition_count = 0;
     function addCondition(id){
          let conditionQuestionIDHtml = $('#template-question_select').html()
               .replace(/__ID__/g, `page_Setting_qu_id-${condition_count}`)
               .replace(/__CLASS__/g, 'js-select2')
               .replace(/__NAME__/g, `page_Setting_qu_id-${condition_count}[]`)
               .replace(/__LABEL__/g, 'Question ID');

          let conditionOptionHtml = $('#template-condition-select').html()
               .replace(/__ID__/g, `page_Setting_conditions-${condition_count}`)
               .replace(/__CLASS__/g, 'js-select2')
               .replace(/__NAME__/g, `page_Setting_conditions-${condition_count}[]`)
               .replace(/__LABEL__/g, 'Condition');

          let conditionValue = $('#template-text').html()
               .replace(/__ID__/g, `page_Setting_qu_val-${condition_count}`)
               .replace(/__CLASS__/g, 'form-control')
               .replace(/__NAME__/g, `page_Setting_qu_val-${condition_count}[]`)
               .replace(/__LABEL__/g, 'Value');

          condition_count++ ;
          const html = `<div class="sec-condition" id="sec-condition${id}" value="appended" data-is_new=true>

                    <div class="row">
                         <div class="col-md-3">
                              <div class="form-group">
                                   <!-- <label class="form-label" for="page_Setting_qu_id-${condition_count}">Question ID</label>
                                   <div class="form-control-wrap question">
                                        <select class="form-select js-select2" data-search="on" name="page_Setting_qu_id-${condition_count}[]" id="page_Setting_qu_id-${condition_count}">
                                             @if(isset($questions) && $questions != null)
                                                  @foreach($questions as $question)
                                                       <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                  @endforeach
                                             @endif
                                        </select>
                                   </div> -->
                                   ${conditionQuestionIDHtml}
                              </div>
                         </div>
                         <div class="col-md-4">
                              <div class="form-group">
                                   <!-- <label class="form-label" for="page_Setting_conditions-${condition_count}">Condition</label>
                                   <div class="form-control-wrap">
                                        <select class="form-select js-select2" name="page_Setting_conditions-${condition_count}[]" id="page_Setting_conditions-${condition_count}">
                                             <option value="" selected disabled>Select</option>
                                             <option value="is_equal_to">is equal to</option>
                                             <option value="is_greater_than">is greater than</option>
                                             <option value="is_less_than">is less than</option>
                                             <option value="not_equal_to">not equal to</option>
                                        </select>
                                   </div> -->
                                   ${conditionOptionHtml}
                              </div>
                         </div>
                         <div class="col-md-3">
                              <div class="form-group">
                                   <!-- <label class="form-label" for="page_Setting_qu_val-${condition_count}">Value</label>
                                   <input type="text" class="form-control" id="page_Setting_qu_val-${condition_count}" name="page_Setting_qu_val-${condition_count}[]" value=""> -->
                                   ${conditionValue}
                              </div>
                         </div>
                         <div class="col-md-2 add_rmv_icn22">
                              <div class="form-group prnt_add_cls">
                                   <span class="remove_icon red_hover" onclick="removeCondition(this)" value="appended"><i class="fa fa-trash"></i></span>
                              </div>
                              <div class="form-group prnt_add_cls">
                                   <span class="remove_icon add_icon" onclick="addCondition('${id}')"><i class="fa-solid fa-plus"></i></span>
                              </div>
                         </div>
                    </div>

               </div>`
          $('#append_page_condition'+id).append(html);

          const anotherCondDiv = $('.another_cond_div' + id);
          if (anotherCondDiv.length > 0) {
               anotherCondDiv.show();
          }else{
               console.log('another_cond_div'+id+' not found.');
          }

          $('.firstCondBtn').hide();
     
     }

     // Remove the condition
     function removeCondition(e){
          if($(e).attr('value') === 'appended'){
               const uniqueId = $(e).closest('.sec-condition').attr('id').replace('sec-condition', '');
               Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
               }).then((result) => {
                    if(result.isConfirmed){
                         $(e).closest('.sec-condition').remove();
                         const parentDiv = $('#append_page_condition' + uniqueId);
                         console.log(parentDiv.find('.sec-condition').length);

                         if(parentDiv.find('.sec-condition').length === 0) {
                              // $('.firstCondBtn').show();
                              $('.cond_div'+uniqueId).hide();
                              $('.go_to_step'+uniqueId).show();
                         }

                    }
               });
          }else{
               Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
               }).then((result) => {
                    if(result.isConfirmed){
                         var id = $(e).data('id');
                         let deleteIds = $('#condition_id').val();
                         if(deleteIds){
                              deleteIds += ',' + id;
                         }else{
                              deleteIds = id;
                         }
                         $('#condition_id').val(deleteIds);
                         $('#sec-condition'+id).hide();
                         $('.firstCondBtn').show();
                         $('.cond_div'+id).hide();
                         $('.go_to_step'+id).show();
                         // $('.another_cond_div'+id).hide();
                    }
               });
          }
     }

     function addGoToStep(id){
          $('.cond_div'+id).show();
          $('.go_to_step'+id).hide();
     }


     // QQ
     const fieldClassMap = {
          textbox: "append_textbox",
          textarea: "append_textarea",
          dropdown: "append_dropdown",
          "radio-button": "append_radio",
          "date-field": "append_dateField",
          pricebox: "append_pricebox",
          "number-field": "append_numberField",
          "percentage-box": "appendPercentageBox",
          "dropdown-link": "append_dropdownLink",
     };


     const fieldTemplates = {
          "textbox": (que_id) => `<div class="form-group">
                              <label class="form-label" for="text_placeholder-${que_id}">Text Box Placeholder</label>
                              <input type="text" class="form-control text_box_placeholder" id="text_placeholder-${que_id}" name="text_placeholder-${que_id}" value="" />
                         </div>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="text_go_to_step-${que_id}">Go to step</label>
                                   <div class="form-control-wrap">
                                        <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${que_id}" id="text_go_to_step-${que_id}">
                                             <option value="0">Checkout</option>
                                             @if(isset($questions) && $questions != null) @foreach($questions as $question)
                                             <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                             @endforeach @endif
                                        </select>
                                   </div>
                              </div>
                         </div>`,
          "textarea": (que_id) => `<div class="form-group">
                         <label class="form-label" for="text_placeholder-${que_id}">Text Box Placeholder</label>
                         <input type="text" class="form-control text_box_placeholder" id="text_placeholder-${que_id}" name="text_placeholder-${que_id}" value="" />
                    </div>
                    <hr>
                    <div class="col-md-12">
                         <div class="form-group">
                              <label class="form-label" for="text_go_to_step-${que_id}">Go to step</label>
                              <div class="form-control-wrap">
                              <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${que_id}" id="text_go_to_step-${que_id}">
                                   <option value="0">Checkout</option>
                                   @if(isset($questions) && $questions != null) @foreach($questions as $question)
                                   <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                   @endforeach @endif
                              </select>
                              </div>
                         </div>
                    </div>`,
          "dropdown": (que_id) => `<div class="form-group">
                         <label class="form-label">Add Dropdown Option</label>
                    </div>
                    <div class="append_options" id="append_options${que_id}"></div>
                    <div class="text-end">
                         <div class="form-group">
                              <span class="remove_icon add_icon" onclick="addOptions('dropdown','${que_id}')"><i class="fa-solid fa-add"></i></span>
                         </div>
                    </div>`,
          "radio-button": (que_id) => `<div class="form-group">
                         <label class="form-label">Add Radio Option</label>
                    </div>
                    <div class="append_options" id="append_options${que_id}"></div>
                    <div class="text-end">
                         <div class="form-group">
                              <span class="remove_icon add_icon" onclick="addOptions('radio-button','${que_id}')"><i class="fa-solid fa-add"></i></span>
                         </div>
                    </div>`,
          "date-field": (que_id) => ` <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="text_go_to_step-${que_id}">Go to step</label>
                                   <div class="form-control-wrap">
                                        <select class="form-select js-select2 new_label_question_id" data-search="on" name="date_go_to_step-${que_id}" id="text_go_to_step-${que_id}">
                                             <option value="0">Checkout</option>
                                             @if(isset($questions) && $questions != null) @foreach($questions as $question)
                                             <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                             @endforeach @endif
                                        </select>
                                   </div>
                              </div>
                         </div>`,
          "pricebox": (que_id) => `
               <div class="form-group">
                    <label class="form-label" for="text_placeholder-${que_id}">Text Box Placeholder</label>
                    <input type="text" class="form-control text_box_placeholder" id="text_placeholder-${que_id}" name="text_placeholder-${que_id}" value="" />
               </div>
               <hr>
               <div class="col-md-12">
                    <div class="form-group">
                         <label class="form-label" for="text_go_to_step-${que_id}">Go to step</label>
                         <div class="form-control-wrap">
                         <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${que_id}" id="text_go_to_step-${que_id}">
                              <option value="0">Checkout</option>
                              @if(isset($questions) && $questions != null) @foreach($questions as $question)
                              <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                              @endforeach @endif
                         </select>
                         </div>
                    </div>
               </div>`,
          "number-field": (que_id) => `
               <div class="form-group">
                    <label class="form-label" for="text_placeholder-${que_id}">Number field Placeholder</label>
                    <input type="text" class="form-control number_placeholder" id="text_placeholder-${que_id}" name="text_placeholder-${que_id}" >
               </div>
               <hr>
               <div class="col-md-12">
                    <div class="form-group">
                         <label class="form-label" for="text_go_to_step-${que_id}">Go to step</label>
                         <div class="form-control-wrap">
                         <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${que_id}" id="text_go_to_step-${que_id}">
                              <option value="0">Checkout</option>
                              @if(isset($questions) && $questions != null) @foreach($questions as $question)
                              <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                              @endforeach @endif
                         </select>
                         </div>
                    </div>
               </div>`,
          "percentage-box": (que_id) => `
               <div class="form-group">
                    <label class="form-label" for="text_placeholder-${que_id}">Text Box Placeholder</label>
                    <input type="text" class="form-control text_box_placeholder" id="text_placeholder-${que_id}" name="text_placeholder-${que_id}" >
               </div>
               <hr>
               <div class="col-md-12">
                    <div class="form-group">
                         <label class="form-label" for="text_go_to_step-${que_id}">Go to step</label>
                         <div class="form-control-wrap">
                         <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${que_id}" id="text_go_to_step-${que_id}">
                              <option value="0">Checkout</option>
                              @if(isset($questions) && $questions != null) @foreach($questions as $question)
                              <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                              @endforeach @endif
                         </select>
                         </div>
                    </div>
               </div>`,
          "dropdown-link": (que_id) => ` <div class="col-md-12">
                              <div class="form-group qu_label_cls${que_id} label_qu">
                                   <label class="form-label" for="text_qu_label-${que_id}">Question Label</label>
                                   <input type="text" class="form-control dropdown_ques" id="text_qu_label-${que_id}" name="text_qu_label-${que_id}" value="">
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="same_contract_link-${que_id}">Same Contract Link Label</label>
                                   <input type="text" class="form-control same_contract" id="same_contract_link-${que_id}" name="same_contract_link-${que_id}" value="">
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="">Different Contract Link</label>
                                   <div class="append_cont_btn" id="append_cont_btn{{ $docQues->id ?? '' }}"></div>
                              </div>
                              <div class="add_cont_rw" id="add_cont_rw${que_id}"></div>
                              <div class="text-end">
                                   <div class="form-group">
                                        <!-- <button type="button" class="btn btn-sm btn-primary" onclick="addContractRow('${que_id}')">Add Row</button> -->
                                        <span class="remove_icon add_icon" onclick="addContractRow('${que_id}','')"><i class="fa-solid fa-add"></i></span>
                                   </div>
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="text_go_to_step-${que_id}">Go to step</label>
                                   <div class="form-control-wrap">
                                        <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${que_id}" id="text_go_to_step-${que_id}">
                                             <option value="0">Checkout</option>
                                             @if(isset($questions) && $questions != null)
                                                  @foreach($questions as $question)
                                                       <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                  @endforeach
                                             @endif
                                        </select>
                                   </div>
                              </div>
                         </div>
                         <hr>`
     };

     // CHANGE QUESTION TYPES
     function ChangeQuestionType(e) {
          var que_id = $(e).data("que-id");
          var change_from = $(e).data("change-from");
          var change_to = $(e).val();
          $(e).data("change-from", change_to);
          var hiddenInput = $("#changed_question_types");
          var existingChanges = JSON.parse(hiddenInput.val());
          const newUniqueId = Date.now();
          let html = ``;
          let head_label = "";
          let mainBoxID = `.custom_box_${que_id}`;

          var closest= fieldClassMap[change_from] ;
          var append_to = fieldClassMap[change_to] ;

          var main = $(e).closest(`.${fieldClassMap[change_from]}`);
          main.removeClass(`${fieldClassMap[change_from]}`).addClass(`${fieldClassMap[change_to]}`);

          if ((change_from === "dropdown" && change_to === "radio-button") || (change_from === "radio-button" && change_to === "dropdown")) {
               console.log('change from', change_from);
               console.log('change to', change_to);
               var all_options = $(`${mainBoxID} .append_options .${change_from}-option`);
               if (all_options.length) {
                    all_options.removeClass(`${change_from}-option`).addClass(`${change_to}-option`);

                    all_options.each(function () {
                         $(this).find(`input[name^=${change_from}_option_label]`).attr("name", `${change_to}_option_label`);
                         $(this).find(`input[name^=${change_from}_option_value]`).attr("name", `${change_to}_option_value`);
                         $(this).find(`select[name^=${change_from}_go_to_step]`).attr("name", `${change_to}_go_to_step`);
                    });
               } else {
                    console.log('kjhjghj');
                    $(mainBoxID).html(fieldTemplates[change_to](que_id));
               }
          }else {
               console.log('else',que_id,change_to);

               if (fieldTemplates[change_to]) {
                    $(mainBoxID).html(fieldTemplates[change_to](que_id));
                    $('#hide_question_label'+que_id).show();
                    $('.cond_div'+que_id).show();

                    if(change_to === 'dropdown-link') {
                         let label = $('#text_qu_label-'+que_id).val();
                         $('#hide_question_label'+que_id).hide();
                         $('.cond_div'+que_id).hide();
                         let goToConditionBtn = $('.go_to_step'+que_id);
                         if(goToConditionBtn){
                              goToConditionBtn.hide();
                         }
                         $('.dropdown_ques').val(label);
                    }
               }
          }

          var hiddenInput = $("#changed_question_types");
          var existingChanges = JSON.parse(hiddenInput.val() || "[]");
          var foundIndex = existingChanges.findIndex((q) => q.que_id === que_id);

          if (foundIndex !== -1) {
               existingChanges[foundIndex].change_to = change_to;
          } else {
               existingChanges.push({ que_id: que_id, change_from: change_from, change_to: change_to });
          }

          hiddenInput.val(JSON.stringify(existingChanges));

          let option_name = '';
          if(change_to == "textbox"){
               option_name = 'Textbox';
          }else if(change_to == "textarea"){
               option_name = 'Textarea';
          }else if(change_to == "dropdown"){
               option_name = 'Dropdown';
          }else if(change_to == "radio-button"){
               option_name = 'Radio button';
          }else if(change_to == "date-field"){
               option_name = 'Date field';
          }else if(change_to == "pricebox"){
               option_name = 'Price Box';
          }else if(change_to == "number-field"){
               option_name = 'Number field';
          }else if(change_to == "percentage-box"){
               option_name = 'Percentage Box';
          }else if(change_to == "dropdown-link"){
               option_name = 'Dropdown Link';
          }

          $(e).closest('.col-md-6').find('.que_type_heading').show();
          $(e).closest('.col-md-6').find('.que_type_heading').html(`<p class="drop_options"><b>${option_name} <em class="icon ni ni-edit drop_options"></em></b></p>`);
          $(e).closest('.col-md-6').find('.drop_box_option').hide();

          return ;
     }


     // Add the options inside the dropdown ADD OPP
     function addOptions(value,id){
          let uID = Date.now();
          let html = ``;
          // $('#append_options' + id + ' .add_icon').hide();
          $('.firstOptBtn').hide();

          if(value === 'dropdown'){
               let optionLabel = $('#template-text').html()
                    .replace(/__ID__/g, `dropdown_option_label-${uID}`)
                    .replace(/__CLASS__/g, 'form-control')
                    .replace(/__NAME__/g, `dropdown_option_label-${uID}[]`)
                    .replace(/__LABEL__/g, 'Label');

               let optionValue = $('#template-text').html()
                    .replace(/__ID__/g, `dropdown_option_value-${uID}`)
                    .replace(/__CLASS__/g, 'form-control')
                    .replace(/__NAME__/g, `dropdown_option_value-${uID}[]`)
                    .replace(/__LABEL__/g, 'Value');

               let optionGoTo = $('#template-select').html()
                    .replace(/__ID__/g, `dropdown_go_to_step-${uID}`)
                    .replace(/__CLASS__/g, 'js-select2 new_label_question_id')
                    .replace(/__NAME__/g, `dropdown_go_to_step-${uID}[]`)
                    .replace(/__LABEL__/g, 'Go to Step');

               html = `<div class="dropdown-option" id="dropdown-option${uID}" value="appended" data-is_new=true>
                    <div class="inner_dropdown">
                         <div class="row">
                              <div class="col-md-3">
                                   <div class="form-group">
                                        <!-- <label class="form-label" for="dropdown_option_label-${uID}">Label</label>
                                        <input type="text" class="form-control" id="dropdown_option_label-${uID}" name="dropdown_option_label-${uID}[]" value=""> -->
                                        ${optionLabel}
                                   </div>
                              </div>
                              <div class="col-md-4">
                                   <div class="form-group">
                                        <!-- <label class="form-label" for="dropdown_option_value-${uID}">Value</label>
                                        <input type="text" class="form-control" id="dropdown_option_value-${uID}" name="dropdown_option_value-${uID}[]" value=""> -->
                                        ${optionValue}
                                   </div>
                              </div>
                              <div class="col-md-3">
                                   <div class="form-group">
                                        <!-- <label class="form-label" for="dropdown_go_to_step-${uID}">Go to Step</label>
                                        <div class="form-control-wrap">
                                             <select class="form-select js-select2 new_label_question_id" data-search="on" name="dropdown_go_to_step-${uID}[]" id="dropdown_go_to_step-${uID}">
                                                  <option value="0">Checkout</option>
                                                  @if(isset($questions) && $questions != null)
                                                       @foreach($questions as $question)
                                                            <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                       @endforeach
                                                  @endif
                                             </select>
                                        </div> -->
                                        ${optionGoTo}
                                   </div>
                              </div>
                              <div class="col-md-2 add_rmv_icn23">
                                   <div class="form-group prnt_add_cls">
                                        <span class="remove_icon red_hover" onclick="removeOptions(this)" data-field="${value}" value="appended">
                                             <i class="fa fa-trash"></i>
                                        </span>
                                   </div>
                                   <div class="form-group prnt_add_cls">
                                        <span class="remove_icon add_icon" onclick="addOptions('dropdown','${id}')"><i class="fa-solid fa-add"></i></span>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <br>
               </div>`;
          }else if(value === 'radio-button'){
               let optionLabel = $('#template-text').html()
                    .replace(/__ID__/g, `radio_option_label-${uID}`)
                    .replace(/__CLASS__/g, 'form-control')
                    .replace(/__NAME__/g, `radio_option_label-${uID}[]`)
                    .replace(/__LABEL__/g, 'Label');

               let optionValue = $('#template-text').html()
                    .replace(/__ID__/g, `radio_option_value-${uID}`)
                    .replace(/__CLASS__/g, 'form-control')
                    .replace(/__NAME__/g, `radio_option_value-${uID}[]`)
                    .replace(/__LABEL__/g, 'Value');

               let optionGoTo = $('#template-select').html()
                    .replace(/__ID__/g, `radio_go_to_step-${uID}`)
                    .replace(/__CLASS__/g, 'js-select2 new_label_question_id')
                    .replace(/__NAME__/g, `radio_go_to_step-${uID}[]`)
                    .replace(/__LABEL__/g, 'Go to Step');

               html = `<div class="radio-option" id="radio-option${uID}" value="appended" data-is_new=true>
                    <div class="inner_radio">
                         <div class="row">
                              <div class="col-md-3">
                                   <div class="form-group">
                                        <!-- <label class="form-label" for="radio_option_label-${uID}">Label</label>
                                        <input type="text" class="form-control" id="radio_option_label-${uID}" name="radio_option_label-${uID}[]" value=""> -->
                                        ${optionLabel}
                                   </div>
                              </div>
                              <div class="col-md-4">
                                   <div class="form-group">
                                        <!-- <label class="form-label" for="radio_option_value-${uID}">Value</label>
                                        <input type="text" class="form-control" id="radio_option_value-${uID}" name="radio_option_value-${uID}[]" value=""> -->
                                        ${optionValue}
                                   </div>
                              </div>
                              <div class="col-md-3">
                                   <div class="form-group">
                                        <!-- <label class="form-label" for="radio_go_to_step-${uID}">Go to Step</label>
                                        <div class="form-control-wrap">
                                             <select class="form-select js-select2 new_label_question_id" data-search="on" name="radio_go_to_step-${uID}[]" id="radio_go_to_step-${uID}">
                                                  <option value="0">Checkout</option>
                                                  @if(isset($questions) && $questions != null)
                                                       @foreach($questions as $question)
                                                            <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                       @endforeach
                                                  @endif
                                             </select>
                                        </div> -->
                                        ${optionGoTo}
                                   </div>
                              </div>
                              <div class="col-md-2 add_rmv_icn24">
                              <div class="form-group prnt_add_cls">
                                        <span class="remove_icon red_hover" onclick="removeOptions(this)" data-field="${value}" value="appended">
                                             <i class="fa fa-trash"></i>
                                        </span>
                                   </div>
                                   <div class="form-group prnt_add_cls">
                                        <span class="remove_icon add_icon" onclick="addOptions('radio-button','${id}')"><i class="fa-solid fa-add"></i></span>
                                   </div>
                              </div
                         </div>
                    </div>
                    <br>
               </div>`;
          }
          $('#append_options'+id).append(html);

     }




     // Remove the options
     function removeOptions(e){

          if($(e).attr('data-field') === 'dropdown'){

               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.dropdown-option').remove();


                              const parentDiv = $('.append_options');

                              if(parentDiv.find('.dropdown-option:visible').length === 0) {
                                   console.log('append if');

                                   $('.firstOptBtn').show();
                              }else{
                                   parentDiv.find('.dropdown-option ').closest('.add_icon').show();
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#option_id').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#option_id').val(deleteIds);
                              $('#dropdown-option'+id).hide();
                              // $('.append_options .add_icon').show();

                              const container = $(e).closest('.append_options');
                              const uniqueId = $(container).attr('id').replace('append_options', '');


                              if(container.find('.dropdown-option:visible').length > 0){
                                   console.log('value if');

                                   $(container).find('.dropdown-option:visible .prnt_add_cls').show();

                              }else{
                                   console.log('value else')

                                   container.find('.dropdown-option ').closest('.add_icon').show();
                              }
                         }


                    });
               }

          }else if($(e).attr('data-field') === 'radio-button'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.radio-option').remove();
                              $('.append_options .add_icon').show();
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#option_id').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#option_id').val(deleteIds);
                              $('#radio-option'+id).hide();


                              const container = $(e).closest('.append_options');
                              const uniqueId = $(container).attr('id').replace('append_options', '');


                              if(container.find('.radio-option:visible').length > 0){

                                   $(container).find('.radio-option:visible .prnt_add_cls').show();

                              }else{
                                   $('.append_options .add_icon').show();
                              }
                         }
                    });
               }
          }
     }

     function addContractRow(id){
          console.log(id);
          // const newUniqueId = Date.now();
          // $('#contract-option' + id + ' .add_icon').hide();
          let contractLabel = $('#template-text').html()
               .replace(/__ID__/g, `dropdown_link_label${id}`)
               .replace(/__CLASS__/g, 'form-control')
               .replace(/__NAME__/g, `dropdown_link_label${id}[]`)
               .replace(/__LABEL__/g, 'Label');

          let contractLink = $('#template-text').html()
               .replace(/__ID__/g, `contract_link${id}`)
               .replace(/__CLASS__/g, 'form-control')
               .replace(/__NAME__/g, `contract_link${id}[]`)
               .replace(/__LABEL__/g, 'Contract Link');

          const html = `<div class="contract-option" id="contract-option${id}" value="appended" data-is_new=true>
                              <div class="row">
                                   <div class="col-md-5">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="dropdown_link_label${id}">Label</label>
                                             <input type="text" class="form-control" id="dropdown_link_label${id}" name="dropdown_link_label${id}[]" value=""> -->
                                             ${contractLabel}
                                        </div>
                                   </div>
                                   <div class="col-md-5">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="contract_link${id}">Contract Link</label>
                                             <input type="text" class="form-control" id="contract_link${id}" name="contract_link${id}[]" value=""> -->
                                             ${contractLink}
                                        </div>
                                   </div>
                                   <div class="col-md-2 add_rmv_icn25">
                                        <div class="form-group prnt_add_cls">
                                             <span class="remove_icon red_hover" onclick="removeContract(this)" value="appended">
                                                  <i class="fa fa-trash"></i>
                                             </span>
                                        </div>
                                        <div class="form-group prnt_add_cls">
                                             <span class="remove_icon add_icon" onclick="addContractRow('${id}','')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                   </div>


                                   <!-- <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="">Send to next step</label>
                                             <div class="custom-control custom-checkbox checked">
                                                  <input type="checkbox" class="custom-control-input" id="contract_send_next_step${id}" name="contract_send_next_step${id}[]">
                                                  <label class="custom-control-label" for="contract_send_next_step${id}"></label>
                                             </div>
                                        </div>
                                   </div> -->
                              </div>

                         </div>`;
          $('#add_cont_rw'+id).append(html);
          $('.contract_btn'+id).hide();
     }

     function removeContract(e){
          if($(e).attr('value') === 'appended'){
               const uniqueId = $(e).closest('.contract-option').attr('id').replace('contract-option', '');
               console.log(uniqueId);
               Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
               }).then((result) => {
                    if(result.isConfirmed){
                         $(e).closest('.contract-option').remove();

                         const parentDiv = $('#add_cont_rw' + uniqueId);
                         console.log(parentDiv);

                         if(parentDiv.find('.contract-option:visible').length === 0) {

                              console.log('inside the parentDiv');

                              if($('.contract_btn' + uniqueId).length) {
                                   console.log('if append');

                                   $('.contract_btn' + uniqueId).show();
                              }else {
                                   console.log('else append');

                                   let html = `<div class="text-end">
                                        <div class="form-group">
                                             <span class="remove_icon add_icon contract_btn${uniqueId}" onclick="addContractRow('${uniqueId}','')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                   </div>`;

                                   $('#append_cont_btn'+uniqueId).html(html);
                              }
                         }
                    }
               });
          }else{

               Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
               }).then((result) => {
                    if(result.isConfirmed){
                         var id = $(e).data('id');
                         let deleteIds = $('#option_id').val();
                         if(deleteIds){
                              deleteIds += ',' + id;
                         }else{
                              deleteIds = id;
                         }
                         $('#option_id').val(deleteIds);
                         $('#contract-option'+id).hide();

                         const container = $(e).closest('.add_cont_rw');
                         const uniqueId = $(container).attr('id').replace('add_cont_rw', '');

                         console.log(uniqueId);
                         if(container.find('.contract-option:visible').length === 0){
                              console.log('if id ');
                              let html = `<div class="text-end">
                                   <div class="form-group">
                                        <span class="remove_icon add_icon contract_btn${uniqueId}" onclick="addContractRow('${uniqueId}','')"><i class="fa-solid fa-add"></i></span>
                                   </div>
                              </div>`;

                              $('#append_cont_btn'+uniqueId).html(html);
                         }else{
                              console.log('else id');
                         }

                    }
               });
          }
     }


     let step_count = 0;
     // Add the new step code
     function addSteps(){
          const uniqueDropdownId = 'dropdown_' + Date.now();
          const unqId = Date.now();
          step_count++ ;
          const html = `<div class="steps_section" id="steps_section${step_count}">
               <div class="card card-bordered card-preview">
                    <div class="card-inner">
                         <div class="row add_step">
                              <div class="col-md-6">
                                   <h6>Add Steps </h6>
                              </div>
                              <div class="col-md-6">
                                   <div class="form-group">
                                        <span class="col-md-2 offset-md-10">
                                             <span class="remove_steps" value="appended"><i class="fa fa-trash"></i></span>
                                        </span>
                                   </div>
                              </div>
                         </div>
                         <!-- <hr> -->

                         <div class="add_qu_sec" id="add_qu_sec_${unqId}"></div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="">Add Questions</label>
                              </div>
                         </div>
                         <div class="text-end">
                              <button type="button" class="btn btn-sm btn-primary question_dropbtn" onclick="toggleDropdown('${uniqueDropdownId}')">Add Question</button>
                              <div class="form-group question_dropdown">
                                   <div id="${uniqueDropdownId}" class="question_dropdown-content">
                                        @foreach($types as $type)
                                             <a onclick="addQuestionfields('{{ $type->slug ?? '' }}','${unqId}')">{{ $type->name ?? '' }}</a>
                                        @endforeach
                                   </div>
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="question_info_text">Question Info Text</label>
                                   <textarea class="form-control question_info_text" id="question_info_text" name="question_info_text"></textarea>
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="">Form submit handler for generating pdf</label>
                              </div>
                         </div>
                         <div class="custom-control custom-checkbox checked">
                              <input type="checkbox" class="custom-control-input" id="last_step" name="last_step">
                              <label class="custom-control-label" for="last_step">Please check this box if you are on the last step</label>
                         </div>
                    </div>
               </div><br>
               </div>`;

          $('.add_steps').append(html);
     }


     // This is the question field
     let textbox_count = 0;
     let textarea_count = 0;
     let dropdown_count = 0;
     let radio_count = 0;
     let datefield_count = 0;
     let pricebox_count = 0;
     let numberfield_count = 0;
     let percentage_count = 0;
     let droplink_count = 0;
     let num = "{{ $order ?? '' }}";
    //  ADD
     function addQuestionfields(name,id,key,element=null){
          // console.log(name,id);
          const newUniqueId = Date.now();
          let html = ``;
          const types = @json($types);
          let questionLabelHtml = $('#template-text').html()
               .replace(/__ID__/g, `text_qu_label-${newUniqueId}`)
               .replace(/__CLASS__/g, 'form-control question_labl')
               .replace(/__NAME__/g, `text_qu_label-${newUniqueId}`)
               .replace(/__LABEL__/g, 'Question Label');

          let infoTextHtml = $('#template-text').html()
               .replace(/__ID__/g, 'question_info_text')
               .replace(/__CLASS__/g, 'form-control question_info_text')
               .replace(/__NAME__/g, 'question_info_text')
               .replace(/__LABEL__/g, 'Question Info Text');

          let placeholderHtml = $('#template-textarea').html()
               .replace(/__ID__/g, `text_placeholder-${newUniqueId}`)
               .replace(/__CLASS__/g, 'form-control text_box_placeholder')
               .replace(/__NAME__/g, `text_placeholder-${newUniqueId}`)
               .replace(/__LABEL__/g, 'Text Box Placeholder');

          let numberPlaceholderHtml = $('#template-textarea').html()
               .replace(/__ID__/g, `text_placeholder-${newUniqueId}`)
               .replace(/__CLASS__/g, 'form-control number_placeholder')
               .replace(/__NAME__/g, `text_placeholder-${newUniqueId}`)
               .replace(/__LABEL__/g, 'Number Field Placeholder');

          let sameContractHtml = $('#template-textarea').html()
               .replace(/__ID__/g, `same_contract_link-${newUniqueId}`)
               .replace(/__CLASS__/g, 'form-control same_contract')
               .replace(/__NAME__/g, `same_contract_link-${newUniqueId}`)
               .replace(/__LABEL__/g, 'Same Contract Link Label');

          let goToStepHtml = $('#template-select').html()
               .replace(/__ID__/g, `text_go_to_step-${newUniqueId}`)
               .replace(/__CLASS__/g, 'js-select2 new_label_question_id')
               .replace(/__NAME__/g, `text_go_to_step-${newUniqueId}`)
               .replace(/__LABEL__/g, 'Go to step');

          let conditionQuestionIDHtml = $('#template-question_select').html()
               .replace(/__ID__/g, `page_Setting_qu_id-${newUniqueId}`)
               .replace(/__CLASS__/g, 'js-select2')
               .replace(/__NAME__/g, `page_Setting_qu_id-${newUniqueId}[]`)
               .replace(/__LABEL__/g, 'Question ID');

          let conditionOptionHtml = $('#template-condition-select').html()
               .replace(/__ID__/g, `page_Setting_conditions-${newUniqueId}`)
               .replace(/__CLASS__/g, 'js-select2')
               .replace(/__NAME__/g, `page_Setting_conditions-${newUniqueId}[]`)
               .replace(/__LABEL__/g, 'Condition');

          let conditionValue = $('#template-text').html()
               .replace(/__ID__/g, `page_Setting_qu_val-${newUniqueId}`)
               .replace(/__CLASS__/g, 'form-control')
               .replace(/__NAME__/g, `page_Setting_qu_val-${newUniqueId}[]`)
               .replace(/__LABEL__/g, 'Value');

          let conditionGoToHtml = $('#template-select').html()
               .replace(/__ID__/g, `conditional_go_to_step-${condition_count}`)
               .replace(/__CLASS__/g, 'js-select2')
               .replace(/__NAME__/g, `conditional_go_to_step-${condition_count}`)
               .replace(/__LABEL__/g, 'Conditional Go to Step');

          if(name === 'textbox'){
               textbox_count++ ;

               html = `<div class="new_que_sec${newUniqueId}" id="for_copy_sec${newUniqueId}">
                    <div class="append_textbox" id="append_textbox${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner main_question_div">
                                   <div class="row add_step">
                                        <div class="col-md-6">
                                             <!-- <h6>Textbox</h6> -->
                                             <div class="form-group">
                                                  <select class="form-select js-select2 type_question" name="question_type${newUniqueId}" id="question_type${newUniqueId}">
                                                       ${types.map(type => `
                                                       <option value="${type.slug}" ${name === type.slug ? 'selected' : ''}>${type.name}</option>
                                                       `).join('')}
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                  <span class="col-md-2 offset-md-10">
                                                       <span class="remove_icon red_hover" onclick="removeFields(this)" value="appended" data-field="textbox"><i class="fa fa-trash"></i></span>
                                                  </span>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="cond_ques_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add conditional questions label</label>
                                             </div>
                                        </div>
                                        <!-- <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div> -->
                                        <div class="text-end">
                                             <div class="form-group">
                                                  <button type="button" class="btn btn-sm btn-primary" onclick="addLabel('${newUniqueId}','')">Add Label</button>
                                             </div>
                                        </div>
                                        <hr>
                                   </div>
                                   <div class="row hide_question_label" id="hide_question_label${newUniqueId}">
                                        <div class="col-md-10 form-group qu_label_cls${newUniqueId} label_qu">
                                             <!-- <label class="form-label" for="text_qu_label-${newUniqueId}">Question Label</label>
                                             <input type="text" class="form-control question_labl" id="text_qu_label-${newUniqueId}" name="text_qu_label-${newUniqueId}" value=""> -->
                                             ${questionLabelHtml}
                                        </div>
                                        <div class="col-md-2 form-group prnt_add_cls qu_label_btn${newUniqueId}">
                                             <span class="remove_icon add_icon" onclick="addLabel('${newUniqueId}','')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                        <!-- <hr> -->
                                        <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="text_placeholder-${newUniqueId}">Text Box Placeholder</label>
                                             <input type="text" class="form-control text_box_placeholder" id="text_placeholder-${newUniqueId}" name="text_placeholder-${newUniqueId}" value=""> -->
                                             ${placeholderHtml}
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="text_go_to_step-${newUniqueId}">Go to step</label>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${newUniqueId}" id="text_go_to_step-${newUniqueId}">
                                                       <option value="0">Checkout</option>
                                                       @if(isset($questions) && $questions != null)
                                                            @foreach($questions as $question)
                                                                 <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                            @endforeach
                                                       @endif
                                                  </select>
                                             </div> -->
                                             ${goToStepHtml}
                                        </div>
                                   </div>
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step${newUniqueId}" onclick="addGoToStep('${newUniqueId}')">Add Condition</button>
                                   </div>
                                   <div class="cond_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add Conditions</label>
                                             </div>
                                        </div>

                                        <div class="append_page_condition" id="append_page_condition${newUniqueId}">
                                             <div class="sec-condition" id="sec-condition${newUniqueId}" value="appended" data-is_new=true>
                                                  <div class="row">
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_id-${newUniqueId}">Question ID</label>
                                                                 <div class="form-control-wrap question">
                                                                      <select class="form-select js-select2" data-search="on" name="page_Setting_qu_id-${newUniqueId}[]" id="page_Setting_qu_id-${newUniqueId}">
                                                                           @if(isset($questions) && $questions != null)
                                                                                @foreach($questions as $question)
                                                                                     <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                                @endforeach
                                                                           @endif
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionQuestionIDHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-4">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_conditions-${newUniqueId}">Condition</label>
                                                                 <div class="form-control-wrap">
                                                                      <select class="form-select js-select2" name="page_Setting_conditions-${newUniqueId}[]" id="page_Setting_conditions-${newUniqueId}">
                                                                           <option value="" selected disabled>Select</option>
                                                                           <option value="is_equal_to">is equal to</option>
                                                                           <option value="is_greater_than">is greater than</option>
                                                                           <option value="is_less_than">is less than</option>
                                                                           <option value="not_equal_to">not equal to</option>
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionOptionHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_val-${newUniqueId}">Value</label>
                                                                 <input type="text" class="form-control" id="page_Setting_qu_val-${newUniqueId}" name="page_Setting_qu_val-${newUniqueId}[]" value=""> -->
                                                                 ${conditionValue}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-2 form-group prnt_add_cls">
                                                            <span class="remove_icon add_icon" onclick="addCondition('${newUniqueId}')"><i class="fa-solid fa-plus"></i></span>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        <br>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <!-- <label class="form-label" for="conditional_go_to_step-${condition_count}">Conditional Go to Step</label>
                                                  <div class="form-control-wrap">
                                                       <select class="form-select js-select2" data-search="on" name="conditional_go_to_step-${condition_count}" id="conditional_go_to_step-${condition_count}">
                                                            <option value="0">Checkout</option>
                                                            @if(isset($questions) && $questions != null)
                                                                 @foreach($questions as $question)
                                                                      <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                 @endforeach
                                                            @endif
                                                       </select>
                                                  </div> -->

                                                  ${conditionGoToHtml}
                                             </div>
                                        </div>
                                        <div class="independent_cond_container" id="independent_cond_container${newUniqueId}"></div>
                                        <hr>
                                        <div class="another_cond_div${newUniqueId}">
                                             <div class="text-end">
                                                  <div class="form-group">
                                                       <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('${newUniqueId}')">Add Condition</button>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="question_info_text">Question Info Text</label>
                                             <textarea class="form-control question_info_text" id="question_info_text" name="question_info_text"></textarea> -->
                                             ${infoTextHtml}
                                        </div>
                                   </div>
                                   <!-- <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Form submit handler for generating pdf</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox checked">
                                        <input type="checkbox" class="custom-control-input" id="is_end${newUniqueId}" name="is_end${newUniqueId}">
                                        <label class="custom-control-label" for="is_end${newUniqueId}">Please check this box if you are on the last
                                             step</label>
                                   </div> -->
                              </div>
                         </div>
                         <br>
                    </div>
                    </div>`;
          }else if(name === 'textarea'){
               textarea_count++ ;

               html =`<div class="new_que_sec${newUniqueId}" id="for_copy_sec${newUniqueId}">
                    <div class="append_textarea" id="append_textarea${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="row add_step">
                                        <div class="col-md-6">
                                             <!-- <h6>Textarea</h6> -->
                                             <div class="form-group">
                                                  <select class="form-select js-select2 type_question" name="question_type${newUniqueId}" id="question_type${newUniqueId}">
                                                       ${types.map(type => `
                                                       <option value="${type.slug}" ${name === type.slug ? 'selected' : ''}>${type.name}</option>
                                                       `).join('')}
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                  <span class="col-md-2 offset-md-10">
                                                       <span class="remove_icon red_hover" onclick="removeFields(this)" value="appended" data-field="textarea"><i class="fa fa-trash"></i></span>
                                                  </span>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <!-- <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Conditional questions label</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input add_conditional_label" id="condition_qu_label${newUniqueId}" name="condition_qu_label${newUniqueId}">
                                        <label class="custom-control-label" for="condition_qu_label${newUniqueId}">Conditional questions label</label>
                                   </div>
                                   <hr> -->
                                   <div class="cond_ques_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add conditional questions label</label>
                                             </div>
                                        </div>
                                        <!-- <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div> -->
                                        <div class="text-end">
                                             <div class="form-group">
                                                  <button type="button" class="btn btn-sm btn-primary" onclick="addLabel('${newUniqueId}','')">Add Label</button>
                                             </div>
                                        </div>
                                        <hr>
                                   </div>
                                   <div class="row hide_question_label" id="hide_question_label${newUniqueId}">
                                        <div class="col-md-10 form-group qu_label_cls${newUniqueId} label_qu">
                                             <!-- <label class="form-label" for="text_qu_label-${newUniqueId}">Question Label</label>
                                             <input type="text" class="form-control question_labl" id="text_qu_label-${newUniqueId}" name="text_qu_label-${newUniqueId}"> -->
                                             ${questionLabelHtml}
                                        </div>
                                        <div class="col-md-2 form-group prnt_add_cls qu_label_btn${newUniqueId}">
                                             <span class="remove_icon add_icon" onclick="addLabel('${newUniqueId}','')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                        <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div>
                                        <!-- <hr> -->
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="text_placeholder-${newUniqueId}">Text Box Placeholder</label>
                                             <input type="text" class="form-control text_box_placeholder" id="text_placeholder-${newUniqueId}" name="text_placeholder-${newUniqueId}"> -->
                                             ${placeholderHtml}
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="text_go_to_step-${newUniqueId}">Go to step</label>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${newUniqueId}" id="text_go_to_step-${newUniqueId}">
                                                       <option value="0">Checkout</option>
                                                       @if(isset($questions) && $questions != null)
                                                            @foreach($questions as $question)
                                                                 <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                            @endforeach
                                                       @endif
                                                  </select>
                                             </div> -->
                                             ${goToStepHtml}
                                        </div>
                                   </div>
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step${newUniqueId}" onclick="addGoToStep('${newUniqueId}')">Add Condition</button>
                                   </div>
                                   <div class="cond_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add Conditions</label>
                                             </div>
                                        </div>

                                        <div class="append_page_condition" id="append_page_condition${newUniqueId}">
                                             <div class="sec-condition" id="sec-condition${newUniqueId}" value="appended" data-is_new=true>
                                                  <div class="row">
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_id-${newUniqueId}">Question ID</label>
                                                                 <div class="form-control-wrap question">
                                                                      <select class="form-select js-select2" data-search="on" name="page_Setting_qu_id-${newUniqueId}[]" id="page_Setting_qu_id-${newUniqueId}">
                                                                           @if(isset($questions) && $questions != null)
                                                                                @foreach($questions as $question)
                                                                                     <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                                @endforeach
                                                                           @endif
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionQuestionIDHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-4">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_conditions-${newUniqueId}">Condition</label>
                                                                 <div class="form-control-wrap">
                                                                      <select class="form-select js-select2" name="page_Setting_conditions-${newUniqueId}[]" id="page_Setting_conditions-${newUniqueId}">
                                                                           <option value="" selected disabled>Select</option>
                                                                           <option value="is_equal_to">is equal to</option>
                                                                           <option value="is_greater_than">is greater than</option>
                                                                           <option value="is_less_than">is less than</option>
                                                                           <option value="not_equal_to">not equal to</option>
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionOptionHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_val-${newUniqueId}">Value</label>
                                                                 <input type="text" class="form-control" id="page_Setting_qu_val-${newUniqueId}" name="page_Setting_qu_val-${newUniqueId}[]" value=""> -->
                                                                 ${conditionValue}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-2 form-group prnt_add_cls">
                                                            <span class="remove_icon add_icon" onclick="addCondition('${newUniqueId}')"><i class="fa-solid fa-plus"></i></span>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        <br>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <!-- <label class="form-label" for="conditional_go_to_step-${condition_count}">Conditional Go to Step</label>
                                                  <div class="form-control-wrap">
                                                       <select class="form-select js-select2" data-search="on" name="conditional_go_to_step-${condition_count}" id="conditional_go_to_step-${condition_count}">
                                                            <option value="0">Checkout</option>
                                                            @if(isset($questions) && $questions != null)
                                                                 @foreach($questions as $question)
                                                                      <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                 @endforeach
                                                            @endif
                                                       </select>
                                                  </div> -->
                                                  ${conditionGoToHtml}
                                             </div>
                                        </div>
                                        <div class="independent_cond_container" id="independent_cond_container${newUniqueId}"></div>
                                        <hr>
                                        <div class="another_cond_div${newUniqueId}">
                                             <div class="text-end">
                                                  <div class="form-group">
                                                       <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('${newUniqueId}')">Add Condition</button>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="question_info_text">Question Info Text</label>
                                             <textarea class="form-control question_info_text" id="question_info_text" name="question_info_text"></textarea> -->
                                             ${infoTextHtml}
                                        </div>
                                   </div>
                                   <!-- <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Form submit handler for generating pdf</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox checked">
                                        <input type="checkbox" class="custom-control-input" id="is_end${newUniqueId}" name="is_end${newUniqueId}">
                                        <label class="custom-control-label" for="is_end${newUniqueId}">Please check this box if you are on the last
                                             step</label>
                                   </div>-->
                              </div>
                         </div>
                         <br>
                    </div>
                    </div>`;
          }else if(name === 'dropdown'){
               dropdown_count++ ;
               html = `<div class="new_que_sec${newUniqueId}" id="for_copy_sec${newUniqueId}">
                         <div class="append_dropdown" id="append_dropdown${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <div class="row add_step">
                                             <div class="col-md-6">
                                                  <div class="form-group">
                                                       <select class="form-select js-select2 type_question"
                                                            name="question_type${newUniqueId}" id="question_type${newUniqueId}">
                                                            ${types.map(type => `
                                                            <option value="${type.slug}" ${name===type.slug ? 'selected' : '' }>${type.name}
                                                            </option>
                                                            `).join('')}
                                                       </select>
                                                  </div>
                                             </div>
                                             <div class="col-md-6">
                                                  <div class="form-group">
                                                       <span class="col-md-2 offset-md-10">
                                                            <span class="remove_icon red_hover" onclick="removeFields(this)" value="appended" data-field="dropdown"><i class="fa fa-trash"></i></span>
                                                       </span>
                                                  </div>
                                             </div>
                                        </div>
                                        <hr>

                                        <div class="cond_ques_div${newUniqueId}" style="display:none;">
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="">Add conditional questions label</label>
                                                  </div>
                                             </div>
                                             <div class="text-end">
                                                  <div class="form-group">
                                                       <button type="button" class="btn btn-sm btn-primary"
                                                            onclick="addLabel('${newUniqueId}','')">Add Label</button>
                                                  </div>
                                             </div>
                                             <hr>
                                        </div>
                                        <div class="row hide_question_label" id="hide_question_label${newUniqueId}">
                                             <div class="col-md-10 form-group qu_label_cls${newUniqueId} label_qu">
                                                  <!-- <label class="form-label" for="text_qu_label-${newUniqueId}">Question Label</label>
                                                  <input type="text" class="form-control question_labl" id="text_qu_label-${newUniqueId}"
                                                       name="text_qu_label-${newUniqueId}"> -->
                                                  ${questionLabelHtml}
                                             </div>
                                             <div class="col-md-2 form-group prnt_add_cls qu_label_btn${newUniqueId}">
                                                  <span class="remove_icon add_icon" onclick="addLabel('${newUniqueId}','')">
                                                  <i class="fa-solid fa-add"></i></span>
                                             </div>
                                             <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div>

                                        </div>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add Dropdown Option</label>
                                             </div>
                                             <div class="append_options" id="append_options${newUniqueId}"></div>
                                             <div class="text-end firstOptBtn">
                                                  <div class="form-group">
                                                  <!-- <button type="button" class="btn btn-sm btn-primary"
                                                            onclick="addOptions('${name}','${newUniqueId}')">Add Option</button> -->
                                                  <span class="remove_icon add_icon" onclick="addOptions('${name}','${newUniqueId}')"><i
                                                            class="fa-solid fa-add"></i></span>
                                                  </div>
                                             </div>
                                        </div>
                                        <hr>
                                        <div class="grey_btn_div">
                                             <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step${newUniqueId}" onclick="addGoToStep('${newUniqueId}')">Add Condition</button>
                                        </div>
                                        <div class="cond_div${newUniqueId}" style="display:none;">
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <label class="form-label" for="">Add Conditions</label>
                                                  </div>
                                             </div>

                                             <div class="append_page_condition" id="append_page_condition${newUniqueId}">
                                                  <div class="sec-condition" id="sec-condition${newUniqueId}" value="appended" data-is_new=true>
                                                       <div class="row">
                                                            <div class="col-md-3">
                                                                 <div class="form-group">
                                                                      <!-- <label class="form-label" for="page_Setting_qu_id-${newUniqueId}">Question ID</label>
                                                                      <div class="form-control-wrap question">
                                                                           <select class="form-select js-select2" data-search="on" name="page_Setting_qu_id-${newUniqueId}[]" id="page_Setting_qu_id-${newUniqueId}">
                                                                                @if(isset($questions) && $questions != null)
                                                                                     @foreach($questions as $question)
                                                                                          <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                                     @endforeach
                                                                                @endif
                                                                           </select>
                                                                      </div> -->
                                                                      ${conditionQuestionIDHtml}
                                                                 </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                 <div class="form-group">
                                                                      <!-- <label class="form-label" for="page_Setting_conditions-${newUniqueId}">Condition</label>
                                                                      <div class="form-control-wrap">
                                                                           <select class="form-select js-select2" name="page_Setting_conditions-${newUniqueId}[]" id="page_Setting_conditions-${newUniqueId}">
                                                                                <option value="" selected disabled>Select</option>
                                                                                <option value="is_equal_to">is equal to</option>
                                                                                <option value="is_greater_than">is greater than</option>
                                                                                <option value="is_less_than">is less than</option>
                                                                                <option value="not_equal_to">not equal to</option>
                                                                           </select>
                                                                      </div> -->
                                                                      ${conditionOptionHtml}
                                                                 </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                 <div class="form-group">
                                                                      <!-- <label class="form-label" for="page_Setting_qu_val-${newUniqueId}">Value</label>
                                                                      <input type="text" class="form-control" id="page_Setting_qu_val-${newUniqueId}" name="page_Setting_qu_val-${newUniqueId}[]" value=""> -->
                                                                      ${conditionValue}
                                                                 </div>
                                                            </div>
                                                            <div class="col-md-2 form-group prnt_add_cls">
                                                                 <span class="remove_icon add_icon" onclick="addCondition('${newUniqueId}')"><i class="fa-solid fa-plus"></i></span>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                             <br>
                                             <div class="col-md-12">
                                                  <div class="form-group">
                                                       <!-- <label class="form-label" for="conditional_go_to_step-${condition_count}">Conditional Go to Step</label>
                                                       <div class="form-control-wrap">
                                                            <select class="form-select js-select2" data-search="on" name="conditional_go_to_step-${condition_count}" id="conditional_go_to_step-${condition_count}">
                                                                 <option value="0">Checkout</option>
                                                                 @if(isset($questions) && $questions != null)
                                                                      @foreach($questions as $question)
                                                                           <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                      @endforeach
                                                                 @endif
                                                            </select>
                                                       </div> -->
                                                       ${conditionGoToHtml}
                                                  </div>
                                             </div>
                                             <div class="independent_cond_container" id="independent_cond_container${newUniqueId}"></div>
                                             <hr>
                                             <div class="another_cond_div${newUniqueId}">
                                                  <div class="text-end">
                                                       <div class="form-group">
                                                            <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('${newUniqueId}')">Add Condition</button>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <!-- <label class="form-label" for="question_info_text">Question Info Text</label>
                                                  <textarea class="form-control question_info_text" id="question_info_text" name="question_info_text"></textarea> -->
                                                  ${infoTextHtml}
                                             </div>
                                        </div>
                                        <!-- <hr>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Form submit handler for generating pdf</label>
                                             </div>
                                        </div>
                                        <div class="custom-control custom-checkbox checked">
                                             <input type="checkbox" class="custom-control-input" id="is_end${newUniqueId}" name="is_end${newUniqueId}">
                                             <label class="custom-control-label" for="is_end${newUniqueId}">Please check this box if you are on the last step</label>
                                        </div> -->
                                   </div>
                              </div>
                              <br>
                         </div>
                    </div>`;
          }else if(name === 'radio-button'){
               radio_count++ ;
               html = `<div class="new_que_sec${newUniqueId}" id="for_copy_sec${newUniqueId}">
                    <div class="append_radio" id="append_radio${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="row add_step">
                                        <div class="col-md-6">
                                             <!-- <h6>Radio Button</h6> -->
                                             <div class="form-group">
                                                  <select class="form-select js-select2 type_question" name="question_type${newUniqueId}" id="question_type${newUniqueId}">
                                                       ${types.map(type => `
                                                       <option value="${type.slug}" ${name === type.slug ? 'selected' : ''}>${type.name}</option>
                                                       `).join('')}
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                  <span class="col-md-2 offset-md-10">
                                                       <span class="remove_icon red_hover" onclick="removeFields(this)" value="appended" data-field="radio"><i class="fa fa-trash"></i></span>
                                                  </span>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <!-- <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Conditional questions label</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input add_conditional_label" id="condition_qu_label${newUniqueId}" name="condition_qu_label${newUniqueId}">
                                        <label class="custom-control-label" for="condition_qu_label${newUniqueId}">Conditional questions label</label>
                                   </div>
                                   <hr> -->
                                   <div class="row hide_question_label" id="hide_question_label${newUniqueId}">
                                        <div class="col-md-10 form-group qu_label_cls${newUniqueId} label_qu">
                                             <!-- <label class="form-label" for="text_qu_label${newUniqueId}">Question Label</label>
                                             <input type="text" class="form-control radio_ques" id="text_qu_label${newUniqueId}" name="text_qu_label${newUniqueId}"> -->
                                             ${questionLabelHtml}
                                        </div>
                                        <div class="col-md-2 form-group prnt_add_cls qu_label_btn${newUniqueId}">
                                             <span class="remove_icon add_icon" onclick="addLabel('${newUniqueId}','')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                        <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div>
                                   </div>
                                   <!-- <hr> -->
                                   <div class="cond_ques_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add conditional questions label</label>
                                             </div>
                                        </div>
                                        <!-- <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div> -->
                                        <div class="text-end">
                                             <div class="form-group">
                                                  <button type="button" class="btn btn-sm btn-primary" onclick="addLabel('${newUniqueId}','')">Add Label</button>
                                             </div>
                                        </div>
                                        <hr>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Add Radio Option</label>
                                        </div>
                                   </div>
                                   <div class="append_options" id="append_options${newUniqueId}"></div>
                                   <div class="text-end firstOptBtn">
                                        <div class="form-group">
                                             <!-- <button type="button" class="btn btn-sm btn-primary" onclick="addOptions('${name}','${newUniqueId}')">Add Option</button> -->
                                             <span class="remove_icon add_icon" onclick="addOptions('${name}','${newUniqueId}')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step${newUniqueId}" onclick="addGoToStep('${newUniqueId}')">Add Condition</button>
                                   </div>
                                   <div class="cond_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add Conditions</label>
                                             </div>
                                        </div>

                                        <div class="append_page_condition" id="append_page_condition${newUniqueId}">
                                             <div class="sec-condition" id="sec-condition${newUniqueId}" value="appended" data-is_new=true>
                                                  <div class="row">
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_id-${newUniqueId}">Question ID</label>
                                                                 <div class="form-control-wrap question">
                                                                      <select class="form-select js-select2" data-search="on" name="page_Setting_qu_id-${newUniqueId}[]" id="page_Setting_qu_id-${newUniqueId}">
                                                                           @if(isset($questions) && $questions != null)
                                                                                @foreach($questions as $question)
                                                                                     <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                                @endforeach
                                                                           @endif
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionQuestionIDHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-4">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_conditions-${newUniqueId}">Condition</label>
                                                                 <div class="form-control-wrap">
                                                                      <select class="form-select js-select2" name="page_Setting_conditions-${newUniqueId}[]" id="page_Setting_conditions-${newUniqueId}">
                                                                           <option value="" selected disabled>Select</option>
                                                                           <option value="is_equal_to">is equal to</option>
                                                                           <option value="is_greater_than">is greater than</option>
                                                                           <option value="is_less_than">is less than</option>
                                                                           <option value="not_equal_to">not equal to</option>
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionOptionHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_val-${newUniqueId}">Value</label>
                                                                 <input type="text" class="form-control" id="page_Setting_qu_val-${newUniqueId}" name="page_Setting_qu_val-${newUniqueId}[]" value=""> -->
                                                                 ${conditionValue}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-2 form-group prnt_add_cls">
                                                            <span class="remove_icon add_icon" onclick="addCondition('${newUniqueId}')"><i class="fa-solid fa-plus"></i></span>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        <br>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <!-- <label class="form-label" for="conditional_go_to_step-${condition_count}">Conditional Go to Step</label>
                                                  <div class="form-control-wrap">
                                                       <select class="form-select js-select2" data-search="on" name="conditional_go_to_step-${condition_count}" id="conditional_go_to_step-${condition_count}">
                                                            <option value="0">Checkout</option>
                                                            @if(isset($questions) && $questions != null)
                                                                 @foreach($questions as $question)
                                                                      <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                 @endforeach
                                                            @endif
                                                       </select>
                                                  </div> -->
                                                  ${conditionGoToHtml}
                                             </div>
                                        </div>
                                        <div class="independent_cond_container" id="independent_cond_container${newUniqueId}"></div>
                                        <hr>
                                        <div class="another_cond_div${newUniqueId}">
                                             <div class="text-end">
                                                  <div class="form-group">
                                                       <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('${newUniqueId}')">Add Condition</button>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="question_info_text">Question Info Text</label>
                                             <textarea class="form-control question_info_text" id="question_info_text" name="question_info_text"></textarea> -->
                                             ${infoTextHtml}
                                        </div>
                                   </div>
                                   <!-- <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Form submit handler for generating pdf</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox checked">
                                        <input type="checkbox" class="custom-control-input" id="is_end${newUniqueId}" name="is_end${newUniqueId}">
                                        <label class="custom-control-label" for="is_end${newUniqueId}">Please check this box if you are on the last
                                             step</label>
                                   </div> -->
                              </div>
                         </div>
                         <br>
                    </div>
                    </div>`;
          }else if(name === 'date-field'){
               datefield_count++ ;
               html = `<div class="new_que_sec${newUniqueId}" id="for_copy_sec${newUniqueId}">
                    <div class="append_dateField" id="append_dateField${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="row add_step">
                                        <div class="col-md-6">
                                             <!-- <h6>Date Field</h6> -->
                                             <div class="form-group">
                                                  <select class="form-select js-select2 type_question" name="question_type${newUniqueId}" id="question_type${newUniqueId}">
                                                       ${types.map(type => `
                                                       <option value="${type.slug}" ${name === type.slug ? 'selected' : ''}>${type.name}</option>
                                                       `).join('')}
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                  <span class="col-md-2 offset-md-10">
                                                       <span class="remove_icon red_hover" onclick="removeFields(this)" value="appended" data-field="date-field"><i class="fa fa-trash"></i></span>
                                                  </span>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <!-- <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Conditional questions label</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input add_conditional_label" id="condition_qu_label${newUniqueId}" name="condition_qu_label${newUniqueId}">
                                        <label class="custom-control-label" for="condition_qu_label${newUniqueId}">Conditional questions label</label>
                                   </div>
                                   <hr> -->
                                   <div class="row hide_question_label" id="hide_question_label${newUniqueId}">
                                        <div class="col-md-10 form-group qu_label_cls${newUniqueId} label_qu">
                                             <!-- <label class="form-label" for="text_qu_label-${newUniqueId}">Question Label</label>
                                             <input type="text" class="form-control date_ques" id="text_qu_label-${newUniqueId}" name="text_qu_label-${newUniqueId}" value=""> -->
                                             ${questionLabelHtml}
                                        </div>
                                        <div class="col-md-2 form-group prnt_add_cls qu_label_btn${newUniqueId}">
                                             <span class="remove_icon add_icon" onclick="addLabel('${newUniqueId}','')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                        <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div>
                                   </div>
                                   <hr>
                                   <div class="cond_ques_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add conditional questions label</label>
                                             </div>
                                        </div>
                                        <!-- <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div> -->
                                        <div class="text-end">
                                             <div class="form-group">
                                                  <button type="button" class="btn btn-sm btn-primary" onclick="addLabel('${newUniqueId}','')">Add Label</button>
                                             </div>
                                        </div>
                                        <hr>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="date_go_to_step-${newUniqueId}">Go to step</label>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2 new_label_question_id" data-search="on" name="date_go_to_step-${newUniqueId}" id="date_go_to_step-${newUniqueId}">
                                                       <option value="0">Checkout</option>
                                                       @if(isset($questions) && $questions != null)
                                                            @foreach($questions as $question)
                                                                 <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                            @endforeach
                                                       @endif
                                                  </select>
                                             </div> -->
                                             ${goToStepHtml}
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step${newUniqueId}" onclick="addGoToStep('${newUniqueId}')">Add Condition</button>
                                   </div>
                                  <div class="cond_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add Conditions</label>
                                             </div>
                                        </div>

                                        <div class="append_page_condition" id="append_page_condition${newUniqueId}">
                                             <div class="sec-condition" id="sec-condition${newUniqueId}" value="appended" data-is_new=true>
                                                  <div class="row">
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_id-${newUniqueId}">Question ID</label>
                                                                 <div class="form-control-wrap question">
                                                                      <select class="form-select js-select2" data-search="on" name="page_Setting_qu_id-${newUniqueId}[]" id="page_Setting_qu_id-${newUniqueId}">
                                                                           @if(isset($questions) && $questions != null)
                                                                                @foreach($questions as $question)
                                                                                     <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                                @endforeach
                                                                           @endif
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionQuestionIDHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-4">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_conditions-${newUniqueId}">Condition</label>
                                                                 <div class="form-control-wrap">
                                                                      <select class="form-select js-select2" name="page_Setting_conditions-${newUniqueId}[]" id="page_Setting_conditions-${newUniqueId}">
                                                                           <option value="" selected disabled>Select</option>
                                                                           <option value="is_equal_to">is equal to</option>
                                                                           <option value="is_greater_than">is greater than</option>
                                                                           <option value="is_less_than">is less than</option>
                                                                           <option value="not_equal_to">not equal to</option>
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionOptionHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_val-${newUniqueId}">Value</label>
                                                                 <input type="text" class="form-control" id="page_Setting_qu_val-${newUniqueId}" name="page_Setting_qu_val-${newUniqueId}[]" value=""> -->
                                                                 ${conditionValue}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-2 form-group prnt_add_cls">
                                                            <span class="remove_icon add_icon" onclick="addCondition('${newUniqueId}')"><i class="fa-solid fa-plus"></i></span>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        <br>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <!-- <label class="form-label" for="conditional_go_to_step-${condition_count}">Conditional Go to Step</label>
                                                  <div class="form-control-wrap">
                                                       <select class="form-select js-select2" data-search="on" name="conditional_go_to_step-${condition_count}" id="conditional_go_to_step-${condition_count}">
                                                            <option value="0">Checkout</option>
                                                            @if(isset($questions) && $questions != null)
                                                                 @foreach($questions as $question)
                                                                      <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                 @endforeach
                                                            @endif
                                                       </select>
                                                  </div> -->
                                                  ${conditionGoToHtml}
                                             </div>
                                        </div>
                                        <div class="independent_cond_container" id="independent_cond_container${newUniqueId}"></div>
                                        <hr>
                                        <div class="another_cond_div${newUniqueId}">
                                             <div class="text-end">
                                                  <div class="form-group">
                                                       <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('${newUniqueId}')">Add Condition</button>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="question_info_text">Question Info Text</label>
                                             <textarea class="form-control question_info_text" id="question_info_text" name="question_info_text"></textarea> -->
                                             ${infoTextHtml}
                                        </div>
                                   </div>
                                   <!-- <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Form submit handler for generating pdf</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox checked">
                                        <input type="checkbox" class="custom-control-input" id="is_end${newUniqueId}" name="is_end${newUniqueId}">
                                        <label class="custom-control-label" for="is_end${newUniqueId}">Please check this box if you are on the last
                                             step</label>
                                   </div> -->
                              </div>
                         </div>
                         <br>
                    </div>
                    </div>`;
          }else if(name === 'pricebox'){
               pricebox_count++ ;
               html = `<div class="new_que_sec${newUniqueId}" id="for_copy_sec${newUniqueId}">
                    <div class="append_priceBox" id="append_priceBox${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="row add_step">
                                        <div class="col-md-6">
                                             <!-- <h6>Pricebox</h6> -->
                                             <div class="form-group">
                                                  <select class="form-select js-select2 type_question" name="question_type${newUniqueId}" id="question_type${newUniqueId}">
                                                       ${types.map(type => `
                                                       <option value="${type.slug}" ${name === type.slug ? 'selected' : ''}>${type.name}</option>
                                                       `).join('')}
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                  <span class="col-md-2 offset-md-10">
                                                       <span class="remove_icon red_hover" onclick="removeFields(this)" value="appended" data-field="pricebox"><i class="fa fa-trash"></i></span>
                                                  </span>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <!-- <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Conditional questions label</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input add_conditional_label" id="condition_qu_label${newUniqueId}" name="condition_qu_label${newUniqueId}">
                                        <label class="custom-control-label" for="condition_qu_label${newUniqueId}">Conditional questions label</label>
                                   </div>
                                   <hr> -->
                                   <div class="cond_ques_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add conditional questions label</label>
                                             </div>
                                        </div>
                                        <!-- <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div> -->
                                        <div class="text-end">
                                             <div class="form-group">
                                                  <button type="button" class="btn btn-sm btn-primary" onclick="addLabel('${newUniqueId}','')">Add Label</button>
                                             </div>
                                        </div>
                                        <hr>
                                   </div>
                                   <div class="row hide_question_label" id="hide_question_label${newUniqueId}">
                                        <div class="col-md-10 form-group qu_label_cls${newUniqueId} label_qu">
                                             <!-- <label class="form-label" for="text_qu_label-${newUniqueId}">Question Label</label>
                                             <input type="text" class="form-control question_labl" id="text_qu_label-${newUniqueId}" name="text_qu_label-${newUniqueId}" value=""> -->
                                             ${questionLabelHtml}
                                        </div>
                                        <div class="col-md-2 form-group prnt_add_cls qu_label_btn${newUniqueId}">
                                             <span class="remove_icon add_icon" onclick="addLabel('${newUniqueId}','')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                        <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="text_placeholder-${newUniqueId}">Text Box Placeholder</label>
                                             <input type="text" class="form-control text_box_placeholder" id="text_placeholder-${newUniqueId}" name="text_placeholder-${newUniqueId}"> -->
                                             ${placeholderHtml}
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="text_go_to_step-${newUniqueId}">Go to step</label>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${newUniqueId}" id="text_go_to_step-${newUniqueId}">
                                                       <option value="0">Checkout</option>
                                                       @if(isset($questions) && $questions != null)
                                                            @foreach($questions as $question)
                                                                 <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                            @endforeach
                                                       @endif
                                                  </select>
                                             </div> -->
                                             ${goToStepHtml}
                                        </div>
                                   </div>
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step${newUniqueId}" onclick="addGoToStep('${newUniqueId}')">Add Condition</button>
                                   </div>
                                  <div class="cond_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add Conditions</label>
                                             </div>
                                        </div>

                                        <div class="append_page_condition" id="append_page_condition${newUniqueId}">
                                             <div class="sec-condition" id="sec-condition${newUniqueId}" value="appended" data-is_new=true>
                                                  <div class="row">
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_id-${newUniqueId}">Question ID</label>
                                                                 <div class="form-control-wrap question">
                                                                      <select class="form-select js-select2" data-search="on" name="page_Setting_qu_id-${newUniqueId}[]" id="page_Setting_qu_id-${newUniqueId}">
                                                                           @if(isset($questions) && $questions != null)
                                                                                @foreach($questions as $question)
                                                                                     <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                                @endforeach
                                                                           @endif
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionQuestionIDHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-4">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_conditions-${newUniqueId}">Condition</label>
                                                                 <div class="form-control-wrap">
                                                                      <select class="form-select js-select2" name="page_Setting_conditions-${newUniqueId}[]" id="page_Setting_conditions-${newUniqueId}">
                                                                           <option value="" selected disabled>Select</option>
                                                                           <option value="is_equal_to">is equal to</option>
                                                                           <option value="is_greater_than">is greater than</option>
                                                                           <option value="is_less_than">is less than</option>
                                                                           <option value="not_equal_to">not equal to</option>
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionOptionHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_val-${newUniqueId}">Value</label>
                                                                 <input type="text" class="form-control" id="page_Setting_qu_val-${newUniqueId}" name="page_Setting_qu_val-${newUniqueId}[]" value=""> -->
                                                                 ${conditionValue}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-2 form-group prnt_add_cls">
                                                            <span class="remove_icon add_icon" onclick="addCondition('${newUniqueId}')"><i class="fa-solid fa-plus"></i></span>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        <br>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <!-- <label class="form-label" for="conditional_go_to_step-${condition_count}">Conditional Go to Step</label>
                                                  <div class="form-control-wrap">
                                                       <select class="form-select js-select2" data-search="on" name="conditional_go_to_step-${condition_count}" id="conditional_go_to_step-${condition_count}">
                                                            <option value="0">Checkout</option>
                                                            @if(isset($questions) && $questions != null)
                                                                 @foreach($questions as $question)
                                                                      <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                 @endforeach
                                                            @endif
                                                       </select>
                                                  </div> -->
                                                  ${conditionGoToHtml}
                                             </div>
                                        </div>
                                        <div class="independent_cond_container" id="independent_cond_container${newUniqueId}"></div>
                                        <hr>
                                        <div class="another_cond_div${newUniqueId}">
                                             <div class="text-end">
                                                  <div class="form-group">
                                                       <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('${newUniqueId}')">Add Condition</button>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="question_info_text">Question Info Text</label>
                                             <textarea class="form-control question_info_text" id="question_info_text" name="question_info_text"></textarea> -->
                                             ${infoTextHtml}
                                        </div>
                                   </div>
                                   <!-- <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Form submit handler for generating pdf</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox checked">
                                        <input type="checkbox" class="custom-control-input" id="is_end${newUniqueId}" name="is_end${newUniqueId}">
                                        <label class="custom-control-label" for="is_end${newUniqueId}">Please check this box if you are on the last
                                             step</label>
                                   </div> -->
                              </div>
                         </div>
                         <br>
                    </div>
                    </div>`;
          }else if(name === 'number-field'){
               numberfield_count++ ;
               html = `<div class="new_que_sec${newUniqueId}" id="for_copy_sec${newUniqueId}">
                    <div class="append_numberField" id="append_numberField${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="row add_step">
                                        <div class="col-md-6">
                                             <!-- <h6>Number field</h6> -->
                                             <div class="form-group">
                                                  <select class="form-select js-select2 type_question" name="question_type${newUniqueId}" id="question_type${newUniqueId}">
                                                       ${types.map(type => `
                                                       <option value="${type.slug}" ${name === type.slug ? 'selected' : ''}>${type.name}</option>
                                                       `).join('')}
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                  <span class="col-md-2 offset-md-10">
                                                       <span class="remove_icon red_hover" onclick="removeFields(this)" value="appended" data-field="number-field"><i class="fa fa-trash"></i></span>
                                                  </span>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="cond_ques_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add conditional questions label</label>
                                             </div>
                                        </div>
                                        <!-- <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div> -->
                                        <div class="text-end">
                                             <div class="form-group">
                                                  <button type="button" class="btn btn-sm btn-primary" onclick="addLabel('${newUniqueId}','')">Add Label</button>
                                             </div>
                                        </div>
                                        <hr>
                                   </div>
                                   <div class="row hide_question_label" id="hide_question_label${newUniqueId}">
                                        <div class="col-md-10 form-group qu_label_cls${newUniqueId} label_qu">
                                            <!-- <label class="form-label" for="text_qu_label-${newUniqueId}">Question Label</label>
                                             <input type="text" class="form-control question_labl" id="text_qu_label-${newUniqueId}" name="text_qu_label-${newUniqueId}" value=""> -->
                                             ${questionLabelHtml}
                                        </div>
                                        <div class="col-md-2 form-group prnt_add_cls qu_label_btn${newUniqueId}">
                                             <span class="remove_icon add_icon" onclick="addLabel('${newUniqueId}','')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                        <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="text_placeholder-${newUniqueId}">Number field Placeholder</label>
                                             <input type="text" class="form-control number_placeholder" id="text_placeholder-${newUniqueId}" name="text_placeholder-${newUniqueId}" value=""> -->
                                             ${numberPlaceholderHtml}
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="text_go_to_step-${newUniqueId}">Go to step</label>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${newUniqueId}" id="text_go_to_step-${newUniqueId}">
                                                       <option value="0">Checkout</option>
                                                       @if(isset($questions) && $questions != null)
                                                            @foreach($questions as $question)
                                                                 <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                            @endforeach
                                                       @endif
                                                  </select>
                                             </div> -->
                                             ${goToStepHtml}
                                        </div>
                                   </div>
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step${newUniqueId}" onclick="addGoToStep('${newUniqueId}')">Add Condition</button>
                                   </div>
                                  <div class="cond_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add Conditions</label>
                                             </div>
                                        </div>

                                        <div class="append_page_condition" id="append_page_condition${newUniqueId}">
                                             <div class="sec-condition" id="sec-condition${newUniqueId}" value="appended" data-is_new=true>
                                                  <div class="row">
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_id-${newUniqueId}">Question ID</label>
                                                                 <div class="form-control-wrap question">
                                                                      <select class="form-select js-select2" data-search="on" name="page_Setting_qu_id-${newUniqueId}[]" id="page_Setting_qu_id-${newUniqueId}">
                                                                           @if(isset($questions) && $questions != null)
                                                                                @foreach($questions as $question)
                                                                                     <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                                @endforeach
                                                                           @endif
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionQuestionIDHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-4">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_conditions-${newUniqueId}">Condition</label>
                                                                 <div class="form-control-wrap">
                                                                      <select class="form-select js-select2" name="page_Setting_conditions-${newUniqueId}[]" id="page_Setting_conditions-${newUniqueId}">
                                                                           <option value="" selected disabled>Select</option>
                                                                           <option value="is_equal_to">is equal to</option>
                                                                           <option value="is_greater_than">is greater than</option>
                                                                           <option value="is_less_than">is less than</option>
                                                                           <option value="not_equal_to">not equal to</option>
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionOptionHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_val-${newUniqueId}">Value</label>
                                                                 <input type="text" class="form-control" id="page_Setting_qu_val-${newUniqueId}" name="page_Setting_qu_val-${newUniqueId}[]" value=""> -->
                                                                 ${conditionValue}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-2 form-group prnt_add_cls">
                                                            <span class="remove_icon add_icon" onclick="addCondition('${newUniqueId}')"><i class="fa-solid fa-plus"></i></span>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        <br>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <!-- <label class="form-label" for="conditional_go_to_step-${condition_count}">Conditional Go to Step</label>
                                                  <div class="form-control-wrap">
                                                       <select class="form-select js-select2" data-search="on" name="conditional_go_to_step-${condition_count}" id="conditional_go_to_step-${condition_count}">
                                                            <option value="0">Checkout</option>
                                                            @if(isset($questions) && $questions != null)
                                                                 @foreach($questions as $question)
                                                                      <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                 @endforeach
                                                            @endif
                                                       </select>
                                                  </div> -->
                                                  ${conditionGoToHtml}
                                             </div>
                                        </div>
                                        <div class="independent_cond_container" id="independent_cond_container${newUniqueId}"></div>
                                        <hr>
                                        <div class="another_cond_div${newUniqueId}">
                                             <div class="text-end">
                                                  <div class="form-group">
                                                       <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('${newUniqueId}')">Add Condition</button>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="question_info_text">Question Info Text</label>
                                             <textarea class="form-control question_info_text" id="question_info_text" name="question_info_text"></textarea> -->
                                             ${infoTextHtml}
                                        </div>
                                   </div>
                                   <!-- <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Form submit handler for generating pdf</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox checked">
                                        <input type="checkbox" class="custom-control-input" id="is_end${newUniqueId}" name="is_end${newUniqueId}">
                                        <label class="custom-control-label" for="is_end${newUniqueId}">Please check this box if you are on the last
                                             step</label>
                                   </div> -->
                              </div>
                         </div>
                         <br>
                    </div>
                    </div>`;
          }else if(name === 'percentage-box'){
               percentage_count++ ;
               html = `<div class="new_que_sec${newUniqueId}" id="for_copy_sec${newUniqueId}">
                    <div class="appendPercentageBox" id="appendPercentageBox${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="row add_step">
                                        <div class="col-md-6">
                                             <!-- <h6>Percentage Box</h6> -->
                                             <div class="form-group">
                                                  <select class="form-select js-select2 type_question" name="question_type${newUniqueId}" id="question_type${newUniqueId}">
                                                       ${types.map(type => `
                                                       <option value="${type.slug}" ${name === type.slug ? 'selected' : ''}>${type.name}</option>
                                                       `).join('')}
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                  <span class="col-md-2 offset-md-10">
                                                       <span class="remove_icon red_hover" onclick="removeFields(this)" value="appended" data-field="percentBox"><i class="fa fa-trash"></i></span>
                                                  </span>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <!-- <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Conditional questions label</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input add_conditional_label" id="condition_qu_label${newUniqueId}" name="condition_qu_label${newUniqueId}">
                                        <label class="custom-control-label" for="condition_qu_label${newUniqueId}">Conditional questions label</label>
                                   </div>
                                   <hr> -->
                                   <div class="cond_ques_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add conditional questions label</label>
                                             </div>
                                        </div>
                                        <!-- <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div> -->
                                        <div class="text-end">
                                             <div class="form-group">
                                                  <button type="button" class="btn btn-sm btn-primary" onclick="addLabel('${newUniqueId}','')">Add Label</button>
                                             </div>
                                        </div>
                                        <hr>
                                   </div>
                                   <div class="row hide_question_label" id="hide_question_label${newUniqueId}">
                                        <div class="col-md-10 form-group qu_label_cls${newUniqueId} label_qu">
                                             <!-- <label class="form-label" for="text_qu_label-${newUniqueId}">Question Label</label>
                                             <input type="text" class="form-control question_labl" id="text_qu_label-${newUniqueId}" name="text_qu_label-${newUniqueId}" value=""> -->
                                             ${questionLabelHtml}
                                        </div>
                                        <div class="col-md-2 form-group prnt_add_cls qu_label_btn${newUniqueId}">
                                             <span class="remove_icon add_icon" onclick="addLabel('${newUniqueId}','')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                        <div class="append_label_condition" id="append_label_condition${newUniqueId}"></div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="text_placeholder-${newUniqueId}">Text Box Placeholder</label>
                                             <input type="text" class="form-control text_box_placeholder" id="text_placeholder-${newUniqueId}" name="text_placeholder-${newUniqueId}"> -->
                                             ${placeholderHtml}
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="text_go_to_step-${newUniqueId}">Go to step</label>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${newUniqueId}" id="text_go_to_step-${newUniqueId}">
                                                       <option value="0">Checkout</option>
                                                       @if(isset($questions) && $questions != null)
                                                            @foreach($questions as $question)
                                                                 <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                            @endforeach
                                                       @endif
                                                  </select>
                                             </div> -->
                                             ${goToStepHtml}
                                        </div>
                                   </div>
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary grey-btn go_to_step${newUniqueId}" onclick="addGoToStep('${newUniqueId}')">Add Condition</button>
                                   </div>
                                   <div class="cond_div${newUniqueId}" style="display:none;">
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <label class="form-label" for="">Add Conditions</label>
                                             </div>
                                        </div>

                                        <div class="append_page_condition" id="append_page_condition${newUniqueId}">
                                             <div class="sec-condition" id="sec-condition${newUniqueId}" value="appended" data-is_new=true>
                                                  <div class="row">
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_id-${newUniqueId}">Question ID</label>
                                                                 <div class="form-control-wrap question">
                                                                      <select class="form-select js-select2" data-search="on" name="page_Setting_qu_id-${newUniqueId}[]" id="page_Setting_qu_id-${newUniqueId}">
                                                                           @if(isset($questions) && $questions != null)
                                                                                @foreach($questions as $question)
                                                                                     <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                                @endforeach
                                                                           @endif
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionQuestionIDHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-4">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_conditions-${newUniqueId}">Condition</label>
                                                                 <div class="form-control-wrap">
                                                                      <select class="form-select js-select2" name="page_Setting_conditions-${newUniqueId}[]" id="page_Setting_conditions-${newUniqueId}">
                                                                           <option value="" selected disabled>Select</option>
                                                                           <option value="is_equal_to">is equal to</option>
                                                                           <option value="is_greater_than">is greater than</option>
                                                                           <option value="is_less_than">is less than</option>
                                                                           <option value="not_equal_to">not equal to</option>
                                                                      </select>
                                                                 </div> -->
                                                                 ${conditionOptionHtml}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-3">
                                                            <div class="form-group">
                                                                 <!-- <label class="form-label" for="page_Setting_qu_val-${newUniqueId}">Value</label>
                                                                 <input type="text" class="form-control" id="page_Setting_qu_val-${newUniqueId}" name="page_Setting_qu_val-${newUniqueId}[]" value=""> -->
                                                                 ${conditionValue}
                                                            </div>
                                                       </div>
                                                       <div class="col-md-2 form-group prnt_add_cls">
                                                            <span class="remove_icon add_icon" onclick="addCondition('${newUniqueId}')"><i class="fa-solid fa-plus"></i></span>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                        <br>
                                        <div class="col-md-12">
                                             <div class="form-group">
                                                  <!-- <label class="form-label" for="conditional_go_to_step-${condition_count}">Conditional Go to Step</label>
                                                  <div class="form-control-wrap">
                                                       <select class="form-select js-select2" data-search="on" name="conditional_go_to_step-${condition_count}" id="conditional_go_to_step-${condition_count}">
                                                            <option value="0">Checkout</option>
                                                            @if(isset($questions) && $questions != null)
                                                                 @foreach($questions as $question)
                                                                      <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                                 @endforeach
                                                            @endif
                                                       </select>
                                                  </div> -->
                                                  ${conditionGoToHtml}
                                             </div>
                                        </div>
                                        <div class="independent_cond_container" id="independent_cond_container${newUniqueId}"></div>
                                        <hr>
                                        <div class="another_cond_div${newUniqueId}">
                                             <div class="text-end">
                                                  <div class="form-group">
                                                       <button type="button" class="btn btn-sm btn-primary grey-btn" onclick="addAnotherCondition('${newUniqueId}')">Add Condition</button>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="question_info_text">Question Info Text</label>
                                             <textarea class="form-control question_info_text" id="question_info_text" name="question_info_text"></textarea> -->
                                             ${infoTextHtml}
                                        </div>
                                   </div>
                                   <!-- <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Form submit handler for generating pdf</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox checked">
                                        <input type="checkbox" class="custom-control-input" id="is_end${newUniqueId}" name="is_end${newUniqueId}">
                                        <label class="custom-control-label" for="is_end${newUniqueId}">Please check this box if you are on the last
                                             step</label>
                                   </div> -->
                              </div>
                         </div>
                         <br>
                    </div>
                    </div>`;
          }else if(name === 'dropdown-link'){
               droplink_count++ ;
               html = `<div class="new_que_sec${newUniqueId}" id="for_copy_sec${newUniqueId}">
                    <div class="append_dropdownLink" id="append_dropdownLink${newUniqueId}" value="appended" data-is_new=true data-order_id="${num}">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="row add_step">
                                        <div class="col-md-6">
                                             <!-- <h6>Dropdown link</h6> -->
                                             <div class="form-group">
                                                  <select class="form-select js-select2 type_question" name="question_type${newUniqueId}" id="question_type${newUniqueId}">
                                                       ${types.map(type => `
                                                       <option value="${type.slug}" ${name === type.slug ? 'selected' : ''}>${type.name}</option>
                                                       `).join('')}
                                                  </select>
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                             <div class="form-group">
                                                  <span class="col-md-2 offset-md-10">
                                                       <span class="remove_icon red_hover" onclick="removeFields(this)" value="appended" data-field="dropdown-link"><i class="fa fa-trash"></i></span>
                                                  </span>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group qu_label_cls${newUniqueId} label_qu">
                                             <!-- <label class="form-label" for="text_qu_label-${newUniqueId}">Question Label</label>
                                             <input type="text" class="form-control dropdown_ques" id="text_qu_label-${newUniqueId}" name="text_qu_label-${newUniqueId}" value=""> -->
                                             ${questionLabelHtml}
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="same_contract_link-${newUniqueId}">Same Contract Link Label</label>
                                             <input type="text" class="form-control same_contract" id="same_contract_link-${newUniqueId}" name="same_contract_link-${newUniqueId}" value=""> -->
                                             ${sameContractHtml}
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Different Contract Link</label>
                                             <div class="append_cont_btn" id="append_cont_btn${newUniqueId}"></div>
                                        </div>
                                        <div class="add_cont_rw" id="add_cont_rw${newUniqueId}"></div>
                                        <div class="text-end">
                                             <div class="form-group">
                                                  <!-- <button type="button" class="btn btn-sm btn-primary" onclick="addContractRow('${newUniqueId}')">Add Row</button> -->
                                                  <span class="remove_icon add_icon contract_btn${newUniqueId}" onclick="addContractRow('${newUniqueId}','')"><i class="fa-solid fa-add"></i></span>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="text_go_to_step-${newUniqueId}">Go to step</label>
                                             <div class="form-control-wrap">
                                                  <select class="form-select js-select2 new_label_question_id" data-search="on" name="text_go_to_step-${newUniqueId}" id="text_go_to_step-${newUniqueId}">
                                                       <option value="0">Checkout</option>
                                                       @if(isset($questions) && $questions != null)
                                                            @foreach($questions as $question)
                                                                 <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                            @endforeach
                                                       @endif
                                                  </select>
                                             </div> -->
                                             ${goToStepHtml}
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <!-- <label class="form-label" for="question_info_text">Question Info Text</label>
                                             <textarea class="form-control question_info_text" id="question_info_text" name="question_info_text"></textarea> -->
                                             ${infoTextHtml}
                                        </div>
                                   </div>
                                   <!-- <hr>
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="">Form submit handler for generating pdf</label>
                                        </div>
                                   </div>
                                   <div class="custom-control custom-checkbox checked">
                                        <input type="checkbox" class="custom-control-input" id="is_end${newUniqueId}" name="is_end${newUniqueId}">
                                        <label class="custom-control-label" for="is_end${newUniqueId}">Please check this box if you are on the last
                                             step</label>
                                   </div> -->
                              </div>
                         </div>
                         <br>
                    </div>
               </div>`;
          }


          if(key === 'first'){
               $('.add_qu_sec').append(html);
               $('.question_dropbtn').hide();
               // $('.saveQuestiondata1').show();
          }else if(key === 'second'){
               $('.new_que_sec'+id).replaceWith(html);
               $('.question_dropbtn').hide();
               // $('.saveQuestiondata1').show();
          }else if(key === 'third'){
               if(!element) {
                    console.error("No element provided for 'third' layout insertion.");
                    return;
               }

               let $clickedBtn = $(element);
               let $nearestSection = $clickedBtn.closest(".add_qu_sec > div");
               let $insertedElement;

               if($nearestSection.length){
                    // alert("Layout has been added");
                    $nearestSection.append(html);
                    // $insertedElement = $nearestSection.next();
               }else{
                    $(".add_qu_sec").append(html);
                    // $insertedElement = $(".add_qu_sec").children().last();
               }

               // if($insertedElement.length){
               //      $insertedElement[0].scrollIntoView({ behavior: "smooth", block: "center" });
               // }

               updateOrderIds();
               // $('.saveQuestiondata1').show();
          }
          num++ ;
     }


     function updateOrderIds(){
          let orderElements = document.querySelectorAll(".add_qu_sec [data-order_id]");

          orderElements.forEach((ol, index) => {
               ol.setAttribute("data-order_id", index + 1); // Assign sequential order
          });
     }

     $(document).ready(function() {
          $(document).on('change', '[id^="condition_qu_label"]', function() {
               const id = $(this).attr('id').replace('condition_qu_label', '');
               conditionalOptions(id);
          });

          $(document).on('change', '[id^="condition_go_to"]', function() {
               const id = $(this).attr('id').replace('condition_go_to', '');
               goToSteps(id);
          });

          $(document).on('change', '.type_question', function() {
               const value = $(this).val();
               const id = $(this).attr('id').replace('question_type', '');
               console.log(id);
               addQuestionfields(value, id, 'second');
          });

          $(document).on('change', '[id^="is_end"]', function() {
               const id = $(this).attr('id').replace("is_end",'');
               isEndSection(id);
          });
     });

     function conditionalOptions(id) {
          if($('#condition_qu_label' + id).is(':checked')) {
               $('.cond_ques_div' + id).show();
               $('#condition_qu_label' + id).val(1);
               $('#hide_question_label'+id).hide();
          } else {
               $('.cond_ques_div' + id).hide();
               $('#condition_qu_label' + id).val(0);
               $('#hide_question_label'+id).show();
          }
     }

     function goToSteps(id) {
          if($('#condition_go_to' + id).is(':checked')) {
               $('.cond_div' + id).show();
               $('#condition_go_to' + id).val(1);
          } else {
               $('.cond_div' + id).hide();
               $('#condition_go_to' + id).val(0);
          }
     }

     function isEndSection(id){
          if($('#is_end' + id).is(':checked')){
               $('#is_end' + id).val(1);
          }else{
               $('#is_end' + id).val(0);
          }
     }

     function removeFields(e){
          $('.question_dropbtn').show();
          if($(e).attr('data-field') === 'textbox'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_textbox').remove();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#remove_question_id').val();
                              if (deleteIds) {
                                   deleteIds += ',' + id;
                              } else {
                                   deleteIds = id;
                              }
                              
                              $('#remove_question_id').val(deleteIds);
                              $('#append_textbox' + id).hide();

                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "question",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.questn_block_'+id).hide();
                                        }
                                   }
                              })

                         }
                    });
               }
          }else if($(e).attr('data-field') === 'textarea'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_textarea').remove();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#remove_question_id').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_question_id').val(deleteIds);
                              $('#append_textarea'+id).hide();

                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "question",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.questn_block_'+id).hide();
                                        }
                                   }
                              })
                         }
                    });
               }
          }else if($(e).attr('data-field') === 'dropdown'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_dropdown').remove();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#remove_question_id').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_question_id').val(deleteIds);
                              $('#append_dropdown'+id).hide();

                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "question",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.questn_block_'+id).hide();
                                        }
                                   }
                              })
                         }
                    });
               }

          }else if($(e).attr('data-field') === 'radio'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_radio').remove();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#remove_question_id').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_question_id').val(deleteIds);
                              $('#append_radio'+id).hide();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "question",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.questn_block_'+id).hide();
                                        }
                                   }
                              })
                         }
                    });
               }
          }else if($(e).attr('data-field') === 'date-field'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_dateField').remove();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#remove_question_id').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_question_id').val(deleteIds);
                              $('#append_dateField'+id).hide();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "question",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.questn_block_'+id).hide();
                                        }
                                   }
                              })
                         }
                    });
               }
          }else if($(e).attr('data-field') === 'pricebox'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_priceBox').remove();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#remove_question_id').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_question_id').val(deleteIds);
                              $('#append_priceBox'+id).hide();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "question",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.questn_block_'+id).hide();
                                        }
                                   }
                              })
                         }
                    });
               }
          }else if($(e).attr('data-field') === 'number-field'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_numberField').remove();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#remove_question_id').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_question_id').val(deleteIds);
                              $('#append_numberField'+id).hide();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "question",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.questn_block_'+id).hide();
                                        }
                                   }
                              })
                         }
                    });
               }
          }else if($(e).attr('data-field') === 'percentBox'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.appendPercentageBox').hide();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#remove_question_id').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_question_id').val(deleteIds);
                              $('#appendPercentageBox'+id).hide();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "question",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.questn_block_'+id).hide();
                                        }
                                   }
                              })
                         }
                    });
               }
          }else if($(e).attr('data-field') === 'dropdown-link'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_dropdownLink').hide();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#remove_question_id').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_question_id').val(deleteIds);
                              $('#append_dropdownLink'+id).hide();
                              if($('.add_qu_sec').length){
                                   $('#modalDefault' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "question",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.questn_block_'+id).hide();
                                        }
                                   }
                              })
                         }
                    });
               }
          }
     }

     $(document).ready(function(){
          $('body').delegate('.drop_options','click', function(){
               console.log('hello')
               $(this).closest('.col-md-6').find('.que_type_heading').hide();
               $(this).closest('.col-md-6').find('.drop_box_option').show();
          });

     });

     function removeDropbox(e){
          console.log(e);
          $(e).closest('.col-md-6').find('.drop_box_option').hide();
          $(e).closest('.col-md-6').find('.que_type_heading').show();
     }
     
     function addGoToStep(id){
          $('.cond_div'+id).show();
          $('.go_to_step'+id).hide();
     }


     function getAllSteps(){
          var questions = [];
          $('.add_qu_sec .append_textbox').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');

               var questionLabel = $(this).find('input[name^="text_qu_label"]').val() || '';
               var textBoxPlaceholder = $(this).find('input[name^="text_placeholder"]').val() || '';
               var infoText = $(this).find('textarea[name^="question_info_text"]').val() || '';
               var goToStep = $(this).find('select[name^="text_go_to_step"]').val() || '';
               var conditionalStep = $(this).find('input[name^="condition_go_to"]').is(':checked') ? 1 : 0;
               var conditionalGoTostep = $(this).find('select[name^="conditional_go_to_step"]').val() || '';
               var isEnd = $(this).find('input[name^="is_end"]').is(':checked') ? 1 : 0;


               var textboxData = {
                    type: 'textbox',
                    is_new: is_new,
                    id: id,
                    is_conditional_question: 0,
                    question_label: questionLabel,
                    text_box_placeholder: textBoxPlaceholder,
                    question_info_text: infoText,
                    go_to_step: goToStep,
                    is_conditional_step: 0,
                    is_another_conditional_step: 0,
                    is_end: isEnd,
                    conditional_question_labels: [],
                    new_conditional_question_labels: [],
                    conditions: [],
                    new_conditions: [],
                    another_conditions: {},
                    new_another_conditions: {},
                    condition_go_to_step: conditionalGoTostep,
                    order_id: order_id,
               };


               if($(this).find('.append_label_condition .label-condition').length > 0){
                    $(this).find('.append_label_condition .label-condition').each(function(){
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   textboxData.new_conditional_question_labels.push(question_label);
                                   textboxData.is_conditional_question = 1;
                              }
                         }else if(status === false){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   textboxData.conditional_question_labels.push(question_label);
                                   textboxData.is_conditional_question = 1;
                              }
                         }
                    });
               }

               if($(this).find('.append_page_condition .sec-condition').length > 0){
                    $(this).find('.append_page_condition .sec-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   textboxData.new_conditions.push(condition);
                                   textboxData.is_conditional_step = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   textboxData.conditions.push(condition);
                                   textboxData.is_conditional_step = 1;
                              }
                         }
                    });
               }

               if($(this).find('.independent_cond_container .independent_cond_div').length > 0){
                    $(this).find('.independent_cond_container .independent_cond_div').each(function () {
                         var no_of_condition = $(this).attr('id').replace(/.*_(\d+)$/, '$1');
                         var ind_status = $(this).data('is_new');
                         var condition_go_to_step = $(this).find('select[name^="another_conditional_go_to_step"]').val() || '';

                         var newIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         var existingIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         $(this).find('.another_page_condition .another-condition').each(function () {
                              var status = $(this).data('is_new');
                              var conditionId = $(this).data('id');

                              var condition = {
                                   questionID: $(this).find('select[name^="another_que_id-"]').val() || '',
                                   question_condition: $(this).find('select[name^="another_conditions_step-"]').val() || '',
                                   question_value: $(this).find('input[name^="another_qu_val-"]').val() || '',
                                   status: status
                              };

                              if(ind_status === true && status === true){
                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        newIndependentConditionData.subconditions.push(condition);
                                   }
                                   if(newIndependentConditionData.subconditions.length > 0){
                                        if(!textboxData.new_another_conditions[no_of_condition]) {
                                             textboxData.new_another_conditions[no_of_condition] = newIndependentConditionData;
                                             textboxData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                              else if(ind_status === false && status === true){
                                   var id = $(this).attr('id');
                                   var match = id.match(/another-condition-(\d+)-/);
                                   var exisiting_id = match ? match[1] : null;

                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!textboxData.another_conditions[no_of_condition]) {
                                             textboxData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             textboxData.is_another_conditional_step = 1;
                                        }
                                   }
                              }else if(ind_status === false && status === false){
                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!textboxData.another_conditions[no_of_condition]) {
                                             textboxData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             textboxData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                         });
                    });
               }

               questions.push(textboxData);
          });

          $('.add_qu_sec .append_textarea').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');

               var questionLabel = $(this).find('input[name^="text_qu_label"]').val() || '';
               var textBoxPlaceholder = $(this).find('input[name^="text_placeholder"]').val() || '';
               var infoText = $(this).find('textarea[name^="question_info_text"]').val() || '';
               var goToStep = $(this).find('select[name^="text_go_to_step"]').val() || '';
               var conditionalStep = $(this).find('input[name^="condition_go_to"]').is(':checked') ? 1 : 0;
               var conditionalGoTostep = $(this).find('select[name^="conditional_go_to_step"]').val() || '';
               var isEnd = $(this).find('input[name^="is_end"]').is(':checked') ? 1 : 0;

               var textareaData = {
                    type: 'textarea',
                    is_new: is_new,
                    id: id,
                    is_conditional_question: 0,
                    question_label: questionLabel,
                    text_box_placeholder: textBoxPlaceholder,
                    question_info_text: infoText,
                    go_to_step: goToStep,
                    is_conditional_step: 0,
                    is_another_conditional_step: 0,
                    is_end: isEnd,
                    conditional_question_labels: [],
                    new_conditional_question_labels: [],
                    conditions: [],
                    new_conditions: [],
                    another_conditions: {},
                    new_another_conditions: {},
                    condition_go_to_step: conditionalGoTostep,
                    order_id: order_id,
               };

               if($(this).find('.append_label_condition .label-condition').length > 0){
                    $(this).find('.append_label_condition .label-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   textareaData.new_conditional_question_labels.push(question_label);
                                   textareaData.is_conditional_question = 1;
                              }
                         }else if(status === false){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   textareaData.conditional_question_labels.push(question_label);
                                   textareaData.is_conditional_question = 1;
                              }
                         }
                    });
               }

               if($(this).find('.append_page_condition .sec-condition').length > 0){
                    $(this).find('.append_page_condition .sec-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   textareaData.new_conditions.push(condition);
                                   textareaData.is_conditional_step = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   textareaData.conditions.push(condition);
                                   textareaData.is_conditional_step = 1;
                              }
                         }
                    });
               }

               if($(this).find('.independent_cond_container .independent_cond_div').length > 0){
                    $(this).find('.independent_cond_container .independent_cond_div').each(function () {
                         var no_of_condition = $(this).attr('id').replace(/.*_(\d+)$/, '$1');
                         var ind_status = $(this).data('is_new');
                         var condition_go_to_step = $(this).find('select[name^="another_conditional_go_to_step"]').val() || '';

                         var newIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         var existingIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         $(this).find('.another_page_condition .another-condition').each(function () {
                              var status = $(this).data('is_new');
                              var conditionId = $(this).data('id');

                              var condition = {
                                   questionID: $(this).find('select[name^="another_que_id-"]').val() || '',
                                   question_condition: $(this).find('select[name^="another_conditions_step-"]').val() || '',
                                   question_value: $(this).find('input[name^="another_qu_val-"]').val() || '',
                                   status: status
                              };

                              if(ind_status === true && status === true){
                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        newIndependentConditionData.subconditions.push(condition);
                                   }
                                   if(newIndependentConditionData.subconditions.length > 0){
                                        if(!textareaData.new_another_conditions[no_of_condition]) {
                                             textareaData.new_another_conditions[no_of_condition] = newIndependentConditionData;
                                             textareaData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                              else if(ind_status === false && status === true){
                                   var id = $(this).attr('id');
                                   var match = id.match(/another-condition-(\d+)-/);
                                   var exisiting_id = match ? match[1] : null;

                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!textareaData.another_conditions[no_of_condition]) {
                                             textareaData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             textareaData.is_another_conditional_step = 1;
                                        }
                                   }
                              }else if(ind_status === false && status === false){
                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!textareaData.another_conditions[no_of_condition]) {
                                             textareaData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             textareaData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                         });
                    });
               }

               questions.push(textareaData);
          });

          $('.add_qu_sec .append_dropdown').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');
               var questionLabel = $(this).find('input[name^="text_qu_label"]').val() || ''
               var conditionalStep = $(this).find('input[name^="condition_go_to"]').is(':checked') ? 1 : 0;
               var conditionalGoTostep = $(this).find('select[name^="conditional_go_to_step"]').val() || '';
               var isEnd = $(this).find('input[name^="is_end"]').is(':checked') ? 1 : 0;
               var infoText = $(this).find('textarea[name^="question_info_text"]').val() || '';

               var dropdownData = {
                    type: 'dropdown',
                    is_new: is_new,
                    id: id,
                    is_conditional_question: 0,
                    question_label: questionLabel,
                    is_conditional_step: 0,
                    is_another_conditional_step: 0,
                    question_info_text: infoText,
                    is_end: isEnd,
                    conditional_question_labels: [],
                    new_conditional_question_labels: [],
                    conditions: [],
                    new_conditions: [],
                    another_conditions: {},
                    new_another_conditions: {},
                    add_options: [],
                    new_options: [],
                    condition_go_to_step: conditionalGoTostep,
                    order_id: order_id,
               };

               if($(this).find('.append_options .dropdown-option') !== 0){
                    $(this).find('.append_options .dropdown-option').each(function(){
                         var status = $(this).data('is_new');
                         var optionId = $(this).data('id');

                         if(status === true){
                              var option = {
                                   option_label: $(this).find('input[name^=dropdown_option_label]').val() || '',
                                   option_value: $(this).find('input[name^=dropdown_option_value]').val() || '',
                                   option_go_to_step: $(this).find('select[name^=dropdown_go_to_step]').val() || '',
                                   status: status,
                              };

                              if(option.option_label && option.option_value && option.option_go_to_step){
                                   console.log('options');
                                   dropdownData.new_options.push(option);
                              }else{
                                   console.log('no');
                              }

                         }else if(status === false){
                              var option = {
                                   option_label: $(this).find('input[name^=dropdown_option_label]').val() || '',
                                   option_value: $(this).find('input[name^=dropdown_option_value]').val() || '',
                                   option_go_to_step: $(this).find('select[name^=dropdown_go_to_step]').val() || '',
                                   status: status,
                                   option_id: optionId,
                              };

                              if(option.option_label && option.option_value && option.option_go_to_step){
                                   dropdownData.add_options.push(option);
                              }
                         }
                    })
               }

               if($(this).find('.append_label_condition .label-condition').length > 0){
                    $(this).find('.append_label_condition .label-condition').each(function(){
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   dropdownData.new_conditional_question_labels.push(question_label);
                                   dropdownData.is_conditional_question = 1;
                              }
                         }else if(status === false){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   dropdownData.conditional_question_labels.push(question_label);
                                   dropdownData.is_conditional_question = 1;
                              }
                         }
                    });
               }


               if($(this).find('.append_page_condition .sec-condition').length > 0){
                    $(this).find('.append_page_condition .sec-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   dropdownData.new_conditions.push(condition);
                                   dropdownData.is_conditional_step = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   dropdownData.conditions.push(condition);
                                   dropdownData.is_conditional_step = 1;
                              }
                         }
                    });
               }

               if($(this).find('.independent_cond_container .independent_cond_div').length > 0){
                    $(this).find('.independent_cond_container .independent_cond_div').each(function () {
                         var no_of_condition = $(this).attr('id').replace(/.*_(\d+)$/, '$1');
                         var ind_status = $(this).data('is_new');
                         var condition_go_to_step = $(this).find('select[name^="another_conditional_go_to_step"]').val() || '';

                         var newIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         var existingIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         $(this).find('.another_page_condition .another-condition').each(function () {
                              var status = $(this).data('is_new');
                              var conditionId = $(this).data('id');

                              var condition = {
                                   questionID: $(this).find('select[name^="another_que_id-"]').val() || '',
                                   question_condition: $(this).find('select[name^="another_conditions_step-"]').val() || '',
                                   question_value: $(this).find('input[name^="another_qu_val-"]').val() || '',
                                   status: status
                              };

                              if(ind_status === true && status === true){
                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        newIndependentConditionData.subconditions.push(condition);
                                   }
                                   if(newIndependentConditionData.subconditions.length > 0){
                                        if(!dropdownData.new_another_conditions[no_of_condition]) {
                                             dropdownData.new_another_conditions[no_of_condition] = newIndependentConditionData;
                                             dropdownData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                              else if(ind_status === false && status === true){
                                   var id = $(this).attr('id');
                                   var match = id.match(/another-condition-(\d+)-/);
                                   var exisiting_id = match ? match[1] : null;

                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!dropdownData.another_conditions[no_of_condition]) {
                                             dropdownData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             dropdownData.is_another_conditional_step = 1;
                                        }
                                   }
                              }else if(ind_status === false && status === false){
                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!dropdownData.another_conditions[no_of_condition]) {
                                             dropdownData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             dropdownData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                         });
                    });
               }
               questions.push(dropdownData);
          });

          $('.add_qu_sec .append_radio').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');
               var questionLabel = $(this).find('input[name^="text_qu_label"]').val() || '';
               var conditionalStep = $(this).find('input[name^="condition_go_to"]').is(':checked') ? 1 : 0;
               var conditionalGoTostep = $(this).find('select[name^="conditional_go_to_step"]').val() || '';
               var isEnd = $(this).find('input[name^="is_end"]').is(':checked') ? 1 : 0;
               var infoText = $(this).find('textarea[name^="question_info_text"]').val() || '';

               var radioData = {
                    type: 'radio-button',
                    is_new: is_new,
                    id: id,
                    is_conditional_question: 0,
                    question_label: questionLabel,
                    question_info_text: infoText,
                    is_conditional_step: 0,
                    is_another_conditional_step: 0,
                    is_end: isEnd,
                    conditional_question_labels: [],
                    new_conditional_question_labels: [],
                    conditions: [],
                    new_conditions: [],
                    another_conditions: {},
                    new_another_conditions: {},
                    condition_go_to_step: conditionalGoTostep,
                    add_options: [],
                    new_options: [],
                    order_id: order_id,
               };

               if($(this).find('.append_options .radio-option') !== 0){
                    $(this).find('.append_options .radio-option').each(function(){
                         var status = $(this).data('is_new');
                         var optionId = $(this).data('id');
                         if(status === true){
                              var option = {
                                   option_label: $(this).find('input[name^=radio_option_label]').val() || '',
                                   option_value: $(this).find('input[name^=radio_option_value]').val() || '',
                                   option_go_to_step: $(this).find('select[name^=radio_go_to_step]').val() || '',
                                   status: status,
                              };

                              if(option.option_label && option.option_value && option.option_go_to_step){
                                   radioData.new_options.push(option);
                              }
                         }else if(status === false){
                              var option = {
                                   option_label: $(this).find('input[name^=radio_option_label]').val() || '',
                                   option_value: $(this).find('input[name^=radio_option_value]').val() || '',
                                   option_go_to_step: $(this).find('select[name^=radio-button_go_to_step]').val() || '',
                                   status: status,
                                   option_id: optionId,
                              };

                              if(option.option_label && option.option_value && option.option_go_to_step){
                                   radioData.add_options.push(option);
                              }
                         }
                    })
               }

               if($(this).find('.append_label_condition .label-condition').length > 0){
                    $(this).find('.append_label_condition .label-condition').each(function(){
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   radioData.new_conditional_question_labels.push(question_label);
                                   radioData.is_conditional_question = 1;
                              }
                         }else if(status === false){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   radioData.conditional_question_labels.push(question_label);
                                   radioData.is_conditional_question = 1;
                              }
                         }
                    });
               }

               if($(this).find('.append_page_condition .sec-condition').length > 0){
                    $(this).find('.append_page_condition .sec-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                              };

                              if(condition.questionID && condition.question_condition && condition.conditional_go_to_step || condition.question_value){
                                   radioData.new_conditions.push(condition);
                                   radioData.is_conditional_step = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   radioData.conditions.push(condition);
                                   radioData.is_conditional_step = 1;
                              }
                         }
                    });
               }

               if($(this).find('.independent_cond_container .independent_cond_div').length > 0){
                    $(this).find('.independent_cond_container .independent_cond_div').each(function () {
                         var no_of_condition = $(this).attr('id').replace(/.*_(\d+)$/, '$1');
                         var ind_status = $(this).data('is_new');
                         var condition_go_to_step = $(this).find('select[name^="another_conditional_go_to_step"]').val() || '';

                         var newIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         var existingIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         $(this).find('.another_page_condition .another-condition').each(function () {
                              var status = $(this).data('is_new');
                              var conditionId = $(this).data('id');

                              var condition = {
                                   questionID: $(this).find('select[name^="another_que_id-"]').val() || '',
                                   question_condition: $(this).find('select[name^="another_conditions_step-"]').val() || '',
                                   question_value: $(this).find('input[name^="another_qu_val-"]').val() || '',
                                   status: status
                              };

                              if(ind_status === true && status === true){
                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        newIndependentConditionData.subconditions.push(condition);
                                   }
                                   if(newIndependentConditionData.subconditions.length > 0){
                                        if(!radioData.new_another_conditions[no_of_condition]) {
                                             radioData.new_another_conditions[no_of_condition] = newIndependentConditionData;
                                             radioData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                              else if(ind_status === false && status === true){
                                   var id = $(this).attr('id');
                                   var match = id.match(/another-condition-(\d+)-/);
                                   var exisiting_id = match ? match[1] : null;

                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!radioData.another_conditions[no_of_condition]) {
                                             radioData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             radioData.is_another_conditional_step = 1;
                                        }
                                   }
                              }else if(ind_status === false && status === false){
                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!radioData.another_conditions[no_of_condition]) {
                                             radioData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             radioData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                         });
                    });
               }

               questions.push(radioData);
          });

          $('.add_qu_sec .append_dateField').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');
               var questionLabel = $(this).find('input[name^="text_qu_label"]').val() || '';
               var goToStep = $(this).find('select[name^="date_go_to_step"]').val() || '';
               var conditionalStep = $(this).find('input[name^="condition_go_to"]').is(':checked') ? 1 : 0;
               var conditionalGoTostep = $(this).find('select[name^="conditional_go_to_step"]').val() || '';
               var isEnd = $(this).find('input[name^="is_end"]').is(':checked') ? 1 : 0;
               var infoText = $(this).find('textarea[name^="question_info_text"]').val() || '';

               var datefieldData = {
                    type: 'date-field',
                    is_new: is_new,
                    id: id,
                    is_conditional_question: 0,
                    question_label: questionLabel,
                    question_info_text: infoText,
                    go_to_step: goToStep,
                    is_conditional_step: 0,
                    is_another_conditional_step: 0,
                    is_end: isEnd,
                    conditional_question_labels: [],
                    new_conditional_question_labels: [],
                    conditions: [],
                    new_conditions: [],
                    another_conditions: {},
                    new_another_conditions: {},
                    condition_go_to_step: conditionalGoTostep,
                    order_id: order_id,
               };

               if($(this).find('.append_label_condition .label-condition').length > 0){
                    $(this).find('.append_label_condition .label-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   datefieldData.new_conditional_question_labels.push(question_label);
                                   datefieldData.is_conditional_question = 1;
                              }
                         }else if(status === false){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   datefieldData.conditional_question_labels.push(question_label);
                                   datefieldData.is_conditional_question = 1;
                              }
                         }
                    });
               }

               if($(this).find('.append_page_condition .sec-condition').length > 0){
                    $(this).find('.append_page_condition .sec-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   datefieldData.new_conditions.push(condition);
                                   datefieldData.is_conditional_step = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   datefieldData.conditions.push(condition);
                                   datefieldData.is_conditional_step = 1;
                              }
                         }
                    });
               }

               if($(this).find('.independent_cond_container .independent_cond_div').length > 0){
                    $(this).find('.independent_cond_container .independent_cond_div').each(function () {
                         var no_of_condition = $(this).attr('id').replace(/.*_(\d+)$/, '$1');
                         var ind_status = $(this).data('is_new');
                         var condition_go_to_step = $(this).find('select[name^="another_conditional_go_to_step"]').val() || '';

                         var newIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         var existingIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         $(this).find('.another_page_condition .another-condition').each(function () {
                              var status = $(this).data('is_new');
                              var conditionId = $(this).data('id');

                              var condition = {
                                   questionID: $(this).find('select[name^="another_que_id-"]').val() || '',
                                   question_condition: $(this).find('select[name^="another_conditions_step-"]').val() || '',
                                   question_value: $(this).find('input[name^="another_qu_val-"]').val() || '',
                                   status: status
                              };

                              if(ind_status === true && status === true){
                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        newIndependentConditionData.subconditions.push(condition);
                                   }
                                   if(newIndependentConditionData.subconditions.length > 0){
                                        if(!datefieldData.new_another_conditions[no_of_condition]) {
                                             datefieldData.new_another_conditions[no_of_condition] = newIndependentConditionData;
                                             datefieldData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                              else if(ind_status === false && status === true){
                                   var id = $(this).attr('id');
                                   var match = id.match(/another-condition-(\d+)-/);
                                   var exisiting_id = match ? match[1] : null;

                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!datefieldData.another_conditions[no_of_condition]) {
                                             datefieldData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             datefieldData.is_another_conditional_step = 1;
                                        }
                                   }
                              }else if(ind_status === false && status === false){
                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!datefieldData.another_conditions[no_of_condition]) {
                                             datefieldData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             datefieldData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                         });
                    });
               }

               questions.push(datefieldData);
          });

          $('.add_qu_sec .append_priceBox').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');
               var questionLabel = $(this).find('input[name^="text_qu_label"]').val() || '';
               var textBoxPlaceholder = $(this).find('input[name^="text_placeholder"]').val() || '';
               var goToStep = $(this).find('select[name^="text_go_to_step"]').val() || '';
               var conditionalStep = $(this).find('input[name^="condition_go_to"]').is(':checked') ? 1 : 0;
               var conditionalGoTostep = $(this).find('select[name^="conditional_go_to_step"]').val() || '';
               var isEnd = $(this).find('input[name^="is_end"]').is(':checked') ? 1 : 0;
               var infoText = $(this).find('textarea[name^="question_info_text"]').val() || '';

               var priceBoxData = {
                    type: 'pricebox',
                    is_new: is_new,
                    id: id,
                    is_conditional_question: 0,
                    question_label: questionLabel,
                    text_box_placeholder: textBoxPlaceholder,
                    question_info_text: infoText,
                    go_to_step: goToStep,
                    is_conditional_step: 0,
                    is_another_conditional_step: 0,
                    is_end: isEnd,
                    conditional_question_labels: [],
                    new_conditional_question_labels: [],
                    conditions: [],
                    new_conditions: [],
                    another_conditions: {},
                    new_another_conditions: {},
                    condition_go_to_step: conditionalGoTostep,
                    order_id: order_id,
               };

               if($(this).find('.append_label_condition .label-condition').length > 0){
                    $(this).find('.append_label_condition .label-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   priceBoxData.new_conditional_question_labels.push(question_label);
                                   priceBoxData.is_conditional_question = 1;
                              }
                         }else if(status === false){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   priceBoxData.conditional_question_labels.push(question_label);
                                   priceBoxData.is_conditional_question = 1;
                              }
                         }
                    });
               }

               if($(this).find('.append_page_condition .sec-condition').length > 0){
                    $(this).find('.append_page_condition .sec-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   priceBoxData.new_conditions.push(condition);
                                   priceBoxData.is_conditional_step = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   priceBoxData.conditions.push(condition);
                                   priceBoxData.is_conditional_step = 1;
                              }
                         }
                    });
               }

               if($(this).find('.independent_cond_container .independent_cond_div').length > 0){
                    $(this).find('.independent_cond_container .independent_cond_div').each(function () {
                         var no_of_condition = $(this).attr('id').replace(/.*_(\d+)$/, '$1');
                         var ind_status = $(this).data('is_new');
                         var condition_go_to_step = $(this).find('select[name^="another_conditional_go_to_step"]').val() || '';

                         var newIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         var existingIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         $(this).find('.another_page_condition .another-condition').each(function () {
                              var status = $(this).data('is_new');
                              var conditionId = $(this).data('id');

                              var condition = {
                                   questionID: $(this).find('select[name^="another_que_id-"]').val() || '',
                                   question_condition: $(this).find('select[name^="another_conditions_step-"]').val() || '',
                                   question_value: $(this).find('input[name^="another_qu_val-"]').val() || '',
                                   status: status
                              };

                              if(ind_status === true && status === true){
                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        newIndependentConditionData.subconditions.push(condition);
                                   }
                                   if(newIndependentConditionData.subconditions.length > 0){
                                        if(!priceBoxData.new_another_conditions[no_of_condition]) {
                                             priceBoxData.new_another_conditions[no_of_condition] = newIndependentConditionData;
                                             priceBoxData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                              else if(ind_status === false && status === true){
                                   var id = $(this).attr('id');
                                   var match = id.match(/another-condition-(\d+)-/);
                                   var exisiting_id = match ? match[1] : null;

                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!priceBoxData.another_conditions[no_of_condition]) {
                                             priceBoxData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             priceBoxData.is_another_conditional_step = 1;
                                        }
                                   }
                              }else if(ind_status === false && status === false){
                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!priceBoxData.another_conditions[no_of_condition]) {
                                             priceBoxData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             priceBoxData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                         });
                    });
               }

               questions.push(priceBoxData);
          });

          $('.add_qu_sec .append_numberField').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');

               var questionLabel = $(this).find('input[name^="text_qu_label"]').val() || '';
               var textBoxPlaceholder = $(this).find('input[name^="text_placeholder"]').val() || '';
               var goToStep = $(this).find('select[name^="text_go_to_step"]').val() || '';
               var conditionalStep = $(this).find('input[name^="condition_go_to"]').is(':checked') ? 1 : 0;
               var conditionalGoTostep = $(this).find('select[name^="conditional_go_to_step"]').val() || '';
               var isEnd = $(this).find('input[name^="is_end"]').is(':checked') ? 1 : 0;
               var infoText = $(this).find('textarea[name^="question_info_text"]').val() || '';

               var numberfieldData = {
                    type: 'number-field',
                    is_new: is_new,
                    id: id,
                    is_conditional_question: 0,
                    question_label: questionLabel,
                    text_box_placeholder: textBoxPlaceholder,
                    question_info_text: infoText,
                    go_to_step: goToStep,
                    is_conditional_step: 0,
                    is_another_conditional_step: 0,
                    is_end: isEnd,
                    conditional_question_labels: [],
                    new_conditional_question_labels: [],
                    conditions: [],
                    new_conditions: [],
                    another_conditions: {},
                    new_another_conditions: {},
                    condition_go_to_step: conditionalGoTostep,
                    order_id: order_id,
               };

               if($(this).find('.append_label_condition .label-condition').length > 0){
                    $(this).find('.append_label_condition .label-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   numberfieldData.new_conditional_question_labels.push(question_label);
                                   numberfieldData.is_conditional_question = 1;
                              }
                         }else if(status === false){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   numberfieldData.conditional_question_labels.push(question_label);
                                   numberfieldData.is_conditional_question = 1;
                              }
                         }
                    });
               }

               if($(this).find('.append_page_condition .sec-condition').length > 0){
                    $(this).find('.append_page_condition .sec-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   numberfieldData.new_conditions.push(condition);
                                   numberfieldData.is_conditional_step = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   numberfieldData.conditions.push(condition);
                                   numberfieldData.is_conditional_step = 1;
                              }
                         }
                    });
               }

               if($(this).find('.independent_cond_container .independent_cond_div').length > 0){
                    $(this).find('.independent_cond_container .independent_cond_div').each(function () {
                         var no_of_condition = $(this).attr('id').replace(/.*_(\d+)$/, '$1');
                         var ind_status = $(this).data('is_new');
                         var condition_go_to_step = $(this).find('select[name^="another_conditional_go_to_step"]').val() || '';

                         var newIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         var existingIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         $(this).find('.another_page_condition .another-condition').each(function () {
                              var status = $(this).data('is_new');
                              var conditionId = $(this).data('id');

                              var condition = {
                                   questionID: $(this).find('select[name^="another_que_id-"]').val() || '',
                                   question_condition: $(this).find('select[name^="another_conditions_step-"]').val() || '',
                                   question_value: $(this).find('input[name^="another_qu_val-"]').val() || '',
                                   status: status
                              };

                              if(ind_status === true && status === true){
                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        newIndependentConditionData.subconditions.push(condition);
                                   }
                                   if(newIndependentConditionData.subconditions.length > 0){
                                        if(!numberfieldData.new_another_conditions[no_of_condition]) {
                                             numberfieldData.new_another_conditions[no_of_condition] = newIndependentConditionData;
                                             numberfieldData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                              else if(ind_status === false && status === true){
                                   var id = $(this).attr('id');
                                   var match = id.match(/another-condition-(\d+)-/);
                                   var exisiting_id = match ? match[1] : null;

                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!numberfieldData.another_conditions[no_of_condition]) {
                                             numberfieldData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             numberfieldData.is_another_conditional_step = 1;
                                        }
                                   }
                              }else if(ind_status === false && status === false){
                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!numberfieldData.another_conditions[no_of_condition]) {
                                             numberfieldData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             numberfieldData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                         });
                    });
               }

               questions.push(numberfieldData);
          });

          $('.add_qu_sec .appendPercentageBox').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');
               var questionLabel = $(this).find('input[name^="text_qu_label"]').val() || '';
               var textBoxPlaceholder = $(this).find('input[name^="text_placeholder"]').val() || '';
               var goToStep = $(this).find('select[name^="text_go_to_step"]').val() || '';
               var conditionalStep = $(this).find('input[name^="condition_go_to"]').is(':checked') ? 1 : 0;
               var conditionalGoTostep = $(this).find('select[name^="conditional_go_to_step"]').val() || '';
               var isEnd = $(this).find('input[name^="is_end"]').is(':checked') ? 1 : 0;
               var infoText = $(this).find('textarea[name^="question_info_text"]').val() || '';

               var percentageBoxData = {
                    type: 'percentage-box',
                    is_new: is_new,
                    id: id,
                    is_conditional_question: 0,
                    question_label: questionLabel,
                    text_box_placeholder: textBoxPlaceholder,
                    question_info_text: infoText,
                    go_to_step: goToStep,
                    is_conditional_step: 0,
                    is_another_conditional_step: 0,
                    is_end: isEnd,
                    conditional_question_labels: [],
                    new_conditional_question_labels: [],
                    conditions: [],
                    new_conditions: [],
                    another_conditions: {},
                    new_another_conditions: {},
                    condition_go_to_step: conditionalGoTostep,
                    order_id: order_id,
               };

               if($(this).find('.append_label_condition .label-condition').length > 0){
                    $(this).find('.append_label_condition .label-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   percentageBoxData.new_conditional_question_labels.push(question_label);
                                   percentageBoxData.is_conditional_question = 1;
                              }
                         }else if(status === false){
                              var question_label = {
                                   label: $(this).find('input[name^="condition_question_label"]').val() || '',
                                   questionID: $(this).find('select[name^="label_qu_id"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(question_label.label && question_label.questionID || question_label.question_value){
                                   percentageBoxData.conditional_question_labels.push(question_label);
                                   percentageBoxData.is_conditional_question = 1;
                              }
                         }
                    });
               }


               if($(this).find('.append_page_condition .sec-condition').length > 0){
                    $(this).find('.append_page_condition .sec-condition').each(function() {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   percentageBoxData.new_conditions.push(condition);
                                   percentageBoxData.is_conditional_step = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   questionID: $(this).find('select[name^="page_Setting_qu_id"]').val() || '',
                                   question_condition: $(this).find('select[name^="page_Setting_conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="page_Setting_qu_val"]').val() || '',
                                   status: status,
                                   condition_id: conditionId,
                              };

                              if(condition.questionID && condition.question_condition || condition.question_value){
                                   percentageBoxData.conditions.push(condition);
                                   percentageBoxData.is_conditional_step = 1;
                              }
                         }
                    });
               }

               if($(this).find('.independent_cond_container .independent_cond_div').length > 0){
                    $(this).find('.independent_cond_container .independent_cond_div').each(function () {
                         var no_of_condition = $(this).attr('id').replace(/.*_(\d+)$/, '$1');
                         var ind_status = $(this).data('is_new');
                         var condition_go_to_step = $(this).find('select[name^="another_conditional_go_to_step"]').val() || '';

                         var newIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         var existingIndependentConditionData = {
                              go_to_step: condition_go_to_step,
                              subconditions: [],
                              existing_condition_id: 0,
                         };

                         $(this).find('.another_page_condition .another-condition').each(function () {
                              var status = $(this).data('is_new');
                              var conditionId = $(this).data('id');

                              var condition = {
                                   questionID: $(this).find('select[name^="another_que_id-"]').val() || '',
                                   question_condition: $(this).find('select[name^="another_conditions_step-"]').val() || '',
                                   question_value: $(this).find('input[name^="another_qu_val-"]').val() || '',
                                   status: status
                              };

                              if(ind_status === true && status === true){
                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        newIndependentConditionData.subconditions.push(condition);
                                   }
                                   if(newIndependentConditionData.subconditions.length > 0){
                                        if(!percentageBoxData.new_another_conditions[no_of_condition]) {
                                             percentageBoxData.new_another_conditions[no_of_condition] = newIndependentConditionData;
                                             percentageBoxData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                              else if(ind_status === false && status === true){
                                   var id = $(this).attr('id');
                                   var match = id.match(/another-condition-(\d+)-/);
                                   var exisiting_id = match ? match[1] : null;

                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!percentageBoxData.another_conditions[no_of_condition]) {
                                             percentageBoxData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             percentageBoxData.is_another_conditional_step = 1;
                                        }
                                   }
                              }else if(ind_status === false && status === false){
                                   condition.condition_id = conditionId;

                                   if(condition.questionID && (condition.question_condition || condition.question_value)){
                                        existingIndependentConditionData.subconditions.push(condition);
                                        existingIndependentConditionData.existing_condition_id = exisiting_id;
                                   }

                                   if(existingIndependentConditionData.subconditions.length > 0){
                                        if(!percentageBoxData.another_conditions[no_of_condition]) {
                                             percentageBoxData.another_conditions[no_of_condition] = existingIndependentConditionData;
                                             percentageBoxData.is_another_conditional_step = 1;
                                        }
                                   }
                              }
                         });
                    });
               }
               questions.push(percentageBoxData);
          });

          $('.add_qu_sec .append_dropdownLink').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');

               var questionLabel = $(this).find('input[name^="text_qu_label"]').val() || '';
               var sameContractlink = $(this).find('input[name^="same_contract_link"]').val() || '';
               var goToStep = $(this).find('select[name^="text_go_to_step"]').val() || '';
               var isEnd = $(this).find('input[name^="is_end"]').is(':checked') ? 1 : 0;
               var infoText = $(this).find('textarea[name^="question_info_text"]').val() || '';

               var dropdownLinkData = {
                    type: 'dropdown-link',
                    is_new: is_new,
                    id: id,
                    question_label: questionLabel,
                    same_contract_link: sameContractlink,
                    question_info_text: infoText,
                    go_to_step: goToStep,
                    is_end: isEnd,
                    add_rows: [],
                    new_rows: [],
                    order_id: order_id,
               };

               if($(this).find('.add_cont_rw .contract-option') !== 0){
                    $(this).find('.add_cont_rw .contract-option').each(function(){
                         var status = $(this).data('is_new');
                         var optionId = $(this).data('id');

                         if(status === true){
                              var row = {
                                   label: $(this).find('input[name^=dropdown_link_label]').val() || '',
                                   contract_link: $(this).find('input[name^=contract_link]').val() || '',
                                   // next_step: $(this).find('input[name^=contract_send_next_step]').is(':checked') ? 1 : 0,
                                   status: status,
                              };

                              if(row.label && row.contract_link || row.next_step){
                                   dropdownLinkData.new_rows.push(row);
                              }
                         }else if(status === false){
                              var row = {
                                   label: $(this).find('input[name^=dropdown_link_label]').val() || '',
                                   contract_link: $(this).find('input[name^=contract_link]').val() || '',
                                   // next_step: $(this).find('input[name^=contract_send_next_step]').is(':checked') ? 1 : 0,
                                   status: status,
                                   option_id: optionId
                              };

                              if(row.label && row.contract_link || row.next_step){
                                   dropdownLinkData.add_rows.push(row);
                              }
                         }
                    })
               }

               questions.push(dropdownLinkData);
          });
          return questions;

     }


     $(document).ready(function(){
          $('.saveQuestiondata1').click(function(){
               var data = getAllSteps();

               $('#formdata').val(JSON.stringify(data));
               var documentName = $('#documentID').val();
               let hasError = false;


               $(".hide_question_label").each(function(){
                    const uniqueId = $(this).attr('id').replace('hide_question_label', '');
                    const questionSection = $(this);
                    const displayStyle = questionSection.css('display');

                    if(displayStyle === 'block'){
                         const questionLabelInput = questionSection.find(".question_labl");
                         if(!hasError && !questionLabelInput.val()){
                              NioApp.Toast('Please fill the Question Label', 'error', { position: 'top-right' });
                              hasError = true;
                              return false;
                         }
                    }
               });

               $(".radio_ques").each(function(){
                    if(!hasError && !$(this).val()){
                         NioApp.Toast('Please fill the Question Label', 'error', { position: 'top-right' });
                         hasError = true;
                         return false;
                    }
               });

               $(".date_ques").each(function(){
                    if(!hasError && !$(this).val()){
                         NioApp.Toast('Please fill the Question Label', 'error', { position: 'top-right' });
                         hasError = true;
                         return false;
                    }
               });

               $(".dropdown_ques").each(function(){
                    if(!hasError && !$(this).val()){
                         NioApp.Toast('Please fill the Question Label', 'error', { position: 'top-right' });
                         hasError = true;
                         return false;
                    }
               });

               $(".same_contract").each(function(){
                    if(!hasError && !$(this).val()){
                         NioApp.Toast('Please fill the Same Contract Link Label', 'error', { position: 'top-right' });
                         hasError = true;
                         return false;
                    }
               });


               // $(".text_box_placeholder").each(function(){
               //      if(!hasError && !$(this).val()){
               //           NioApp.Toast('Please fill the Text Box Placeholder', 'error', { position: 'top-right' });
               //           hasError = true;
               //           return false;
               //      }
               // });

               $(".number_placeholder").each(function(){
                    if(!hasError && !$(this).val()){
                         NioApp.Toast('Please fill the Number field Placeholder', 'error', { position: 'top-right' });
                         hasError = true;
                         return false;
                    }
               });

               $('.add_cont_rw').each(function(){
                    const uniqueId = $(this).attr('id').replace('add_cont_rw', '');
                    const appendSection = $('#add_cont_rw' + uniqueId);
                    const contractSections = appendSection.find('.contract-option');
                    let conditionInvalid = false;

                    if(!hasError && contractSections.length !== 0){
                         contractSections.find('input').each(function(){
                              if(!$(this).val()){
                                   conditionInvalid = true;
                                   return false;
                              }
                         });
                    }

                    if(!hasError && conditionInvalid){
                         NioApp.Toast('Please fill in all required rows fields.', 'error', { position: 'top-right' });
                         hasError = true;
                         return false;
                    }
               })

               $(".go_to_step").each(function(){
                    if(!hasError && !$(this).val()){
                         NioApp.Toast('Please fill the Go to step', 'error', { position: 'top-right' });
                         hasError = true;
                         return false;
                    }
               });

               $('.append_options').each(function(){
                    if($(this).children().length > 0){
                         const uniqueId = $(this).attr('id').replace('append_options', '');
                         const appendSection = $('#append_options' + uniqueId);
                         const dropdownOptionSections = appendSection.find('.dropdown-option');
                         const radioOptionSections = appendSection.find('.radio-option');
                         let conditionInvalid = false;

                         if(!hasError && dropdownOptionSections.length !== 0){
                              dropdownOptionSections.find('input').each(function(){
                                   if(!$(this).val()){
                                        conditionInvalid = true;
                                        return false;
                                   }
                              });
                         }

                         if(!hasError && radioOptionSections.length !== 0){
                              radioOptionSections.find('input').each(function(){
                                   if(!$(this).val()){
                                        conditionInvalid = true;
                                        return false;
                                   }
                              });
                         }

                         if(!hasError && conditionInvalid){
                              NioApp.Toast('Please fill in all required options.', 'error', { position: 'top-right' });
                              hasError = true;
                              return false;
                         }
                    }else{
                         if(!hasError){
                              NioApp.Toast('Please add at least one option.', 'error', { position: 'top-right' });
                              hasError = true;
                              return false;
                         }
                    }
               })


               if(!hasError && data){
                    $('#questionForm').submit();
               }
          })
     })

</script>

<script>
     const contentClassMap = {
          content_heading: "append_content_heading",
          content: "append_content",
          signature_field: "append_signature_field",
     };

     const contentTemplates = {
          "content_heading": (content_id) => `<div class="form-group">
                                             <div class="row">
                                                  <div class="col-md-6">
                                                      <!-- <label class="form-label" for="content_heading_html${content_id}">Text</label>-->
                                                  </div>
                                                  <div class="col-md-6 text_align_div">
                                                       <div class="form-group">
                                                            <div class="form-control-wrap">
                                                                 <select class="form-select js-select2 text_align" name="text_align-${content_id}" id="text_align${content_id}">
                                                                      <option value="left" selected>Align Left</option>
                                                                      <option value="right">Align Right</option>
                                                                      <option value="center">Align Center</option>
                                                                 </select>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="form-group input-box active">
                                                <label class="form-label" for="content_heading_html${content_id}">Text</label>
                                                <input type="text" class="form-control new_heading_html mt-2" name="content_heading_html-${content_id}" id="content_heading_html${content_id}" value="">
                                                 </div>
                                             </div>`,
          "content": (content_id) => `<div class="col-md-12">
                                        <div class="form-group">
                                             <div class="row">
                                                  <div class="col-md-6">
                                                       <!--<label class="form-label" for="content_content_html">Text</label>-->
                                                  </div>
                                                  <div class="col-md-6 text_align_div">
                                                       <div class="form-group">
                                                            <div class="form-control-wrap">
                                                                 <select class="form-select js-select2 text_align" name="text_align-${content_id}" id="text_align${content_id}">
                                                                      <option value="left" selected>Align Left</option>
                                                                      <option value="right">Align Right</option>
                                                                      <option value="center">Align Center</option>
                                                                 </select>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                             <div class="form-group input-box active">
                                                 <label class="form-label" for="content_content_html">Text</label>

                                             <textarea class="form-control content_content_html mt-2" name="content_content_html-${content_id}" id="content_content_html${content_id}"></textarea>
                                            </div>
                                             </div>
                                   </div>
                                   <hr>
                                   <div class="ad_cnd_div" id="ad_cnd_div${content_id}">
                                        <div class="grey_btn_div">
                                             <button type="button" class="btn btn-sm btn-primary add_btn${content_id} grey-btn" onclick="addContractCondition('${content_id}','content')">Add Condition</button>
                                        </div>
                                        <div class="append_condition" id="append_condition${content_id}"></div>
                                   </div>
                                   <hr>
                                   <div class="row">
                                        <div class="col-md-6">
                                             <!-- <p class="p_label">Blur Content</p> -->
                                             <div class="custom-control custom-checkbox">
                                                  <input type="checkbox" class="custom-control-input" id="secure_blurr_content${content_id}" name="secure_blurr_content${content_id}">
                                                  <label class="custom-control-label" for="secure_blurr_content${content_id}">Blur Content</label>
                                             </div>
                                        </div>
                                   </div>`,
          'signature_field':(content_id) =>
                                        `<div class="append_textBox" id="append_textBox${content_id}">
                                   <div class="row textbox_section" id="textbox_section${content_id}">
                                        <div class="col-md-10">
                                             <div class="form-group">
                                                  <div class="row">
                                                       <div class="col-md-6">
                                                            <!--<label class="form-label" for="sign_content">Text</label>-->
                                                       </div>
                                                       <div class="col-md-6 text_align_div">
                                                            <div class="form-group">
                                                                 <div class="form-control-wrap">
                                                                      <select class="form-select js-select2 text_align" name="text_align-${content_id}" id="text_align${content_id}">
                                                                           <option value="left" selected>Align Left</option>
                                                                           <option value="right">Align Right</option>
                                                                           <option value="center">Align Center</option>
                                                                      </select>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                                <div class="form-group input-box active">
                                                     <label class="form-label" for="sign_content">Text</label>
                                                    <input type="text" class="form-control signature_text mt-2" name="sign_content" id="sign_content" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 form-group prnt_add_cls">
                                             <span class="remove_icon add_icon" id="textbox_add_btn${content_id}" onclick="addTextbox('${content_id}')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                   </div>
                              </div>
                              <hr>
                              <div class="ad_cnd_div" id="ad_cnd_div${content_id}">
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary add_btn${content_id} grey-btn" onclick="addContractCondition('${content_id}','signature_field')">Add Condition</button>
                                   </div>
                                   <div class="append_signature_condition" id="append_signature_condition${content_id}"></div>
                              </div>
                              <hr>
                              <div class="row">
                                   <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                             <input type="checkbox" class="custom-control-input" id="secure_blurr_content${content_id}" name="secure_blurr_content${content_id}">
                                             <label class="custom-control-label" for="secure_blurr_content${content_id}">Blur Content</label>
                                        </div>
                                   </div>
                              </div>`,
     }

     function changeContentType(e) {
          var content_id = $(e).data("content-id");
          var change_from = $(e).data("change-from");
          var change_to = $(e).val();
          var option_name = '';

          $(e).data("change-from", change_to);
          var hiddenInput = $("#changed_content_type");
          var existingChanges = JSON.parse(hiddenInput.val());

          let mainBoxID = `.custom_box_${content_id}`;
          let hideBoxID = `.hide_box_${content_id}`;
          let main = $(e).closest(`.${contentClassMap[change_from]}`);
          main.removeClass(`${contentClassMap[change_from]}`).addClass(`${contentClassMap[change_to]}`);

          if(change_from === "content_heading" && change_to === "content"){
               const value = $(mainBoxID).find('input[type="text"]').val();
               $(mainBoxID).html(contentTemplates[change_to](content_id));
               $(mainBoxID).find('textarea').val(value);

          }else if(change_from === "content" && change_to === "content_heading") {
               const value = $(mainBoxID).find('textarea').val();
               $(mainBoxID).html(contentTemplates[change_to](content_id));
               $(mainBoxID).find('input[type="text"]').val(value);
               $(hideBoxID).hide();
          }else{

               let value = '';
               if($(mainBoxID).find('textarea').length){
                    value = $(mainBoxID).find('textarea').val();
               }else if($(mainBoxID).find('input[type="text"]').length){
                    value = $(mainBoxID).find('input[type="text"]').val();
               }

               $(mainBoxID).html(contentTemplates[change_to](content_id));
               $(mainBoxID).find('input[type="text"]').val(value);
          }

          var foundIndex = existingChanges.findIndex((q) => q.content_id === content_id);
          if(foundIndex !== -1){
               existingChanges[foundIndex].change_to = change_to;
          }else{
               existingChanges.push({ content_id: content_id, change_from: change_from, change_to: change_to });
          }
          hiddenInput.val(JSON.stringify(existingChanges));

          if(change_to == "content"){
               option_name = 'Content';
          }else if(change_to == "content_heading"){
               option_name = 'Content Heading';
          }else if(change_to == "signature_field"){
               option_name = 'Signature Field';
          }

          $(e).closest('.col-md-6').find('.cnt_heding').show();
          $(e).closest('.col-md-6').find('.cnt_heding').html(`<p class="drop_options"><b>${option_name} <em class="icon ni ni-edit drop_options"></em></b></p>`);
          $(e).closest('.col-md-6').find('.drop_box_option').hide();

     }

     $(document).on('change', '.type_content', function() {
          const value = $(this).val();
          const id = $(this).attr('id').replace('content_type', '');
          addContent(value, id, 'second');
     });

     let heading_section_count = 0;
     let content_section_count = 0;
     let num1 = "{{ $order1 ?? '' }}";

     function addContent(name,id,key,element=null){
          const newUniqueId = Date.now();
          let html = ``;

          if(name === 'content_heading'){
               heading_section_count++ ;
               html = `<div class="append_content_heading new_cont_sec${newUniqueId}" id="content_heading${newUniqueId}" value="appended" data-is_new=true data-order_id="${num1}">
                    <!-- <hr> -->
                    <div class="card card-bordered card-preview">
                         <div class="card-inner">
                              <div class="text-end">
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <span class="col-md-2 offset-md-10">
                                                  <span class="remove_icon red_hover" onclick="removeContent(this)" value="appended" data-field="content_heading"><i class="fa fa-trash"></i></span>
                                             </span>
                                        </div>
                                   </div>
                              </div>
                              <div class="row add_content_heading">
                                   <div class="col-md-6">
                                        <select class="form-select js-select2 type_content" name="content_type${newUniqueId}" id="content_type${newUniqueId}">
                                             <option value="content_heading" ${name === 'content_heading' ? 'selected' : ''}>Headline</option>
                                             <option value="content" ${name === 'content' ? 'selected' : ''}>Content</option>
                                             <option value="signature_field" ${name === 'signature_field' ? 'selected' : ''}>Signature</option>
                                        </select>
                                   </div>
                              </div>
                              <hr>
                              <div class="col-md-12">

                                        <div class="row">
                                             <div class="col-md-6">
                                                  <!--<label class="form-label" for="content_heading_html${newUniqueId}">Text</label>-->
                                             </div>
                                             <div class="col-md-6 text_align_div">
                                                  <div class="form-group">
                                                       <div class="form-control-wrap">
                                                            <select class="form-select js-select2 text_align" name="text_align-${newUniqueId}" id="text_align${newUniqueId}">
                                                                 <option value="left" selected>Align Left</option>
                                                                 <option value="right">Align Right</option>
                                                                 <option value="center">Align Center</option>
                                                            </select>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                    <div class="form-group input-box active">
                                         <label class="form-label" for="content_heading_html${newUniqueId}">Text</label>

                                        <input type="text" class="form-control new_heading_html mt-2" name="content_heading_html-${newUniqueId}" id="content_heading_html${newUniqueId}" value="">
                                   </div>
                              </div>
                         </div>
                    </div>
                    <br>
               </div>`
          }else if(name === 'content'){
               content_section_count++ ;

               html = `<div class="append_content new_cont_sec${newUniqueId}" id="content${newUniqueId}" value="appended" data-is_new=true data-order_id="${num1}">
                    <!-- <hr> -->
                    <div class="card card-bordered card-preview">
                         <div class="card-inner">
                              <div class="text-end">
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <span class="col-md-2 offset-md-10">
                                                  <span class="remove_icon red_hover" onclick="removeContent(this)" value="appended" data-field="content"><i class="fa fa-trash"></i></span>
                                             </span>
                                        </div>
                                   </div>
                              </div>
                              <div class="row">
                                   <div class="col-md-6">
                                        <select class="form-select js-select2 type_content" name="content_type${newUniqueId}" id="content_type${newUniqueId}">
                                             <option value="content_heading" ${name === 'content_heading' ? 'selected' : ''}>Headline</option>
                                             <option value="content" ${name === 'content' ? 'selected' : ''}>Content</option>
                                             <option value="signature_field" ${name === 'signature_field' ? 'selected' : ''}>Signature</option>
                                        </select>
                                   </div>

                              </div>
                              <hr>
                              <div class="col-md-12">
                                        <div class="row">
                                             <div class="col-md-6">
                                                <!--<label class="form-label" for="content_content_html">Text</label>-->
                                                  </div>
                                             <div class="col-md-6 text_align_div">
                                                  <div class="form-group">
                                                       <div class="form-control-wrap">
                                                            <select class="form-select js-select2 text_align" name="text_align-${newUniqueId}" id="text_align${newUniqueId}">
                                                                 <option value="left" selected>Align Left</option>
                                                                 <option value="right">Align Right</option>
                                                                 <option value="center">Align Center</option>
                                                            </select>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                    <div class="form-group input-box active">
                                        <label class="form-label" for="content_content_html">Text</label>
                                        <textarea class="form-control content_content_html mt-2" name="content_content_html-${newUniqueId}" id="content_content_html${newUniqueId}"></textarea>
                                   </div>
                              </div>
                              <hr>
                              <div class="ad_cnd_div" id="ad_cnd_div${newUniqueId}">
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary add_btn${newUniqueId} grey-btn" onclick="addContractCondition('${newUniqueId}','content')">Add Condition</button>
                                   </div>
                                   <div class="append_condition" id="append_condition${newUniqueId}"></div>
                              </div>
                              <hr>
                              <div class="row">
                                   <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                             <input type="checkbox" class="custom-control-input" id="secure_blurr_content${newUniqueId}" name="secure_blurr_content${newUniqueId}">
                                             <label class="custom-control-label" for="secure_blurr_content${newUniqueId}">Blur Content</label>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <br>
               </div>`
          }else if(name === 'signature_field'){
               // $('#signature_field_hidden').val(1);
               html = `<div class="append_signature_field new_cont_sec${newUniqueId}" id="signature${newUniqueId}" value="appended" data-is_new=true data-order_id="${num1}">
                    <!-- <hr> -->
                    <div class="card card-bordered card-preview">
                         <div class="card-inner">
                              <div class="text-end">
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <span class="col-md-2 offset-md-10">
                                                  <span class="remove_icon red_hover" onclick="removeContent(this)" value="appended" data-field="signature"><i class="fa fa-trash"></i></span>
                                             </span>
                                        </div>
                                   </div>
                              </div>
                              <hr>
                              <div class="row">
                                   <div class="col-md-6">
                                        <select class="form-select js-select2 type_content" name="content_type${newUniqueId}" id="content_type${newUniqueId}">
                                             <option value="content_heading" ${name === 'content_heading' ? 'selected' : ''}>Headline</option>
                                             <option value="content" ${name === 'content' ? 'selected' : ''}>Content</option>
                                             <option value="signature_field" ${name === 'signature_field' ? 'selected' : ''}>Signature</option>
                                        </select>
                                   </div>

                              </div>
                              <hr>
                              <!-- <div class="col-md-12">
                                  <p class="p_label">This is signature field</p>
                                   <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="signature_field${newUniqueId}" name="signature_field-${newUniqueId}">
                                        <label class="custom-control-label" for="signature_field${newUniqueId}"></label>
                                   </div>
                              </div> -->
                              <div class="append_textBox" id="append_textBox${newUniqueId}">
                                   <div class="row textbox_section" id="textbox_section${newUniqueId}">
                                        <div class="col-md-10">
                                             <!--<div class="form-group">-->
                                                  <div class="row">
                                                       <div class="col-md-6">
                                                            <!--<label class="form-label" for="sign_content">Text</label>-->
                                                       </div>
                                                       <div class="col-md-6 text_align_div">
                                                            <div class="form-group">
                                                                 <div class="form-control-wrap">
                                                                      <select class="form-select js-select2 text_align" name="text_align-${newUniqueId}" id="text_align${newUniqueId}">
                                                                           <option value="left" selected>Align Left</option>
                                                                           <option value="right">Align Right</option>
                                                                           <option value="center">Align Center</option>
                                                                      </select>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                                <div class="form-group input-box active">
                                                    <label class="form-label" for="sign_content">Text</label>
                                                  <input type="text" class="form-control signature_text mt-2" name="sign_content" id="sign_content" value="">
                                             </div>
                                        </div>
                                        <div class="col-md-2 form-group prnt_add_cls">
                                             <span class="remove_icon add_icon" id="textbox_add_btn${newUniqueId}" onclick="addTextbox('${newUniqueId}')"><i class="fa-solid fa-add"></i></span>
                                        </div>
                                   </div>
                              </div>
                              <hr>
                              <div class="ad_cnd_div" id="ad_cnd_div${newUniqueId}">
                                   <div class="grey_btn_div">
                                        <button type="button" class="btn btn-sm btn-primary add_btn${newUniqueId} grey-btn" onclick="addContractCondition('${newUniqueId}','signature_field')">Add Condition</button>
                                   </div>
                                   <div class="append_signature_condition" id="append_signature_condition${newUniqueId}"></div>
                              </div>
                              <hr>
                              <div class="row">
                                   <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                             <input type="checkbox" class="custom-control-input" id="secure_blurr_content${newUniqueId}" name="secure_blurr_content${newUniqueId}">
                                             <label class="custom-control-label" for="secure_blurr_content${newUniqueId}">Blur Content</label>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <br>
               </div>`;
          }

          if(key === 'first'){
               $('.add_contents').append(html);
               $('.question_dropbtn').hide();
          }else if(key === 'second'){
               $('.new_cont_sec'+id).replaceWith(html);
               $('.question_dropbtn').hide();
          }else if(key === 'third'){
               if(!element) {
                    console.error("No element provided for 'third' layout insertion.");
                    return;
               }

               let $clickedBtn = $(element);
               let $nearestSection = $clickedBtn.closest(".add_contents > div");
               let $insertedElement;

               if($nearestSection.length){
                    // alert("Layout has been added");
                    $nearestSection.append(html);
               }else{
                    $(".add_contents").append(html);
               }

               updateContentIds();
          }

          num1++ ;
     }

     function removeContent(e){
          $('.question_dropbtn').show();
          if($(e).attr('data-field') === 'content_heading'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_content_heading').remove();
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).attr('data-id');
                              let deleteIds = $('#remove_content_heading').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_content_heading').val(deleteIds);
                              $('#content_heading'+id).hide();

                              if($('.add_contents').length){
                                   $('#textmodal' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "text",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.text_block_'+id).hide();
                                        }
                                   }
                              })
                         }
                    });
               }
          }else if($(e).attr('data-field') === 'content'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_content').remove();
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).attr('data-id');
                              let deleteIds = $('#remove_content').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_content').val(deleteIds);
                              $('#content'+id).hide();

                              if($('.add_contents').length){
                                   $('#textmodal' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "text",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.text_block_'+id).hide();
                                        }
                                   }
                              })
                         }
                    });
               }
          }else if($(e).attr('data-field') === 'signature'){
               if($(e).attr('value') === 'appended'){
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.append_signature_field').remove();
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).attr('data-id');
                              let deleteIds = $('#remove_signature').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              }else{
                                   deleteIds = id;
                              }
                              $('#remove_signature').val(deleteIds);
                              $('#signature'+id).hide();

                              if($('.add_contents').length){
                                   $('#textmodal' + id).modal('hide');
                              }

                              var data = {
                                   id: id,
                                   type: "text",
                                   _token: "{{ csrf_token() }}",
                              }

                              $.ajax({
                                   url: "{{ route('admin.delete.document_questions') }}",
                                   type: "post",
                                   dataType: "json",
                                   data: data,
                                   success: function(response){
                                        console.log(response);
                                        if(response){
                                             $('.text_block_'+id).hide();
                                        }
                                   }
                              })
                         }
                    });
               }
          }
     }

     let textCount = 0;
     function addTextbox(id){
          console.log(id);
          textCount++ ;
          const parentDiv = $('#append_textBox' + id);
          if(parentDiv.find('.textbox_section').length === 2){
               $('#textbox_add_btn'+id).hide();
          }

          let html = `<div class="row textbox_section" id="textbox_section${id}">
                    <div class="col-md-10">
                         <div class="form-group input-box active">
                              <label class="form-label" for="new_sign_content${textCount}">Text</label>
                              <input type="text" class="form-control signature_text" name="new_sign_content-${textCount}[]" id="new_sign_content${textCount}" value="">
                         </div>
                    </div>
               </div>`;


          console.log(html);
          $('#append_textBox'+id).append(html);

     }

     function removeTextbox(e){
          if($(e).attr('value') === 'appended'){
               Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
               }).then((result) => {
                    if(result.isConfirmed){
                         $(e).closest('.add_textbox').remove();
                    }
               });
          }
     }

     let condition_count1 = 0;
     function addContractCondition(id,type){
          condition_count1++ ;

          if(type == 'content'){
               const html = `<div class="condition-section" id="condition-section${id}" value="appended" data-is_new=true>
                         <div class="row">
                              <div class="col-md-3">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="condition_question_id-${condition_count1}">Question ID</label>
                                        <div class="form-control-wrap question">
                                             <select class="form-select js-select2 condition_question_id" data-search="on" name="condition_question_id-${condition_count1}[]" id="condition_question_id-${condition_count1}">
                                                  @if(isset($questions) && $questions != null)
                                                       @foreach($questions as $question)
                                                            <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                       @endforeach
                                                  @endif
                                             </select>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-md-4">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="conditions-${condition_count1}">Condition</label>
                                        <div class="form-control-wrap">
                                             <select class="form-select js-select2 conditions" name="conditions-${condition_count1}[]" id="conditions-${condition_count1}">
                                                  <option value="" selected disabled>Select</option>
                                                  <option value="is_equal_to">is equal to</option>
                                                  <option value="is_greater_than">is greater than</option>
                                                  <option value="is_less_than">is less than</option>
                                                  <option value="not_equal_to">not equal to</option>
                                             </select>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-md-3">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="condition_question_value-${condition_count1}">Question Value</label>
                                        <input type="text" class="form-control new_condition_question_value" id="condition_question_value-${condition_count1}" name="condition_question_value-${condition_count1}[]" value="">
                                   </div>
                              </div>
                              <div class="col-md-2 cont_add_rmv3">
                                   <div class=form-group prnt_add_cls">
                                        <span class="remove_icon red_hover" onclick="removeContractCondition(this,'content')" value="appended">
                                             <i class="fa fa-trash"></i>
                                        </span>
                                   </div>
                                   <div class="form-group prnt_add_cls">
                                        <span class="remove_icon add_icon" onclick="addContractCondition('${id}','content')"><i class="fa-solid fa-add"></i></span>
                                   </div>                                                               
                              </div>
                         </div>
                         <br>
                    </div> `
               $('#append_condition'+id).append(html);
               $('.add_btn'+id).hide();
          }else if(type == 'signature_field'){
               const html = `<div class="condition-section" id="condition-section${id}" value="appended" data-is_new=true>
                         <div class="row">
                              <div class="col-md-3">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="condition_question_id-${condition_count1}">Question ID</label>
                                        <div class="form-control-wrap question">
                                             <select class="form-select js-select2 condition_question_id" data-search="on" name="condition_question_id-${condition_count1}[]" id="condition_question_id-${condition_count1}">
                                                  @if(isset($questions) && $questions != null)
                                                       @foreach($questions as $question)
                                                            <option value="{{ $question->getName() ?? '' }}">{{ $question->getName() ?? '' }}</option>
                                                       @endforeach
                                                  @endif
                                             </select>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-md-4">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="conditions-${condition_count1}">Condition</label>
                                        <div class="form-control-wrap">
                                             <select class="form-select js-select2 conditions" name="conditions-${condition_count1}[]" id="conditions-${condition_count1}">
                                                  <option value="" selected disabled>Select</option>
                                                  <option value="is_equal_to">is equal to</option>
                                                  <option value="is_greater_than">is greater than</option>
                                                  <option value="is_less_than">is less than</option>
                                                  <option value="not_equal_to">not equal to</option>
                                             </select>
                                        </div>
                                   </div>
                              </div>
                              <div class="col-md-3">
                                   <div class="form-group input-box active">
                                        <label class="form-label" for="condition_question_value-${condition_count1}">Question Value</label>
                                        <input type="text" class="form-control condition_question_value" id="condition_question_value-${condition_count1}" name="condition_question_value-${condition_count1}[]" value="">
                                   </div>
                              </div>
                              <div class="col-md-2 cont_add_rmv4">
                                   <div class="form-group prnt_add_cls">
                                        <span class="remove_icon red_hover" onclick="removeContractCondition(this,'signature_field')" value="appended">
                                             <i class="fa fa-trash"></i>
                                        </span>
                                   </div>
                                   <div class="form-group prnt_add_cls">
                                        <span class="remove_icon add_icon" onclick="addContractCondition('${id}','signature_field')"><i class="fa-solid fa-add"></i></span>
                                   </div>
                              </div>
                         </div>
                         <br>
                    </div> `
               $('#append_signature_condition'+id).append(html);
               $('.add_btn'+id).hide();
          }


     }

     function removeContractCondition(e,type){
          if(type == 'content'){
               if($(e).attr('value') === 'appended'){
                    const uniqueId = $(e).closest('.condition-section').attr('id').replace('condition-section', '');
                    console.log(uniqueId);

                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.condition-section').remove();
                              const parentDiv = $('#append_condition' + uniqueId);
                              if(parentDiv.find('.condition-section').length === 0){
                                   $('.add_btn'+uniqueId).show();
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              console.log(id);
                              let deleteIds = $('#remove_condition').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              } else {
                                   deleteIds = id;
                              }
                              $('#remove_condition').val(deleteIds);
                              $('#condition-section' + id).hide();

                              const container = $(e).closest('.append_condition');
                              const uniqueId = $(container).attr('id').replace('append_condition', '');

                              if(container.find('.condition-section:visible').length === 0){
                                   console.log('if');
                                   const html = `<div class="grey_btn_div"><button type="button" class="btn btn-sm btn-primary add_btn${uniqueId} grey-btn" onclick="addCondition('${uniqueId}','content')">Add Condition</button></div>
                                        <div class="append_condition" id="append_condition${uniqueId}"></div>`;

                                   $('#ad_cnd_div' + uniqueId).html(html);
                              }

                         }
                    });
               }
          }else if(type == 'signature_field'){
               if($(e).attr('value') === 'appended'){
                    const uniqueId = $(e).closest('.condition-section').attr('id').replace('condition-section', '');
                    console.log(uniqueId);

                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              $(e).closest('.condition-section').remove();
                              const parentDiv = $('#append_signature_condition' + uniqueId);
                              if(parentDiv.find('.condition-section').length === 0){
                                   $('.add_btn'+uniqueId).show();
                              }
                         }
                    });
               }else{
                    Swal.fire({
                         title: 'Are you sure?',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#DD6B55',
                         confirmButtonText: 'Yes',
                         cancelButtonText: 'Cancel',
                    }).then((result) => {
                         if(result.isConfirmed){
                              var id = $(e).data('id');
                              let deleteIds = $('#remove_condition').val();
                              if(deleteIds){
                                   deleteIds += ',' + id;
                              } else {
                                   deleteIds = id;
                              }
                              $('#remove_condition').val(deleteIds);
                              $('#condition-section' + id).hide();

                              const container = $(e).closest('.append_signature_condition');
                              const uniqueId = $(container).attr('id').replace('append_signature_condition', '');

                              if(container.find('.condition-section:visible').length === 0){
                                   console.log('if');
                                   const html = `<div class="grey_btn_div"><button type="button" class="btn btn-sm btn-primary add_btn${uniqueId} grey-btn" onclick="addCondition('${uniqueId}','content')">Add Condition</button></div>
                                        <div class="append_signature_condition" id="append_signature_condition${uniqueId}"></div>`;

                                   $('#ad_cnd_div' + uniqueId).html(html);
                              }

                         }
                    });
               }
          }

     }

     $(document).ready(function() {
          $(document).on('change', '[id^="add_condition"]', function() {
               const id = $(this).attr('id').replace('add_condition', '');
               conditionalOptions(id);
          });

          $(document).on('change', '[id^="start_new_section"]', function() {
               const id = $(this).attr('id').replace('start_new_section', '');
               startNewSection(id);
          });

          $(document).on('change', '[id^="signature_field"]', function() {
               const id = $(this).attr('id').replace('signature_field', '');
               toggleCheckboxValue($(this), 'signature_field' + id);
          });

          $(document).on('change', '[id^="secure_blurr_content"]', function() {
               const id = $(this).attr('id').replace('secure_blurr_content', '');
               goToSteps(id);
          });

          $(document).on('change', '[id^="blurr_content"]', function() {
               const id = $(this).attr('id').replace('blurr_content', '');
               toggleCheckboxValue($(this), 'blurr_content' + id);
          });
     });

     function conditionalOptions(id) {
          if ($('#add_condition' + id).is(':checked')) {
               $('.add_condition_section' + id).show();
               $('#add_condition' + id).val(1);
          } else {
               $('.add_condition_section' + id).hide();
               $('#add_condition' + id).val(0);
          }
     }

     function goToSteps(id) {
          if($('#secure_blurr_content' + id).is(':checked')) {
               $('#secure_blurr_content' + id).val(1);
          }else {
               $('#secure_blurr_content' + id).val(0);
          }
     }

     function toggleCheckboxValue(element, id) {
          if(element.is(':checked')) {
               $('#' + id).val(1);
          } else {
               $('#' + id).val(0);
          }
     }

     function getAllContents() {
          var contents = [];

          $('.add_contents .append_content_heading').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');
               var headingInputValue = $(this).find('input[type="text"]').val();
               var textAlign = $(this).find('select[name^="text_align"]').val() || '';

               contents.push({
                    section: 'content_heading',
                    id: id,
                    is_new: is_new,
                    heading_html: headingInputValue,
                    text_align: textAlign,
                    order_id: order_id,
               });
          });

          $('.add_contents .append_content').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');
               var textAlign = $(this).find('select[name^="text_align"]').val() || '';
               var contentHtml = $(this).find('textarea[name^="content_content_html"]').val();
               var contentClass = $(this).find('input[name^="content_class"]').val() || '';
               var secureBlurrContent = $(this).find('input[name^="secure_blurr_content"]').is(':checked') ? 1 : 0;

               var contentData = {
                    section: 'content',
                    is_new: is_new,
                    id: id,
                    text_align: textAlign,
                    content_html: contentHtml,
                    content_class: contentClass,
                    add_condition: 0,
                    secure_blurr_content: secureBlurrContent,
                    conditions: [],
                    new_conditions: [],
                    order_id: order_id,
               };

               if($(this).find('.append_condition .condition-section').length > 0){
                    $(this).find('.append_condition .condition-section').each(function () {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var new_condition = {
                                   question_id: $(this).find('select[name^="condition_question_id"]').val() || '',
                                   condition: $(this).find('select[name^="conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(new_condition.question_id && new_condition.condition || new_condition.question_value) {
                                   contentData.new_conditions.push(new_condition);
                                   contentData.add_condition = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   question_id: $(this).find('select[name^="condition_question_id"]').val() || '',
                                   condition: $(this).find('select[name^="conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId
                              };

                              if(condition.question_id && condition.condition || condition.question_value) {
                                   contentData.conditions.push(condition);
                                   contentData.add_condition = 1;
                              }
                         }
                    });
               }
               contents.push(contentData);
          });

          $('.add_contents .append_signature_field').each(function(){
               var is_new = $(this).data('is_new');
               var id = $(this).data('id');
               var order_id = $(this).data('order_id');
               var textAlign = $(this).find('select[name^="text_align"]').val() || '';
               var content =  $(this).find('input[name^="sign_content_1"]').val() || '';
               var content2 =  $(this).find('input[name^="sign_content2"]').val() || '';
               var content3 =  $(this).find('input[name^="sign_content3"]').val() || '';
               var sign_content =  $(this).find('input[name="sign_content"]').val() || '';
               var new_sign_content = [];
               $(this).find('input[name^="new_sign_content"]').each(function(){
                    new_sign_content.push($(this).val());
               });

               var secureBlurrContent = $(this).find('input[name^="secure_blurr_content"]').is(':checked') ? 1 : 0;

               var contentData = {
                    section: 'signature_field',
                    id: id,
                    is_new: is_new,
                    text_align: textAlign,
                    content: content,
                    content2: content2,
                    content3: content3,
                    sign_content: sign_content,
                    new_sign_content: new_sign_content,
                    secure_blurr_content: secureBlurrContent,
                    order_id: order_id,
                    add_condition: 0,
                    conditions: [],
                    new_conditions: [],
               };

               if($(this).find('.append_signature_condition .condition-section').length > 0){
                    $(this).find('.append_signature_condition .condition-section').each(function () {
                         var status = $(this).data('is_new');
                         var conditionId = $(this).data('id');

                         if(status === true){
                              var new_condition = {
                                   question_id: $(this).find('select[name^="condition_question_id"]').val() || '',
                                   condition: $(this).find('select[name^="conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                              };

                              if(new_condition.question_id && new_condition.condition || new_condition.question_value) {
                                   contentData.new_conditions.push(new_condition);
                                   contentData.add_condition = 1;
                              }
                         }else if(status === false){
                              var condition = {
                                   question_id: $(this).find('select[name^="condition_question_id"]').val() || '',
                                   condition: $(this).find('select[name^="conditions"]').val() || '',
                                   question_value: $(this).find('input[name^="condition_question_value"]').val() || '',
                                   status: status,
                                   condition_id: conditionId
                              };

                              if(condition.question_id && condition.condition || condition.question_value) {
                                   contentData.conditions.push(condition);
                                   contentData.add_condition = 1;
                              }
                         }
                    });
               }
               contents.push(contentData);
          });

          // console.log(contents);
          return contents;
     }

     $(document).ready(function () {
          $('.saveFormdata').click(function (e) {
               var data = getAllContents();
               $('#contentdata').val(JSON.stringify(data));
               
               var documentName = $('#documentId').val();
               console.log(documentName);
               // return;
               let hasError = false;

               $(".new_heading_html").each(function(){
                    if (!hasError && !$(this).val()) {
                         NioApp.Toast('Please fill the heading HTML field', 'error', { position: 'top-right' });
                         hasError = true;
                         return false;
                    }
               });


               $(".content_content_html").each(function(){
                    const uniqueId = $(this).attr('id').replace('content_content_html', '');

                    if (!hasError && !$(this).val().trim()) {
                         NioApp.Toast('Please fill the Text field', 'error', { position: 'top-right' });
                         hasError = true;
                         return false;
                    }
               });

               $('.ad_cnd_div').each(function (){
                    const uniqueId = $(this).attr('id').replace('ad_cnd_div', '');
                    if(!hasError){
                         const appendSection = $('#append_condition' + uniqueId);
                         const conditionSections = appendSection.find('.condition-section');

                         let conditionInvalid = false;
                         conditionSections.find('select').each(function(){
                              if (!$(this).val()) {
                                   conditionInvalid = true;
                                   return false; // Breaks the .each loop
                              }
                         });

                         if(conditionInvalid){
                              NioApp.Toast('Please fill in all required condition fields.', 'error', { position: 'top-right' });
                              hasError = true;
                              return false;
                         }
                    }
               });


               if(!hasError && (!documentName || documentName.trim() === "")){
                    NioApp.Toast('Please select the document', 'error', { position: 'top-right' });
                    hasError = true;
               }

               if(!hasError){
                    $('#updatecontentForm').submit();
               }

          });

          var switchStatus = false;
          $(".publish").on('change', function() {
               if($(this).is(':checked')) {
                    switchStatus = $(this).is(':checked');
                    $('#published').val(1);
               }else{
                    switchStatus = $(this).is(':checked');
                    $('#published').val(0);
               }
          })
     });

     $(document).ready(function(){
          $('body').delegate('.drop_options','click', function(){
               $(this).closest('.col-md-6').find('.cnt_heding').hide();
               $(this).closest('.col-md-6').find('.drop_box_option').show();
          });
     });

     function removeTextDropbox(e){
          console.log(e);
          $(e).closest('.col-md-6').find('.drop_box_option').hide();
          $(e).closest('.col-md-6').find('.cnt_heding').show();
     }

    

     function updateContentIds() {
          $(".add_contents [data-order_id]").each(function(index) {
               $(this).attr("data-order_id", index + 1);
          });
     }
</script>

<script>
     document.addEventListener("DOMContentLoaded", function () {
          document.querySelectorAll(".input-box input, .input-box select, .input-box textarea").forEach(function (input) {

               if (input.value.trim()) {
                    input.parentNode.classList.add("active");
               }

               input.addEventListener("focus", function () {
                    input.parentNode.classList.add("active");
               });

               input.addEventListener("blur", function () {
                    if (!input.value) {
                         input.parentNode.classList.remove("active");
                    }
               });
          });
     });
</script>

<script>
     $(document).ready(function(){
          $('#back').click(function(){
               $('.step2').hide();
               $('.step1').show();
          })
     })
</script>

@endsection

