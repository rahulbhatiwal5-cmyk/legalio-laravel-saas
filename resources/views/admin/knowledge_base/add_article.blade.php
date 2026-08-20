@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <form action="{{route('knowledge.base.store.article')}}" method="post" enctype="multipart/form-data">
            @csrf
            {{-- <input type="hidden" name="id" value="{{ $register->id ?? '' }}">
            <input type="hidden" name="bg_img_id" id="bg_img_id" value=""> --}}
            <div class="row main_section">
                <div class="col-md-8 left_content">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            {{-- <hr> --}}
                            <h6>Select Category</h6>
                            <div class="form-group">
                                <select name="category_id" id="category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <hr>


                            {{-- <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="title"><b><h4>Article Title</b></h4></label>
                                    <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{  old('title') }}" >

                                    @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                     @enderror
                                </div>
                            </div> --}}

                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="preview_title"><b>Preview Title</b></label>
                                    {{-- <input type="text" class="form-control form-control-lg" id="seo" name="seo" > --}}
                                    <input type="text" class="form-control form-control-lg" id="preview_title" name="preview_title" value="{{$article->preview_title ?? ''}}">

                                    @error('preview_title')
                                    <span class="text-danger">{{ $message }}</span>
                                     @enderror
                                </div>
                            </div>
                            <div class="row gy-12 mt-2">
                                <div class="col-md-12 doc-short-des">
                                     <div class="form-group">
                                          <label class="form-label" for="preview_description">Preview Description</label>
                                          <textarea class="form-control" id="preview_description" name="preview_description">{{ old('preview_description', $article->preview_description ?? '') }}</textarea>

                                          @error('preview_description')
                                               <span class="text-danger">{{ $message }}</span>
                                          @enderror
                                     </div>
                                </div>
                           </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="seo"><b>SEO Title</b></label>
                                    <input type="text" class="form-control form-control-lg" id="seo" name="seo"  value="{{  old('seo') }}"  >
                                    {{-- <input type="text" class="form-control form-control-lg" id="seo" name="seo" value="{{$article->seo}}"> --}}

                                    @error('seo')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <hr>
                            <div class="row gy-12 mt-2">
                                 <div class="col-md-12 doc-short-des">
                                      <div class="form-group">
                                           <label class="form-label" for="seo_description">SEO Description<span class="required_field">*</span></label>
                                           <textarea class="form-control" id="seo_description" name="seo_description">{{  old('seo_description') }}</textarea>
                                           {{-- <textarea class="form-control" id="seo_description" name="seo_description">{{ old('seo_description', $document->seo_description ?? '') }}</textarea> --}}

                                           @error('seo_description')
                                                <span class="text-danger">{{ $message }}</span>
                                           @enderror
                                      </div>
                                 </div>
                            </div>

                            <hr>

                            <!--<div class="row">
                                <div class="col-md-6 mt-2">
                                    <div class="form-group">
                                        <label class="form-label" for="heading"><b>heading</b></label>
                                        <input type="text" class="form-control form-control-lg" id="heading" name="heading" value="{{  old('heading') }}" >
                                        {{-- <input type="text" class="form-control form-control-lg" id="heading" name="heading" value="{{$article->heading}}"> --}}

                                        @error('heading')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <div class="form-group">
                                        <label class="form-label" for="sub_heading"><b>Sub_heading</b></label>
                                        <input type="text" class="form-control form-control-lg" id="sub_heading" name="sub_heading" value="{{  old('sub_heading') }}" >
                                        {{-- <input type="text" class="form-control form-control-lg" id="sub_heading" name="sub_heading" value="{{$article->sub_heading}}"> --}}

                                        @error('sub_heading')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>-->
                            {{-- inage --}}
                            {{-- <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="image">Image</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                @if($mediaUrl)
                                    <div class="bg_image_div" id="bg_image{{ $article->id ?? '' }}" >

                                        <div class="form-group">
                                            <span class="col-md-9 offset-md-3 remove_background_image" data-id="{{ $article->id ?? '' }}" >

                                                <i class="fa fa-times"></i>
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <img src="{{ $mediaUrl }}" height="140px" width="160px">
                                        </div>
                                    </div>
                                @endif

                            </div> --}}
                            <hr>
                            {{-- <h5>Register Page</h5>   --}}

                            {{-- <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="description">Description</label>

                                    <textarea id="description"   class="form-control form-control"  name="description" cols="30" rows="10">{{$article->description}}</textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="title"><b>Article Title</b></label>
                                    {{-- <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{$article->title}}"> --}}
                                    <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{  old('title') }}" >

                                    @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                     @enderror
                                </div>
                            </div>
                            <div class="row gy-12 mt-2">
                                <div class="col-md-12 doc-short-des">
                                     <div class="form-group">
                                          <label class="form-label" for="article_overview">Article Overview<span class="required_field">*</span></label>
                                          <textarea class="form-control" id="article_overview" name="article_overview">{{  old('article_overview') }}</textarea>
                                          {{-- <textarea class="form-control" id="article_overview" name="article_overview">{{ old('article_overview', $document->article_overview ?? '') }}</textarea> --}}

                                          @error('article_overview')
                                               <span class="text-danger">{{ $message }}</span>
                                          @enderror
                                     </div>
                                </div>
                           </div>

                          <hr>
                            <div class="col-md-12 ">
                                <div class="form-group">
                                    <label class="form-label" for="title"><b><h4>Article content</b></h4></label>
                                </div>
                            </div>

                            <div id="parent_div">
                                <div class="row mt-2 clone-div">
                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                            <label class="form-label"><b>Content Heading</b></label>
                                            <input type="text" class="form-control form-control-lg" name="content_heading[]" placeholder="Enter heading">
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                            <label class="form-label">Description <span class="required_field">*</span></label>
                                            <textarea class="form-control content-description" name="content_description[]" placeholder="Enter description..."></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12 text-end mt-2">
                                        <button type="button" class="btn btn-danger data_remove"><i class="fas fa-trash-alt"></i> Remove</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mt-2">
                                <button type="button" class="btn btn-primary text-center" id="add_content"><i class="fas fa-plus"></i> Add More</button>
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

                                         {{-- <a href="{{ route('help.center') }}" target="_blank" class="view_page">View Page</a> --}}

                                    </div>
                               </div>
                               <div class="nk-block-head-content">
                                    <div class="up-btn mbsc-form-group">
                                        <button class="btn btn-primary" type="submit">Add</button>
                                    </div>
                               </div>

                            </div>
                            {{-- <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="meta_title">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="50" value="">
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label class="form-label" for="meta_description">Meta Description</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" maxlength="155"></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="nk-block-head-content butn-cls">
                                    <div class="mbsc-form-group view_btn mt-3">
                                        <a href="{{ url('/register') }}" class="view_page" target="_blank">View Page</a>
                                    </div>
                                </div>
                            </div> --}}
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

               $.ajax
            ({
                url: "{{ route('knowledge.base.delete.article.image') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: $('input[name="id"]').val(),
                        },
                        success: function(response) {
                            if (response.success) {
                                alert("Image deleted successfully!");
                            } else {
                                alert("Error deleting image.");
                            }
                        }
            });
          });

          function initializeCKEditor(textarea) {
        ClassicEditor
            .create(textarea[0] , {
                extraPlugins: [CustomUploadAdapterPlugin],
            })
            .catch(error => {
                console.error(error);
            });
    }

    // Initialize CKEditor on existing textareas
    $('textarea.content-description').each(function() {
        initializeCKEditor($(this));
    });

    $('#add_content').click(function() {
        var newContent = `
            <div class="row mt-2 clone-div">
                <div class="col-md-12 mt-2">
                    <div class="form-group">
                        <label class="form-label"><b>Content Heading</b></label>
                        <input type="text" class="form-control form-control-lg" name="content_heading[]" placeholder="Enter heading">
                    </div>
                </div>

                <div class="col-md-12 mt-2">
                    <div class="form-group">
                        <label class="form-label">Description <span class="required_field">*</span></label>
                        <textarea class="form-control content-description" name="content_description[]" placeholder="Enter description..."></textarea>
                    </div>
                </div>

                <div class="col-md-12 text-end mt-2">
                    <button type="button" class="btn btn-danger data_remove"><i class="fas fa-trash-alt"></i> Remove</button>
                </div>
            </div>`;

        $('#parent_div').append(newContent);

        // Initialize CKEditor for newly added textarea
        var newTextarea = $('#parent_div .clone-div:last-child textarea');
        initializeCKEditor(newTextarea);
    });

    // Remove dynamically added sections
    $('#parent_div').on('click', '.data_remove', function() {
        $(this).closest('.clone-div').remove();
    });

    });


