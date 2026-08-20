@extends('users_layout.master')
<style>
    .how-it-works-steps {
        background: #fff;
    }

    .hiw-step-title {
        font-size: 26px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 14px;
        line-height: 1.25;
    }

    .hiw-step-desc {
        font-size: 15px;
        color: #555;
        line-height: 1.75;
        margin-bottom: 24px;
    }

    .hiw-testimonial {
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        padding: 20px 24px;
    }

    .hiw-testimonial-text {
        font-size: 14px;
        color: #555;
        line-height: 1.7;
        font-style: italic;
        margin-bottom: 16px;
    }

    .hiw-author {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .hiw-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #002655;
        color: #FFF;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .hiw-avatar--blue {
        background: #fd5602;
        color: #fff;
    }

    .hiw-author-name {
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0;
    }

    .hiw-author-role {
        font-size: 12px;
        color: #999;
        margin: 0;
    }

    .testimonials-image {
        background: #eef0f5;
        height: 300px;
        width: 70%;
        margin-left: 50px;
        margin-top: 31px;
    }

    @media (max-width: 767px) {

        .hiw-floating-doc,
        .hiw-checklist-card {
            display: none;
        }

        .hiw-step-title {
            font-size: 20px;
        }
    }

</style>
@section('title',$data['meta_title'])
@section('content')

@if(isset($data['background_image']) && $data['background_image'] != null)
{{-- <section class="banner_sec dark inner-banner" style="background-image: url('{{ asset('storage/'.$data['background_image'] ?? '' ) }}');"> --}}
@endif
<div class="container banner-col-width">
    <div class="row align-items-center">
        {{-- <div class="col-md-6 banner-col">
            <div class="banner_content">
                <h1>{{ $data['banner_title'] ?? '' }}</h1>
                <p>
                    {{ $data['banner_description'] ?? '' }}
                </p>
            </div>
        </div> --}}
        {{-- <div class="col-md-6 banner-col">
            <img src="{{ asset('storage/'.$data['banner_image'] ?? '' ) }}" alt="Así funciona">
        </div> --}}
    </div>
</div>
</section>
<section class="explore_sec p_120">
    <div class="container" style="margin-top: 70px;">
        <div class="text-center">
            <h2>{{ $data['main_heading'] ?? '' }}</h2>
            <p>{{ $data['short_description'] ?? '' }}</p>
        </div>
        <div class="row">
            @if(isset($works) && $works->isNotEmpty())
            @foreach($works as $work)
            <?php
                $path = getStorageFilepath($work->media->file_path);
            ?>
            <div class="col-md-4">
                <div class="explore-cntnt">
                    <div class="explore-img">
                        <img src="{{ asset('storage/'.$path ?? '' ) }}" alt="explore">
                    </div>
                    <div class="explore-txt">
                        <h5 class="b-dark">{{ $work->heading ?? '' }}</h5>
                        <p class="">
                            {{ $work->description ?? '' }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</section>

{{-- ===== HOW IT WORKS STEPS SECTION ===== --}}
<section class="how-it-works-steps p_100">
    <div class="container">

        <div class="hiw-row row align-items-center mb-5 pb-5">
            <div class="col-md-6 hiw-text-col">
                <div class="hiw-accent-line"></div>
                {{-- <h3 class="hiw-step-title">{{ $works[0]->heading ?? '1. Find the document you need' }}</h3>
                <p class="hiw-step-desc">{{ $works[0]->description ?? '' }}</p> --}}

                <h3 class="hiw-step-title">Explore our variety of documents</h3>
                <p class="hiw-step-desc">Find the legal document you need in minutes. From lease agreements and employment contracts to business and personal legal forms, our platform offers a comprehensive library designed to cover everyday legal needs.
                    Each document is carefully structured to meet current legal standards, while remaining simple and easy to customize.</p>
                <div class="hiw-testimonial">
                    <p class="hiw-testimonial-text">“Legalio has been an incredibly helpful tool. It simplifies complex legal processes and makes document creation accessible, especially for those just getting started."</p>
                    <div class="hiw-author">
                        <div class="hiw-avatar">MR</div>
                        <div>
                            <p class="hiw-author-name">Teejnam Anar.</p>
                            <p class="hiw-author-role">LawDepot Member</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="testimonials-image">
                    <img src="" alt="image here">
                </div>
            </div>
        </div>

        {{-- Step 2 --}}
        <div class="hiw-row row align-items-center flex-md-row-reverse">
            <div class="col-md-6 hiw-text-col">
                <div class="hiw-accent-line"></div>
                {{-- <h3 class="hiw-step-title">{{ $works[1]->heading ?? '2. Create, customize, and save' }}</h3>
                <p class="hiw-step-desc">{{ $works[1]->description ?? '' }}</p> --}}


                <h3 class="hiw-step-title">Personalize your document step by step</h3>
                <p class="hiw-step-desc">Creating legal documents shouldn’t be complicated. Our guided questionnaire walks you through each step, asking simple questions to ensure all important details are included.
                    As you progress, your answers are instantly applied to your document, resulting in a clear, accurate, and personalized final version. Whether for personal or business use, Legalio helps you create professional documents with confidence and ease.</p>
                <div class="hiw-testimonial">
                    <p class="hiw-testimonial-text">“The platform makes it easy to create precise and professional documents. The guided steps are clear, and the final results are exactly what I needed.”</p>
                    <div class="hiw-author">
                        <div class="hiw-avatar hiw-avatar--blue">PB</div>
                        <div>
                            <p class="hiw-author-name">Ecnirp ttahb</p>
                            <p class="hiw-author-role">LawDepot Member</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="testimonials-image">
                    <img src="" alt="image here">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="generate-sec p_100 Comienza_sec" style="background: #F5F5F5;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="asi_img_div">
                    <img src="{{ asset('storage/'.$data['second_banner_img'] ?? '' ) }}" alt="image here">
                </div>
            </div>
            <div class="col-md-6">
                <div class="asi-right">
                    <h2 class="b-dark">{{ $data['second_banner_heading'] ?? '' }}</h2>
                    <p class="">
                        {{ $data['second_banner_sub_heading'] ?? '' }}
                    </p>
                    <div class="asi_btn Comienza-btn ">
                        <a href="{{ route('user.all__documents') }}" class="cta_org">{{ $data['button_label'] ?? '' }}<i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
