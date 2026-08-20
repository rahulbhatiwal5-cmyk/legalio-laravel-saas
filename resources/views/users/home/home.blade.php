{{-- @php $hideHeader = true; @endphp --}}
@extends('users_layout.master')
<style>
.categories-updt {
    display: flex;
    overflow-x: auto;
    gap: 15px;
    scroll-behavior: smooth;
    padding-bottom: 10px;
    cursor: grab;
}

/* Scrollbar */
.categories-updt::-webkit-scrollbar {
    height: 4px;
}
.categories-updt::-webkit-scrollbar-thumb {
    background: #fd5602;
    border-radius: 10px;
}

/* FIX: 4 cards visible */
.category-item {
    flex: 0 0 calc(25% - 12px);
}

/* Responsive */
@media (max-width: 992px) {
    .category-item {
        flex: 0 0 calc(33.33% - 10px);
    }
}

@media (max-width: 768px) {
    .category-item {
        flex: 0 0 calc(70% - 10px);
    }
}
</style>
@section('title',$data['meta_title'])

@section('content')
{{-- @include('users_layout.categories_header') --}}

<?php use Illuminate\Support\Str; ?>

<section class="banner_sec dark" style="background-image: url({{ asset('storage/'.$data['background_image'] ?? '' ) }});">
	<div class="container banner-col-width">
		<div class="row align-items-center home-banner-row">
			<div class="col-md-6 banner-col">
				<div class="banner_content">
					<h1>{{ $data['banner_title'] ?? '' }}</h1> 
				</div>
                   	<livewire:document-search>

			</div>
			<div class="col-md-6 banner-col">
				<div class="banner_img">
					<img src="{{ asset('storage/'.$data['banner_image'] ?? '' ) }}" alt="">
				</div>
			</div>
		</div>
	</div>
</section>
    <!------------------------------ tabs section start  ------------------------------------ -->

<section class=" tab_sec_ot p_120 popul-docu-spc">
	<div class="container">
		<div class="row">
			<div class="heading_sec_tabs">
				<h2 class="doc_h">{{ $data['most_popular_title'] ?? '' }}</h2>
			</div>
		</div>
	</div>	
	<div class="container ">
		<div class="wrapper">
			<div class="tabContentWrap">
			@foreach($popular_categories ?? [] as $category)
				<div class="tabContent tab_box_sec {{ $loop->first ? 'show' : 'tab_btn'.$loop->iteration }}">
					<div class="sldr-to-bar-wrp">
						@foreach($category->documents->take(7) as $document)
						@php
							$isLast = $loop->last && $category->documents->count() > 1;
							$is_show = !$isLast;

							if($isLast){
								$btn_txt   = $data['most_popular_ryt_doc_text'] ?? 'See All';
								$route     = route('user.all_categories',['slug'=>$category->slug]);
								$image     = asset('assets/static/doc_ryt_img.svg');
								$title     = null; 
							}else{
								$btn_txt   = $data['most_popular_btn_text'] ?? 'View';
								$route     = route('get.document',['slug'=>$document->slug]);
								$image     = $document->document_image;
								$title     = $document->title;
							}
						@endphp

						<div class="inside_box_b">
							<div class="inside_box_tab">
								<a href="{{ $route }}" class="contract_link">
									<div class="img_tab_sec">
										<img src="{{ $image }}" alt="">
									</div>
								</a>

								<div class="cont_tab_ot">
									<a href="{{ $route }}" class="contract_link">
										@if($is_show)
										<div class="tab_text">
											<h5 class="size20">{{ $title }}</h5>
											@if(($avgRating = $document->getavgRating()) !== false)
												<x-rating-component :rating="$avgRating" />
											@else
												<x-rating-component :rating="5" />
											@endif
										</div>
										@endif
									</a>
									<div class="tab_btn">
										<a href="{{ $route }}" class="cta_org">{{ $btn_txt }}</a>
									</div>
								</div>
							</div>
						</div>
						@endforeach
					</div>
				</div>
			@endforeach
			</div>
		</div>
	</div>
</section>

    <!------------------------------ tabs section end  ------------------------------------ -->

<section class="Comienza_sec dark">
	<div class="container">
		<div class="Comienza_bg" style="background-color: #002655;">
			<div class="row align-items-center">
				<div class="col-md-6">
					<div class="Comienza-img">
						<img src="{{ asset('storage/'.$data['bottom_banner_image'] ?? '' ) }}" alt="">
					</div>
				</div>
				<div class="col-md-6">
					<div class="Comienza-content">
						<h2>{{ $data['bottom_heading'] ?? '' }}</h2>
						<p>{{ $data['bottom_subheading'] ?? '' }}</p>
						<div class="Comienza-btn">
							{{-- <a href="{{ $data['bottom_button_link'] ?? '' }}" class="cta_org">{{ $data['bottom_button_label'] ?? '' }} <i class="fa-solid fa-arrow-right-long"></i></a> --}}
							<a href="{{route('user.all__documents') }}" class="cta_org">{{ $data['bottom_button_label'] ?? '' }} <i class="fa-solid fa-arrow-right-long"></i></a>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

    <!---------------- catagors section start ------------------------------- -->

