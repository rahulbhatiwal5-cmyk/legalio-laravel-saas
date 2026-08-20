@extends('users_layout.master')
@section('content')

@if(isset($data['background_image']) && $data['background_image'] != null)
    <section class="banner_sec dark inner-banner centro" style="background-image: url({{ asset('storage/'.$data['background_image'])  }});">
@else
    <section class="banner_sec dark inner-banner centro" style="background-image: url({{ asset('assets/img/banner-img.png')  }});">
@endif
        <div class="container banner-col-width">
            <div class="row align-items-center support-banner-row">
                <div class="col-md-6 banner-col">
                    <div class="banner_content">
                        {{-- <h1>Centro de Ayuda</h1> --}}
                        <h1>Help Center</h1>
                    </div>
                    <livewire:article-search>
                    {{-- <div class="search_bar">
                        <div class="wrap">
                            <div class="search">
                                <input type="text" class="searchTerm" placeholder="{{ $data['banner_placeholder'] ?? '¿Cómo podemos ayudarte?' }}">
                                <button type="submit" class="searchButton">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>
                        </div>
                    </div> --}}
                </div>
                <div class="col-md-6 banner-col">
                    <div class="banner_img">
                    @if(isset($data['banner_image']) && $data['banner_image'] != null)
                        <img src="{{ asset('storage/'.$data['banner_image']) }}" alt="help">
                    @else
                        <img src="{{ asset('assets/img/centro.png') }}" alt="help">
                    @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="como_sec p_120">

        <section class="Knowledge_page1">
            <div class="container">
                <div class="knolwdge_page_inr">

                    <div class="row">
                        <div class="col mb-5 d-flex flex-wrap gap-3 justify-content-center">
                            @foreach ($category as $cat)
                            @php
                                $isActive = request()->is('help/' . $cat->slug);
                            @endphp
                            <a href="{{ route('knowledgebase.category', ['category' => $cat->slug]) }}"
                                class="text-center text-decoration-none {{ $isActive ? 'text-white fw-bold' : 'text-dark' }}"
                                style="
                                border: {{ $isActive ? 'none' : '1px solid #464444' }};
                                border-radius: 50px;
                                padding: 10px 20px;
                                background-color: {{ $isActive ? '#007bff' : '#ffffff' }};
                                display: inline-block;
                                ">
                                {{ $cat->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- <div class="row">
                        <div class="col mb-5 d-flex flex-wrap gap-3 justify-content-center">
                             @foreach ($category as $cat)
                                
                                    <a type="button" href="{{ route('knowledgebase.category', ['category' => $cat->slug]) }}"
                                        class="btn btn-primary text-center {{ request()->is('centro-de-ayuda/' . $cat->slug) ? 'fw-bold' : '' }}">
                                        {{ $cat->name }}
                                    </a>
                                
                            @endforeach
                        </div>
                    </div> --}}

                    @if($article && $article->name)
                    <h2>{{ $article->name }}</h2>
                    @else
                    <h2>Category Not Found</h2>
                    @endif
                    
                    {{-- <div class="knwlge-lft"> --}}
                        <div class="row">
                            
                            {{-- <div class="col-lg-7"> --}}
                                <div class="knwlge-lft ">

                                    {{-- <div class="knwlge-hd">
                                        @if($article && $article->name)
                                        <h2>{{ $article->name }}</h2>
                                    @else
                                        <h2>Category Not Found</h2>
                                    @endif
                                    <img src="{{ asset('assets/img/Group 1.svg')}}" alt="image here">
                                    </div> --}}

                                    {{-- old --}}
                                    {{-- <div class="knwlge-cntnt">
                                        @if($article && $article->article->count())
                                            @foreach ($article->article as $art )
                                                <ul class="list-unstyled">
                                                    <li><a href="{{route('knowledgebase.article',['article' => $art->slug])}}"><i class="fa-solid fa-chevron-right"></i>{{$art->preview_title}}</a></li>
                                                </ul>
                                                <p style="
                                                text-align: justify;
                                            ">{{$art->preview_description}}</p>
                                            @endforeach
                                        @else
                                            <p>No articles found for this category.</p>
                                        @endif
                                    </div> --}}

                                    {{-- new --}}
                                    <div class="knwlge-cntnt">
                                        @if($article && $article->article->count())
                                            @foreach ($article->article as $art )
                                                        <a class="fs-5 fw-bold" href="{{route('knowledgebase.article',['article' => $art->slug])}}">
                                                            {{$art->preview_title}}
                                                        </a>
                                                <p style="text-align: justify;">{{$art->preview_description}}</p>
                                            @endforeach
                                        @else
                                            <p>No articles found for this category.</p>
                                        @endif
                                    </div>


                                </div>
                            {{-- </div> --}}
                            {{-- <div class="col-lg-5">
                                <div class="knwlge-rgt">
                                    <div class="knwlge-hd">
                                        <h2>
                                            Categoría de la base de conocimientos.</h2>
                                    </div>
                                    <div class="knwlge-cntnt knwlge-rgt-cntnt">
                                        <ul class="list-unstyled">
                                            @foreach ($category as $cat)
                                                <li>
                                                    <a href="{{ route('knowledgebase.category', ['category' => $cat->slug]) }}"
                                                       class="{{ request()->is('centro-de-ayuda/' . $cat->slug) ? 'fw-bold' : '' }}">
                                                        {{ $cat->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
    
                                    </div>
    
                                </div>
                            </div> --}}
                        </div>
                    {{-- </div> --}} 
                </div>
            </div>
        </section>






    </section>

    <section class="generate-sec p_100 Comienza_sec">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-5">
                    <div class="Comienza-img">
                    @if(isset($data['bottom_banner_image']) && $data['bottom_banner_image'] != null)
                        <img src="{{ asset('storage/'.$data['bottom_banner_image'] )}}" alt="image here">
                    @else
                        <img src="{{ asset('assets/img/Comienza-img.png')}}" alt="image here">
                    @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="Comienza-content">
                        <h2 class="b-dark">{{ $data['banner_heading'] ?? 'Genera tus documentos legales de forma rápida y sencilla' }}</h2>
                        <p class="">
                            {{ $data['banner_description'] ?? ' Nuestro sistema intuitivo te guía paso a paso para crear documentos legales personalizados.
                            Descárgalos al instante en los formatos PDF y DOCX (Word) y tenlos listos en cuestión de
                            minutos.' }}
                        </p>
                        <a href="#" class="cta_org">{{ $data['button_text'] ?? '' }}<i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="faq_sec p_120">
        <div class="help_last_sec">
            <div class="container">
                <div class="help_main_faq">
                    <div class="help_faq">
                        <h2 class="b-dark">
                            {{ $data['faq_heading'] ?? 'Frequently Asked Questions' }}
                        </h2>
                        <p>{{ $data['faq_description'] ?? '' }}</p>
                    </div>
                    <div class="accordion accordion-flush" id="accordionExample">
                        @if(isset($faqs) && $faqs != null)
                        @foreach($faqs as $faq)
                        <div class="accordion-item">
                            <h6 class="accordion-header" id="heading{{ $loop->iteration ?? '' }}">
                                <button class="{{ $loop->first ? 'accordion-button':'accordion-button collapsed' }}" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $loop->iteration ?? '' }}" aria-expanded="{{ $loop->first ? 'true':'false' }}" aria-controls="collapse{{ $loop->iteration ?? '' }}">
                                    {{ $faq->question ?? '' }}
                                </button>
                            </h6>
                            <div id="collapse{{ $loop->iteration ?? '' }}" class="{{ $loop->first ? 'accordion-collapse collapse show':'accordion-collapse collapse' }}" aria-labelledby="heading{{ $loop->iteration ?? '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <?php
                                        $answer = strip_tags($faq->answer);
                                        print_r($answer);
                                    ?>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                        <div class="faq-view-more">
                            {{-- <a href="{{ url('/preguntas-frecuentes') }}" class="cta_org">Ver más</a> --}}
                            <a href="{{ url('/faq') }}" class="cta_org">Ver más</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>




@endsection
