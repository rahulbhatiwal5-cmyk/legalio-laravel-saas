<!DOCTYPE html>
<html lang="en">

<head>
     @livewireStyles


     {!! getMetadata('begin_of_head') !!}
     <meta charset="UTF-8" />

     <link rel="icon" type="image/png" href="{{ asset('assets/img/Favicon-legalio/favIcon2.png') }}">
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css"
          integrity="sha512-6lLUdeQ5uheMFbWm3CP271l14RsX1xtx+J5x2yeIDkkiBpeVTNhTqijME7GgRKKi6hCqovwCoBTlRBEC20M8Mg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css"
          integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
     <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@icon/dashicons@0.9.0-alpha.4/dashicons.min.css"> -->
     <link data-minify="1" rel='stylesheet' id='dashicons-css' href='https://documentos-legales.mx/wp-content/cache/min/1/wp-includes/css/dashicons.min.css?ver=1729539507' media='all' />
     <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.7.1/slick.min.js">
     <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/css/iziToast.min.css">

     <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
     <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}">

     <!-- <link rel="stylesheet" href="{{ asset('assets/css/custom.css?fdgvfg') }}"> -->
     <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}?v={{ time() }}">

     <script src="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/js/iziToast.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
          integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
          crossorigin="anonymous" referrerpolicy="no-referrer"></script>

		 <title>@yield('title')</title>
     {!! getMetadata('end_of_head') !!}


</head>


<body>

    @php
$keys = [
'header_logo',
'header_btn_1',
'header_btn_2',
'favicon',
'header_blue_logo',
];

$results = App\Models\MetaData::whereIn('key', $keys)->get()->keyBy('key');
$data = [
'header_logo' => str_replace('public/', '', $results['header_logo']->file_path ?? null),
'button1' => $results['header_btn_1']->value ?? null,
'button2' => $results['header_btn_2']->value ?? null,
'favicon' => str_replace('public/', '', $results['favicon']->file_path ?? null),
'header_blue_logo' => str_replace('public/', '', $results['header_blue_logo']->file_path ?? null),
];
// dd($data);
$current_url = url()->current();
$home_url = url('/');

$keys2 = [
'banner_placeholder',
'button_name'
];

$results2 = App\Models\HomeContent::whereIn('key', $keys2)->get()->keyBy('key');

$data2 = [
'banner_placeholder' => $results2['banner_placeholder']->value ?? null,
'button_name' => $results2['button_name']->value ?? null,
];

@endphp


