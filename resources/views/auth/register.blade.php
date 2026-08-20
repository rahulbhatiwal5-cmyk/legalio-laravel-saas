@extends('users_layout.master')
<style>
    .input-box.error .input-1 {
    border: 1px solid #FD5602 !important;
}

.sub-heading-CA {
    margin: 2px 0 30px;
    font-size: 16px;
    font-weight: 400;
}
</style>

@section('title',$register->meta_title)
@section('content')

@if($register->background_image != null)
    <?php $path = getStorageFilepath($register->file_path); ?>
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

<section class="social_login h_page light p_120">
    <div class="regisater-form-div">
        <div class="inner_social_log">
            <div class="container">
                <div class="social_sec_wt">
                    <div class="social_contct">
                        <div class="social_hd">
                            {{-- <h2>{{ $register->main_heading ?? 'Crear cuenta' }}</h2> --}}
                            <h2>{{ $register->main_heading ?? 'Create Account' }}</h2>
                            <h3 class="sub-heading-CA">Get started. Create legal documents with ease.</h3>
                        </div>
                        <div class="goog_fb_box">
                            <div class="in_gfb_box">
                                <a class="social_btn" href="{{ route('login.google') }}" class="link-url"><i class="fa-brands fa-google"></i>  <span
                                        class="span1"></span> <small>Continue with <b>Google</b></small> </a>
                            </div>
                            {{-- <div class="in_gfb_box">
                                <a class="social_btn2" href=""><i class="fa-brands fa-facebook" class="link-url"></i>
                                    <span class="span1"></span> </a>
                            </div> --}}
                        </div>
                        <div class="af_bfore_line">
                            <div class="right-line left-line center-text">or</div>
                        </div>
                        <form method="post" action="{{ url('/registerProcc') }}" id="register-form" enctype="multipart/form-data"  autocomplete="off">
                            @csrf
                            <input type="hidden" name="redirecturl" value="{{ request()->get('redirecturl', '/legal-documents') }}">    
                            <div class="fs-n-lt-n">
                                <div class="contac_ot_box">
                                    <div class="form-group">
                                        {{-- <input type="text" class="inside_contac" name="first_name" placeholder="Nombre" required> --}}
                                        <x-google-input type="text" name="first_name" id="first_name" label="First Name" />
                                        <span class="name-error error-msg"></span>
                                        @error('first_name')
                                        <span class="name-error error-msg">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="contac_ot_box">
                                    <div class="form-group">
                                        {{-- <input type="text" class="inside_contac" name="last_name" placeholder="Apellido" required> --}}
                                        <x-google-input type="text" name="last_name" id="last_name" label="Last Name" />
                                        <span class="last-error error-msg"></span>
                                        @error('last_name')
                                        <span class="last-error error-msg">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="fs-n-lt-n">
                                <div class="contac_ot_box">
                                    <div class="form-group">
                                        <x-google-input type="text" name="email" id="email" label="Email" :attributes="['autocomplete' => 'off']" />
                                        <span class="email-error error-msg"></span>
                                        @error('email')
                                        <span class="email-error error-msg">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="contac_ot_box">
                                <div class="form-group">
                                    {{-- <input id="password-field" type="password" class="form-control inside_contac" name="password" placeholder="Contraseña" required> --}}
                                    <x-google-input type="password" name="password" id="password-field"  label="Password" :attributes="['autocomplete' => 'new-password']" />
                                    <span toggle="#password-field" class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
                                    <span class="pass-error error-msg"></span>
                                    @error('password')
                                        <span class="pass-error error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="contac_ot_box">
                                <div class="form-group">
                                    <input id="confirm-password-field" type="password" class="form-control inside_contac" name="password_confirmation" placeholder="Confirmar Contraseña" required>
                                    <span toggle="#confirm-password-field" class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
                                </div>
                                <span class="error-msg">@error('password_confirmation') {{ $message }} @enderror</span>
                            </div> --}}
                            <div class="outer_aft_btn mt-5">
                                <button class="cta_org submit-btn register_btn" type="button" tabindex="0">
                                    {{-- Crear cuenta --}}
                                    Register
                                </button>
                            </div>
                            <div class="contac_ot_box">
                                <p class="contaco_para_in">
                                    {{-- ¿Ya tienes una cuenta? <span class="span1"><a href="{{ route('login.user', ['redirecturl' => request('redirecturl')]) }}" class="link-url">Iniciar sesión</a></span> --}}
                                    Already have an account? <span class="span1"><a href="{{ route('login.user', ['redirecturl' => request('redirecturl')]) }}" class="link-url">Log In</a></span>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
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

