{{-- @extends('admin_layout.master')
@section('content')
<div class="nk-content">

    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">Configuration</h4>
        </div>
    </div>

    <div class="container-fluid">
        <form method="post" enctype="multipart/form-data" id="settingsForm">
            @csrf
            <input type="hidden" name="type" value="{{ request()->input('type', 'config') }}">

            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    @foreach($data as $item)
                 
                        <div class="col-md-8 mt-2">
                            <div class="form-group">
                                <label class="form-label" for="{{ $item->key }}">{{ $item->name  ?? ""}} </label>
                                <input type="{{$item->ftype}}" class="form-control" id="{{ $item->key }}" name="{{ $item->key }}" value="{{  $item->value  }}">

                         
                            </div>
                            @if($item->key == 'user_default_image')
                                <div class="form-group">
                                    <img src="{{ dimage() }}">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary" type="button" id="saveSettingsBtn" >Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    $('#saveSettingsBtn').on('click', function(e) {
        e.preventDefault();

        let formData = $('#settingsForm').serialize();

        // Clear old errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.ajax({
            url: "{{ route('admin.dashboard.update_web_setting') }}",
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response);
                Swal.fire({
                    title: "Success!",
                    text: response.message || "Settings updated!",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#FD5602"
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function (key) {
                        let input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');

                        // Append error message
                        input.after('<div class="invalid-feedback">' + errors[key][0] + '</div>');
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong.",
                        icon: "error",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#d33"
                    });
                }
            }
        });
    });
</script>



@endsection --}}




@extends('admin_layout.master')
@section('content')
<div class="nk-content">

    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">Configuration</h4>
        </div>
    </div>

    <div class="container-fluid">
        <form method="post" enctype="multipart/form-data" id="settingsForm">
            @csrf
            <input type="hidden" name="type" value="{{ request()->input('type', 'config') }}">

            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    @foreach($data as $item)

                        <div class="col-md-8 mt-2">
                            @if($item->key === 'date_format')
                                <div class="form-group">
                                    <label class="form-label" for="{{ $item->key }}">
                                        {{ $item->name ?? 'Date Format' }}
                                    </label>
                                    @php
                                        $dateFormats = [
                                            'M d, Y'  => 'March 24, 2026  (US Long)',
                                            'm/d/Y'   => '03/24/2026  (US Short)',
                                            'd/m/Y'   => '24/03/2026  (EU Short)',
                                            'd M Y'   => '24 March 2026  (EU Long)',
                                            'Y-m-d'   => '2026-03-24  (ISO 8601)',
                                            'd-m-Y'   => '24-03-2026',
                                        ];
                                    @endphp
                                    <select class="form-control" id="{{ $item->key }}" name="{{ $item->key }}">
                                        @foreach($dateFormats as $formatValue => $formatLabel)
                                            <option value="{{ $formatValue }}" {{ $item->value === $formatValue ? 'selected' : '' }}>
                                                {{ $formatLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted mt-1 d-block">
                                        Preview: <strong id="date-format-preview">{{ now()->format($item->value ?? 'M d, Y') }}</strong>
                                    </small>
                                </div>

                            @elseif($item->ftype === 'select')
                                <div class="form-group">
                                    <label class="form-label" for="{{ $item->key }}">{{ $item->name ?? '' }}</label>
                                    <select class="form-control" id="{{ $item->key }}" name="{{ $item->key }}">
                                    </select>
                                </div>

                            @else
                                <div class="form-group">
                                    <label class="form-label" for="{{ $item->key }}">{{ $item->name ?? '' }}</label>
                                    <input type="{{ $item->ftype }}" class="form-control"
                                           id="{{ $item->key }}"
                                           name="{{ $item->key }}"
                                           value="{{ $item->value }}">
                                </div>
                                @if($item->key === 'user_default_image')
                                    <div class="form-group">
                                        <img src="{{ dimage() }}">
                                    </div>
                                @endif
                            @endif
                        </div>

                    @endforeach
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary" type="button" id="saveSettingsBtn">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    const formatPreviews = {
        'M d, Y':  'March 24, 2026',
        'm/d/Y':   '03/24/2026',
        'd/m/Y':   '24/03/2026',
        'd M Y':   '24 March 2026',
        'Y-m-d':   '2026-03-24',
        'd-m-Y':   '24-03-2026',
    };

    $('#date_format').on('change', function () {
        const selected = $(this).val();
        $('#date-format-preview').text(formatPreviews[selected] || selected);
    });

    $('#saveSettingsBtn').on('click', function (e) {
        e.preventDefault();

        let formData = $('#settingsForm').serialize();

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.ajax({
            url: "{{ route('admin.dashboard.update_web_setting') }}",
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Swal.fire({
                    title: "Success!",
                    text: response.message || "Settings updated!",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#FD5602"
                });
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function (key) {
                        let input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.after('<div class="invalid-feedback">' + errors[key][0] + '</div>');
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: "Something went wrong.",
                        icon: "error",
                        confirmButtonText: "OK",
                        confirmButtonColor: "#d33"
                    });
                }
            }
        });
    });
</script>

@endsection
