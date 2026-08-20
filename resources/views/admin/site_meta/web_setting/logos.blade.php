@extends('admin_layout.master')
@section('content')
{{-- <div class="nk-content">
    <div class="container-fluid">
        <form action="{{ url('/admin-dashboard/add/logos') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="remove_logo1" id="remove_logo1" value="">
            <input type="hidden" name="remove_logo2" id="remove_logo2" value="">
            <input type="hidden" name="favicon_img_id" id="favicon_img_id" value="">

            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="col-md-8">
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
                    <div class="col-md-8 mt-2">
                         <div class="form-group">
                              <label class="form-label" for="footer_logo">Footer Logo</label>
                              <input type="file" class="form-control" id="footer_logo" name="footer_logo">
                         </div>
                         @if(isset($data['footer_logo']) && $data['footer_logo'] != null)
                              <div class="footer_image_div" id="footer_image_div{{ $data['footer_logo_id'] ?? '' }}">
                                   <div class="form-group">
                                        <span class="col-md-8 offset-md-4 remove_footer_logo" data-id="{{ $data['footer_logo_id'] ?? '' }}">
                                             <i class="fa fa-times"></i>
                                        </span>
                                   </div>
                                   <div class="form-group">
                                        <img src="{{ asset('storage/'.$data['footer_logo'] ?? '' ) }}" style="height:150px;width:150px">
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

</script> --}}

@endsection