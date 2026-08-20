@extends('admin_layout.master')
@section('content')

<div class="nk-content">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">
                @if(isset($notification) && $notification->id) Edit Notification @else Add Notification @endif
            </h4>
        </div>
    </div>

    <div class="container-fluid">
        <form
    id="notification-form"
    action="{{ isset($notification) && $notification->id ? route('admin.notifications.update', $notification->id) : route('admin.dashboard.add_notifications') }}"
    method="POST"
>
    @csrf
    @if(isset($notification) && $notification->id)
        @method('PUT')
    @endif

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label" for="title">Notification Title</label>
                    <input
                        type="text"
                        class="form-control"
                        id="title"
                        name="title"
                        value="{{ old('title', $notification->title ?? '') }}"
                    >
                    <span class="text text-danger title-error" style="display:none;">This field is required</span>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label" for="title">Notification Type</label>
                    <input
                        type="text"
                        class="form-control"
                        id="type"
                        name="type"
                        value="{{ old('type', $notification->type ?? '') }}"
                        {{ isset($notification) && $notification->id ? 'readonly' : '' }}
                        {{ isset($notification) && $notification->id ? 'disabled' : '' }}
                    >
                    <span class="text text-danger title-error" style="display:none;">This field is required</span>
                </div>
            </div>

            <div class="col-md-8">
                <div class="form-group">
                    <label class="form-label" for="content">Notification Content</label>
                    <textarea
                        class="form-control"
                        id="content"
                        name="content"
                    >{{ old('content', $notification->content ?? '') }}</textarea>
                    <span class="text text-danger content-error" style="display:none;">This field is required</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button class="btn btn-primary submitform" type="submit">
            {{ isset($notification) && $notification->id ? 'Update' : 'Save' }}
        </button>
    </div>
</form>

    </div>
</div>




<script>
    $(document).ready(function () {
        $('.submitform').on('click', function (e) {
            e.preventDefault();
            $('.text-danger').hide();

            let title = $('#title').val();
            let type = $('#type').val();
            let content = $('#content').val();

            let isValid = true;

            if (!title) {
                $('#title').siblings('.text-danger').show();
                isValid = false;
            }

            if (!type) {
                $('#type').siblings('.text-danger').show();
                isValid = false;
            }

            if (!content) {
                $('#content').siblings('.text-danger').show();
                isValid = false;
            }

            if (isValid) {
                $('#notification-form').submit();
            }
        });
    });
</script>
@endsection
