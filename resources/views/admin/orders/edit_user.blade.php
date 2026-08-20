@extends('admin_layout.master')
@section('content')
<div class="nk-content">
    <div class="container-fluid"> 
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">User Information</h3>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="card card-bordered card-stretch">
                <div class="card-inner-group">
                    <div class="card-inner p-2">
                        <form action="{{ route('update.user') }}" id="user-form" method="post">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $user->id ?? ''}}">
                            <div class="col-lg-12 d-flex" >
                                <div class="col-lg-6 p-2 form-group mb-3">
                                    <div class="form-label-group">
                                        <label class="form-label" for="firstName">First name:</label>
                                    </div>
                                    <div class="form-control-warp">
                                        <input type="text" class="form-control form-control-lg" name="first_name" id="firstName" placeholder="Enter First Name" value="{{ $user->first_name ?? ''}}">
                                    </div>
                                    <span id="first_name_error" class="text-danger"></span>
                                </div>
                                <div class="col-lg-6 p-2 form-group mb-3">
                                    <div class="form-label-group">
                                        <label  class="form-label" for="lastName">Last name:</label>
                                    </div>
                                    <div class="form-control-warp">
                                        <input type="text" class="form-control form-control-lg" name="last_name" id="lastName" placeholder="Enter Last Name" value="{{ $user->last_name ?? ''}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 d-flex" >
                                <div class="col-lg-6 p-2 form-group mb-3">
                                    <div class="form-label-group">
                                        <label  class="form-label" for="email">Email:</label>
                                    </div>
                                    <div class="form-control-warp">
                                        <input type="email" class="form-control form-control-lg" placeholder="Enter Email" name="email" id="email" value="{{ $user->email ?? ''}}">
                                    </div>
                                    <span id="email_error" class="text-danger"></span>
                                </div>
                                <div class="col-lg-6 p-2 form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="password">Password:</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                            <em class="passcode-icon icon-hide icon ni ni-eye"></em>
                                            <em class="passcode-icon icon-show icon ni ni-eye-off"></em>
                                        </a>
                                        <input  type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Enter Password">
                                    </div>
                                    <span id="password_error" class="text-danger"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 d-flex">
                                <div class="col-lg-12 p-3 form-group mb-3">
                                    <div class="form-label-group">
                                        <label class="form-label" for="role">Role:</label>
                                    </div>
                                    <div class="form-control-wrap">
                                        <select class="form-select form-control-lg" name="is_admin" id="role">
                                            <option value="0" {{ isset($user->is_admin) && $user->is_admin == 0 ? 'selected' : '' }}>User</option>
                                            <option value="1" {{ isset($user->is_admin) && $user->is_admin == 1 ? 'selected' : '' }}>Admin</option>
                                            <option value="2" {{ isset($user->is_admin) && $user->is_admin == 2 ? 'selected' : '' }}>abogada</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 d-flex">
                                <div class="form-group p-2">
                                    <button type="button" id="submit-btn" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#submit-btn').on('click',function(){
            first_name = $('#firstName').val();
            email = $('#email').val();
            id = $('#id').val();
            password = $('#password').val();

            if(first_name == null || first_name == undefined || first_name == ""){
                $('#first_name_error').show();
                $('#first_name_error').text('First Name is Required');
                return false;
            }

            if(email == null || email == undefined || email == ""){
                $('#email_error').show();
                $('#email_error').text('email is Required');
                return false;
            }

            if(id == null || id == undefined || id == ''){
                if(password == null || password == undefined || password == ""){
                    $('#password_error').show();
                    $('#password_error').text('Password is Required');
                    return false;
                }
            }

            $('#user-form').submit();
           
        });

        $('#firstName').on('input',function(){
            $('#first_name_error').hide();
        });
        $('#email').on('input',function(){
            $('#email_error').hide();
        });
        $('#password').on('input',function(){
            $('#password_error').hide();
        });
    });
</script>
@error('first_name')
    <script>
        $('#first_name_error').show();
        $('#first_name_error').text("{{ $message }}");
    </script>
@enderror

@error('email')
    <script>
        $('#email_error').show();
        $('#email_error').text("{{ $message }}");
    </script>
@enderror

@error('password')
    <script>
        $('#password_error').show();
        $('#password_error').text("{{ $message }}");
    </script>
@enderror
@endsection