@extends('admin_layout.master')
@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <form action="{{ url('/admin-dashboard/faq-process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="removefaq" id="removefaq" value="">
            <input type="hidden" id="bg_image_id" name="bg_image_id" value="">
            <input type="hidden" id="baner_image_id" name="baner_image_id" value="">

            <div class="row main_section">
                <div class="col-md-8 left-content">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <div class="col-md-12">
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
                                        <img src="{{ asset('storage/'.$data['banner_image']) }}" alt="banner_img" height="100px" width="160px">
                                    </div>
                                </div>
                                @endif
                            </div>
                            <hr>
                            <h6>Faq Section</h6>
                            <hr>
                            <div class="col-md-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label" for="">Faq</label>
                                </div>
                            </div>
                            @if(isset($faqs) && $faqs != null)
                            @foreach($faqs as $value)
                            <div class="faq-append-sec{{ $value->id ?? '' }}">
                                <hr>
                                <div class="text-end">
                                    <div class="form-group">
                                        <div><span class="remove-faq-sec" data-id="{{ $value->id ?? '' }}"><i class="fa fa-times"></i></span></div>
                                    </div>
                                </div>
                                <div class="row gy-12">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="category">Category</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select js-select2" name="category[{{ $value->id ?? '' }}]" id="category">
                                                    <option value="" selected disabled>Select</option>
                                                    @if(isset($faqCategory) && count($faqCategory) > 0)
                                                        @foreach($faqCategory as $catg)
                                                            @php
                                                                $isSelected = isset($value->category_id) && $value->category_id == $catg->id;
                                                            @endphp
                                                            <option value="{{ $catg->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                            {{ $catg->category_name ?? '' }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="question">Question</label>
                                            <input class="form-control" name="question[{{ $value->id ?? '' }}]" id="question" value="{{ $value->question ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label" for="answer">Answer</label>
                                            <textarea class="form-control answer_editor" name="answer[{{ $value->id ?? '' }}]" id="answer">{{ $value->answer ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                            <div id="new-faq-container"></div>
                            <br>
                            <div class="text-end">
                                <div class="form-group">
                                    <button type="button" class="btn btn-sm btn-primary" id="add-faq-sec">Add</button>
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
                                        {{-- <a href="{{ url('/preguntas-frecuentes') }}" class="view_page" target="_blank">View Page</a> --}}
                                        <a href="{{ url('/faq') }}" class="view_page" target="_blank">View Page</a>
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
        $('.remove_background_image').click(function(){
            id = $(this).data('id');
            // $('#bg_image_id').val(id);
            $('#bg_image'+id).hide();
        });

        $('.remove_banner_image').click(function(){
            id = $(this).data('id');
            // $('#baner_image_id').val(id);
            $('#banner_div'+id).hide();
        });
    })

</script>

<script>
    class MyUploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file.then(file => {
                return new Promise((resolve, reject) => {
                    const data = new FormData();
                    data.append('upload', file);

                    fetch('classicEditor/upload-image', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: data
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.url) {
                            resolve({ default: data.url });
                        } else {
                            reject('Upload failed');
                        }
                    })
                    .catch(error => {
                        reject('Upload failed');
                        console.error(error);
                    });
                });
            });
        }

        abort() {}
    }

    function CustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }


</script>

<script>
// function initializeCKEditor(element) {
//     ClassicEditor.create(element).catch(error => {
//         console.error(error);
//     });
// }

function initializeCKEditor(element) {
    ClassicEditor
        .create(element, {
            extraPlugins: [CustomUploadAdapterPlugin],
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


$(document).ready(function() {
    var faq_category = {!! json_encode($faqCategory) !!};

    $('.answer_editor').each(function() {
        initializeCKEditor(this);
    });

    $('#add-faq-sec').click(function() {
        var category_option = [];
        $.each(faq_category, function(key, val){
            var category_html = `<option value="${val.id}">${val.category_name}</option>`;
            category_option.push(category_html);
        });
        var category_options_html = category_option.join('');
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
                    <label class="form-label" for="new_category">Category</label>
                    <div class="form-control-wrap">
                        <select class="form-select js-select2" name="new_category[]" id="new_category">
                            <option value="" selected disabled>Select</option>
                            ${category_options_html}
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label" for="new_question">Question</label>
                    <input class="form-control" name="new_question[]" id="new_question" value="">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label" for="new_answer">Answer</label>
                    <textarea class="form-control answer_editor" name="new_answer[]" id="new_answer"></textarea>
                </div>
            </div>
        </div>
        </div>`;

        document.getElementById('new-faq-container').insertAdjacentHTML('beforeend', faqHtml);

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
});


</script>


@endsection
