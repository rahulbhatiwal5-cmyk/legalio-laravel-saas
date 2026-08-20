@extends('admin_layout.master')
@section('content')
<div class="nk-content">

     <div class="nk-block-head">
          <div class="nk-block-head-content">
              <h4 class="nk-block-title">Who We are Page</h4>
          </div>
      </div>
      <div class="container-fluid">
      <div class="row">
          <div class="col-md-8">
               <div class="card card-bordered card-preview">
                    <div class="card-inner">
          <form action="{{ url('/admin-dashboard/add/who-we-are') }}" method="POST" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="removeVision" id="removeVision" value="">
               <input type="hidden" id="removeOffer" name="removeOffer" value="">
               <input type="hidden" id="bg_image_id" name="bg_image_id" value="">
               <input type="hidden" id="baner_image_id" name="baner_image_id" value="">
               <input type="hidden" id="legal_image_id" name="legal_image_id" value="">
               <input type="hidden" id="vision_image_id" name="vision_image_id" value="">
               <input type="hidden" id="offer_id" name="offer_id" value="">

               <div class="row main_section">
                    <div class="col-md-12 left-content">
                         <div class="col-md-12 pb-2">
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
                                        <img src="{{ asset('storage/'.$data['background_image']) }}" alt="background_img" height="160px" width="160px">
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
                         <h6>About Legal Documents Section</h6>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="image">Image</label>
                                   <input type="file" class="form-control" name="image" id="image">
                              </div>
                              @if(isset($data['image']) && $data['image'] != null)
                              <div class="legal_image_div" id="legal_image_div{{ $data['image_id'] ?? '' }}">
                                   <div class="form-group">
                                        <span class="col-md-9 offset-md-3 remove_image" data-id="{{ $data['image_id'] ?? '' }}">
                                             <i class="fa fa-times"></i>
                                        </span>
                                   </div>
                                   <div class="form-group">
                                        <img src="{{ asset('storage/'.$data['image']) }}" alt="img" height="140px" width="160px">
                                   </div>
                              </div>
                              @endif
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="heading">Heading</label>
                                   <input type="text" class="form-control" name="heading" id="heading" value="{{ $data['heading'] ?? '' }}">
                              </div>
                         </div>
                         <div class="col-md-12 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="description">Description</label>
                                   <textarea class="form-control answer_editor" name="description" id="description">{{ $data['description'] ?? '' }}</textarea>
                              </div>
                         </div>
                         <hr>
                         <h6>Vision Section</h6>
                         @if(isset($visions) && $visions != null)
                         @foreach($visions as $vision)
                         <?php $path = getStorageFilepath($vision->media->file_path); ?>
                         <div class="vision_append{{ $vision->id ?? '' }}">
                              <hr>
                              <div class="row gy-12">
                                   <div class="text-end">
                                        <div class="form-group">
                                             <div><span class="remove_vision" data-id="{{ $vision->id ?? '' }}"><i class="fa fa-times"></i></span></div>
                                        </div>
                                   </div>
                                   <div class="col-md-3">
                                        <div class="form-group">
                                             <input type="file" name="vision_up_img" class="up_img" data-id="{{ $vision->id ?? '' }}" id="vision_up_img{{ $vision->id ?? '' }}" style="display:none;">
                                             <button class="btn-sm btn btn-primary" type="button" data-id="{{ $vision->id ?? '' }}">Add New</button>
                                        </div>
                                        <div class="vision_img_div" id="vision_img_div{{ $vision->id ?? '' }}">
                                             <div class="form-group">
                                                  <span class="col-md-7 offset-md-5 remove_vision_img" data-id="{{ $vision->id ?? '' }}">
                                                       <i class="fa fa-times"></i>
                                                  </span>
                                             </div>
                                             <div class="form-group">
                                                  <img src="{{ asset('storage/'.$path ?? '' ) }}" alt="Vision image">
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-4">
                                        <div class="form-group">
                                             <label class="form-label" for="old_vision_heading">Heading</label>
                                             <input class="form-control" name="old_vision_heading[{{ $vision->id ?? '' }}]" id="old_vision_heading" value="{{ $vision->heading ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-5">
                                        <div class="form-group">
                                             <label class="form-label" for="old_vision_description">Description</label>
                                             <textarea class="form-control" name="old_vision_description[{{ $vision->id ?? '' }}]" id="old_vision_description">{{ $vision->description ?? '' }}</textarea>
                                        </div>
                                   </div>
                              </div>
                         </div>
                         @endforeach
                         @endif
                         <div id="vision_container"></div>
                         <br>
                         <div class="text-end">
                              <div class="form-group">
                                   <button type="button" class="btn btn-sm btn-primary" id="add-sec">Add</button>
                              </div>
                         </div>
                         <hr>
                         <h6>Offer Section</h6>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="offer_image">Image</label>
                                   <input type="file" class="form-control" name="offer_image" id="offer_image">
                              </div>
                              @if(isset($data['offer_image']) && $data['offer_image'] != null)
                              <div class="offer_image_div" id="offer_image_div{{ $data['offer_image_id'] ?? '' }}">
                                   <div class="form-group">
                                        <span class="col-md-9 offset-md-3 remove_offer_image" data-id="{{ $data['offer_image_id'] ?? '' }}">
                                             <i class="fa fa-times"></i>
                                        </span>
                                   </div>
                                   <div class="form-group">
                                        <img src="{{ asset('storage/'.$data['offer_image']) }}" alt="img" height="140px" width="160px">
                                   </div>
                              </div>
                              @endif
                         </div>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="offer_heading">Heading</label>
                                   <input type="text" class="form-control" name="offer_heading" id="offer_heading" value="{{ $data['offer_heading'] ?? '' }}">
                              </div>
                         </div>
                         <div class="col-md-12 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="offer_description">Description</label>
                                   <textarea class="form-control" name="offer_description" id="offer_description">{{ $data['offer_description'] ?? '' }}</textarea>
                              </div>
                         </div>
                         <hr>
                         <div class="col-md-12">
                              <div class="form-group">
                                   <label class="form-label" for="">Offers</label>
                              </div>
                         </div>
                         @if(isset($offers) && $offers != null)
                         @foreach($offers as $offer)
                         <div class="offer_append{{ $offer->id ?? '' }}">
                              <hr>
                              <div class="row gy-12">
                                   <div class="text-end">
                                        <div class="form-group">
                                             <div><span class="remove_offer" data-id="{{ $offer->id ?? '' }}"><i class="fa fa-times"></i></span></div>
                                        </div>
                                   </div>
                                   <div class="col-md-6">
                                        <div class="form-group">
                                             <label class="form-label" for="of_heading">Offer Heading</label>
                                             <input class="form-control" name="of_heading[{{ $offer->id ?? '' }}]" id="of_heading" value="{{ $offer->offer_section_heading ?? '' }}">
                                        </div>
                                   </div>
                                   <div class="col-md-6">
                                        <div class="form-group">
                                             <label class="form-label" for="of_description">Offer Description</label>
                                             <textarea class="form-control" name="of_description[{{ $offer->id ?? '' }}]" id="of_description">{{ $offer->offer_section_description ?? '' }}</textarea>
                                        </div>
                                   </div>
                              </div>
                         </div>
                         @endforeach
                         @endif
                         <div id="offer_container"></div>
                         <br>
                         <div class="text-end">
                              <div class="form-group">
                                   <button type="button" class="btn btn-sm btn-primary" id="add-offer-sec">Add</button>
                              </div>
                         </div>
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
                                                  {{-- <a href="{{ url('/sobre-nosotros') }}" class="view_page" target="_blank">View Page</a> --}}
                                                  <a href="{{ url('/about-us') }}" class="view_page" target="_blank">View Page</a>
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
                                             <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="50" value="{{ $data['meta_title']  ?? ''}}">
                                        </div>
                                   </div>
                                   <div class="col-md-12 mt-2">
                                        <div class="form-group">
                                             <label class="form-label" for="meta_description">Meta Description</label>
                                             <textarea class="form-control" id="meta_description" name="meta_description" maxlength="155">{{ $data['meta_description']  ?? ''}}</textarea>
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

     $('.update_vision_img').click(function(){
          var id = $(this).data('id');
          $('#vision_up_img' + id).trigger('click');
     });

     $('.up_img').change(function() {
          var id = $(this).data('id');
          var file = this.files[0];
          var formData = new FormData();
          formData.append('image', file);
          formData.append('_token', "{{ csrf_token() }}");
          formData.append('image_id', id);

          $.ajax({
               url: "{{ url('/update/vision/image') }}",
               type: 'POST',
               data: formData,
               processData: false,
               contentType: false,
               dataType: "json",
               success: function(response){
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

     $('.remove_vision_img').click(function(){
          id = $(this).data('id');
          let removeIds = $('#vision_image_id').val();

          if(removeIds) {
               removeIds += ',' + id;
          }else{
               removeIds = id;
          }
          $('#vision_image_id').val(removeIds);

          $('#vision_img_div'+id).hide();
     })


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

     $('.remove_image').click(function(){
          id = $(this).data('id');
          // $('#legal_image_id').val(id);
          $('#legal_image_div'+id).hide();
     });

     $('.remove_offer_image').click(function(){
          id = $(this).data('id');
          // $('#offer_id').val(id);
          $('#offer_image_div'+id).hide();
     });
});
</script>


<script>
function initializeCKEditor(element) {
     ClassicEditor.create(element).catch(error => {
          console.error(error);
     });
}

$('.answer_editor').each(function() {
     initializeCKEditor(this);
});

$(document).ready(function() {
     $('#add-sec').click(function() {
          let html = `
          <div class="vision_append">
          <hr>
          <div class="row gy-12">
               <div class="text-end">
                    <div class="form-group">
                         <div><span class="remove_vision" value="appended"><i class="fa fa-times"></i></span></div>
                    </div>
               </div>
               <div class="col-md-2">
                    <div class="form-group">
                         <label class="form-label" for="icon">Icon</label>
                         <input type="file" class="form-control" id="icon" name="icon[]">
                    </div>
               </div>
               <div class="col-md-5">
                    <div class="form-group">
                         <label class="form-label" for="vision_heading">Heading</label>
                         <input class="form-control" name="vision_heading[]" id="vision_heading" value="">
                    </div>
               </div>
               <div class="col-md-5">
                    <div class="form-group">
                         <label class="form-label" for="vision_description">Description</label>
                         <textarea class="form-control" name="vision_description[]" id="vision_description"></textarea>
                    </div>
               </div>
          </div>
          </div>`;

          $('#vision_container').append(html);
     });

     $('body').delegate('.remove_vision', 'click', function(){
          var id = $(this).data('id');
          if($(this).attr('value') === 'appended'){
               $(this).closest('.vision_append').remove();
               return false;
          }

          let deleteIds = $('#removeVision').val();

          if(deleteIds) {
               deleteIds += ',' + id;
          }else{
               deleteIds = id;
          }
          $('#removeVision').val(deleteIds);

          $('.vision_append'+id).hide();
     });

     $('#add-offer-sec').click(function(){
          let html = `
          <div class="offer_append">
          <hr>
          <div class="row gy-12">
               <div class="text-end">
                    <div class="form-group">
                         <div><span class="remove_offer" value="appended"><i class="fa fa-times"></i></span></div>
                    </div>
               </div>
               <div class="col-md-6">
                    <div class="form-group">
                         <label class="form-label" for="of_new_heading">Offer Heading</label>
                         <input class="form-control" name="of_new_heading[]" id="of_new_heading" value="">
                    </div>
               </div>
               <div class="col-md-6">
                    <div class="form-group">
                         <label class="form-label" for="of_new_description">Offer Description</label>
                         <textarea class="form-control" name="of_new_description[]" id="of_new_description"></textarea>
                    </div>
               </div>
          </div>
          </div>`;

          $('#offer_container').append(html);
     });

     $('body').delegate('.remove_offer', 'click', function(){
          var id = $(this).data('id');
          if($(this).attr('value') === 'appended'){
               $(this).closest('.offer_append').remove();
               return false;
          }

          let deleteIds = $('#removeOffer').val();

          if(deleteIds) {
               deleteIds += ',' + id;
          }else{
               deleteIds = id;
          }
          $('#removeOffer').val(deleteIds);

          $('.offer_append'+id).hide();
     });
});


</script>


@endsection