<section class="outer_cate p_120 light">
	<div class="in_cate">
		<div class="head_cata">
			<div class="container">
				<div class="cata_h">
					<h2>
						{{ $data['category_title'] ?? '' }}
					</h2>
				</div>
			</div>
		</div>
		<div class="container">
			{{-- <div class="categories-updt">
				@if(isset($home_category) && $home_category != null)
				@foreach($home_category as $category)
					<?php
						$path = getStorageFilepath($category->media->file_path);
					?>
					<div class="col-lg-3">
						<x-image-category :category="$category" :path="$path" />
					</div>
				@endforeach
				@endif
		</div> --}}
		<div class="categories-updt">
    @if(isset($home_category) && $home_category != null)
        @foreach($home_category as $category)
			<?php
			$path = null;
			if (!empty($category->media) && !empty($category->media->file_path)) {
				$path = getStorageFilepath($category->media->file_path);
			}
            // $path = getStorageFilepath($category->media->file_path);
		    ?>
			
            <div class="category-item">
                <x-image-category :category="$category" :path="$path ?? ''" />
            </div>
        @endforeach
    @endif
</div>
	</div>
</section>

    <!---------------- catagors section end ------------------------------- -->
    <!----------------- card_section start ------------------------>

<section class="card_sec_out">
	<div class="in_card_bg p_120">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="card_ot_lyr">
						<div class="card_h">
						<h2 style="text-align:center;">Create an account</h2>
							<h3>{{ $data['join_us_text'] ?? '' }}</h3>

						</div>
						<div class="ot_plat dark">
							<div class="othr_platform">
								<a class="google" href="{{ route('login.google') }}"><i class="fa-brands fa-google"></i>{{ $data['home_text_register'] ?? 'Regístrese con' }} <span
									class="span1">{{ $data['home_text_google'] ?? 'Google' }}</span> </a>
							</div>
							{{-- <div class="othr_platform">
								<a class=" facebook" href=""><i class="fa-brands fa-facebook"></i>{{ $data['home_text_register'] ?? 'Regístrese con' }}
								<span class="span1"> {{ $data['home_text_facebook'] ?? 'Facebook' }} </span>
								</a>
							</div> --}}
							<div class="othr_platform">
								<a class=" email" href="{{route('register')}}"><i class="fa-regular fa-envelope"></i>{{ $data['home_text_register'] ?? 'Regístrese con' }} <span
									class="span1"> {{ $data['home_text_email'] ?? 'Email' }}</span> </a>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

</section>
    <!----------------- card_section end ------------------------>

<section class="clientes_slider p_140 light">

	<x-review-section :reviews="$reviews" :data="$data" />
</section>

<script>

	$(document).ready(function(){
		$(".client-slider").slick({
			slidesToShow: 2,
			slidesToScroll: 1,
			arrows: true,
			infinite: true,
            	dots: false,
			autoplay: false,
			responsive: [

				{
					breakpoint: 1024,
					settings: {
						slidesToShow: 1,
					},
				}

			],
		});

		$(".prev-btn").click(function () {
			$(".client-slider").slick("slickPrev");
		});

		$('.next-btn').on('click', function() {
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

	$(function () {
		// vars
		var slider,
		btn,
		tabC,
		prevIndex,
		objTab = {};

		btn = $(".home_tab_btns");
		tabC = $(".tabContent");

		prevIndex = 0;

		btn.on("click", function (e) {
			var th, thIndex;
			// Current button and the index of the current button
			th = $(this);
			thIndex = th.index();
			if(!th.hasClass("active")) {
				if(prevIndex != thIndex && prevIndex !== "undefined"){
				btn.eq(prevIndex).removeClass("active");
				tabC.eq(prevIndex).removeClass("show");
				}
				btn.eq(thIndex).addClass("active");
				tabC.eq(thIndex).addClass("show");
				prevIndex = thIndex;
				tabC.eq(thIndex).find(".slider").slick("setPosition");
			}
		});
		slider = $(".slider");
		slider.slick({
			arrows: false,
			slidesToShow: 5,
            dots: true,
			slidesToScroll: 1,
            infinite: false,
			responsive: [
				{
				breakpoint: 991,
				settings: {
					slidesToShow: 3,
				},
				},
				{
				breakpoint: 767,
				settings: {
					slidesToShow: 2,
				},
				},
			],
		});
	});
</script>
<script>
const slider = document.querySelector('.categories-updt');

let isDown = false;
let startX;
let scrollLeft;

slider.addEventListener('mousedown', (e) => {
    isDown = true;
    slider.classList.add('active');
    startX = e.pageX - slider.offsetLeft;
    scrollLeft = slider.scrollLeft;
});

slider.addEventListener('mouseleave', () => {
    isDown = false;
});

slider.addEventListener('mouseup', () => {
    isDown = false;
});

slider.addEventListener('mousemove', (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - slider.offsetLeft;
    const walk = (x - startX) * 2; // speed
    slider.scrollLeft = scrollLeft - walk;
});
</script>
@endsection

