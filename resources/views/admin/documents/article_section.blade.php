@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="container-fluid">
          <form action="{{ route('admin.dashboard.add_article_section') }}" id="articleForm" method="post" enctype="multipart/form-data">
               @csrf
               <div class="col-md-12 pb-4">
                    <h2>Article Section</h2>
               </div>
               <div class="row">
                    <div class="col-md-8">
                         @if(isset($article_sections) && count($article_sections) > 0)
                         @foreach($article_sections as $index => $section)
                         @php $i = $index + 1; @endphp
                       
                         <div class="article_section{{ $i ?? ''}}">
                              <div class="card card-bordered card-preview mt-4">
                                   <div class="card-inner">
                                        <div class="d-flex justify-content-between align-items-center">
                                             <h6>Article Section</h6>
                                        </div>
                                        <hr>
                                        <div class="col-md-12 mt-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="article_heading">
                                                       Heading
                                                  </label>
                                                  
                                                  <input type="text" class="form-control" id="article_heading{{ $section->id ?? '' }}"
                                                       name="article_heading[{{ $section->id ?? '' }}]" value="{{ $section->heading ?? '' }}">
                                                  
                                                  <span class="text-danger heading_error"></span>
                                             </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="article_description">
                                                       Description
                                                  </label>
                                                  
                                                  <textarea class="form-control" id="article_description{{ $section->id ?? '' }}"
                                                       name="article_description[{{ $section->id ?? '' }}]">{{ $section->description ?? '' }}</textarea>
                                                  
                                                  <span class="text-danger description_error"></span>
                                             </div>
                                        </div>
                                        
                                   </div>
                              </div>
                         </div>
                         <script>
                              // Article description //
                              if (!window.ClassicEditorInstances) window.ClassicEditorInstances = {};
                              ClassicEditor
                                   .create(document.querySelector('#article_description{{ $section->id ?? '' }}'), {
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
                              .then(editor => {
                                   ClassicEditorInstances['article_description{{ $section->id ?? '' }}'] = editor;
                              })
                              .catch(error => {
                                   console.error('error:', error);
                              });

                              
                         </script>
                         @endforeach
                         @else
                         @php $num=3; @endphp
                         @for($i=1; $i<=$num; $i++)
                         <div class="article_section{{ $i ?? ''}}">
                              <div class="card card-bordered card-preview mt-4">
                                   <div class="card-inner">
                                        <div class="d-flex justify-content-between align-items-center">
                                             <h6>Article Section</h6>
                                        </div>
                                        <hr>
                                        <div class="col-md-12 mt-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="new_article_heading">
                                                       Heading
                                                  </label>
                                                  
                                                  <input type="text" class="form-control" id="new_article_heading"
                                                       name="new_article_heading[]" value="">

                                                  <span class="text-danger heading_error"></span>
                                             </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="new_article_description">
                                                       Description
                                                  </label>
                                                  
                                                  <textarea class="form-control" id="new_article_description{{ $i ?? '' }}"
                                                       name="new_article_description[]"></textarea>

                                                  <span class="text-danger description_error"></span>
                                             </div>
                                        </div>
                                        
                                   </div>
                              </div>
                         </div>
                         @endfor
                         <script>
                              if (!window.ClassicEditorInstances) window.ClassicEditorInstances = {};

                              // Article description //
                              ClassicEditor
                                   .create(document.querySelector('#new_article_description{{ $i ?? '' }}'), {
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
                              .then(editor => {
                                   ClassicEditorInstances['new_article_description{{ $section->id ?? '' }}'] = editor;
                              })
                              
                              .catch(error => {
                                   console.error('error:', error);
                              });

                         </script>
                         @endif
                         <div class="example_section">
                              <div class="card card-bordered card-preview mt-4">
                                   <div class="card-inner">
                                        <div class="d-flex justify-content-between align-items-center">
                                             <h6>Additional Section</h6>
                                        </div>
                                        <hr>
                                        <div class="col-md-12 mt-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="example_section_heading">
                                                       Heading
                                                  </label>
                                                  
                                                  <input type="text" class="form-control" id="example_section_heading"
                                                       name="example_section_heading" value="{{ $data['example_section_heading'] ?? ''}}">

                                                  <span class="text-danger example_heading_error"></span>
                                             </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="example_section_description1">
                                                       Description
                                                  </label>
                                                  
                                                  <textarea class="form-control" id="example_section_description1"
                                                       name="example_section_description1">{{ $data['example_section_description1'] ?? ''}}</textarea>

                                                  <span class="text-danger example_description1_error"></span>
                                             </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="example_section_description2">
                                                       Description
                                                  </label>
                                                  
                                                  <textarea class="form-control" id="example_section_description2"
                                                       name="example_section_description2">{{ $data['example_section_description2'] ?? ''}}</textarea>

                                                  <span class="text-danger example_description2_error"></span>
                                             </div>
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
                                                  <button class="btn btn-sm btn-primary" type="button" onclick="validateForm()">Update</button>
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

<!-- <script>
     if (!window.ClassicEditorInstances) window.ClassicEditorInstances = {};

     ClassicEditor
          .create(document.querySelector('#example_description1'), {
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
     .then(editor => {
          ClassicEditorInstances['example_description1'] = editor;
     })
     
     .catch(error => {
          console.error('error:', error);
     });

     ClassicEditor
          .create(document.querySelector('#example_description2'), {
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
     .then(editor => {
          ClassicEditorInstances['example_description2'] = editor;
     })
     
     .catch(error => {
          console.error('error:', error);
     });


     function validateForm() {
          let isValid = true;

          let exampleHeading = $('input[name="example_heading"]').val().trim();
          let exampleDescription1 = $('textarea[name="example_description1"]').val().trim();
          let exampleDescription2 = $('textarea[name="example_description2"]').val().trim();
              
          $('input[name^="article_heading"], input[name="new_article_heading[]"]').each(function () {
               const val = $(this).val().trim();
               const errorSpan = $(this).closest('.form-group').find('.heading_error');

               if (val === '') {
                    isValid = false;
                    errorSpan.text('Heading is required.');
               } else {
                    errorSpan.text('');
               }
          });


          $('textarea[name^="article_description"], textarea[name="new_article_description[]"]').each(function () {
               const editorId = $(this).attr('id');
               const editorInstance = ClassicEditorInstances?.[editorId]; 
               const errorSpan = $(this).closest('.form-group').find('.description_error');

               let htmlContent = '';
               if (editorInstance) {
                    htmlContent = editorInstance.getData();
               } else {
                    htmlContent = $(this).val(); 
               }

               const textContent = $('<div>').html(htmlContent).text().trim();

               if (textContent === '') {
                    isValid = false;
                    errorSpan.text('Description is required.');
               } else {
                    errorSpan.text('');
               }
          });

          if( exampleHeading === '') {
               isValid = false;
               $('.example_heading_error').text('Example heading is required.');
          } else {
               $('.example_heading_error').text('');
          }

          if( exampleDescription1 === '') {
               const editorId = $(this).attr('id');
               const editorInstance = ClassicEditorInstances?.[editorId]; 
               const errorSpan = $(this).closest('.form-group').find('.example_description1_error');

               let htmlContent = '';
               if (editorInstance) {
                    htmlContent = editorInstance.getData();
               } else {
                    htmlContent = $(this).val(); 
               }

               const textContent = $('<div>').html(htmlContent).text().trim();

               if (textContent === '') {
                    isValid = false;
                    errorSpan.text('Description is required.');
               } else {
                    errorSpan.text('');
               }
          }
          if( exampleDescription2 === '') {
               const editorId = $(this).attr('id');
               const editorInstance = ClassicEditorInstances?.[editorId]; 
               const errorSpan = $(this).closest('.form-group').find('.example_description2_error');

               let htmlContent = '';
               if (editorInstance) {
                    htmlContent = editorInstance.getData();
               } else {
                    htmlContent = $(this).val(); 
               }

               const textContent = $('<div>').html(htmlContent).text().trim();

               if (textContent === '') {
                    isValid = false;
                    errorSpan.text('Description is required.');
               } else {
                    errorSpan.text('');
               }
          }


          if (isValid) {
               $('#articleForm').submit();
          }
     }
</script> -->

<script>
     if (!window.ClassicEditorInstances) window.ClassicEditorInstances = {};

     ClassicEditor
          .create(document.querySelector('#example_section_description1'), {
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
          .then(editor => {
               ClassicEditorInstances['example_section_description1'] = editor;
          })
          .catch(error => {
               console.error('error:', error);
          });

     ClassicEditor
          .create(document.querySelector('#example_section_description2'), {
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
          .then(editor => {
               ClassicEditorInstances['example_section_description2'] = editor;
          })
          .catch(error => {
               console.error('error:', error);
          });

     function validateForm() {
          let isValid = true;

          let exampleHeading = $('input[name="example_section_heading"]').val().trim();

          if (exampleHeading === '') {
               isValid = false;
               $('.example_heading_error').text('Example heading is required.');
          } else {
               $('.example_heading_error').text('');
          }

         
          const exampleDescription1Editor = ClassicEditorInstances['example_section_description1'];
          const exampleDescription1Html = exampleDescription1Editor?.getData() || '';
          const exampleDescription1Text = $('<div>').html(exampleDescription1Html).text().trim();

          if (exampleDescription1Text === '') {
               isValid = false;
               $('.example_description1_error').text('Description is required.');
          } else {
               $('.example_description1_error').text('');
          }

          // Validate example_description2
          const exampleDescription2Editor = ClassicEditorInstances['example_section_description2'];
          const exampleDescription2Html = exampleDescription2Editor?.getData() || '';
          const exampleDescription2Text = $('<div>').html(exampleDescription2Html).text().trim();

          if (exampleDescription2Text === '') {
               isValid = false;
               $('.example_description2_error').text('Description is required.');
          } else {
               $('.example_description2_error').text('');
          }

          // Validate dynamic article headings
          $('input[name^="article_heading"], input[name="new_article_heading[]"]').each(function () {
               const val = $(this).val().trim();
               const errorSpan = $(this).closest('.form-group').find('.heading_error');

               if (val === '') {
                    isValid = false;
                    errorSpan.text('Heading is required.');
               } else {
                    errorSpan.text('');
               }
          });

          // Validate dynamic article descriptions using CKEditor
          $('textarea[name^="article_description"], textarea[name="new_article_description[]"]').each(function () {
               const editorId = $(this).attr('id');
               const editorInstance = ClassicEditorInstances?.[editorId];
               const errorSpan = $(this).closest('.form-group').find('.description_error');

               let htmlContent = '';
               if (editorInstance) {
                    htmlContent = editorInstance.getData();
               } else {
                    htmlContent = $(this).val();
               }

               const textContent = $('<div>').html(htmlContent).text().trim();

               if (textContent === '') {
                    isValid = false;
                    errorSpan.text('Description is required.');
               } else {
                    errorSpan.text('');
               }
          });

          if (isValid) {
               $('#articleForm').submit();
          }
     }
</script>

@endsection

