<!-- resources/views/admin/emails/recover_password_mail.blade.php -->

@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title">{{ $template ? 'Edit' : 'Add' }} Email Template</h4>
            </div>
        </div>

        <div class="container-fluid">
            <form action="{{ route('admin.dashboard.store.recovery.password.email') }}" method="post">
                @csrf
                @if($template)
                    <input type="hidden" name="template_id" value="{{ $template->id }}">
                @endif

                <div class="row main_section">
                    <div class="col-md-8 left_content">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">

                                <!-- Subject Field -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="subject"><b><h5>Subject</h5></b></label>
                                        <input type="text" class="form-control form-control-lg" id="subject" name="subject" value="{{ old('subject', $template->subject ?? '') }}">
                                        @error('subject')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                <!-- Heading Field -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="heading"><b><h5>Heading</h5></b></label>
                                        <input type="text" class="form-control form-control-lg" id="heading" name="heading" value="{{ old('heading', $template->heading ?? '') }}">
                                        @error('heading')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                <!-- Body Field -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="body"><b><h5>Body</h5></b></label>
                                        <textarea name="body" id="body" cols="30" rows="10" class="form-control form-control-lg">{{ old('body', $template->body ?? '') }}</textarea>
                                        @error('body')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                <!-- Button Text Field -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="button_text"><b><h5>Button Text</h5></b></label>
                                        <input type="text" class="form-control form-control-lg" id="button_text" name="button_text" value="{{ old('button_text', $template->button_text ?? '') }}">
                                        @error('button_text')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                <!-- Footer Field -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="footer"><b><h5>Footer</h5></b></label>
                                        <textarea name="footer" id="footer" cols="30" rows="5" class="form-control form-control-lg">{{ old('footer', $template->footer ?? '') }}</textarea>
                                        @error('footer')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <hr>

                                <!-- Submit Button -->
                                <div class="col-md-12">
                                    <div class="nk-block-head-content">
                                        <div class="up-btn mbsc-form-group">
                                            <button class="btn btn-primary" type="submit">{{ $template ? 'Update' : 'Add' }} Email Template</button>
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

@endsection

