@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="nk-block-head">
          <div class="nk-block-head-content">
              <h4 class="nk-block-title">@if($review->id ?? '' ) Edit Review @else Add Reviews @endif</h4>
          </div>
      </div>
     <div class="container-fluid">
          <form id="review-form" action="{{ url('/admin-dashboard/add-review') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" name="id" id="review_id" value="{{ $review->id ?? '' }}">
               <input type="hidden" name="doc_id" id="doc_id" value="{{ $review->document->id ?? '' }}">
               <input type="hidden" id="doc_name" value="{{ $review->document->title ?? '' }}">
               <div class="card card-bordered card-preview">
                    <div class="card-inner">
                         @if(isset($review) && $review != null)
                         <div class="col-md-8 revw-title">
                            <h5 >{{ $review->document->title ?? ""}}</h5>
                         </div>
                         @else
                         <div class="col-md-8 revw-drpdwn">
                              <div class="form-group">
                                   <label class="form-label" for="">Select Document</label>
                                   <select class="form-select js-select2" id="document" name="document" data-search="on" data-placeholder="Select Document">
                                        <option value="">Select Document</option>
                                        @foreach($documents as $docu)
                                             <option value="{{ $docu->id ?? '' }}">{{ $docu->title ?? '' }}</option>
                                        @endforeach
                                   </select>
                                   <span class="text text-danger" id="error" style="display:none;">This field is required</span>
                              </div>
                         </div>
                         @endif
                         <!-- @if(isset($review->user) && isset($review->user->file_path))
                         <div class="profil_shw">
                              <label class="form-label" for="">Show Profile Image</label>
                              <div class="col-md-8">
                                   <div class="form-group">
                                        <div class="custom-control custom-switch">
                                             <input type="checkbox" class="custom-control-input" id="is_show" name="is_show" value="1"
                                                  {{ isset($review) && $review->is_show == 1 ? 'checked' : '' }}>
                                             <label class="custom-control-label" for="is_show"></label>
                                        </div>
                                   </div>
                              </div>
                              <img id="profile-image" 
                              src="{{ isset($review->user->file_path) && $review->is_show 
                                  ? asset($review->user->file_path) 
                                  : dimage() }}"
                              alt="User Image" style="width: 50px; height: 50px; object-fit: cover;">
                         </div>
                         @endif -->

                         <!-- {{ $review->is_show ?? ''}} -->
                         @if(isset($review->user))
                         <div class="profil_shw">
                              <label class="form-label" for="">Show Profile Image</label>
                              <div class="col-md-8">
                                   <div class="form-group">
                                        <div class="custom-control custom-switch">
                                             <input type="checkbox" class="custom-control-input" id="is_show" name="is_show" value="1"
                                             {{ isset($review) && $review->is_show == 1 ? 'checked' : '' }}
                                             {{ empty($review->user->file_path) ? 'disabled' : '' }}>
                                             <label class="custom-control-label" for="is_show"></label>
                                        </div>
                                   </div>
                              </div>
                              @php
                                   $profileImage = (!empty($review->user->file_path) && $review->is_show == 1)
                                        ? asset($review->user->file_path)
                                        : dimage(); // your default image path
                              @endphp
                              <img id="profile-image"
                                   src="{{ $profileImage }}"
                                   alt="User Image"
                                   style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                         </div>
                         @endif
                         <div class="col-md-8 revw-rating">
                              <div class="form-group">
                                   <label class="form-label" for="rating">Document Rating</label>

                                   <div id="full-stars-example-two">
                                        <div class="ratings">
                                        @if(isset($review->rating) && $review->rating != null)
                                        @for($i = 1; $i <= 5; $i++)
                                             <label for="rating{{ $i }}">
                                                  <i rate="{{ $i }}" class="star fa fa-star {{ $review->rating >= $i ? 'rating-color' : '' }}"></i>
                                             </label>
                                             <input type="checkbox" name="rating" id="rating{{ $i }}" class="chkbox" style="display:none;" value="{{ $i }}" {{ $review->rating == $i ? 'checked' : '' }}>
                                        @endfor
                                        @else
                                             <label for="rating1">
                                                  <i rate="1" class="star fa fa-star rating-color"></i>
                                             </label>
                                             <input type="checkbox" name="rating" id="rating1" class="chkbox" style="display:none;" value="1">
                                             <label for="rating2">
                                                  <i rate="2" class="star fa fa-star rating-color"></i>
                                             </label>
                                             <input type="checkbox" name="rating" id="rating2" class="chkbox" style="display:none;" value="2">
                                             <label for="rating3">
                                                  <i rate="3" class="star fa fa-star rating-color"></i>
                                             </label>
                                             <input type="checkbox" name="rating" id="rating3" class="chkbox" style="display:none;" value="3">
                                             <label for="rating4">
                                                  <i rate="4" class="star fa fa-star rating-color"></i>
                                             </label>
                                             <input type="checkbox" name="rating" id="rating4" class="chkbox" style="display:none;" value="4">
                                             <label for="rating5">
                                                  <i rate="5" class="star fa fa-star rating-color"></i>
                                             </label>
                                             <input type="checkbox" name="rating" id="rating5" class="chkbox" style="display:none;" value="5" checked>
                                        @endif
                                        <span class="text text-danger" id="error" style="display:none;">This field is required</span>
                                        </div>
                                   </div>
                                   @error('rating')
                                        <span class="text text-danger">{{ $message }}</span>
                                   @enderror
                              </div>
                         </div>
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="first_name">First Name</label>
                                   @if(isset($review) && $review->user_id != null)
                                   <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $review->user->first_name ?? '' }}">
                                   @else
                                   <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $review->first_name ?? '' }}">
                                   @endif
                                   <span class="text text-danger" id="error" style="display:none;">This field is required</span>
                              </div>
                         </div>
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="last_name">Last Name</label>
                                   @if(isset($review) && $review->user_id != null)
                                   <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $review->user->last_name ?? '' }}">
                                   @else
                                   <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $review->last_name ?? '' }}">
                                   @endif
                                   <span class="text text-danger" id="error" style="display:none;">This field is required</span>
                              </div>
                         </div>
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="city_name">City Name</label>
                                   <input type="text" class="form-control" id="city_name" name="city_name" value="{{ $review->city ?? '' }}">
                                   <span class="text text-danger" id="error" style="display:none;">This field is required</span>
                              </div>
                         </div>
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="date">Date</label>
                                   <input type="date" class="form-control" id="date" name="date" value="{{ $review->date ?? '' }}">
                                   <span class="text text-danger" id="error" style="display:none;">This field is required</span>
                              </div>
                         </div>
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="description">Document Review Description</label>
                                   <textarea class="form-control" id="description" name="description">{{ $review->description ?? '' }}</textarea>
                                   <span class="text text-danger" id="error" style="display:none;">This field is required</span>
                              </div>
                         </div>
                    </div>
               </div>
               <div class="mt-3">
                    @if(isset($review) && $review != null)
                    <button class="btn btn-primary submitform" type="submit">Update</button>
                    @else
                    <button class="btn btn-primary submitform" type="submit">Save</button>
                    @endif
               </div>
          </form>
     </div>
