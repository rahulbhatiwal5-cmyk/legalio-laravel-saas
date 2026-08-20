<!DOCTYPE html>
<html>
<head>
    <title>Contract PDF</title>
    <link rel="stylesheet" href="{{ public_path('assets/css/style.css') }}">
    <style>
        .active_sec {
            animation: colorHighlight 4s forwards;
            transition: all 0.3s ease-in-out;
        }

        .pdf_sec {
            padding: 80px 0px 80px 0px;
            color: #002655;
        }
        
        .d-none {
            display: none;
        }
    </style>
</head>
<body>
    <section class="pdf_sec">
        <div class="container">
            <div class="pdf_content">
                <div class="right-content">
                    @foreach ($documentContents as $content)
                        @if (!empty($content->show) && $content->show)
                            <div id="right_content_div_{{ $content->id ?? '' }}"
                                 style="text-align:{{ $content->text_align ?? '' }}"
                                 class="r_div right-sec-div {{ $content->secure_blur_content ? 'secure_content secure_blur_sec' : '' }}"
                                 conditional_section="{{ $content->is_condition ? 'true' : null }}"
                                 data-conditions="{{ $content->conditions && count($content->conditions) > 0 ? json_encode($content->conditions) : null }}">

                                @if ($content->type == 'content_heading')
                                    <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                        {!! $content->content !!}
                                    </p>
                                @elseif($content->type == 'signature_field')
                                    @if ($content->signature_field != 0)
                                        <span class="sign_field">__________________</span>
                                        <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                            {!! $content->content !!}
                                        </p>
                                        <!-- <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                            {!! $content->content2 !!}
                                        </p>
                                        <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                            {!! $content->content3 !!}
                                        </p> -->
                                    @endif
                                @else
                                    <p style="text-align:{{ $content->text_align ?? '' }}; font-size:18px; font-weight:400;">
                                        {!! $content->content !!}
                                    </p>
                                @endif

                                <!-- @if ($content->secure_blur_content)
                                    <span class="text-hover">El texto borroso se hace visibe al descargar el documento.</span>
                                @endif -->
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</body>
</html>


