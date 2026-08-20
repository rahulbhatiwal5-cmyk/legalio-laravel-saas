<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../../../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Page Title  -->
    <title>Login</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{ asset('admin-theme/assets/css/dashlite.css?ver=3.1.2?kjkjkjkjkkjk') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('admin-theme/assets/css/theme.css?ver=3.1.2') }}">
</head>

<body class="nk-body bg-white npc-general pg-auth">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <!-- <a href="#" class="logo-link"> -->
                                <h4>Legalio</h4>
                            <!-- </a> -->
                        </div>
                        <div class="card card-bordered" >
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head" id="sign">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title text-center">Sign-In</h4>
                                    </div>
                                </div><!-- .nk-block-head -->
                                <div id="login-form">    
                                    <form id="myform" action="{{ url('/loginprocc') }}" class="form-validate is-alter" autocomplete="off" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label" for="email">Email</label>
                                            </div>
                                            <div class="form-control-wrap">
                                                <input autocomplete="off" type="text" class="form-control form-control-lg" id="email" name="email">
                                            </div>
                                            @if($errors->has('email'))
                                             <span class="text-danger">{{ $errors->first('email') }}</span>
                                             @endif
                                        </div><!-- .form-group -->
                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label" for="password">Password</label>
                                            </div>
                                            <div class="form-control-wrap">
                                                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                </a>
                                                <input autocomplete="new-password" type="password" class="form-control form-control-lg" id="password" name="password">
                                            </div>
                                             @if($errors->has('password'))
                                             <span class="text-danger">{{ $errors->first('password') }}</span>
                                             @endif
                                        </div><!-- .form-group -->
                                        <div class="form-group">
                                            <button class="btn btn-lg btn-primary btn-block">Sign in</button>
                                        </div>
                                    </form>
                                </div>
                            </div><!-- .nk-block -->
                        </div>
                    </div><!-- .nk-split -->
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script src="{{ asset('admin-theme/assets/js/bundle.js?ver=3.1.2') }}"></script>
    <script src="{{ asset('admin-theme/assets/js/scripts.js?ver=3.1.2') }}"></script>
    <script src="{{ asset('admin-theme/assets/js/example-toastr.js?ver=3.1.2') }}"></script>

    @if(Session::get('error'))
     <script>
          toastr.clear();
          NioApp.Toast('{{ Session::get("error") }}', 'error', {position: 'top-right'});
     </script>
     @endif
     @if(Session::get('success'))
     <script>
          toastr.clear();
          NioApp.Toast('{{ Session::get("success") }}', 'info', {position: 'top-right'});
     </script>
     @endif

</body>
</html>