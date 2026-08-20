@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                {{-- <h4 class="nk-block-title">{{ isset($faq) ? 'Edit' : 'Add' }} FAQ</h4> --}}
                <h4 class="nk-block-title">Change Email & Password</h4>
            </div>
        </div>
     
        <div class="container-fluid">
            <form action="{{ route('admin.dashboard.store.change.password') }}" method="post" enctype="multipart/form-data">
                @csrf
                <!-- If it's an update, pass the FAQ ID as a hidden field -->
                {{-- @if(isset($faq))
                    <input type="hidden" name="faq_id" value="{{ $faq->id }}">
                @endif --}}

                <div class="row main_section">
                    <div class="col-md-8 left_content">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">

                                <!-- Email Field -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="email"><b><h5>Email</h5></b></label>
                                        <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email' , $email ?? '') }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                <!-- Current Password Field -->
                                <div class="col-md-12">
                                    <div class="form-group" >
                                        <label class="form-label" for="current_password"><b><h5>Current Password</h5></b></label>
                                        <div style="position: relative;">
                                            <input type="password" class="form-control form-control-lg" id="current_password" name="current_password" autocomplete="current-password" style="padding-right: 40px;" >
                                            <span toggle="#current_password" class="fa fa-fw fa-eye-slash field-icon toggle-password" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;" ></span>
                                        </div>
                                        @error('current_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                <!-- New Password Field -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="new_password"><b><h5>New Password</h5></b></label>
                                        <div style="position: relative;">
                                            <input type="password" class="form-control form-control-lg" id="new_password" name="new_password" style="padding-right: 40px;">
                                            <span toggle="#new_password" class="fa fa-fw fa-eye-slash field-icon toggle-password" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></span>
                                        </div>
                                        
                                        @error('new_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                {{-- Confirm New Password Field --}}
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="new_password_confirmation"><b><h5>Confirm New Password</h5></b></label>
                                        <div style="position: relative;">
                                            <input type="password" class="form-control form-control-lg" id="new_password_confirmation" name="new_password_confirmation" style="padding-right: 40px;">
                                            <span toggle="#new_password_confirmation" class="fa fa-fw fa-eye-slash field-icon toggle-password" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></span>
                                        </div>
                                        @error('new_password_confirmation')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12">
                                    <div class="nk-block-head-content">
                                        <div class="up-btn mbsc-form-group">
                                            <button class="btn btn-primary" type="submit"> Update </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".toggle-password").click(function() {
            const input = $($(this).attr("toggle"));
            // alert(input.prop("type"));
            const type = input.attr("type") === "password" ? "text" : "password";

            input.attr("type", type);

            // Toggle the icon
            $(this).toggleClass("fa-eye fa-eye-slash");
        });
    });
</script>


@endsection