<header class="inner-header fun-header custom-header contract-hdr-brdr @yield('headerClass')">
	<!-- profile at tab -->
	<div class="logged_out_profile profile_tab_show">
		<div class="logged_out_img">
			<img src="{{ optional(auth()->user())->profile_image ?? dimage() }}" class="img-fluid">
		</div>
		<div class="profile-drpdwn">
			<div class="UserFrontOverlay"></div>
			<div class="profile-drpdwn-innr">
				<div class="drpdwn-hd">
					<div class="logo">
						<a href="{{ url('/') }}">
							<img src="{{ asset('storage/'.$data['header_logo']) ?? '' }}" alt="">
						</a>
					</div>
					<div class="cross-icon">
						<img src="{{ asset('assets/images/cross_icon.svg') }}" alt="" srcset="">
					</div>
				</div>
				<div class="profile_mid">
					<h2>Get Started!</h2>
					<p>Create an Account and Secure Your Exclusive Logo Today. </p>
				</div>
				<div class="drpdwn-cntnt">
					<div class="hedaer_bnt">
						<div class="all_doc_btn">
							<a href="{{ route('user.all__documents') }}"
								class="cta_dark">{{ $data['button1'] ?? 'Crear documento' }}</a>
						</div>
						@if(auth()->user())
						<!-- <a href="{{ route('logout') }}" class="cta_light">Cerrar sesión</a> -->
						<div class="hdr_ryt">
							<div class="hdr_info">
								<div class="notf drop_menu">
									<a class="notfictn_lnk">
										<img src="{{ asset('assets/img/bell_img.png') }}" class="img-fluid">
										<span class="badge custom-badge badge-success">6</span>
									</a>
									<div class="dropdown-menu dropdown-menu-right" style="margin-right: 20px;">
										<div class="dropdown-main">
											<div class="user_detail">
												<h5>Notificación</h5>
											</div>
											{{-- <div class="dash-icon">
												<a class="dropdown-item" href="{{route('user.dashboard')}}"><i class="fa fa-user"></i>Panel</a>
											</div> --}}
										</div>
									</div>
								</div>

								<div class="user_img drop_menu">
									<div class="usr_profile">
										<img src="{{ optional(auth()->user())->profile_image ?? dimage() }}" class="img-fluid">
									</div>
									<div class="dropdown-menu dropdown-menu-right" style="margin-right: 20px;">
										<div class="dropdown-main">

											<div class="user_detail">

												<div class="user_img">
													{{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
												</div>
												<a href="{{route('user.profile')}}">
													<div class="user_name">
														<h5>{{Auth::user()->first_name}}</h5>
														<p>{{Auth::user()->email}}</p>
													</div>
												</a>
											</div>

											<div class="dash-icon">
												<a class="dropdown-item" href="{{route('user.profile')}}"><i class="fa fa-user"></i>Mi perfil</a>
											</div>

											<div class="dash-icon">
												<a class="dropdown-item" href="{{route('user.dashboard')}}"><i class="fa fa-user"></i>Panel</a>
											</div>

											<div class="dash-icon">
												<a class="dropdown-item" href="{{route('user.configuration')}}"><i class="fa fa-cog"></i>Configuración</a>
											</div>
											<div class="dash-icon">
											<a class="dropdown-item" href="{{ route('subscription.details') }}">
												<i class="fa-solid fa-credit-card"></i>
													Subscription
												</a>
											</div>

											<div class="dash-icon">
												<a class="dropdown-item" href="{{route('user.invoice')}}"><i class="fa-solid fa-envelope-open-text"></i>Facturas</a>
											</div>

											<div class="dash-icon">
												<a class="dropdown-item" href="#"><i class="fa-solid fa-headset"></i>Boletos de soporte</a>
											</div>

											<div class="dash-icon">
												<a class="dropdown-item" href="{{ url('/logout') }}"><i class="fa fa-power-off"></i>Finalizar la sesión</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						@else
						<a href="{{ route('login.user') }}" class="cta_light">{{ $data['button2'] ?? 'Iniciar sesión' }}</a>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- profile at tab ends 123-->
	<div class="main_header">
		<div class="container-fluid">
			<div class="srch-hdr contract-white-hdr @if(auth()->check())user-icon @endif">
				<div class="hedaer_logo">
					{{-- <a href="{{ auth()->check() ? url('/legal-documents') : url('/') }}">
						<img src="{{ asset('storage/' . $data['header_logo']) }}" alt="">
					</a> --}}
			    	<a href="{{ auth()->check() ? url('/legal-documents') : url('/') }}">
						<img src="{{ asset('storage/' . $data['header_blue_logo']) }}">
					</a>
				</div>
				@if($current_url == $home_url)
				{{-- <div class="header_search_bar">
					<div class="search_bar">
						<div class="wrap" id="myID" style="display:none;">
							<div class="search">
								<input type="text" class="searchTerm"
									placeholder="{{ $data2['banner_placeholder'] ?? '¿Qué documento necesitas?' }}">
								<button class="btn cta_dark"><i class="fa-solid fa-magnifying-glass"></i></button>
							</div>
						</div>
					</div>
				</div> --}}
				<livewire:header-search-box>
				@else

				@endif
				


			<div class='contract-hdr-rgt-sd'>
				<div class="hedaer_bnt">
					<div class="right_menu">
							<ul>
								<li>
									<a href="{{ route('user.how_it_works') }}">
										
										How It Works
									</a>
								</li>
								<li>
									<a href="{{ route('user.faq') }}">
										FAQs
									</a>
								</li>
							</ul>
					</div>

					@if(auth()->user())

					<div class="right_menu">
                        <ul>
                            <li>
                                {{-- <a href="{{ route('user.how_it_works') }}"> --}}
									 {{-- Así funciona --}}
									{{-- How It Works --}}
								{{-- </a> --}}
                            </li>
                            <li>
                                {{-- <a href="{{ route('help.center') }}">
									Help
								</a> --}}
                            </li>
                        </ul>
                </div>
					@else
					<a href="{{ route('login.user') }}" class="cta_light">{{ $data['button2'] ?? 'Iniciar sesión' }}</a>
					@endif


				</div>
{{-- 
				<div id="dynamic_menu">
					<div class="right_menu">
							<ul>
								<li>
									<a href="{{ route('user.how_it_works') }}">
										
										How It Works
									</a>
								</li>
								<li>
									<a href="{{ route('user.faq') }}">
										FAQs
									</a>
								</li>
							</ul>
					</div>
				</div> --}}
			</div>



			</div>
		</div>
	</div>
	</div>
	<div class="top_header dark">
		<div class="container-fluid">
			<nav class="navbar navbar-expand-lg">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse"
					data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
					aria-expanded="false" aria-label="Toggle navigation" id="navbarSupportedContentBtn">
					<div class="bar bar1"></div>
					<div class="bar bar2"></div>
					<div class="bar bar3"></div>
				</button>

				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<div class="UserFrontOverlay"></div>
					<div class="navbar-innr-div left-sidebr-cato">
						<div class="logo">
							<a class='desk-log' href="{{ url('/') }}">
								<img src="{{ asset('storage/' . $data['header_logo']) }}" alt="">
							</a>

							<a class='mob-log' href="{{ url('/') }}">
								{{-- <img src="{{ asset('storage/' . $data['mobile_header_logo']) }}" alt="">	 --}}
								
								 {{-- <img src="{{ asset('storage/logos/' . $data['mobile_header_logo']) }}" alt=""> --}}
							</a>
						</div>
						<div class="left_menu">
						<h3>Categories</h3>
							<ul class="menu">
								{{-- @foreach($header_popular_categories as $navCategory)
									<li class="menu-item">
										<a href="{{ url('/' . $navCategory->slug) }}">
											<span class="dropdown_tittle">{{ $navCategory->name }}</span>
										</a>
									</li>
								@endforeach --}}
								@foreach($header_popular_categories as $navCategory)
								<li class="menu-item">
									<a href="{{ url('/' . $navCategory->slug) }}"
									class="{{ request()->is($navCategory->slug) ? 'active-category' : '' }}">
										<span class="dropdown_tittle">{{ $navCategory->name }}</span>
									</a>
								</li>
								@endforeach
							</ul>

							
						</div>
						<div class="rgt_menu_info right_menu">
                           <h3>Information</h3>
							<ul class="rgt-menu">							

								<li>
								<a href="{{ url('/how-it-works') }}"
								class="{{ request()->is('how-it-works') ? 'how-it-work-UI' : '' }}">
									{{ $data['asi_funciona_header'] ?? 'Así funciona' }}
								</a>
								</li>
							 <li><a href="{{ url('/faq') }}"
								class="{{ request()->is('faq') ? 'faq-UI' : '' }}">
									{{ $data['preguntas_frecuentes_footer'] ?? 'FAQs' }}
								</a></li>
							 {{-- <li><a href="{{ url('/how-it-works') }}"
								class="{{ request()->is('how-it-works') ? 'how-it-work-UI' : '' }}">
									{{ $data['asi_funciona_header'] ?? 'Así funciona' }}
								</a></li> --}}
							</ul>

							<ul class="desk-pages-menu">
								{{-- <li>
									<a href="{{ url('/asi-funciona') }}">{{ $data['asi_funciona_header'] ?? 'Así funciona' }} </a>
									<a href="{{ url('/how-it-works') }}" class="how-it-work-UI">{{ $data['asi_funciona_header'] ?? 'Así funciona' }} </a>
								</li> --}}
								<li>
								<a href="{{ url('/how-it-works') }}"
								class="{{ request()->is('how-it-works') ? 'how-it-work-UI' : '' }}">
									{{ $data['asi_funciona_header'] ?? 'Así funciona' }}
								</a>
								</li>
								{{-- <li> --}}
									{{-- <a href="{{ url('/centro-de-ayuda') }}">{{ $data['ayuda_header'] ?? 'Ayuda' }}</a> --}}
									{{-- <a href="{{ url('/help') }}">{{ $data['ayuda_header'] ?? 'Ayuda' }}</a> --}}
								{{-- </li> --}}
								<li> 
									<a href="{{ url('/faq') }}">
									   FAQs
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="cross-icon-lft">
						<img src="{{ asset('assets/images/cross_icon.svg') }}" alt="" srcset="">
					</div>
				</div>
				<!-- <div class="hedaer_logo">
					<a href="{{ url('/') }}"><img src="{{ asset('storage/' . $data['header_logo']) }}" alt=""></a>
				</div> -->
			</nav>
		</div>
	</div>

</header>


<!-- required js header -->

<script src="https://code.jquery.com/jquery;5-3.6.0.min.js"></script>


<script>
	$(document).ready(function() {
		$(".logged_out_img").click(function(event) {
			event.stopPropagation();

			let profileDropdown = $(".profile-drpdwn");


			if (profileDropdown.css("opacity") == "0") {
				profileDropdown.css("opacity", "1").css("visibility", "visible").toggleClass("show");
			} else {
				profileDropdown.css("opacity", "0").css("v+./isibility", "hidden").removeClass("show");
			}
		});

		// Close when clicking outside
		$(document).click(function(event) {
			if (!$(event.target).closest(".profile-drpdwn, .logged_out_img").length) {
				$(".profile-drpdwn").css("opacity", "0").css("visibility", "hidden").removeClass("show");
			}
		});

		// Close when clicking cross icon
		$(".cross-icon").click(function() {
			$(".profile-drpdwn").css("opacity", "0").css("visibility", "hidden").removeClass("show");
		});
	});;
	5
	$('.cross-icon').click(function() {
		$('.UserFrontOverlay').hide();
	});
	$(".cross-icon-lft").click(function() {
		$(".navbar-collapse").removeClass("show");
	});



	// back icon js for sliding  dropdown
	// $(document).ready(function() {
	// 	$('.menu-item.dropdown').click(function(event) {
	// 		event.stopPropagation(); // Prevent event bubbling
	// 		$(this).find('.dropdown_menu').addClass('active-dropdown').css('display', 'block');
	// 	});

	// 	$('.back-icon').click(function(event) {
	// 		event.stopPropagation();
	// 		$(this).closest('.dropdown_menu').removeClass('active-dropdown').css('display', 'none');
	// 	});
	// });


	// arrw toggle js
	$(document).ready(function() {
		function toggleIcons() {
			if ($(window).width() > 767) {
				$('.dropdown_toggle i').show();
				$('.arrw-right').hide();
			} else {
				$('.dropdown_toggle i').hide();
				$('.arrw-right').show();
			}
		}

		// Run on page load
		toggleIcons();

		// Run on window resize
		$(window).resize(function() {
			toggleIcons();
		});
	});
</script>






     @yield('content')
	 @php
	 $keys = [
	 'footer_logo',
	 'footer_copyright',
	 'footer_text',
 ];
 $setting = App\Models\MetaData::whereIn('key', $keys)->get()->keyBy('key');

 // $path = str_replace('public/', '', $setting->file_path ?? null);
 // $copyright = App\Models\MetaData::where('key', 'footer_copyright')->first();
 $data = [
	 'footer_logo' => str_replace('public/', '', $setting['footer_logo']->file_path ?? null),
	 'footer_copyright' => $setting['footer_copyright']->value ?? null,
	 'footer_text' => $setting['footer_text']->value ?? null,
 ];
// dd($data);

@endphp


<footer @if(request()->segment(1)=='order-confirmation' ||  request()->segment(1)=='checkout' ) class = {{'hide_footer'}} @endif  >
	<div class="outer_foot_bg dark cstm-ftr">
		<div class="container">

			{{-- <div class="in1_foot ">
				<div class="row">
					<div class="col-lg-3 col-md-8">
						<div class="fot_logo">
							<a href="{{ url('/') }}">
								<img src="{{ asset('storage/' . $data['footer_logo']) }}" alt="">
							</a>

							<p class="logo-text">{{$data['footer_text'] ?? ''}}</p>

							<div class="social_foot">
								<ul class="soc_icon_fot">
									<li class="soc_icon_li">
									<a href="" class="fot_icon">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z"/></svg>
									</a>
									</li>
									<li class="soc_icon_li">
									<a href="" class="fot_icon">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
									</a>

									</li>
									<li class="soc_icon_li">
									<a href="" class="fot_icon">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M204 6.5C101.4 6.5 0 74.9 0 185.6 0 256 39.6 296 63.6 296c9.9 0 15.6-27.6 15.6-35.4 0-9.3-23.7-29.1-23.7-67.8 0-80.4 61.2-137.4 140.4-137.4 68.1 0 118.5 38.7 118.5 109.8 0 53.1-21.3 152.7-90.3 152.7-24.9 0-46.2-18-46.2-43.8 0-37.8 26.4-74.4 26.4-113.4 0-66.2-93.9-54.2-93.9 25.8 0 16.8 2.1 35.4 9.6 50.7-13.8 59.4-42 147.9-42 209.1 0 18.9 2.7 37.5 4.5 56.4 3.4 3.8 1.7 3.4 6.9 1.5 50.4-69 48.6-82.5 71.4-172.8 12.3 23.4 44.1 36 69.3 36 106.2 0 153.9-103.5 153.9-196.8C384 71.3 298.2 6.5 204 6.5z"/></svg>
									</a>
									</li>
									<li class="soc_icon_li">
									<a href="" class="fot_icon">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
									</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="col-lg-2 col-md-6">
						<div class="foot_sec">
							<div class="foot_h">
								<h5>
									Documentos
								</h5>
							</div>
							<ul class="foot_ul">
								<li class="foot_li"><a href="{{ url('/negocios-y-comercio') }}">Negocios y Comercio</a></li>
								<li class="foot_li"><a href="{{ url('/vida-personal') }}">Vida Personal </a></li>
								<li class="foot_li"><a href="{{ url('/laboral-y-cumplimiento') }}">Laboral y Cumplimiento </a></li>
								<li class="foot_li"><a href="{{ url('/tecnologia-y-consumo') }}">Tecnología y Consumo</a></li>
							</ul>
						</div>
					</div>
					<div class="col-lg-2 col-md-6">
						<div class="foot_sec">
							<div class="foot_h">
								<h5>
									Información
								</h5>
							</div>
							<ul class="foot_ul">
								<li class="foot_li"><a href="{{ url('/sobre-nosotros') }}">Sobre nosotros</a></li>
								<li class="foot_li"><a href="{{ url('/precios') }}">Precios </a></li>
								<li class="foot_li"><a href="{{ url('/contacto') }}">Contacto </a></li>
								<li class="foot_li"><a href="">Facturación</a></li>
							</ul>
						</div>
					</div>
					<div class="col-lg-2 col-md-6">
						<div class="foot_sec">
							<div class="foot_h">
								<h5>
									Ayuda
								</h5>
							</div>
							<ul class="foot_ul">
								<li class="foot_li"><a href="{{ url('/centro-de-ayuda') }}">Centro de Ayuda </a></li>
								<li class="foot_li"> <a href="{{ url('/asi-funciona') }}">Así funciona </a></li>
								<li class="foot_li"><a href="{{ url('/preguntas-frecuentes') }}">Preguntas Frecuentes
									</a></li>
							</ul>
						</div>
					</div>
					<div class="col-lg-2 col-md-6">
						<div class="foot_sec">
							<div class="foot_h">
								<h5>
									Legal
								</h5>
							</div>
							<ul class="foot_ul">
								<li class="foot_li"> <a href="{{ url('/terminos-y-condiciones') }}">Términos y
										Condiciones </a></li>
								<li class="foot_li"><a href="{{ url('/aviso-de-privacidad') }}">Aviso de Privacidad</a>
								</li>
								<li class="foot_li"><a href="">Aviso Legal </a></li>
							</ul>
						</div>
					</div>
				</div>
			</div> --}}
			<div class="foot_end_box">
				<div class="reserve_box">
					<!-- Copyright © 2020-2024 Legalio. Todos los derechos reservados. -->
					{{ str_replace('{current_year}',date('Y'),$data['footer_copyright'] ?? '') }}
				</div>
				<div class="reserve_box custom_links">
					<a href="{{ url('/terms-conditions') }}" class="custom_li">
						{{-- Términos y Condiciones --}}
						Terms & Conditions
					</a>
					{{-- <a href="{{ url('/terminos-y-condiciones') }}" class="custom_li">
						Terms & Conditions
					</a> --}}
					<a href="{{ url('/privacy-policy') }}" class="custom_li">
						{{-- Aviso de Privacidad --}}
						Privacy Policy
					</a>
					{{-- <a href="{{ url('/aviso-de-privacidad') }}" class="custom_li">
						Privacy Policy
					</a> --}}
				</div>
			</div>
		</div>
	</div>

 {!! getMetadata('end_of_footer') !!}
</footer>

     {{ $slot ?? '' }}


     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"
          integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A=="
          crossorigin="anonymous" referrerpolicy="no-referrer"></script>
     <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
     <script src="{{ asset('assets/js/script.js') }}?v={{ time() }}"></script>

     <script>
          AOS.init();
     </script>

     <!-- @if(Session::get('error'))
          <script>
          console.log('error');
          iziToast.error({
               message: "{{ Session::get('error') }}",
               position: 'topRight'
          });
          </script>
     @endif -->
     <!-- @if(Session::get('success'))
          <script>
          iziToast.success({
               message: "{{ Session::get('success') }}",
               position: 'topRight'
          });
          </script>
     @endif -->
     @livewireScripts
</body>

</html>



