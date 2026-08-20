@extends('user_dashboard_layout.master')
@section('content')
<div class="scroll_div">
    <div class="main_ryt">
        <h2 class="m-0">
            {{-- Cambiar contraseña --}}
            Change Password
        </h2>
        <div class="Cont_from">
            <form action="{{route('user.configuration.update')}}" method="POST" id="updatePasswordForm">
                @csrf

                <!-- Current Password Field -->

                    {{-- <label for="CurrentPassword">Contraseña actual</label> --}}
                    <div class="password-field">
                        {{-- <input type="password" id="CurrentPassword" class="fakePassword" name="CurrentPassword" placeholder="Ingresa la contraseña" /> --}}
                        <x-google-input type="password" name="CurrentPassword" id="CurrentPassword"  label="Current Password" :attributes="['autocomplete' => 'new-password']" />

                        <span><i id="toggler" class="far fa-eye-slash"></i></span>

                    </div>
                    <span class="text error-msg" id="curr_pass"></span>

                <div class="fuild_2">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- New Password Field -->

                                {{-- <label for="NewPassword">Nueva contraseña</label> --}}
                                <div class="password-field">
                                    {{-- <input type="password" id="NewPassword" class="fakePassword" name="NewPassword" placeholder="Ingresa la nueva contraseña" /> --}}
                                    <x-google-input type="password" name="NewPassword" id="NewPassword"  label="New Password" :attributes="['autocomplete' => 'new-password']" />

                                    <span><i id="toggler" class="far fa-eye-slash"></i></span>

                                </div>
                                <span class="text error-msg" id="new_pass"></span>

                        </div>
                        <div class="col-md-6">
                            <!-- Confirm Password Field -->

                                {{-- <label for="ConfirmPassword">Confirmar contraseña</label> --}}
                                <div class="password-field">
                                    {{-- <input type="password" id="ConfirmPassword" name="ConfirmPassword" class="fakePassword" placeholder="Confirma la nueva contraseña" /> --}}
                                    <x-google-input type="password" name="ConfirmPassword" id="ConfirmPassword"  label="Confirm New Password" :attributes="['autocomplete' => 'new-password']" />

                                    <span><i id="toggler" class="far fa-eye-slash"></i></span>

                                </div>
                                <span class="text error-msg" id="cnfrm_pass"></span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="fuild mt-3">
                    <button type="button" class="user_link unq_btn up_password">
                        {{-- Actualizar contraseña --}}
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.querySelectorAll('.password-field').forEach(field => {
        let passwordInput = field.querySelector('input');
        let toggler = field.querySelector('i');

        toggler.addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggler.classList.remove('fa-eye-slash');
                toggler.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggler.classList.remove('fa-eye');
                toggler.classList.add('fa-eye-slash');
            }
        });
    });



    $(document).ready(function(){
        $('.up_password').click(function(e){
            e.preventDefault();
            let current_password = $("#CurrentPassword").val();
            let new_password = $("#NewPassword").val();
            let confirm_password = $("#ConfirmPassword").val();

            let isvalid = true;

            $('#curr_pass').text('').hide();
            $('#CurrentPassword').removeClass('invalid');

            $('#new_pass').text('').hide();
            $('#NewPassword').removeClass('invalid');

            // $('#cnfrm_pass').text('').hide();
            // $('#ConfirmPassword').removeClass('invalid');

            $('#CurrentPassword').on('input', function() {
                $('#curr_pass').hide();
                $('#CurrentPassword').removeClass('invalid');
            });

            $('#NewPassword').on('input', function() {
                $('#new_pass').hide();
                $('#NewPassword').removeClass('invalid');
            });


            $('#ConfirmPassword').on('input', function() {
                $('#cnfrm_pass').hide();
                $('#ConfirmPassword').removeClass('invalid');
            });

            // Validate current password
            if(current_password === '' || current_password === undefined || current_password === null){
                $('#curr_pass').text('Enter your current password').show();
                $('#CurrentPassword').addClass('invalid');
                isvalid = false;
            }else{
                $('#curr_pass').hide();
            }

            if(new_password === '' || new_password === undefined || new_password === null){
                $('#new_pass').text('Enter your new password').show();
                $('#NewPassword').addClass('invalid');
                isvalid = false;
            }else{
                $('#new_pass').hide();
            }

            if(confirm_password === '' || confirm_password === undefined || confirm_password === null){

                $('#cnfrm_pass').text('Confirm your new password').show();
                $('#ConfirmPassword').addClass('invalid');
                isvalid = false;
            }else{
                $('#cnfrm_pass').hide();
            }

            // if(new_password !== confirm_password){
            //     $('#cnfrm_pass').text('New password and confirm password do not match').show();
            //     $('#ConfirmPassword').addClass('invalid');
            //     isvalid = false;
            // }else{
            //     $('#cnfrm_pass').hide();
            // }

            if(isvalid == true){
                $('#updatePasswordForm').submit();
            }
        })
    })


</script>
@endsection
