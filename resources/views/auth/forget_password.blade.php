@extends('users_layout.master')
@section('content')

@if($login->background_image != null)
    <?php $path = getStorageFilepath($login->document_file_path); ?>
    <section class="banner_sec dark inner-banner acerca model_banner" style="background-image: url({{ asset('storage/'.$path) }});">
@else
    <section class="banner_sec dark inner-banner acerca model_banner" style="background-image: url({{ asset('assets/img/banner-img.png') }});">
@endif
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="banner_content">
                </div>
            </div>
            <div class="col-md-5">
                <div class="banner_img">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="social_login forget_pass p_120">
    <div class="inner_social_log">
        <div class="container">
            <div class="social_sec_wt">
                <div class="social_contct">
                    <form method="post" id="passwordReset" action="{{ url('forget-password-email') }}" class="needs-validation" novalidate>
                        @csrf
                        <div class="social_hd">
                            <h2>Recuperar contraseña</h2>
                            <p class="hd_para_consta">
                                Recibirás un enlace para crear una nueva contraseña por correo electrónico.
                            </p>
                        </div>

                        <div class="contac_ot_box">
                            <div class="form-group">
                                <x-google-input 
                                    type="email" 
                                    name="email" 
                                    id="email" 
                                    label="Correo electrónico" 
                                    :value="old('email')" 
                                    :attributes="['autocomplete' => 'off']" 
                                />
                                <div class="invalid-feedback">
                                    Por favor, introduce un correo electrónico válido.
                                </div>
                                <span class="error-msg"></span>
                            </div>
                        </div>

                        <div class="contac_ot_box">
                            <button type="button" class="cta_org recover_password">Recuperar contraseña</button>
                        </div>

                        <div class="contac_ot_box">
                            <p class="contaco_para_in">
                                ¿No estás registrado? <span class="span1"><a href="{{route('register')}}" class="link-url">Crear cuenta</a></span>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>

    $(document).ready(function(){
        $('.recover_password').click(function(e){
            e.preventDefault();

            let email = $("input[name='email']").val();
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            let isValid = true;

            $('.error-msg').text('').hide();
            $('#email').removeClass('invalid');

            $('#email').on('input', function() {
                $(this).removeClass('invalid');
                $('.error-msg').hide();
            });
      
            if (email === '') {
                $('.error-msg').text('Enter your email').show();
                $('#email').addClass('invalid');
                isValid = false;
            } else if (!emailRegex.test(email)) {
                $('.error-msg').text('Please enter a valid email address').show();
                $('#email').addClass('invalid');
                isValid = false;
            }

            if (isValid) {
                $('#passwordReset').submit(); 
            }
        });
    });

</script>

@endsection