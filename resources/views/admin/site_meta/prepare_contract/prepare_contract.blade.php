@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="container-fluid">
          <form action="{{ url('/admin-dashboard/prepare-contract-procc') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="work_sec_ids" id="work_sec_ids" value="">
               <input type="hidden" name="image_id" id="image_id" value="">
               <div class="card card-bordered card-preview">
                    <div class="card-inner">
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="title"><b><h4>Page Title</b></h4></label>
                                   <input type="text" class="form-control form-control-lg" id="title" name="title" value="{{ $data['title_name'] ?? '' }}">
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="image">Image</label>
                                   <input type="file" class="form-control" id="image" name="image" value="">
                              </div>
                         </div>
                         <br>
                         @if(isset($image) && $image != null)
                         <div class="col-md-2" id="img{{ $image->id ?? '' }}">
                              <div class="text-end"><span class="remove-image" data-id="{{ $image->id ?? '' }}"><i class="fa fa-times"></i></span></div>
                              <div class="form-group">
                                   <img src="{{ asset('storage/'.$image->media->file_name) }}" alt="img">
                              </div>
                         </div>
                         @endif
                         <hr>
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="fb_link">Facebook Link</label>
                                   <input type="text" class="form-control" id="fb_link" name="fb_link" value="{{ $data['fb_link'] ?? '' }}">
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="twitter_link">Twitter Link</label>
                                   <input type="text" class="form-control" id="twitter_link" name="twitter_link" value="{{ $data['twitter_link'] ?? '' }}">
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="pinterest_link">Pinterest Link</label>
                                   <input type="text" class="form-control" id="pinterest_link" name="pinterest_link" value="{{ $data['pinterest_link'] ?? '' }}">
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-8 pb-2">
                              <div class="form-group">
                                   <label class="form-label" for="short_description"></label>
                                   <textarea class="form-control" id="short_description" name="short_description">{{ $data['short_description'] ?? '' }}</textarea>
                              </div>
                         </div>
                         <div class="card card-bordered card-preview">
                              <div class="card-inner">
                                   <h5>Elabora tu Contrato con un Abogado</h5>  
                                   <hr>
                                   <div class="row g-3">
                                        <div class="col-lg-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="description">Description</label>
                                             </div>
                                        </div>
                                        <div class="col-lg-7">
                                             <div class="form-group">
                                                  <div class="form-control-wrap">
                                                       <textarea class="form-control" id="description" name="description">{{ $data['description'] ?? '' }}</textarea>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="row g-3 mt-2">
                                        <div class="col-lg-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="page_sub_heading">Page sub heading</label>
                                             </div>
                                        </div>
                                        <div class="col-lg-7">
                                             <div class="form-group">
                                                  <div class="form-control-wrap">
                                                       <input type="text" class="form-control" id="page_sub_heading" name="page_sub_heading" value="{{ $data['page_sub_heading'] ?? '' }}">
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="row g-3 mt-2">
                                        <div class="col-lg-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="">how it works</label>
                                             </div>
                                        </div>
                                        <div class="col-lg-7">
                                             <div class="card card-bordered card-preview">
                                                  <div class="card-inner">
                                                       <div class="form-group">
                                                            <label class="form-label" for="work_main_heading">Main Heading</label>
                                                            <input type="text" class="form-control" id="work_main_heading" name="work_main_heading" value="{{ $data['work_main_heading'] ?? '' }}">
                                                       </div>
                                                       <hr>
                                                       <div class="form-group">
                                                            <label class="form-label" for="">work process</label>
                                                       </div>
                                                       @if(isset($work_sec) && $work_sec != null)
                                                       @foreach($work_sec as $value)
                                                       @if($value->contract_work)
                                                       <div class="work-append-sec{{ $value->contract_work->id ?? '' }}">
                                                            <hr>
                                                            <div class="row gy-12">
                                                                 <div class="text-end"><span class="remove-work-sec" data-id="{{ $value->contract_work->id ?? '' }}"><i class="fa fa-times"></i></span></div>
                                                                 <div class="col-md-4">
                                                                      <div class="form-group">
                                                                           <img src="{{ asset('storage/'.$value->media->file_name) }}" alt="">
                                                                      </div>
                                                                 </div>
                                                                 <div class="col-md-4">
                                                                      <div class="form-group">
                                                                           <label class="form-label" for="work_header">Header</label>
                                                                           <input type="text" class="form-control" id="work_header" name="work_header[{{ $value->contract_work->id ?? '' }}]" value="{{ $value->contract_work->header ?? '' }}">
                                                                      </div>
                                                                 </div>
                                                                 <div class="col-md-4">
                                                                      <div class="form-group">
                                                                           <label class="form-label" for="work_short_description">Short Description</label>
                                                                           <input type="text" class="form-control" id="work_short_description" name="work_short_description[ {{ $value->contract_work->id ?? '' }} ]" value="{{ $value->contract_work->description ?? '' }}">
                                                                      </div>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                       <br>
                                                       @endif
                                                       @endforeach
                                                       @endif
                                                       <div class="col-md-3 offset-md-9">
                                                            <div class="form-group">
                                                                 <button type="button" class="btn btn-sm btn-primary" id="add-works-sec">Add Row</button>
                                                            </div>
                                                       </div>
                                                       <div id="work-section">
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <hr>
                                   <div class="row g-3 mt-2">
                                        <div class="col-lg-2">
                                             <div class="form-group">
                                                  <label class="form-label" for="">economical option</label>
                                             </div>
                                        </div>
                                        <div class="col-lg-7">
                                             <div class="card card-bordered card-preview">
                                                  <div class="card-inner">
                                                       <div class="form-group">
                                                            <label class="form-label" for="economical_main_heading">Main Heading</label>
                                                            <input type="text" class="form-control" id="economical_main_heading" name="economical_main_heading" value="{{ $data['economical_main_heading'] ?? '' }}">
                                                       </div>
                                                       <hr>
                                                       <div class="form-group">
                                                            <label class="form-label" for="economical_description">Description</label>
                                                            <textarea class="form-control" id="economical_description" name="economical_description">{{ $data['economical_description'] ?? '' }}</textarea>
                                                       </div>
                                                       <hr>
                                                       <div class="form-group">
                                                            <label class="form-label" for="button_text">Button text</label>
                                                            <input type="text" class="form-control" id="button_text" name="button_text" value="{{ $data['button_text'] ?? '' }}">
                                                       </div>
                                                       <hr>
                                                       <div class="form-group">
                                                            <label class="form-label" for="button_link">Button Link</label>
                                                            <input type="text" class="form-control" id="button_link" name="button_link" value="{{ $data['button_link'] ?? '' }}">
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
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
     ClassicEditor
     .create( document.querySelector('#short_description'))
     .catch( error => {
          console.error( error );
     });

     ClassicEditor
     .create( document.querySelector('#description'))
     .catch( error => {
          console.error( error );
     });


     $(document).ready(function(){
          // Append Work Section // 
          $('#add-works-sec').click(function(){
               var html = `<div class="work-append-sec">
                              <hr>
                              <div class="row gy-12">
                                   <div class="text-end"><span class="remove-work-sec" value="appended"><i class="fa fa-times"></i></span></div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="new_work_icon">work icon</label>
                                             <input type="file" class="form-control" id="new_work_icon" name="new_work_icon[]" value="">
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="new_work_header">Header</label>
                                             <input type="text" class="form-control" id="new_work_header" name="new_work_header[]" value="">
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="new_work_short_description">Short Description</label>
                                             <input type="text" class="form-control" id="new_work_short_description" name="new_work_short_description[]" value="">
                                        </div>
                                   </div>
                              </div>
                         </div>
                    <br>`;
               $('#work-section').append(html);
          });


          $('body').delegate('.remove-work-sec', 'click', function () {
               var id = $(this).data('id');
               if($(this).attr('value') === 'appended'){
                    $(this).closest('.work-append-sec').remove();
                    return false;
               }

               let deleteIds = $('#work_sec_ids').val();
               if(deleteIds) {
                    deleteIds += ',' + id;
               }else {
                    deleteIds = id;
               }
               $('#work_sec_ids').val(deleteIds);

               $('.work-append-sec'+id).hide();
          });


          $('.remove-image').click(function(){
               var id = $(this).data('id');
               $('#image_id').val(id);
               $('#img'+id).remove();
          })
     });
</script>

@endsection
