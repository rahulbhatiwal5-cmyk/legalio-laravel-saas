@extends('users_layout.master')
<style>
               .contact-banner {
               padding: 170px 0 60px !important;
               }
</style>
@section('title',$contact->meta_title)
@section('content')

<?php
     $path = getStorageFilepath($contact->background_image_path);
?>
<section class="banner_sec dark inner-banner acerca contact-banner" style="background-image: url({{ asset('storage/'.$path) }});">
     <div class="container banner-col-width">
          <div class="row align-items-center contact-us-banner-row">
               <div class="col-md-6 banner-col">
                    <div class="banner_content">
                         <h1>{{ $contact->banner_title ?? '' }}</h1>
                         <p>
                         {{ $contact->banner_description ?? '' }}
                         </p>
                    </div>
               </div>
               {{-- <div class="col-md-6 banner-col">
                    <div class="banner_img">
                         $banner_path = getStorageFilepath($contact->banner_image_path);
                    ?>
                         <img src="{{ asset('storage/'.$banner_path) }}" alt="">
                    </div>
               </div> --}}
          </div>
     </div>
</section>

<section class="contac_login_card p_120 light">
     <div class="container">
          <div class="wt_ot">
               <div class="inside_contac_box">
                    <div class="h_contac_box" id="top">
                         <h2 class="ponte_h">{{ $contact->main_title ?? '' }}</h2>
                    </div>
                    <form action="{{ url('/contactusProcc') }}" id="contact-form" method="post" enctype="multipart/form-data">
                         @csrf
                         <div class="contac_inp_fld">

                              <div class="inside_contac_fild">

                                   {{-- Reason Dropdown --}}
                                   <select name="reason_id" class="mine_input">
                                       <option value="">Select a topic</option>
                                       {{-- <option value="">Seleccione un motivo</option> --}}
                                       {{-- @foreach($reasons as $reason)
                                           <option value="{{ $reason->id }}" {{ old('reason_id') == $reason->id ? 'selected' : '' }}>
                                               {{ $reason->name }}
                                           </option>
                                       @endforeach --}}
                                       <option value="Default Problem">
                                      Default Problem
                                       </option>
                                   </select>
                                   @if($errors->has('reason_id'))
                                       <span class="text-danger">{{ $errors->first('reason_id') }}</span>
                                   @endif

                                   {{-- Subject Text Input --}}
                                   {{-- <input type="text" class="mine_input mt-2" name="subject" placeholder="Subject" value="{{ old('subject') }}">
                                   @if($errors->has('subject'))
                                       <span class="text-danger">{{ $errors->first('subject') }}</span>
                                   @endif --}}

                                   {{-- Description Textarea --}}


                               </div>


                              <div class="mensaje_img">
                                   <div class="inside_contac_fild textarea-wrapper">
                                        <div class="message_div">

                                             <textarea name="message" class="mine_input" placeholder="Your message" cols="0" rows="6">{{ old('message') }}</textarea>
                                                  @if($errors->has('message'))
                                                  <span class="text-danger">{{ $errors->first('message') }}</span>
                                                  @endif
                                        </div>
                                        <div class="image-wrapper">
                                             <img id="contact_image" src="{{ asset('assets/img/Group1.svg') }}" alt="Upload Icon">
                                        </div>

                                        <!-- @if($errors->has('message'))
                                        <span class="text-danger">{{ $errors->first('message') }}</span>
                                        @endif -->
                                   </div>
                                   <input type="file" id="fileInput" class="form-control-file upload_input_file" name="fileInput" style="display:none;">
                                   <span id="fileName" class="file-name-display"></span>
                              </div>
                              @if($errors->has('fileInput'))
                                   <span class="text-danger">{{ $errors->first('fileInput') }}</span>
                              @endif
                              <!-- <div class="inside_contac_fild">
                                   <div class="drag_h_contac">
                                        <h6 class="drg_hd">
                                             Agregar archivo :
                                        </h6>
                                   </div>
                                   <div class="drag_drop_box">
                                        <div class="upload-box mine_input">
                                             <input type="file" name="file" id="fileInput" hidden />
                                             <label for="fileInput">
                                                  <img src="{{ asset('assets/img/drag_dp.svg') }}" alt="">
                                                  <p>Arrastra una imagen aquí o <span>sube un archivo</span></p>
                                             </label>
                                        </div>
                                        <div class="upload-box mine_input">
                                             <div class="file_input_para">
                                                  <input type="file" id="fileInput" class="form-control-file upload_input_file" name="fileInput" style="display:none;">
                                                  <p>Arrastra una imagen aquí o <span>sube un archivo</span></p>
                                             </div>
                                             <div class="file_img_icon">
                                                  <label for="fileInput11">
                                                       <img id="contact_image" src="{{ asset('assets/img/upload_img.svg') }}" alt="">
                                                  </label>
                                             </div>
                                        </div>
                                   </div>
                                   @if($errors->has('file'))
                                   <span class="text-danger">{{ $errors->first('file') }}</span>
                                   @endif
                              </div> -->

                              {{-- <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                              @if($errors->has('g-recaptcha-response'))
                              <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                              @endif --}}
                              <div class="outer_aft_btn">
                                   <button class="cta_org submit-btn" type="submit" tabindex="0">Send Message</button>
                              </div>
                         </div>
                    </form>
               </div>
          </div>
     </div>

</section>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@if(Session::has('ticket_success'))

    <script>
            document.addEventListener("DOMContentLoaded", function () {
            const successData = @json(Session::get('ticket_success'));

            Swal.fire({
                title: "Ticket Confirmed!",
                html: successData.message + "<br><br><a href='/track-ticket/" + successData.ticket_id + "'>Track your ticket status here</a>",
                icon: "success",
                confirmButtonText: "Go to Dashboard",
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('user.support') }}"; // <-- replace with your actual dashboard route name
                }
            });
        });
    </script>
@endif


<script>
     $(document).ready(function(){
          $('#contact_image').on('click', function(){
               console.log('Image clicked'); // Log message to console
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
     });
</script>

@endsection
