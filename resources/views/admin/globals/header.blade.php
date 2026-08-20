@extends('admin_layout.master')
@section('content')
<div class="nk-content">

    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">Header Section</h4>
        </div>
    </div>
    <div class="container-fluid">
        <form action="{{ url('/admin-dashboard/add/header') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="begin_of_head">Code at Beginning of <<span>head</span>> Tag</label>

                            <textarea class="form-control" id="begin_of_head" name="begin_of_head"  cols="15" rows="7">{{$data['begin_of_head'] ?? ''}}</textarea>
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="end_of_head">Code before Closing <<span>/head</span>> Tag
                            </label>

                            <textarea  class="form-control" id="end_of_head" name="end_of_head" cols="15" rows="7">{{$data['end_of_head'] ?? ''}}</textarea>
                        </div>
                    </div>


                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="header_btn_1">Header First Button</label>
                            <input type="text" class="form-control" id="header_btn_1" name="header_btn_1" value="{{ $data['button1'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="header_btn_2">Header Second Button</label>
                            <input type="text" class="form-control" id="header_btn_2" name="header_btn_2" value="{{ $data['button2'] ?? '' }}">
                        </div>
                    </div>

                    <div class="col-md-8  mt-2">
                        <div class="form-group">
                             <label class="form-label" for="header_logo">Header Logo</label>
                             <input type="file" class="form-control" id="header_logo" name="header_logo">
                        </div>

                        @if(isset($data['header_logo']) && $data['header_logo'] != null)
                             <div class="header_image_div" id="header_image_div{{ $data['header_logo_id'] ?? '' }}">
                                  <div class="form-group">
                                       <span class="col-md-8 offset-md-4 remove_header_logo" data-id="{{ $data['header_logo_id'] ?? '' }}">
                                            <i class="fa fa-times"></i>
                                       </span>
                                  </div>
                                  <div class="form-group">
                                       <img src="{{ asset('storage/'.$data['header_logo'] ?? '' ) }}" style="height:150px;width:150px">
                                  </div>
                             </div>
                        @endif

                    </div>

                    <div class="col-md-8  mt-2">
                        <div class="form-group">
                             <label class="form-label" for="user_dash_header_logo">User Dashboard Header Logo</label>
                             <input type="file" class="form-control" id="user_dash_header_logo" name="user_dash_header_logo">
                        </div>

                        @if(isset($data['user_dash_header_logo']) && $data['user_dash_header_logo'] != null)
                             <div class="header_image_div" id="header_image_div{{ $data['user_dash_header_logo_id'] ?? '' }}">
                                  <div class="form-group">
                                       <span class="col-md-8 offset-md-4 remove_user_dash_header_logo" data-id="{{ $data['user_dash_header_logo_id'] ?? '' }}">
                                            <i class="fa fa-times"></i>
                                       </span>
                                  </div>
                                  <div class="form-group">
                                       <img src="{{ asset('storage/'.$data['user_dash_header_logo'] ?? '' ) }}" style="height:150px;width:150px">
                                  </div>
                             </div>
                        @endif

                    </div>

                    <div class="col-md-8 mt-2">
                            <div class="form-group">
                                 <label class="form-label" for="favicon">Favicon</label>
                                 <input type="file" class="form-control" id="favicon" name="favicon">
                            </div>
                            @if(isset($data['favicon']) && $data['favicon'] != null)
                            <div class="favicon_image_div" id="favicon_image_div{{ $data['favicon_id'] ?? '' }}">
                                 <div class="form-group">
                                      <span class="col-md-7 offset-md-5 remove_favicon" data-id="{{ $data['favicon_id'] ?? '' }}">
                                           <i class="fa fa-times"></i>
                                      </span>
                                 </div>
                                 <div class="form-group">
                                      <img src="{{ asset('storage/'.$data['favicon'] ?? '' ) }}">
                                 </div>
                            </div>
                            @endif
                    </div>
                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="asi_funciona_header">Asi Funciona Header Text</label>
                            <input type="text" class="form-control" id="asi_funciona_header" name="asi_funciona_header" value="{{ $data['asi_funciona_header'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="ayuda_header">Ayuda Header Text</label>
                            <input type="text" class="form-control" id="ayuda_header" name="ayuda_header" value="{{ $data['ayuda_header'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="header_search_placeholder">Header Search Placeholder</label>
                            <input type="text" class="form-control" id="header_search_placeholder" name="header_search_placeholder" value="{{ $data['header_search_placeholder'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="header_document_search_placeholder">Header Document Search Placeholder</label>
                            <input type="text" class="form-control" id="header_document_search_placeholder" name="header_document_search_placeholder" value="{{ $data['header_document_search_placeholder'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-8 mt-2">
                        <div class="form-group">
                            <label class="form-label" for="header_document_search_message">Header Document Search message</label>
                            <input type="text" class="form-control" id="header_document_search_message" name="header_document_search_message" value="{{ $data['header_document_search_message'] ?? '' }}">
                        </div>
                    </div>

                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<script>

    $(document).ready(function(){
        $('.remove_header_logo').click(function(){
            id = $(this).data('id');
            // $('#remove_logo1').val(id);
            $('#header_image_div'+id).hide();
        });

        $('.remove_footer_logo').click(function(){
            id = $(this).data('id');
            // $('#remove_logo2').val(id);
            $('#footer_image_div'+id).hide();
        });

        $('.remove_favicon').click(function(){
            id = $(this).data('id');
            // $('#favicon_img_id').val(id);
            $('#favicon_image_div'+id).hide();
        });
    });

    </script>


@endsection

