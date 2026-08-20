@extends('admin_layout.master')
@section('content')

<div class="nk-content">
                       
     <div class="nk-block-head">
          <div class="nk-block-head-content">
              <h4 class="nk-block-title">Message</h4>
          </div>
      </div>
     <div class="container-fluid">
          <form action="{{ url('/admin-dashboard/save/messages') }}" method="post" enctype="multipart/form-data">
               @csrf
               <div class="card card-bordered card-preview">
                    <div class="card-inner">
                         <div class="col-md-8">
                              <div class="form-group">
                                   <label class="form-label" for="page_type">Select Page</label>
                                   <div class="form-control-wrap"> 
                                        <select class="form-select js-select2 page_type" id="page_type" name="page_type" data-search="on" tabindex="-1" aria-hidden="true">
                                             <!-- <option value="" disabled selected>Select</option> 
                                             <option value="login">Login</option>
                                             <option value="register">Register</option>
                                             <option value="contact">Contact us</option> -->
                                             <option value="" disabled selected>Select</option>
                                             <option value="login" {{ old('page_type') == 'login' ? 'selected' : '' }}>Login</option>
                                             <option value="register" {{ old('page_type') == 'register' ? 'selected' : '' }}>Register</option>
                                             <option value="contact" {{ old('page_type') == 'contact' ? 'selected' : '' }}>Contact us</option>
                                        </select>
                                   </div>
                              </div>
                         </div>
                         <br>
                         <div class="login_message_fields" style="display:none;">
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <div class="col-md-8">
                                             <div class="form-group">
                                                  <label class="form-label" for="incorrect_username">Incorrect username</label>
                                                  <input type="text" class="form-control" id="incorrect_username" name="incorrect_username" value="{{ $login_data['incorrect_username'] ?? '' }}">
                                             </div>
                                             <div class="form-group">
                                                  <label class="form-label" for="incorrect_password">Incorrect password</label>
                                                  <input type="text" class="form-control" id="incorrect_password" name="incorrect_password" value="{{ $login_data['incorrect_password'] ?? '' }}">
                                             </div>
                                             <div class="form-group">
                                                  <label class="form-label" for="google_login_error">Login with Google error</label>
                                                  <input type="text" class="form-control" id="google_login_error" name="google_login_error" value="{{ $login_data['google_login_error'] ?? '' }}">
                                             </div>
                                             <div class="form-group">
                                                  <label class="form-label" for="fb_login_error">Login with Facebook error</label>
                                                  <input type="text" class="form-control" id="fb_login_error" name="fb_login_error" value="{{ $login_data['fb_login_error'] ?? '' }}">
                                             </div>
                                             <div class="form-group">
                                                  <label class="form-label" for="login_success_message">Success message</label>
                                                  <input type="text" class="form-control" id="login_success_message" name="login_success_message" value="{{ $login_data['login_success_message'] ?? '' }}">
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                         <div class="register_message_fields" style="display:none;">
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <div class="col-md-8">
                                             <div class="form-group">
                                                  <label class="form-label" for="required_field_msg">Required fields</label>
                                                  <input type="text" class="form-control" id="required_field_msg" name="required_field_msg" value="{{ $register_data['required_field_msg'] ?? '' }}">
                                             </div>
                                             <div class="form-group">
                                                  <label class="form-label" for="invalid_email_error">Invalid email error</label>
                                                  <input type="text" class="form-control" id="invalid_email_error" name="invalid_email_error" value="{{ $register_data['invalid_email_error'] ?? '' }}">
                                             </div>
                                             <div class="form-group">
                                                  <label class="form-label" for="unique_email">Unique email</label>
                                                  <input type="text" class="form-control" id="unique_email" name="unique_email" value="{{ $register_data['unique_email'] ?? '' }}">
                                             </div>
                                             <div class="form-group">
                                                  <label class="form-label" for="register_success_msg">Success message</label>
                                                  <input type="text" class="form-control" id="register_success_msg" name="register_success_msg" value="{{ $register_data['register_success_msg'] ?? '' }}">
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                         <div class="contact_message_fields" style="display:none;">
                              <div class="card card-bordered card-preview">
                                   <div class="card-inner">
                                        <div class="col-md-8">
                                             <div class="form-group">
                                                  <label class="form-label" for="required_field">Required fields</label>
                                                  <input type="text" class="form-control" id="required_field" name="required_field" value="{{ $contact_data['required_field'] ?? '' }}">
                                             </div>
                                             <div class="form-group">
                                                  <label class="form-label" for="recaptcha_error">Recaptcha error</label>
                                                  <input type="text" class="form-control" id="recaptcha_error" name="recaptcha_error" value="{{ $contact_data['recaptcha_error'] ?? '' }}">
                                             </div>
                                             <div class="form-group">
                                                  <label class="form-label" for="contact_success_msg">Success Message</label>
                                                  <input type="text" class="form-control" id="contact_success_msg" name="contact_success_msg" value="{{ $contact_data['contact_success_msg'] ?? '' }}">
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

     $(document).ready(function(){
          var current_page_type = "{{ session('page_type') }}";
          // console.log(current_page_type);
          var saved_page_type = '{{ old('page_type', session('page_type')) }}';
          // console.log(saved_page_type);

          if(saved_page_type){
               if(current_page_type == 'login'){
                    $('.login_message_fields').show();
               }else if(current_page_type == 'register'){
                    $('.register_message_fields').show();
               }else if(current_page_type == 'contact'){
                    $('.contact_message_fields').show();
               }
          }

          $('.page_type').on('change', function(){
               var selected_page = $(this).val();
               // console.log(selected_page);
               $('.login_message_fields').hide();
               $('.register_message_fields').hide();
               $('.contact_message_fields').hide();

               if(selected_page === 'login') {
                    $('.login_message_fields').show();
               }else if(selected_page === 'register'){
                    $('.register_message_fields').show();
               }else if(selected_page === 'contact'){
                    $('.contact_message_fields').show();
               }
          });
     })

</script>

@endsection

