@extends('users_layout.master')
@section('title',$data['meta_title'])
@section('content')

@if(isset($data['background_image']) && $data['background_image'] != null)
     <section class="banner_sec dark inner-banner acerca" style="background-image: url({{ asset('storage/'.$data['background_image']) }});">
@else
     <section class="banner_sec dark inner-banner acerca" style="background-image: url({{ asset('assets/img/banner-img.png') }});">
@endif
     <div class="container banner-col-width">
          <div class="row align-items-center">
               <div class="col-md-6 banner-col">
                    <div class="banner_content">
                         <h1>{{ $data['banner_title'] ?? 'About Us' }}</h1>
                         <p>{{ $data['banner_description'] ?? 'Discover our mission, services, and how we can meet your legal needs.' }}</p>
                         {{-- <h1>{{ 'About Us' }}</h1> --}}
                         {{-- <p>{{ 'Discover our mission, services, and how we can meet your legal needs.' }}</p> --}}
                    </div>
               </div>
               {{-- <div class="col-md-6 banner-col">
                    <div class="banner_img">
                    @if(isset($data['banner_image']) && $data['banner_image'] != null)
                         <img src="{{ asset('storage/'.$data['banner_image']) }}" alt="">
                    @else
                         <img src="{{ asset('assets/img/somo.png') }}" alt="">
                    @endif
                    </div>
               </div> --}}
          </div>
     </div>
</section>

<!-- ********(Acerca_sec)*********** -->
<section class="acerca_sec p_110">
     <div class="container">
          <div class="row">
               <div class="col-lg-6">
                    <div class="acerca-img">
                    @if(isset($data['image']) && $data['image'] != null)
                         <img src="{{ asset('storage/'.$data['image']) }}" alt="about-legal-document">
                    @else
                         <img src="{{ asset('assets/img/abt1.png') }}" alt="about-legal-document">
                    @endif
                    </div>
               </div>,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
               <div class="col-lg-6">
                    <div class="acerca-txt">
                         <h2 class="b-dark">
                              {{ $data['heading'] ?? 'Acerca de los Documentos Legales' }}
                         </h2>
                         <?php print_r($data['description']); ?>
                    </div>
               </div>
          </div>
     </div>
</section>
<!-- ********(vision_sec)*********** -->
<section class="vision_sec">
     <div class="container">
          <div class="row sec-row p_120" style="background-color: #012555;">
          @if(isset($visions) && $visions != null)
          @foreach($visions as $vision)
          <?php
               $path = getStorageFilepath($vision->media->file_path);
          ?>
          <div class="{{ $loop->first ? 'col-lg-6 hv-brdr':'col-lg-6' }}">
               <div class="vision">
                    <div class="vision-img">
                         <img src="{{ asset('storage/'.$path) }}" alt="our_vision">
                    </div>
                    <div class="vision-txt" style="color: white;">
                         <h3>{{ $vision->heading ?? '' }}</h3>
                         <p class="">{{ $vision->description ?? '' }}</p>
                    </div>
               </div>
          </div>
          @endforeach
          @endif
          </div>
     </div>