<script>
$(document).ready(function(){
    $('.register_btn').click(function(e){
        e.preventDefault();
        let first_name = $("input[name='first_name']").val();
        let last_name = $("input[name='last_name']").val();
        let password = $("input[name='password']").val();
        let email = $("input[name='email']").val();
        //let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email regex
        let emailRegex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;

        let isvalid = true;

        $('.name-error').text('').hide();
        $('#first_name').removeClass('invalid');

        $('.last-error').text('').hide();
        $('#last_name').removeClass('invalid');

        $('.email-error').text('').hide();
        $('#email').removeClass('invalid');

        $('.pass-error').text('').hide();
        $('#password-field').removeClass('invalid');

        $('#first_name').on('input', function() {
            $(this).removeClass('invalid');
            $('.name-error').hide();
        });

        $('#last_name').on('input', function() {
            $(this).removeClass('invalid');
            $('.last-error').hide();
        });

        $('#email').on('input', function() {
            $(this).removeClass('invalid');
            $('.email-error').hide();
        });

        $('#password-field').on('input', function() {
            $(this).removeClass('invalid');
            $('.pass-error').hide();
        });

        if(first_name === '' || first_name === undefined || first_name === null){
            $('.name-error').text('Enter your first name').show();
            $('#first_name').addClass('invalid');
            isvalid = false;
        }else{
            $('.name-error').hide();
        }

        if(last_name === '' || last_name === undefined || last_name === null){
            $('.last-error').text('Enter your last name').show();
            $('#last_name').addClass('invalid');
            isvalid = false;
        }else{
            $('.last-error').hide();
        }

        // if (email === '') {
        //     $('.email-error').text('Enter your email').show();
        //     $('#email').addClass('invalid');
        //     hasError = true;
        // } else if (!emailRegex.test(email)) {
        //     $('.email-error').text('Please enter a valid email address').show();
        //     $('#email').addClass('invalid');
        //     hasError = true;
        // } else {
        //     $('#email').removeClass('invalid');
        // }

        if (email === '') {
            $('.email-error').text('Enter your email').show();
            $('#email').addClass('invalid');
            isvalid = false;
        } else if (!emailRegex.test(email)) {
            $('.email-error').text('Please enter a valid email address').show();
            $('#email').addClass('invalid');
            isvalid = false;
        } else {
            $('#email').removeClass('invalid');
        }

        if(password === '' || password === undefined || password === null){
            $('.pass-error').text('Enter your password').show();
            $('#password-field').addClass('invalid');
            isvalid = false;
        }else{
            $('.pass-error').hide();
        }

        if(isvalid == true){
            $('#register-form').submit();
        }

    })
})
</script>

<script>
    function setFocus(on) {
  var element = document.activeElement;

  if (on) {
    setTimeout(function () {
      element.parentNode.classList.add("focus");
    });
  } else {
    let box = document.querySelector(".input-box");
    box.classList.remove("focus");
    $("input").each(function () {
      var $input = $(this);
      var $parent = $input.closest(".input-box");
      if ($input.val()) $parent.addClass("focus");
      else $parent.removeClass("focus");
    });
  }
}

</script>
@endsection
