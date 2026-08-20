@php $hideHeader = true; @endphp
@extends('users_layout.master')
@section('title', $document->title ?? 'Legalio')
@section('content')
{{-- @include('users_layout.categories_header') --}}
@include('users_layout.detail_page_header')



<style>
.card_sec4_conrt .img_sec4 img {
    width: 80px;
    height: 80px;
}

@media (max-width:575px){
    
    .inner-row-grid {
    display: grid;
    grid-template-columns: 48% 48%;
    padding: 0 20px;
    width: 100%;
    gap: 10px !important;
    margin: auto;
    }

    .inner-row-grid .col-lg-2.col-md-6 {
        padding: 0 !important;
    }

    .inner-row-grid .inside_box_b {
        width: 100%;
    }

    .inner-row-grid .inside_box_b .img_tab_sec {
        padding: 20px;
        height: 100%;
    }

    .inner-row-grid .inside_box_b .img_tab_sec img {
        max-width: 70px;
    }
}
</style>
    @php use Carbon\Carbon; @endphp
    @php
        $keys = ['pinterest_link', 'twitter_link', 'fb_link'];

        $results = App\Models\Setting::whereIn('key', $keys)->get()->keyBy('key');

    @endphp

    </section>
    <!---------------------------------------------------- section2 start ------------------------------------ -->

    <section class="outer_sec2  p_120 ">
        <div class="inner_sec2 light">
            <div class="container">
                <div class="row desk_view_row">
                    <div class="col-lg-5">
                        <div class="pdf_in1">

                            <a href="{{ route('user.attempt_contract_questions', ['slug' => $document->slug ?? '']) }}"><img
                                    src="{{ $document->document_image }}" alt=""></a>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="pdf_in2">
                            <div class="pdf_head">
                                <h1>{{ $document->title ?? '' }} ({{ \Carbon\Carbon::now()->year }})</h1>
                            </div>
                            <div class="ul_st">
                                <ul class="inside_ul_pdf">
                                    <li><img src="{{ asset('assets/img/org_tick.svg') }}" alt=""></li>
                                    <li>{{ $data2['valid_in'] ?? '' }}</li>
                                </ul>
                            </div>
                            <div class="share_icon">
                                <div class="review">
                                    <ul class="cont_ul">

                                        <li class="drop_cont_li">
                                            <div class="select_ul">
                                                @php
                                                    $avgRating = $document->getavgRating();
                                                    $allDocumentRating = \App\Models\Document::getAllDocumentAvgRating();
                                                @endphp

                                                @if ($avgRating !== false)
                                                    <x-rating-component :rating="$avgRating" ratingClass="cont_li" />
                                                @else
                                                    <x-rating-component :rating="$allDocumentRating" ratingClass="cont_li"
                                                        ratingText="{{ $data2['rating_text'] ?? '' }}"
                                                        :showDescription="true" />
                                                @endif
                                            </div>
                                        </li>
                                        @php $showDescription = true; @endphp
                                        |
                                        @if ($avgRating !== false)
                                            <a href="#" type="button" data-bs-toggle="modal"
                                                data-bs-target="#exampleModalCenter" onclick="event.preventDefault();">
                                                <li class="cont_li review_opinion">
                                                      {{ $_Reviews->count() }} Reviews
                                                </li>
                                            </a>
                                        @elseif($showDescription == true)
                                            <div class="rating-description">
                                                {{ $ratingText ?? 'Legalio average' }}
                                            </div>
                                        @endif

                                    </ul>
                                </div>
                                <div class="sharing_icons social-fb">

                                    <div class="sharing_ul">
                                        <a aria-label="Facebook" class="fb_icon" href="{{ $dataLinks['fb_link'] ?? '' }}"
                                            title="Facebook" target="_blank">
                                            <span class="svg">
                                                <i class="fa-brands fa-facebook-f"></i>
                                            </span>
                                        </a>
                                    </div>

                                    <div class="sharing_ul">
                                        <a aria-label="X" class="twitter_icon" href="{{ $dataLinks['twitter_link'] ?? '' }}"
                                            title="Twitter" target="_blank">
                                            <span class="svg">
                                                <i class="fa-brands fa-x-twitter"></i>
                                            </span>
                                        </a>
                                    </div>

                                    <div class="sharing_ul">
                                        <a aria-label="LinkedIn" class="linkedin_icon"
                                            href="{{ $dataLinks['linkedin_link'] ?? '' }}" title="LinkedIn" target="_blank">
                                            <span class="svg">
                                                <i class="fa-brands fa-linkedin-in fa-rotate-by"></i>
                                            </span>
                                        </a>
                                    </div>

                                    <div class="sharing_ul">
                                        <a aria-label="WhatsApp" class="whatsapp_icon"
                                            href="{{ $dataLinks['whatsapp_link'] ?? '' }}" title="WhatsApp" target="_blank">
                                            <span class="svg">
                                                {{-- <svg style="display:block;border-radius:999px;" focusable="false"
                                                  aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%"
                                                    height="100%" viewBox="-2 -2 35 35">
                                                    <path fill="#1E2C4F"
                                                        d="M16 4C9.373 4 4 9.373 4 16c0 2.385.658 4.615 1.806 6.516L4 28l5.688-1.783A11.938 11.938 0 0 0 16 28c6.627 0 12-5.373 12-12S22.627 4 16 4zm5.894 16.471c-.247.696-1.443 1.33-1.972 1.375-.53.046-1.025.237-3.455-.72-2.926-1.148-4.788-4.143-4.934-4.334-.146-.192-1.192-1.585-1.192-3.023s.755-2.147 1.023-2.44c.267-.292.584-.365.779-.365.194 0 .389.002.559.01.18.008.42-.068.658.502.247.585.838 2.027.912 2.175.073.146.122.317.024.511-.097.195-.146.316-.291.487-.146.17-.307.38-.438.511-.146.146-.298.304-.128.597.17.292.755 1.245 1.621 2.017 1.113.993 2.052 1.3 2.345 1.446.292.146.463.122.634-.073.17-.195.73-.852.925-1.144.194-.292.389-.243.657-.146.268.097 1.7.802 1.992.948.292.146.486.219.559.34.073.122.073.706-.174 1.401z">
                                                    </path>
                                                </svg> --}}
                                                <i class="fa-brands fa-xl fa-whatsapp" style="font-size:20px"></i>
                                            </span>
                                        </a>
                                    </div>
                                    
                                    <div class="sharing_ul">
                                        <a aria-label="Copy Link" class="copy_link_icon" href="" title="Copy Link"
                                            target="_blank">
                                            <span class="svg">
                                                {{-- <svg style="display:block;border-radius:999px;" focusable="false"
                                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%"
                                                    height="100%" viewBox="-4 -4 40 40">
                                                    <path fill="#1E2C4F"
                                                        d="M24.412 21.177c0-.36-.126-.665-.377-.917l-2.804-2.804a1.235 1.235 0 0 0-.913-.378c-.377 0-.7.144-.97.43.026.028.11.11.255.25.144.14.24.236.29.29s.117.14.2.256c.087.117.146.232.177.344.03.112.046.236.046.37 0 .36-.126.666-.377.918a1.25 1.25 0 0 1-.918.377 1.4 1.4 0 0 1-.373-.047 1.062 1.062 0 0 1-.345-.175 2.268 2.268 0 0 1-.256-.2 6.815 6.815 0 0 1-.29-.29c-.14-.142-.223-.23-.25-.254-.297.28-.445.607-.445.984 0 .36.126.664.377.916l2.778 2.79c.243.243.548.364.917.364.36 0 .665-.118.917-.35l1.982-1.97c.252-.25.378-.55.378-.9zm-9.477-9.504c0-.36-.126-.665-.377-.917l-2.777-2.79a1.235 1.235 0 0 0-.913-.378c-.35 0-.656.12-.917.364L7.967 9.92c-.254.252-.38.553-.38.903 0 .36.126.665.38.917l2.802 2.804c.242.243.547.364.916.364.377 0 .7-.14.97-.418-.026-.027-.11-.11-.255-.25s-.24-.235-.29-.29a2.675 2.675 0 0 1-.2-.255 1.052 1.052 0 0 1-.176-.344 1.396 1.396 0 0 1-.047-.37c0-.36.126-.662.377-.914.252-.252.557-.377.917-.377.136 0 .26.015.37.046.114.03.23.09.346.175.117.085.202.153.256.2.054.05.15.148.29.29.14.146.222.23.25.258.294-.278.442-.606.442-.983zM27 21.177c0 1.078-.382 1.99-1.146 2.736l-1.982 1.968c-.745.75-1.658 1.12-2.736 1.12-1.087 0-2.004-.38-2.75-1.143l-2.777-2.79c-.75-.747-1.12-1.66-1.12-2.737 0-1.106.392-2.046 1.183-2.818l-1.186-1.185c-.774.79-1.708 1.186-2.805 1.186-1.078 0-1.995-.376-2.75-1.13l-2.803-2.81C5.377 12.82 5 11.903 5 10.826c0-1.08.382-1.993 1.146-2.738L8.128 6.12C8.873 5.372 9.785 5 10.864 5c1.087 0 2.004.382 2.75 1.146l2.777 2.79c.75.747 1.12 1.66 1.12 2.737 0 1.105-.392 2.045-1.183 2.817l1.186 1.186c.774-.79 1.708-1.186 2.805-1.186 1.078 0 1.995.377 2.75 1.132l2.804 2.804c.754.755 1.13 1.672 1.13 2.75z">
                                                    </path>
                                                </svg> --}}
                                                <i class="fa-solid fa-link"></i>
                                            </span>
                                        </a>
                                    </div>

                                </div>
                            </div>


                            <div class="cont_content">
                                @if (isset($document->short_description) && $document->short_description != null)
                                    <?php    print_r($document->short_description); ?>
                                @else
                                    <p class="text_contr">
                                        El Acuerdo Unilateral de Confidencialidad es de gran importancia cuando se trata de
                                        proteger información confidencial entre dos personas físicas o morales. En este
                                        acuerdo, la parte que recibe la información se compromete a no divulgarla,
                                        asegurando así su confidencialidad.

                                    </p>
                                    <p class="text_contr">
                                        <span class="span1"> En tan solo unos minutos, crea un Acuerdo Unilateral de
                                            Confidencialidad</span>
                                        ajustado a tus necesidades y en total cumplimiento con las leyes y regulaciones
                                        vigentes en México. Descárgalo al instante en PDF y DOCX (Word).
                                    </p>
                                @endif
                            </div>
                            <div class="time_box">
                                <ul class="time_ul">
                                    <li class="time_li"> <span
                                            class=" span1">{{ $data2['formatos_disponibles_text'] ?? 'Available formats' }}:
                                        </span>{{ $data2['formatos_disponibles_data_text'] ?? 'PDF, DOCX, Pages' }}</li>
                                </ul>
                                <ul class="time_ul">
                                    <li class="time_li">
                                        <span class="span1">
                                            {{ $data2['ultima_revision_text'] ?? 'Last updated' }}:
                                        </span>
                                        @php
                                            Carbon::setLocale('en');
                                            $formattedDate = Carbon::now()->translatedFormat('F Y');
                                        @endphp
                                            {{ ucfirst($formattedDate) }}
                                    </li>
                                </ul>
                                <ul class="time_ul">
                                    <li class="time_li"> <span
                                            class=" span1">{{ $data2['aplicable_en_text'] ?? 'Applicable in' }}:
                                        </span>{{ $data2['applicable_in'] ?? 'All United States ' }}</li>
                                </ul>
                                <ul class="time_ul">
                                    <li class="time_li"> <span class=" span1">{{ $data2['descargas_text'] ?? 'Downloads' }}:
                                        </span>{{ $data2['descargas_data_text'] ?? '1,587' }}</li>
                                </ul>
                                <div class="con_btn_div">
                                    <a href="{{ url('contracts/' . $document->slug ?? '') }}" class="cta_light_cont">
                                        {{ $data2['detail_page_letter_now_btn'] ?? 'Create Document Now' }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="docum_det_mob_sec">
                    <div class="pdf_in2">
                        <div class="pdf_head">
                            <h1>{{ $document->title ?? '' }} ({{ \Carbon\Carbon::now()->year }})</h1>
                        </div>
                        <div class="ul_st">
                            <ul class="inside_ul_pdf">
                                <li><img src="{{ asset('assets/img/org_tick.svg') }}" alt=""></li>
                                <li>{{ $data2['valid_in'] ?? '' }}</li>
                            </ul>
                        </div>
                        <div class="review">
                            <ul class="cont_ul">

                                <li class="drop_cont_li">
                                    <div class="select_ul">
                                        @php
                                            $avgRating = $document->getavgRating();
                                            $allDocumentRating = \App\Models\Document::getAllDocumentAvgRating();
                                        @endphp

                                        @if ($avgRating !== false)
                                            <x-rating-component :rating="$avgRating" ratingClass="cont_li" />
                                        @else
                                            <x-rating-component :rating="$allDocumentRating" ratingClass="cont_li"
                                                ratingText="{{ $data2['rating_text'] ?? '' }}"
                                                :showDescription="true" />
                                        @endif
                                    </div>
                                </li>
                                @php $showDescription = true; @endphp
                                |
                                @if ($avgRating !== false)
                                    <a href="#" type="button" data-bs-toggle="modal"
                                        data-bs-target="#exampleModalCenter" onclick="event.preventDefault();">
                                        <li class="cont_li review_opinion">
                                            {{ $showReviews ? $showReviews->count() : 0 }} reviews
                                        </li>
                                    </a>
                                @elseif($showDescription == true)
                                    <div class="rating-description">
                                        {{ $ratingText ?? 'Legalio average' }}
                                    </div>
                                @endif

                            </ul>
                        </div>
               
                        <div class="row dcu_row_mob">
                            <div class=" col-5 col-md-6">
                                <div class="pdf_in1">
                                    <a href="{{ route('user.attempt_contract_questions', ['slug' => $document->slug ?? '']) }}"><img
                                            src="{{ $document->document_image }}" alt=""></a>
                                </div>
                            </div>
                            <div class=" col-7 col-md-6">
                                <div class="docu_cont_area">
                                    <div class="share_icon">
                                        <div class="sharing_icons social-fb">

                                            <div class="sharing_ul">
                                                <a aria-label="Facebook" class="fb_icon" href="{{ $dataLinks['fb_link'] ?? '' }}"
                                                    title="Facebook" target="_blank">
                                                    <span class="svg">
                                                        <i class="fa-brands fa-facebook-f"></i>
                                                    </span>
                                                </a>
                                            </div>

                                            <div class="sharing_ul">
                                                <a aria-label="X" class="twitter_icon" href="{{ $dataLinks['twitter_link'] ?? '' }}"
                                                    title="Twitter" target="_blank">
                                                    <span class="svg">
                                                        <i class="fa-brands fa-x-twitter"></i>
                                                    </span>
                                                </a>
                                            </div>

                                            <div class="sharing_ul">
                                                <a aria-label="LinkedIn" class="linkedin_icon"
                                                    href="{{ $dataLinks['linkedin_link'] ?? '' }}" title="LinkedIn" target="_blank">
                                                    <span class="svg">
                                                        <i class="fa-brands fa-linkedin-in fa-rotate-by"></i>
                                                    </span>
                                                </a>
                                            </div>

                                            <div class="sharing_ul">
                                                <a aria-label="WhatsApp" class="whatsapp_icon"
                                                    href="{{ $dataLinks['whatsapp_link'] ?? '' }}" title="WhatsApp" target="_blank">
                                                    <span class="svg">
                                                        <svg style="display:block;border-radius:999px;" focusable="false"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%"
                                                            height="100%" viewBox="-2 -2 35 35">
                                                            <path fill="#1E2C4F"
                                                                d="M16 4C9.373 4 4 9.373 4 16c0 2.385.658 4.615 1.806 6.516L4 28l5.688-1.783A11.938 11.938 0 0 0 16 28c6.627 0 12-5.373 12-12S22.627 4 16 4zm5.894 16.471c-.247.696-1.443 1.33-1.972 1.375-.53.046-1.025.237-3.455-.72-2.926-1.148-4.788-4.143-4.934-4.334-.146-.192-1.192-1.585-1.192-3.023s.755-2.147 1.023-2.44c.267-.292.584-.365.779-.365.194 0 .389.002.559.01.18.008.42-.068.658.502.247.585.838 2.027.912 2.175.073.146.122.317.024.511-.097.195-.146.316-.291.487-.146.17-.307.38-.438.511-.146.146-.298.304-.128.597.17.292.755 1.245 1.621 2.017 1.113.993 2.052 1.3 2.345 1.446.292.146.463.122.634-.073.17-.195.73-.852.925-1.144.194-.292.389-.243.657-.146.268.097 1.7.802 1.992.948.292.146.486.219.559.34.073.122.073.706-.174 1.401z">
                                                            </path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                            
                                            <div class="sharing_ul">
                                                <a aria-label="Copy Link" class="copy_link_icon" href="" title="Copy Link"
                                                    target="_blank">
                                                    <span class="svg">
                                                        <svg style="display:block;border-radius:999px;" focusable="false"
                                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%"
                                                            height="100%" viewBox="-4 -4 40 40">
                                                            <path fill="#1E2C4F"
                                                                d="M24.412 21.177c0-.36-.126-.665-.377-.917l-2.804-2.804a1.235 1.235 0 0 0-.913-.378c-.377 0-.7.144-.97.43.026.028.11.11.255.25.144.14.24.236.29.29s.117.14.2.256c.087.117.146.232.177.344.03.112.046.236.046.37 0 .36-.126.666-.377.918a1.25 1.25 0 0 1-.918.377 1.4 1.4 0 0 1-.373-.047 1.062 1.062 0 0 1-.345-.175 2.268 2.268 0 0 1-.256-.2 6.815 6.815 0 0 1-.29-.29c-.14-.142-.223-.23-.25-.254-.297.28-.445.607-.445.984 0 .36.126.664.377.916l2.778 2.79c.243.243.548.364.917.364.36 0 .665-.118.917-.35l1.982-1.97c.252-.25.378-.55.378-.9zm-9.477-9.504c0-.36-.126-.665-.377-.917l-2.777-2.79a1.235 1.235 0 0 0-.913-.378c-.35 0-.656.12-.917.364L7.967 9.92c-.254.252-.38.553-.38.903 0 .36.126.665.38.917l2.802 2.804c.242.243.547.364.916.364.377 0 .7-.14.97-.418-.026-.027-.11-.11-.255-.25s-.24-.235-.29-.29a2.675 2.675 0 0 1-.2-.255 1.052 1.052 0 0 1-.176-.344 1.396 1.396 0 0 1-.047-.37c0-.36.126-.662.377-.914.252-.252.557-.377.917-.377.136 0 .26.015.37.046.114.03.23.09.346.175.117.085.202.153.256.2.054.05.15.148.29.29.14.146.222.23.25.258.294-.278.442-.606.442-.983zM27 21.177c0 1.078-.382 1.99-1.146 2.736l-1.982 1.968c-.745.75-1.658 1.12-2.736 1.12-1.087 0-2.004-.38-2.75-1.143l-2.777-2.79c-.75-.747-1.12-1.66-1.12-2.737 0-1.106.392-2.046 1.183-2.818l-1.186-1.185c-.774.79-1.708 1.186-2.805 1.186-1.078 0-1.995-.376-2.75-1.13l-2.803-2.81C5.377 12.82 5 11.903 5 10.826c0-1.08.382-1.993 1.146-2.738L8.128 6.12C8.873 5.372 9.785 5 10.864 5c1.087 0 2.004.382 2.75 1.146l2.777 2.79c.75.747 1.12 1.66 1.12 2.737 0 1.105-.392 2.045-1.183 2.817l1.186 1.186c.774-.79 1.708-1.186 2.805-1.186 1.078 0 1.995.377 2.75 1.132l2.804 2.804c.754.755 1.13 1.672 1.13 2.75z">
                                                            </path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>

                                        </div>
                                    </div>

                                    {{--========================= time-box ======================= --}}
                                    <div class="time_box">
                                    <ul class="time_ul">
                                            <li class="time_li"> <span
                                                    class=" span1">{{ $data2['formatos_disponibles_text'] ?? 'Available formats' }}:
                                                </span>{{ $data2['formatos_disponibles_data_text'] ?? 'PDF, DOCX, Pages' }}</li>
                                        </ul>
                                        <ul class="time_ul">
                                            <li class="time_li">
                                                <span class="span1">
                                                    {{ $data2['ultima_revision_text'] ?? 'Last updated' }}:
                                                </span>
                                                @php
                                                    Carbon::setLocale('en');
                                                    $formattedDate = Carbon::now()->translatedFormat('F Y');
                                                @endphp
                                                    {{ ucfirst($formattedDate) }}
                                            </li>
                                        </ul>
                                        <ul class="time_ul">
                                            <li class="time_li"> <span
                                                    class=" span1">{{ $data2['aplicable_en_text'] ?? 'Applicable in' }}:
                                                </span>{{ $data2['applicable_in'] ?? 'All United States ' }}</li>
                                        </ul>
                                        <ul class="time_ul">
                                            <li class="time_li"> <span class=" span1">{{ $data2['descargas_text'] ?? 'Downloads' }}:
                                                </span>{{ $data2['descargas_data_text'] ?? '1,587' }}</li>
                                        </ul>
                                        <div class="con_btn_div">
                                            <a href="{{ url('contracts/' . $document->slug ?? '') }}" class="cta_light_cont ">
                                                {{ $data2['detail_page_letter_now_btn'] ?? 'Create Document Now' }}</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>


    <!---------------------------------------------------- section4 start ---------------------------------- -->
    <section class=" sec4_conrt_ot light size18 ">
        <div class="in_sec4_cont">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sec4_conrt_h_ot">
                            <h2>{{ $legal_section->heading ?? '' }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="in_sec4_card_box p_120 pt-0">
            <div class="container">
                <div class="row">
                    @if (isset($legals) && $legals != null)
                        @foreach ($legals as $legal)
                                    <?php
                            $legal_path = getStorageFilepath($legal->media?->file_path);
                                                                                                                                                                                                                                                            ?>
                                    <div class="col-lg-4 col-md-6 mb-2">
                                        <div class="card_sec4_conrt ">
                                            <div class="img_sec4">
                                                <img src="{{ asset('storage/' . $legal_path) }}" alt="">
                                            </div>
                                            <div class="sec4_card_p">
                                                <h6 class="size20">{{ $legal->heading ?? '' }}</h6>
                                                <p class="sec4_card_para">
                                                    {{ $legal->description ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- ---------------------------------------------------- Artcile section start------------------------------------ -->
    <section class="sec6_outer_para light" style="margin:40px;">
        <div class="inside_para_sec6">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        @if (isset($article_sections) && $article_sections != null)
                            @foreach ($article_sections as $article)
                                <div class="Para_ot_box">
                                    <div class="head_sec6_para">
                                        <h2>
                                            @if ($loop->first)
                                                {{ $article->heading ?? '' }}
                                                {{ $document->title ?? '' }}{{ '?' }}
                                            @else
                                                {{ $article->heading ?? '' }}
                                            @endif
                                        </h2>
                                        <?php        print_r($article->description ?? ''); ?>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!---------------------------------------------------- Example section start------------------------------------>
    <section class="sec4_conrt_ot  light size18" style="padding:50px 0px; display: none;">
        <div class="in_sec4_cont exp_sec1">
            <div class="container">
                <div class="example_section_heading">
                    <h2>{{ $article_data['example_section_heading'] ?? '' }} {{ $document->title ?? '' }}</h2>
                </div>
                <div class="row">
                    <div class="col-lg-3 exp_sec2">
                        <a href="{{ url('contracts/' . $document->slug ?? '') }}">
                            <div class="img_contract">
                                <div class="img_overlay"></div>
                                <img src="{{ asset('preview_images/document_' . $document->id . '.png') }}">
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-9 exp_sec3">
                        <div class="head_sec6_para">
                            <p>
                                <?php print_r($article_data['example_section_description1'] ?? ''); ?>
                            </p>
                            <p>
                                <?php print_r($article_data['example_section_description2'] ?? ''); ?>
                            </p>

                            <p style="font-weight: 600">
                                <img src="{{ asset('assets/img/org_tick.svg') }}" alt="">
                                <span>
                                    Editable U.S. format in PDF, Word and Pages
                                </span>
                            </p>
                            <p style="font-weight: 600">
                                <img src="{{ asset('assets/img/org_tick.svg') }}" alt="">
                                <span>
                                    Editable U.S. format in PDF, Word and Pages
                                </span>
                            </p>
                            <p style="font-weight: 600">
                                <img src="{{ asset('assets/img/org_tick.svg') }}" alt="">
                                <span>
                                    Editable U.S. format in PDF, Word and Pages
                                </span>
                            </p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!---------------------------------------------------- section7 start------------------------------------>
    <!-- replaed sec 19 march -->
    <section class="sec7_cont_out  blue_bg_chng pb-0">
        <div class="const_bg_sec7">
            <div class="const_hed_sec7">
                <h2>
                    {{ $data2['guide_heading'] ?? '' }}
                </h2>
            </div>
            <div class="sec7_const_content">
                <div class="container">
                    <div class="row">
                        @if (isset($guides) && $guides != null)
                            @foreach ($guides as $key => $guide)
                                <div class="col-lg-6 {{ $key == 0 ? 'b_right' : '' }}">
                                    <div class="sec7_const_h">
                                        <div class="sec7_const_img">
                                        @if($key == 0)
                                            <img height="80px" width="80px" src="{{ asset('assets/images/DUD_logo.svg') }}" alt="Step 1">
                                        @elseif($key == 1)
                                            <img height="80px" width="80px" src="{{ asset('assets/images/CUD_logo.svg') }}" alt="Step 2">
                                        @endif
                                    </div>

                                        <div class="h_sec_const">
                                            <h3>{{ $guide->heading ?? '' }}</h3>
                                            <p>
                                                {!! $guide->description !!}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="con_btn_div h_sec_btn">
                <a href="{{ url('contracts/' . $document->slug ?? '') }}"
                    class="cta_light_cont ">{{ $data2['guide_button'] ?? '' }}</a>
            </div>
        </div>
    </section>

    <!---------------------------------------------------- section6 start------------------------------------>
    <section class="sec6_outer_para light" style="margin:40px;">
        <div class="inside_para_sec6">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        @if (isset($document->documentField) && $document->documentField != null)
                            @foreach ($document->documentField as $field)
                                            <?php
                                $path = getStorageFilepath($field->media?->file_path);
                                                                                                                                                                                                                                                                                                            ?>
                                            <div class="Para_ot_box">
                                                <div class="head_sec6_para">
                                                    <h2>
                                                        {{ $field->heading ?? '' }}
                                                    </h2>
                                                    <?php        print_r($field->description ?? ''); ?>

                                                    <div class="img_sec6_box">
                                                        <img class="sec6_inner_img" src="{{ asset('storage/' . $path) }}" alt="">
                                                    </div>
                                                    <?php        print_r($field->description2 ?? ''); ?>
                                                </div>

                                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

  

    <!---------------------------------------------------- FAQ section start------------------------------------>
    @if (isset($document->documentFaq) && $document->documentFaq->isNotEmpty())
        <section class="faq_sec p_120">
            <div class="help_last_sec">
                <div class="container">
                    <div class="help_main_faq">
                        <div class="help_faq">
                            <h2 class="b-dark">
                                Frequently Asked Questions
                            </h2>
                            <p>{{ $data2['document_faq_heading'] ?? '' }} {{ $document->title ?? '' }}</p>
                        </div>
                        <div class="accordion accordion-flush" id="accordionExample">

                            @foreach ($document->documentFaq as $faq)
                                <div class="accordion-item">
                                    <h6 class="accordion-header" id="heading{{ $loop->iteration ?? '' }}">
                                        <button class="{{ $loop->first ? 'accordion-button' : 'accordion-button collapsed' }}"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $loop->iteration ?? '' }}"
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                            aria-controls="collapse{{ $loop->iteration ?? '' }}">
                                            {{ $faq->question ?? '' }}
                                        </button>
                                    </h6>
                                    <div id="collapse{{ $loop->iteration ?? '' }}"
                                        class="{{ $loop->first ? 'accordion-collapse collapse show' : 'accordion-collapse collapse' }}"
                                        aria-labelledby="heading{{ $loop->iteration ?? '' }}" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            {{ strip_tags($faq->answer) ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif



   <!--------------------------------- section9 start ------------------------------------ -->

    <section class="sec9_outer_cont p_120">
        <section class="clientes_slider p_140 light pt-0">
            <x-review-section :reviews="$showReviews" :data="$data" />
        </section>
    </section>

    

    <!---------------------------------------------------- section8 start------------------------------------>
    <section class="sec8_cont_ot light p_120">
        <div class="inside_sec8_const">
            <div class="container">
             <div class="heading_sec_tabs">
                        <h2 class="doc_h">{{ $data2['related_heading'] ?? 'Related Documents' }}</h2>
                        <p class="doc_sub_heading">
                            {{ $data2['related_description'] ?? 'Explore similar documents popular among other users.' }}
                        </p>
                    </div>
                    <div class="grid-wrp-inr">
                <div class=" inner-row-grid">
                    @if (isset($document->relatedDocuments) && $document->relatedDocuments != null)
                        @foreach ($document->relatedDocuments as $related)
                                    <div class="docu-crd">
                                        <div class="inside_box_b" style="width: 100%; display: inline-block;">
                                            <div class="inside_box_tab">
                                                <a href="{{ url('document/' . $related->slug) }}" class="contract_link">
                                                    <div class="img_tab_sec">
                                                        <?php
                            $image_path = $related->document_image;
                                                                                                                                                                                                                                                                                ?>
                                                        <img src="{{ $image_path ?? '' }}" alt="">
                                                    </div>
                                                </a>
                                                <div class="cont_tab_ot">
                                                    <a href="{{ url('document/' . $related->slug) }}" class="contract_link">
                                                        <div class="tab_ot_text">

                                                            <div class="tab_text">
                                                                <h5 class=" size20">
                                                                    {{ $related->title ?? '' }}
                                                                </h5>
                                                                @php
                                                                    $avgRating = $document->getavgRating();
                                                                    $allDocumentRating = \App\Models\Document::getAllDocumentAvgRating();
                                                                @endphp

                                                                @if($avgRating !== false)
                                                                    <x-rating-component :rating="$avgRating" />
                                                                @else
                                                                    <x-rating-component :rating="$allDocumentRating" ratingClass="cont_li"
                                                                        ratingText="Legalio Average " :showDescription="true" />
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="tab_btn">
                                                    <a href="{{ url('document/' . $related->slug) }}" class="cta_blue" tabindex="-1">
                                                        Create
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                        @endforeach
                    @endif

                </div>
                </div>
            </div>
        </div>

    </section>

      <!---------------------------------------------------- section5 start------------------------------------>
    <section class=" sec4_conrt_ot for_new_bg_ot light size18 ">
        <div class="in_sec4_cont">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="sec4_conrt_h_ot for_new_bg">
                            <h2>{{ $data2['agreement_headline'] ?? '' }}</h2>
                            <p>{{ $data2['agreement_short_description'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="in_sec4_card_box p_120 pt-0">
            <div class="container">
                <div class="row">
                    @if (isset($agreements) && $agreements != null)
                        @foreach ($agreements as $agreement)
                                    <?php
                            $ag_path = getStorageFilepath($agreement->media?->file_path);
                                                                                                                                                                                                                                                            ?>
                                    <div class="col-lg-3 col-md-6  mb-2">
                                        <div class="card_sec4_conrt ">
                                            <div class="img_sec4">
                                                <img src="{{ asset('storage/' . $ag_path) }}" alt="">
                                            </div>
                                            <div class="sec4_card_p">
                                                <h6 class="size20">{{ $agreement->heading ?? '' }}</h6>
                                                <p class="sec4_card_para">
                                                    {{ $agreement->description ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>


    {{-- ================================================
         SINGLE MODAL INSTANCE — used by both desktop and mobile triggers
         ================================================ --}}
    <div class="modal fade review-modal-main" id="exampleModalCenter" tabindex="-1"
        aria-modal="true" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal_inner_bx">
            <div class="modal-content">
                <!-- Close Button -->
                <div class="close-btn-wrp">
                    <button type="button" class="close btn-close"
                        data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i></button>
                </div>

                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="modal-hd-lft">
                        <h5 class="modal-title color-blue" id="exampleModalCenterTitle">
                            {{ $document->title ?? '' }}
                        </h5>
                        @if ($showReviews->isNotEmpty())
                            <!-- Star Ratings -->
                            <div class="all_rating">
                                <ul class="star-rate-div d-flex p-0">
                                    <li class="drop_cont_li">
                                        <div class="select_ul">
                                            <div class="tab_ul">
                                                <div class="tab_star_li">
                                                    <span class="rating-on rate-1"></span>
                                                    <span class="rating-on rate-2"></span>
                                                    <span class="rating-on rate-3"></span>
                                                    <span class="rating-on rate-4"></span>
                                                    <span class="rating-on rate-5"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="cont_li">5.0</li>
                                    <span>|</span>
                                    <li class="opinion">
                                            {{ $_Reviews->count() }} opiniones
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="modal-rgt">
                        <!-- Open Review Modal Button -->
                        <button type="button" class="btn ad_rvw">
                            {{ $data2['open_review_modal_button_text'] ?? 'Escribir una opinión' }}
                        </button>
                    </div>
                </div>

                <!-- Modal Body - User Reviews -->
                <div class="modal-body">
                    <div class="scroll-div" id="all_rev">
                        @if ($showReviews->isNotEmpty())
                            <div class="user-review-wrp">
                                <div class="user-review-hd"  id="reviewContainer">
                                    @php $count = 0; @endphp
                                    @foreach ($showReviews as $review)
                                        <div class="body-cmt-sec d-flex ">
                                            <?php
                                            if ($review->user_id == null) {
                                                $first_name = $review->first_name;
                                                $last_name = $review->last_name;
                                                $initials = strtoupper(substr($first_name, 0, 1)) . strtoupper(substr($last_name, 0, 1));
                                            } 
                                            ?>
                                            <span class="person-profile">{{ $initials ?? '' }}</span>
                                            <div class="imtext">
                                                <h4 class="color-blue m-0">
                                                
                                                    <h4>{{ ($first_name ?? '') . ' ' . ($last_name ?? '') }}</h4>
                                                </h4>
                                                <p class="m-0 loaction">
                                                    <i class="fa-solid fa-location-dot"></i>{{ $review->city ?? $review->user->addresses->first()->city ?? '' }}
                                                </p>

                                                <!-- Star Ratings -->
                                                <div class="star_Av">
                                                    <div class="tab_ul cmt_star">
                                                        <div class="tab_star_li">
                                                            <span class="rating-on rate-1"></span>
                                                            <span class="rating-on rate-2"></span>
                                                            <span class="rating-on rate-3"></span>
                                                            <span class="rating-on rate-4"></span>
                                                            <span class="rating-on rate-5"></span>
                                                        </div>
                                                    </div>
                                                    <p class="ms-2">
                                                        {{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}
                                                    </p>
                                                </div>
                                                <p class="comnt_cnt m-0">
                                                    {{ $review->description ?? '' }}
                                                </p>
                                            </div>
                                        </div>

                                        @php $count++; @endphp
                                    @endforeach
                                </div>
                                {{-- @if ($count >= 10)
                                    <div class="user-review-btm">
                                        <button class="view-more-cta d-flex"><i
                                                class="fa-solid fa-chevron-down"></i>
                                            View
                                            More</button>
                                    </div>
                                @endif --}}
                                <button
                                    id="loadMoreBtn"
                                    data-document="{{ $document->id }}"
                                    class="view-more-cta d-flex">
                                    <i class="fa-solid fa-chevron-down"></i>
                                    View More
                                </button>
                                
                            </div>
                        @else
                            Comentarios no encontrados.
                        @endif
                    </div>

                    <div class="form-scroll-wrap" style="display:none;">
                        @if (auth()->check())
                            <div class="scroll-div">
                                <div class="write-review-form">
                                    <div class="write-profile">
                                        <form class="review-frm"
                                            action="{{ route('add.review', $document->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="sec-wrap">
                                                <div class="person-txt d-flex gp-12">
                                                    <?php
                                                    if (auth()->user()->public_name) {
                                                        $initials1 = strtoupper(substr(auth()->user()->public_name, 0, 1));
                                                    } else {
                                                        $initials1 = strtoupper(substr(auth()->user()->first_name, 0, 1)) . strtoupper(substr(auth()->user()->last_name, 0, 1));
                                                    }
                                                    ?>
                                                    <span class="person-profile">{{ $initials1 }}</span>

                                                    <div class="imtext">
                                                        <h4 class="color-blue d-flex edit-hd m-0">
                                                            <div class="rvw_username_div">
                                                                <div class="modal_public_name">
                                                                    @if (auth()->user()->public_name)
                                                                        <h4>{{ auth()->user()->public_name ?? '' }}</h4>
                                                                    @else
                                                                        <h4>
                                                                            {{ auth()->user()->first_name ?? '' }}
                                                                            {{ auth()->user()->last_name ?? '' }}
                                                                        </h4>
                                                                    @endif
                                                                </div>

                                                                <div class="name_fields" style="display:none;">
                                                                    <div class="col-lg-12">
                                                                        <div class="review-frm-inr">
                                                                            <input
                                                                                type="text"
                                                                                placeholder="{{ $data2['review_modal_nombre_publico_placeholder'] ?? 'Nombre público' }}"
                                                                                id="public_name"
                                                                                name="public_name">
                                                                        </div>
                                                                    </div>
                                                                    <span class="review_public_name_confirm"><i class="fa-solid fa-check"></i></span>
                                                                </div>

                                                                <div class="edit append_name_fields">
                                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                                </div>
                                                            </div>
                                                        </h4>
                                                        <div class="cmnt_loc_div">
                                                            <p class="m-0 loaction modal_location_name">
                                                                <i class="fa-solid fa-location-dot"></i>{{ auth()->user()->city ?? 'Monterrey N.L' }}
                                                            </p>
                                                            <p class="comnt_cnt m-0">
                                                                {{ $data2['review_modal_publicamente_text'] ?? 'Se mostrará públicamente' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="model-section">
                                                    <div class="star_Av d-flex align-items-center">
                                                        <div class="ratings review-modalrating">
                                                            <label for="rating1">
                                                                <i rate="1" class="star fa fa-star"></i>
                                                            </label>
                                                            <input type="checkbox" name="rating" id="rating1" class="chkbox" style="display:none;" value="1">
                                                            <label for="rating2">
                                                                <i rate="2" class="star fa fa-star"></i>
                                                            </label>
                                                            <input type="checkbox" name="rating" id="rating2" class="chkbox" style="display:none;" value="2">
                                                            <label for="rating3">
                                                                <i rate="3" class="star fa fa-star"></i>
                                                            </label>
                                                            <input type="checkbox" name="rating" id="rating3" class="chkbox" style="display:none;" value="3">
                                                            <label for="rating4">
                                                                <i rate="4" class="star fa fa-star"></i>
                                                            </label>
                                                            <input type="checkbox" name="rating" id="rating4" class="chkbox" style="display:none;" value="4">
                                                            <label for="rating5">
                                                                <i rate="5" class="star fa fa-star"></i>
                                                            </label>
                                                            <input type="checkbox" name="rating" id="rating5" class="chkbox" style="display:none;" value="5" checked>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="review-frm-inr review_field">
                                                                <textarea rows="4"
                                                                    placeholder="{{ $data2['review_modal_description_placeholder'] ?? 'Comparte tu opinión sobre este documento' }}"
                                                                    id="description"
                                                                    name="description"
                                                                    required="true"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-btns d-flex justify-content-end gp-12">
                                                <div class="cancel-btn">
                                                    <button class="cta-white" id="cancel_btn">Cancelar</button>
                                                </div>
                                                <div class="submit-btn">
                                                    <button class="cta-blue" type="submit">Publicar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            @php
                                $text =
                                    $data2['review_modal_not_login_message_text'] ??
                                    'Por favor,ingresa,a tu cuenta para opinar sobre este documento.';
                                $parts = explode(',', $text);
                            @endphp
                            <span>{{ $parts[0] ?? 'Por favor' }} <a
                                    href="{{ route('login.user', ['redirecturl' => url()->current()]) }}">{{ $parts[1] ?? 'ingresa' }}</a>
                                {{ $parts[2] ?? 'a tu cuenta para opinar sobre este documento.' }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- ================================================ END SINGLE MODAL ================================================ --}}


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- AddToAny Script -->
    <script async src="https://static.addtoany.com/menu/page.js"></script>

    <script>
        $('.chkbox').change(function () {
            $(".chkbox").prop('checked', false);
            $(this).prop('checked', true);
            val = $(this).val();
            $('.star').removeClass('rating-color');

            for (x = val; x > 0; x--) {
                $(`i[rate="${x}"]`).addClass('rating-color');
            }
        });
    </script>

    <script>
        $(document).ready(function () {
            $(".client-slider").slick({
                slidesToShow: 2,
                slidesToScroll: 1,
                arrows: true,
                infinite: true,
                autoplay: false,
                responsive: [{
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 2,
                    },
                },
                {
                    breakpoint: 767,
                    settings: {
                        slidesToShow: 1,
                    },
                },
                ],
            });

            $(".prev-btn").click(function () {
                $(".client-slider").slick("slickPrev");
            });

            $('.next-btn').on('click', function () {
                $('.client-slider').slick('slickNext');
            });

            $(".prev-btn").addClass("slick-disabled");
            $(".slick-list").on("afterChange", function () {
                if ($(".slick-prev").hasClass("slick-disabled")) {
                    $(".prev-btn").addClass("slick-disabled");
                } else {
                    $(".prev-btn").removeClass("slick-disabled");
                }
                if ($(".slick-next").hasClass("slick-disabled")) {
                    $(".next-btn").addClass("slick-disabled");
                } else {
                    $(".next-btn").removeClass("slick-disabled");
                }
            });
        })
    </script>


    <script>
        function isClose() {
            $('#all_rating').show();
            $('#all_rev').show();
            $('.modal-rgt').show();
            $('.form-scroll-wrap').css({
                display: "none"
            });

            $('.name_fields').hide();

            $('.form-scroll-wrap').removeClass('form-scroll-wrap-show');
        }

        $(document).ready(function () {
            // If modal is already closed on page load, reset sections
            if ($('#exampleModalCenter').css('display') == 'none') {
                isClose();
            }

            // Show the review form when "Escribir una opinión" is clicked
            $('.ad_rvw').click(function () {
                let is_login = "{{ auth()->check() ?? '' }}";

                $('.modal-rgt').hide();
                $('#all_rev').hide();

                if (!is_login) {
                    $('#all_rating').show();
                } else {
                    $('#all_rating').hide();
                }

                $('.form-scroll-wrap').css({
                    display: "block"
                }).addClass('form-scroll-wrap-show');
            });

            // Cancel button handler - restore to review list view
            $('#cancel_btn').click(function (e) {
                e.preventDefault();
                $('.modal_public_name').show();
                $('.append_name_fields').show();
                $('.modal_location_name').show();
                isClose();
            });

            // Toggle name fields visibility
            $('.append_name_fields').click(function () {
                $('.name_fields').toggle();
                $('.modal_public_name').toggle();
                $('.append_name_fields').hide();
                $('.modal_location_name').hide();
            });

            $('.close-btn-wrp').click(function () {
                $('.modal_public_name').show();
                $('.append_name_fields').show();
                $('.modal_location_name').show();
            })

            $('.review_public_name_confirm').click(function () {
                var publicName = $('#public_name').val().trim();

                if (publicName === '') {
                    alert('Please enter a public name.');
                    return;
                }

                $('.modal_public_name').html('<h4>' + publicName + '</h4>');
                $('.name_fields').hide();
                $('.modal_public_name').show();
                $('.append_name_fields').show();
                $('.modal_location_name').show();

            });

            // Reset modal view on close
            $('#exampleModalCenter').on('hidden.bs.modal', function () {
                isClose();
            });
        });
    </script>

    <script>
        $('.copy_link_icon').on('click', function (e) {
            e.preventDefault();

            const urlToCopy = "{{ url()->current() ?? '' }}";
            const tooltext = $(this).attr('title', 'Copied!');

            navigator.clipboard.writeText(urlToCopy).then(function () {
                console.log('Link copied')
            }).catch(function (error) {
                console.error('Copy failed:', error);
            });
        });
        document.querySelectorAll('.accordion-button').forEach(button => {
            button.addEventListener('click', function () {
                const target = document.querySelector(this.getAttribute('data-bs-target'));
                if (target.classList.contains('show')) {
                    const collapse = bootstrap.Collapse.getInstance(target);
                    collapse.hide(); // Manually collapse if already open
                }
            });
        });
    </script>

    <script>
        function triggerSharePopup() {
            document.querySelector('.a2a_dd').click();
        }

        $(document).ready(function () {
            $('.secure_content .blur-text-spn').on('cut copy paste', function (e) {
                e.preventDefault();
            });

            $('.secure_content').mousedown(function (e) {
                if (e.button == 2) {
                    e.preventDefault();
                }
            });

        });

        const stars = document.querySelectorAll('.ratings .star');

        stars.forEach(star => {
            star.addEventListener('mouseenter', function () {
                const currentRate = parseInt(this.getAttribute('rate'));

                stars.forEach(s => {
                    const rate = parseInt(s.getAttribute('rate'));
                    if (rate <= currentRate) {
                        s.classList.add('hover-active');
                    } else {
                        s.classList.remove('hover-active');
                    }
                });
            });
        });

        document.querySelector('.ratings').addEventListener('mouseleave', function () {
            stars.forEach(s => s.classList.remove('hover-active'));
        });
    </script>
    <script>
let offset = 10;

$('#loadMoreBtn').click(function() {

    $.ajax({
        url: "{{ route('load.more.reviews') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            document_id: $(this).data('document'),
            offset: offset
        },
        success: function(response) {

            if ($.trim(response) == '') {
                $('#loadMoreBtn').hide();
                return;
            }

            $('#reviewContainer').append(response);

            offset += 10;
        }
    });

});
</script>
@endsection