</script>

@section('js')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
@endsection

<script>
     $('#title').on('keyup',function(){
          const name = $(this).val();
          const url = name.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g, '');
          $('#slug').val(url);
     })

      class MyUploadAdapter {
        constructor(loader) {
            this.loader = loader;
            this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        upload() {
            return this.loader.file.then(file => new Promise((resolve, reject) => {
                const data = new FormData();
                data.append('upload', file);

                fetch('{{ route('knowledge.base.upload.editor.image') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: data
                })
                .then(response => response.json())
                .then(data => {
                    if (data.url) {
                        resolve({ default: data.url });
                    } else {
                        reject(data.error || 'Upload failed');
                    }
                })
                .catch(error => {
                    reject('Upload failed: ' + error.message);
                });
            }));
        }
    }

    function CustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }
    //  ClassicEditor
    //  .create( document.querySelector('#short_description'),{
    //       toolbar: {
    //            items: [
    //                 'heading',
    //                 'bold',
    //                 'bulletedList',
    //                 'numberedList',
    //            ]
    //       },
    //       heading: {
    //            options: [
    //                 { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
    //                 { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
    //                 { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
    //            ]
    //       },
    //       removePlugins: [
    //            'Table','MediaEmbed', 'BlockQuote',
    //       ]
    //  })
    //  .catch( error => {
    //       console.error( error );
    //  });

     ClassicEditor
     .create( document.querySelector('#seo_description'),{
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
               'Table','MediaEmbed', 'BlockQuote',
          ]
     })
     .catch( error => {
          console.error( error );
     });


    //  ClassicEditor
    //  .create( document.querySelector('#description'),{
    //       toolbar: {
    //            items: [
    //                 'heading',
    //                 'bold',
    //                 'bulletedList',
    //                 'numberedList',
    //            ]
    //       },
    //       heading: {
    //            options: [
    //                 { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
    //                 { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
    //                 { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
    //            ]
    //       },
    //       removePlugins: [
    //            'Table','MediaEmbed', 'BlockQuote',
    //       ]
    //  })
    //  .catch( error => {
    //       console.error( error );
    //  });

     ClassicEditor
     .create( document.querySelector('#article_overview'),{
        extraPlugins: [CustomUploadAdapterPlugin],
          toolbar: {
               items: [
                    'heading',
                    'bold',
                    'bulletedList',
                    'numberedList',
                    'imageUpload',
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
               'Table','MediaEmbed', 'BlockQuote',
          ]
     })
     .catch( error => {
          console.error( error );
     });

    //  ClassicEditor
    //  .create( document.querySelector('#long_description'),{
    //       toolbar: {
    //            items: [
    //                 'heading',
    //                 'bold',
    //                 'bulletedList',
    //                 'numberedList',
    //            ]
    //       },
    //       heading: {
    //            options: [
    //                 { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
    //                 { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
    //                 { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
    //            ]
    //       },
    //       removePlugins: [
    //            'Table','MediaEmbed', 'BlockQuote',
    //       ]
    //  })
    //  .catch( error => {
    //       console.error( error );
    //  });

    //  ClassicEditor
    //  .create( document.querySelector('#img_description'),{
    //       toolbar: {
    //            items: [
    //                 'heading',
    //                 'bold',
    //                 'bulletedList',
    //                 'numberedList',
    //            ]
    //       },
    //       heading: {
    //            options: [
    //                 { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
    //                 { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
    //                 { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
    //            ]
    //       },
    //       removePlugins: [
    //            'Table','MediaEmbed', 'BlockQuote',
    //       ]
    //  })
    //  .catch( error => {
    //       console.error( error );
    //  });

    //  ClassicEditor
    //  .create( document.querySelector('#img_description_second'),{
    //       toolbar: {
    //            items: [
    //                 'heading',
    //                 'bold',
    //                 'bulletedList',
    //                 'numberedList',
    //            ]
    //       },
    //       heading: {
    //            options: [
    //                 { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
    //                 { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
    //                 { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
    //            ]
    //       },
    //       removePlugins: [
    //            'Table','MediaEmbed', 'BlockQuote',
    //       ]
    //  })
    //  .catch( error => {
    //       console.error( error );
    //  });
</script>


@endsection
