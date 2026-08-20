@extends('users_layout.master')
<style>
    .input-box.error .input-1 {
    border: 1px solid #FD5602 !important;

}
</style>

@section('title',$login->meta_title)
@section('content')

@if($login->background_image != null)
    <?php $path = getStorageFilepath($login->file_path); ?>
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
    <!---------------------------------------------- section social ------------------------------------- -->

<section class="social_login h_page light p_120">
    <div class="inner_social_log">
        <div class="container">
            <div class="social_sec_wt">
                <div class="social_contct">
                    <form action="{{route('login.process')}}" id="loginForm" method="post" autocomplete="off" >
                        @csrf
                        <!-- <input type="hidden" name="redirect_url" id="redirect_url" value=""> -->
                        <input type="hidden" name="redirect_url" id="redirect_url" value="{{ request()->get('redirecturl' , '/legal-documents') }}">

                        <div class="social_hd">
                            <h2>
                                {{-- {{ $login->main_heading ?? 'Iniciar sesión' }} --}}
                                {{ $login->main_heading ?? 'Sign in to your account' }}
                            </h2>
                            <p class="hd_para_consta">
                                {{-- {{ $login->main_sub_heading ?? 'Bienvenido! Por favor seleccione un método de inicio de sesión' }} --}}
                                {{ $login->main_sub_heading ?? 'Welcome! Please choose a login method.' }}
                            </p>
                        </div>
                        <div class="goog_fb_box">
                            <div class="in_gfb_box">
                                <a class="social_btn" href="{{ route('login.google') }}" class="link-url"><i class="fa-brands fa-google"></i>  <span
                                        class="span1"></span><small>Continue with <b>Google</b></small> </a>
                            </div>
                            {{-- <div class="in_gfb_box">
                                <a class="social_btn2" href=""><i class="fa-brands fa-facebook" class="link-url"></i>
                                    <span class="span1"></span> </a>
                            </div> --}}
                            {{-- <div class="in_gfb_box">
                                <a class="social_btn2" href="{{ route('login.facebook') }}"><i class="fa-brands fa-facebook" class="link-url"></i>
                                    <span class="span1"></span> </a>
                            </div> --}}
                        </div>

                        <div class="af_bfore_line">
                            <div class="right-line left-line center-text">or</div>
                        </div>
                        <div class="contac_ot_box">
                            <div class="form-group">
                                <x-google-input
                                    type="text"
                                    name="email"
                                    id="email"
                                    {{-- label="Correo electrónico" --}}
                                    label="Email"
                                    :attributes="['autocomplete' => 'off']"
                                />
                                <span class="error-msg" id="email-wrong"></span>
                                @error('email')
                                <span class="error-msg">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="contac_ot_box">
                            <div class="form-group">
                                {{-- <input id="password-field" type="password" class="form-control inside_contac"
                                    name="password" placeholder="Contraseña"> --}}
                                    <x-google-input
                                        type="password"
                                        name="password"
                                        id="password-field"
                                        {{-- label="Contraseña" --}}
                                        label="Password"
                                        :attributes="['autocomplete' => 'new-password']"
                                    />

                                <span toggle="#password-field"
                                    class="fa fa-fw fa-eye-slash field-icon toggle-password" style="width:0px;">
                                </span>
                                <span class="error-msg" id="pass-wrong" style="display:none"></span>
                                @error('password')
                                <span class="error-msg">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="selct_div">
                            <div class="mainte_box">
                                {{-- <div class="ot_check_mainte">
                                    <div class="form-group">
                                        <input type="checkbox" id="html">
                                        <label for="html">Mantener sesión activa </label>
                                    </div>
                                </div> --}}
                                <div></div>
                                <div class="ot_check_mainte">
                                    <p class=" ot_check_mainte_pra">
                                        <a href="{{route('recover.password')}}" class="link-url" >
                                            {{-- Recuperar contraseña --}}
                                            Forgot Password?
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="contac_ot_box">
                            <button id="login_submit" type="button" class="cta_org" tabindex="0">
                                {{-- Ingresar --}}
                                Sign In
                            </a>
                        </div>
                        <div class="contac_ot_box">
                            <p class="contaco_para_in">
                                    {{-- ¿No estas registrado? <span class="span1"><a href="{{route('register')}}" class="link-url">Crear cuenta</a></span> --}}
                                    Not registered? <span class="span1"><a href="{{route('register')}}" class="link-url">Create an account</a></span>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
  $(document).ready(function() {


    $('#loginForm').keypress(function (event) {
        if (event.which === 13) {
            event.preventDefault();
            $(this).submit();
        }
    });


    $('#login_submit').click(function(e) {
        e.preventDefault();
        let last_visited = document.referrer;
        // $('#redirect_url').val(last_visited);

        let email = $('#email').val();
        let password = $('#password-field').val();
        //let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email regex
        let emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;

        // Clear any previous error messages

        let hasError = false;

        // Clear previous error states
        $('#pass-wrong').text('').hide();
        $('#password-field').removeClass('invalid');

        $('#email-wrong').text('').hide();
        $('#email').removeClass('invalid');

        $('#email').on('input', function() {
            $(this).removeClass('invalid');
            $('#email-wrong').hide();
        });

        $('#password-field').on('input', function() {
            $(this).removeClass('invalid');
            $('#pass-wrong').hide();
        });

        // Validate password
        if (password === '') {
            $('#pass-wrong').text('Enter your password').show();
            $('#password-field').addClass('invalid');
            hasError = true;
        }

        // Validate email
        if (email === '') {
            $('#email-wrong').text('Enter your email').show();
            $('#email').addClass('invalid');
            hasError = true;
        } else if (!emailRegex.test(email)) {
            $('#email-wrong').text('Please enter a valid email address').show();
            $('#email').addClass('invalid');
            hasError = true;
        } else {
            $('#email').removeClass('invalid');
        }

        if(!hasError){
            $('#loginForm').submit();
        }

    });

});



$(document).ready(function() {
    $(".toggle-password").click(function() {
        const passwordField = $($(this).attr("toggle"));
        const type = passwordField.attr("type") === "password" ? "text" : "password";
        passwordField.attr("type", type);

        // Toggle the eye icon
        if (type === "password") {
            $(this).removeClass("fa-eye").addClass("fa-eye-slash"); // When hidden
        } else {
            $(this).removeClass("fa-eye-slash").addClass("fa-eye"); // When shown
        }
    });
});
</script>

@endsection