</div>

<script>

$('.chkbox').change(function(){
     $(".chkbox").prop('checked', false);
     $(this).prop('checked', true);
     val = $(this).val();
     $('.star').removeClass('rating-color');

     for(x=val; x>0; x--){
          $(`i[rate="${x}"]`).addClass('rating-color');
     }
});

</script>

<script>
     $(document).ready(function(){
          $('.submitform').on('click', function(e){
               e.preventDefault();
               $('.text-danger').hide();

               var document = $('#document').val();
               var review_id = $('#review_id').val();
               if(review_id){
                    document = $('#doc_name').val();
               }
               var rating = $("input[name='rating']:checked").val(); 
               var first_name = $('#first_name').val();
               var last_name = $('#last_name').val();
               var city_name = $('#city_name').val();
               var date = $('#date').val();
               var description = $('#description').val();
               var is_show = $("input[name='is_show']").is(':checked') ? 1 : 0;
               var isValid = true;
               
               if(!document){  
                    $('#document').siblings('.text-danger').show();
                    isValid = false;
               }

               if(!rating){
                    $('#full-stars-example-two .text-danger').show();
                    isValid = false;
               }
               if(!first_name){
                    $('#first_name').siblings('.text-danger').show();
                    isValid = false;
               }
               if(!last_name){
                    $('#last_name').siblings('.text-danger').show();
                    isValid = false;
               }
               if(!city_name){
                    $('#city_name').siblings('.text-danger').show();
                    isValid = false;
               }
               if(!date){
                    $('#date').siblings('.text-danger').show();
                    isValid = false;
               }
               if(!description){
                    $('#description').siblings('.text-danger').show();
                    isValid = false;
               }

               if(isValid){
                    $('#review-form').submit();
               }
          });
     })
</script>


<script>
     // Pass the profile image URL and default image URL into JavaScript from PHP
     const profileImageUrl = "{{ isset($review->user->file_path) ? asset($review->user->file_path) : '' }}";
     const defaultImageUrl = "{{ dimage() }}"; // This calls the method properly
 
     document.getElementById('is_show').addEventListener('change', function() {
         const profileImage = document.getElementById('profile-image');
         
         if (this.checked) {
             // If checkbox is checked, use the user's profile image
             profileImage.src = profileImageUrl || defaultImageUrl; // Fallback to default image if no file_path
         } else {
        
             // If unchecked, use the default image
             profileImage.src = defaultImageUrl;
         }
     });
</script>


@endsection
