@extends('admin_layout.master')
@section('content')
<div class="nk-content">

     <div class="nk-block-head">
          <div class="nk-block-head-content">
              <h4 class="nk-block-title">Legal Document Page</h4>
          </div>
      </div>
     <div class="container-fluid">
          <form action="{{ url('/admin-dashboard/add-legal-document') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="id" value="{{ $legal->id ?? '' }}">
               <div class="row main_section">
                    <div class="col-md-8 left_content">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="col-md-12">
                                        <div class="form-group">
                                             <label class="form-label" for="page_title"><b><h5>Page Title</h5></b></label>
                                             <input type="text" class="form-control" id="page_title" name="page_title" value="{{ $legal->title ?? '' }}">
                                        </div>
                                        <div class="col-md-12 mt-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="heading">Heading</label>
                                                  <input type="text" class="form-control" id="heading" name="heading" value="{{ $legal->main_heading ?? '' }}">
                                             </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="sub_heading">Sub heading</label>
                                                  <input type="text" class="form-control" id="sub_heading" name="sub_heading" value="{{ $legal->main_sub_heading ?? '' }}">
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <div class="col-md-4 right_content">
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <div class="d-flex justify-content-between align-items-center">
                                        <div class="nk-block-head-content butn-cls">
                                             <div class="mbsc-form-group view_btn">
                                                  {{-- <a href="{{ url('/documentos-legales') }}" class="view_page" target="_blank">View Page</a> --}}
                                                  <a href="{{ url('/legal-documents') }}" class="view_page" target="_blank">View Page</a>
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
                                             <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="50" value="{{ $legal->meta_title ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="meta_description">Meta Description</label>
                                             <textarea class="form-control" id="meta_description" name="meta_description" maxlength="155">{{ $legal->meta_description ?? '' }}</textarea>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </form>
     </div>
</div>


@endsection