</section>
<!-- ********( v-ofr_sec)*********** -->
<section class="acerca_sec p_110 v-ofr_sec">
     <div class="container">
          <div class="row">
               <div class="col-lg-6">
                    <div class="acerca-txt">
                         <h2 class="b-dark">
                              {{ $data['offer_heading'] ?? 'Lo que ofrecemos' }}
                         </h2>
                         <p class=" mb-2">{{ $data['offer_description'] ?? 'En documentos legales simplificamos el mundo legal para que usted pueda
                              concentrarse en el éxito. Únase a nosotros hoy y experimente una nueva era de creación de
                              documentos legales.' }}
                         </p>
                         <div class="ofrs">
                         @if(isset($offers) && $offers != null)
                         @foreach($offers as $offer)
                              <p>{{ $offer->offer_section_heading ?? '' }}
                              {{ $offer->offer_section_description ?? '' }}</p>
                         @endforeach
                         @endif
                         </div>
                    </div>
               </div>
               <div class="col-lg-6">
                    <div class="acerca-img">
                    @if(isset($data['offer_image']) && $data['offer_image'] != null)
                         <img src="{{ asset('storage/'.$data['offer_image']) }}" alt="about-legal-document">
                    @else
                         <img src="{{ asset('assets/img/abt2.png') }}" alt="about-legal-document">
                    @endif
                    </div>
               </div>
          </div>
     </div>
</section>
<!-- ********(clienbtes_slider)***********  -->

<section class="clientes_slider p_140 light cd" style="background: #F5F5F5;">
     {{-- <div class="container">
          <div class="row">
               <div class="col-md-4">
                    <div class="clientes_data size20 ">
                         <h2>{{ $data2['reviews_heading'] ?? '' }}</h2>
                         <p>{{ $data2['reviews_sub_heading'] ?? '' }}</p>
                    </div>
                    <div class="btn-wrap">
					<button class="prev-btn">
						<img src="{{ asset('assets/img/Vector1.png') }}" alt="">
					</button>
					<button class="next-btn">
						<img src="{{ asset('assets/img/Vector2.png') }}" alt="">
					</button>
				</div>
               </div>
               <div class="col-md-8">
                    <div class="client-slider">
                    @if(isset($reviews) && $reviews != null)
                    @foreach($reviews as $review)
                         <div class="control_box">
                              <div class="d-flex">
                                   <div class="slider-img">
                                        @if(isset($review->media->file_name) && $review->media->file_name != null)
                                             <img src="{{ asset('storage/'.$review->media->file_name) }}" alt="">
                                        @else
                                        <?php $initials = strtoupper(substr($review->first_name, 0, 1)) . strtoupper(substr($review->last_name, 0, 1)); ?>
                                        <span>{{ $initials ?? '' }}</span>
                                        @endif
                                   </div>
                                   @if($review->type == 'custom')
                                   <div class="txt_slider">
                                        <h6>{{ $review->first_name ?? '' }} {{ $review->last_name ?? '' }}</h6>
                                        <span>{{ $review->city ?? '' }}</span>
                                   </div>
                                   @endif
                              </div>
                              <div class="tab_star_li">
                                   <span class="rating-on rate-1" data-rating="1"></span>
                                   <span class="rating-on rate-2" data-rating="2"></span>
                                   <span class="rating-on rate-3" data-rating="3"></span>
                                   <span class="rating-on rate-4" data-rating="4"></span>
                                   <span class="rating-on rate-5" data-rating="5"></span>
                              </div>
                              <!-- @if(isset($review->rating) && $review->rating != null)
                              <div id="full-stars-example-two">
                                   <div class="ratings">
                                   @if($review->rating == 1)
                                        <label for="rating1">
                                             <i rate="1" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating1" class="chkbox" style="display:none;" value="1">
                                   @elseif($review->rating == 2)
                                        <label for="rating1">
                                             <i rate="1" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating1" class="chkbox" style="display:none;" value="1">
                                        <label for="rating2">
                                             <i rate="2" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating2" class="chkbox" style="display:none;" value="2">
                                   @elseif($review->rating == 3)
                                        <label for="rating1">
                                             <i rate="1" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating1" class="chkbox" style="display:none;" value="1">
                                        <label for="rating2">
                                             <i rate="2" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating2" class="chkbox" style="display:none;" value="2">
                                        <label for="rating3">
                                             <i rate="3" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating3" class="chkbox" style="display:none;" value="3">
                                   @elseif($review->rating == 4)
                                        <label for="rating1">
                                             <i rate="1" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating1" class="chkbox" style="display:none;" value="1">
                                        <label for="rating2">
                                             <i rate="2" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating2" class="chkbox" style="display:none;" value="2">
                                        <label for="rating3">
                                             <i rate="3" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating3" class="chkbox" style="display:none;" value="3">
                                        <label for="rating4">
                                             <i rate="4" class="star fa fa-star rating-color"></i>
                                        </label>
                                   @elseif($review->rating == 5)
                                        <label for="rating1">
                                             <i rate="1" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating1" class="chkbox" style="display:none;" value="1">
                                        <label for="rating2">
                                             <i rate="2" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating2" class="chkbox" style="display:none;" value="2">
                                        <label for="rating3">
                                             <i rate="3" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating3" class="chkbox" style="display:none;" value="3">
                                        <label for="rating4">
                                             <i rate="4" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating4" class="chkbox" style="display:none;" value="4">
                                        <label for="rating5">
                                             <i rate="5" class="star fa fa-star rating-color"></i>
                                        </label>
                                        <input name="rating" id="rating5" class="chkbox" style="display:none;" value="5" checked>
                                   @endif
                                   </div>
                              </div>
                              @endif -->
                              <p>“{{ $review->description ?? '' }}”</p>
                              <span>{{ $review->date ? \Carbon\Carbon::parse($review->date)->diffForHumans() : '' }}</span>
                         </div>
                    @endforeach
                    @endif
                    </div>
               </div>
          </div>
    </div> --}}
    <x-review-section :reviews="$reviews" :data="$data2" />
</section>

<script>
$(document).ready(function(){
	$(".client-slider").slick({
		slidesToShow: 2,
		slidesToScroll: 1,
		arrows: true,
		infinite: true,
		autoplay: false,
		responsive: [
			{
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

</script>


@endsection
