<style>
    .nk-dropdown-title font:hover{
        color:#6576ff;
    }
    .all-read font:hover{
        color:#6576ff;
    }

		.hedaer_logo a img {
        max-width: 176px !important;
      }

	  .active-category {
		background: #ffffff3b;
		}
</style>

@php
$keys = [
'header_logo',
'header_btn_1',
'header_btn_2',
'favicon',
'ayuda_header',
'asi_funciona_header',
'header_search_placeholder',
'mobile_header_logo',
'mobile_header_logo',
'header_blue_logo',
];

$results = App\Models\MetaData::whereIn('key', $keys)->get()->keyBy('key');
$data = [
'header_logo' => str_replace('public/', '', $results['header_logo']->file_path ?? null),
'button1' => $results['header_btn_1']->value ?? null,
'button2' => $results['header_btn_2']->value ?? null,
'ayuda_header' => $results['ayuda_header']->value ?? null,
'asi_funciona_header' => $results['asi_funciona_header']->value ?? null,
'favicon' => str_replace('public/', '', $results['favicon']->file_path ?? null),
'header_search_placeholder' => $results['header_search_placeholder']->value ?? null,
'mobile_header_logo' => str_replace('public/', '', $results['mobile_header_logo']->file_path ?? null),
'mobile_header_logo' => str_replace('public/', '', $results['mobile_header_logo']->file_path ?? null),
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


<header class="inner-header fun-header category-searchbar   docu_creat_hdr">
	<!-- profile at tab -->
	<div class="logged_out_profile profile_tab_show">
		<div class="logged_out_img">
			<!-- <img src="{{ asset('assets/img/usr_img.png') }}" class="img-fluid"> -->
			<img src="{{ optional(auth()->user())->profile_image ?? dimage() }}" class="img-fluid">
		</div>
		<div class="profile-drpdwn">
			<div class="UserFrontOverlay"></div>
			<div class="profile-drpdwn-innr str_login_btn">
				<div class="drpdwn-hd">
					<div class="logo">
						<a class='desk-log-rgt' href="{{ url('/') }}">
							<img src="{{ asset('storage/'.$data['header_logo']) ?? '' }}" alt="">
						</a>
						<a class='mob-log-rgt' href="{{ url('/') }}">
								<img src="{{ asset('storage/' . $data['mobile_header_logo']) }}" alt="">
							</a>
					</div>
					<div class="cross-icon">
						<img src="{{ asset('assets/images/cross_icon.svg') }}" alt="" srcset="">
					</div>
				</div>

					@guest
						<div class="profile_mid">
							<h2>Get Started!</h2>
							<p>Create professional legal documents quickly and easily.</p>
						</div>
						<div class="all_doc_btn">
							<a href="{{ route('user.all__documents') }}"
							class="cta_dark">
							{{ $data['button1'] ?? 'Crear documento' }}
							</a>
						</div>
					@endguest

				<div class="drpdwn-cntnt">
					<div class="hedaer_bnt">
						
						@if(auth()->user())
						<!-- <a href="{{ route('logout') }}" class="cta_light">Cerrar sesión</a> -->
						 @php
							$unread = auth()->user()->unreadNotifications()->limit(10)->get();
							$all = auth()->user()->notifications()->limit(10)->get();
                                @endphp
                                <div class="hdr_ryt   moble_sidbr_sec">

								<div class="moble_sid_prof_upr">
                                      <div class="mobile-side_inr_box"> 
									  <!-- notif icon -->
										<div class="notf drop_menu">
											<a class="notfictn_lnk">
												<img src="{{ asset('assets/img/bell_img.png') }}" class="img-fluid">
												<span class="badge custom-badge badge-success">{{ $unread->count() }}</span>
											</a>
											@auth
											<div class="dropdown-menu dropdown-menu-xl dropdown-menu-right" style=" min-width: 360px !important;max-width: 360px !important;border-radius:20px !important; margin-right: 20px; min-width: 320px;">
										<div class="dropdown-main" >
											<div class="row"style="font-size: 0.8125rem;padding: 14px;border-bottom:1px solid #e5e9f2">
												<div class="col-12 text-left">
												All Notification
												</div>
											</div>
											<div style="overflow-y: auto; overflow-x: hidden;height:250px;">
												<div class="row"style="font-size: 0.8125rem;padding: 14px;border-bottom:1px solid #e5e9f2">
													<div class="col-2 showAll"> <span class="sub-title nk-dropdown-title ">All </span></div>
													<div class="col-2 showUnread"style="text-align: left;"> <span class="sub-title nk-dropdown-title ">Unread({{ $unread->count() }}) </span></div>
													<div class="col-8"style="text-align: right;">
														<form action="{{ route('notifications.readAll') }}" method="POST">
															@csrf
															<button class="all-read"style="
															background: transparent;
															border: none;
															color:#012555;
														"type="submit" >Mark all as read</button>
														</form>
													</div>
												</div>
												<div class="row">
													<div class="col-12">
													<small class="p-3 pt-2">Today</small>
													</div>
												</div>
												<div class="allNotification">
													@forelse($all as $notification)
													<div class="row"style="font-size: 0.8125rem;padding: 14px;border-bottom:1px solid #e5e9f2">
													<div class="col-2">
														<div class="bg-light rounded-circle p-2 text-warning text-center" >
															<img src="https://localio.com/public/user-dashboard-theme/img/bell_icon.svg" alt="Notification Icon" width="24" height="24">
														</div>
													</div>
													<div class="col-7">
														<span><strong>{{ $notification->data['title'] ?? 'Notification' }}</strong></span>

													</div>
													<div class="col-3">
														@php
															$hours = $notification->created_at->diffForHumans(null, true);
														@endphp

														<small>
															@if ($hours < 1)
																Just now
															@elseif ($hours === 1)
																1 hr ago
															@else
																{{ $hours }}
															@endif
														</small>
													</div>
													</div>
													@empty
													<div class="text-muted text-center">No notifications</div>
													@endforelse
												</div>

												<div class="unRead d-none"style="overflow-y: auto; overflow-x: hidden;height:200px;">
												@forelse($unread as $notification)
													<div class="row" style="font-size: 0.8125rem; padding: 14px; border-bottom: 1px solid #e5e9f2">
														<div class="col-1">
															<div class="col-2">
																<div class="bg-light rounded-circle p-2 text-warning text-center" >
																	<img src="https://localio.com/public/user-dashboard-theme/img/bell_icon.svg" alt="Notification Icon" width="24" height="24">
																</div>
															</div>
														</div>
														<div class="col-8">
															<span>
																<strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
															</span>
														</div>
														<div class="col-3">
															@php
																$hours = $notification->created_at->diffForHumans(null, true);
															@endphp

															<small>
																@if ($hours < 1)
																	Just now
																@elseif ($hours === 1)
																	1 hr ago
																@else
																	{{ $hours }}
																@endif
															</small>
														</div>
													</div>
													@empty
													<div class="text-muted text-center">No unread notifications</div>
												@endforelse
												</div>
											</div>
											<div class="row row text-center p-2 fs-7 text-muted">
												<div class="col-12">

												</div>
											</div>
										</div>
										</div>
										@endauth
									   </div>

								  <!-- profile image -->
									<div class="user_img drop_menu">
										<div class="usr_profile">
											<img src="{{ optional(auth()->user())->profile_image ?? dimage() }}" class="img-fluid">
										</div>
										<div class="dropdown-menu dropdown-menu-right" style="margin-right: 20px;">
											<div class="dropdown-main">
												<a href="{{route('user.profile')}}">
													<div class="user_detail">
														<div class="user_img">
															{{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
														</div>
														<div class="user_name">
															<h5>{{Auth::user()->first_name}}</h5>
															<p>{{Auth::user()->email}}</p>
														</div>
													</div>
												</a>
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
													<a class="dropdown-item" href="{{route('user.invoice')}}"><i class="fa-solid fa-envelope-open-text"></i>Facturas</a>
												</div>
												<div class="dash-icon">
												<a class="dropdown-item" href="{{ route('subscription.details') }}">
													<i class="fa-solid fa-credit-card"></i>
														Subscription
													</a>
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

								{{-- only show at logout --}}
								<div class="profile_mid">
									{{-- <h2>Get Started!</h2> --}}
									{{-- <p>Create an Account and Secure Your Exclusive Logo Today. </p> --}}
									<h2>Welcome Back!</h2>
									<p>Create, manage, and organize your documents all in one place.</p>
								</div>

								<div class="all_doc_btn">
									<a href="{{ route('user.all__documents') }}"
										class="cta_dark">{{ $data['button1'] ?? 'Crear documento' }}</a>
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
	<!-- profile at tab ends-->
	<div class="main_header">
		<div class="container-fluid">
			<div class="srch-hdr iner-search-side detl-page-hdr @if(auth()->check())user-icon @endif">
				<div class="hedaer_logo ">
					{{-- <a href="{{ auth()->check() ? url('/legal-documents') : url('/') }}">
						<img src="{{ asset('storage/' . $data['header_logo']) }}" alt="">
					</a> --}}
					<a href="{{ auth()->check() ? url('/legal-documents') : url('/') }}">
						<img src="{{ asset('storage/' . $data['header_blue_logo']) }}">
					</a>
				</div>
				
				{{-- @if($current_url == $home_url)
				<livewire:header-search-box>
                @else
				<livewire:other-header-search>
				@endif --}}

				<div class="detl-page-rgt-sde">
				<div id="dynamic_menu">
					<div class="right_menu">
						<ul>
							<li>
								<a href="{{ route('user.how_it_works') }}">{{ $data['asi_funciona_header'] ?? 'Así funciona' }} </a>
							</li>
							<li>
								<a href="{{ route('user.faq') }}">FAQs</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="hedaer_bnt">
						<!-- <div class="all_doc_btn">
							<a href="{{ route('user.all__documents') }}"
								class="cta_dark">{{ $data['button1'] ?? 'Crear documento' }}</a>
						</div> -->
					@if(auth()->user())
                    @php
                        $unread = auth()->user()->unreadNotifications()->limit(10)->get();
                        $all = auth()->user()->notifications()->limit(10)->get();
                    @endphp
					<div class="hdr_ryt">	
						<div class="hdr_info">
							<div class="notf drop_menu">
								<a class="notfictn_lnk">
									<img src="{{ asset('assets/img/bell_img.png') }}" class="img-fluid">
									<span class="badge custom-badge badge-success">{{ $unread->count() }}</span>
								</a>
								@auth
                                <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right" style=" min-width: 360px !important;max-width: 360px !important;border-radius:20px !important; margin-right: 20px; min-width: 320px;">
                                        <div class="dropdown-main" >
                                            <div class="row"style="font-size: 0.8125rem;padding: 14px;border-bottom:1px solid #e5e9f2">
                                                <div class="col-12 text-left">
                                                    All Notification
                                                </div>
                                            </div>
                                            <div style="overflow-y: auto; overflow-x: hidden;height:250px;">
                                            <div class="row"style="font-size: 0.8125rem;padding: 14px;border-bottom:1px solid #e5e9f2">
                                                <div class="col-2 showAll"> <span class="sub-title nk-dropdown-title ">All </span></div>
                                                <div class="col-2 showUnread"style="text-align: left;"> <span class="sub-title nk-dropdown-title ">Unread({{ $unread->count() }}) </span></div>
                                                <div class="col-8"style="text-align: right;">
                                                    <form action="{{ route('notifications.readAll') }}" method="POST">
                                                        @csrf
                                                        <button class="all-read"style="
                                                        background: transparent;
                                                        border: none;
                                                        color:#012555;
                                                    "type="submit" >Mark all as read</button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <small class="p-3 pt-2">Today</small>
                                                </div>
                                            </div>
                                            <div class="allNotification">
                                                @forelse($all as $notification)
                                                <div class="row"style="font-size: 0.8125rem;padding: 14px;border-bottom:1px solid #e5e9f2">
                                                    <div class="col-2">
                                                        <div class="bg-light rounded-circle p-2 text-warning text-center" >
                                                            <img src="https://localio.com/public/user-dashboard-theme/img/bell_icon.svg" alt="Notification Icon" width="24" height="24">
                                                        </div>
                                                    </div>
                                                    <div class="col-7">
                                                        <span><strong>{{ $notification->data['title'] ?? 'Notification' }}</strong></span>

                                                    </div>
                                                    <div class="col-3">
                                                        @php
                                                            $hours = $notification->created_at->diffForHumans(null, true);
                                                        @endphp

                                                        <small>
                                                            @if ($hours < 1)
                                                                Just now
                                                            @elseif ($hours === 1)
                                                                1 hr ago
                                                            @else
                                                                {{ $hours }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                                @empty
                                                    <div class="text-muted text-center">No notifications</div>
                                                @endforelse
                                            </div>

                                            <div class="unRead d-none"style="overflow-y: auto; overflow-x: hidden;height:200px;">
                                               @forelse($unread as $notification)
                                                    <div class="row" style="font-size: 0.8125rem; padding: 14px; border-bottom: 1px solid #e5e9f2">
                                                        <div class="col-1">
                                                            <div class="col-2">
                                                                <div class="bg-light rounded-circle p-2 text-warning text-center" >
                                                                    <img src="https://localio.com/public/user-dashboard-theme/img/bell_icon.svg" alt="Notification Icon" width="24" height="24">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-8">
                                                            <span>
                                                                <strong>{{ $notification->data['title'] ?? 'Notification' }}</strong>
                                                            </span>
                                                        </div>
                                                        <div class="col-3">
                                                            @php
                                                                $hours = $notification->created_at->diffForHumans(null, true);
                                                            @endphp

                                                            <small>
                                                                @if ($hours < 1)
                                                                    Just now
                                                                @elseif ($hours === 1)
                                                                    1 hr ago
                                                                @else
                                                                    {{ $hours }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-muted text-center">No unread notifications</div>
                                                @endforelse

                                            </div>
                                        </div>
                                            <div class="row row text-center p-2 fs-7 text-muted">
                                                <div class="col-12">

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    @endauth

							</div>

							<div class="user_img drop_menu">
								<div class="usr_profile">
									<img src="{{ optional(auth()->user())->profile_image ?? dimage() }}" class="img-fluid" alt="">
								</div>
								<div class="dropdown-menu dropdown-menu-right" style="margin-right: 20px;">
									{{-- <div class="dropdown-main">
										<a href="{{route('user.profile')}}">
											<div class="user_detail">
												<div class="user_img">
													{{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
												</div>
												<div class="user_name">
													<h5>{{Auth::user()->first_name}}</h5>
													<p>{{Auth::user()->email}}</p>
												</div>
											</div>
										</a>
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
											<a class="dropdown-item" href="{{route('user.invoice')}}"><i class="fa-solid fa-envelope-open-text"></i>Facturas</a>
										</div>

										<div class="dash-icon">
											<a class="dropdown-item" href="#"><i class="fa-solid fa-headset"></i>Boletos de soporte</a>
										</div>

										<div class="dash-icon">
											<a class="dropdown-item" href="{{ url('/logout') }}"><i class="fa fa-power-off"></i>Finalizar la sesión</a>
										</div>
									</div> --}}
									<x-user-profile/>
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
								<img src="{{ asset('storage/' . $data['mobile_header_logo']) }}" alt="">
								
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


	{{-- <div class="top_header dark categories-hdr-updt">
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
								<img src="{{ asset('storage/' . $data['mobile_header_logo']) }}" alt="">
								
							</a>
						</div>
				
						<div class="rgt_menu_info right_menu">
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
							</ul>

						</div>
					</div>



					<div class="cross-icon-lft">
						<img src="{{ asset('assets/images/cross_icon.svg') }}" alt="" srcset="">
					</div>
				</div>
			</nav>
		</div>
	</div> --}}
                {{-- <div class="categories-link-button">
	                   <div class="left_menu">
							<ul class="menu">
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
			    </div> --}}
</header>


<!-- required js header -->

<script src="https://code.jquery.com/jquery;5-3.6.0.min.js"></script>

{{-- 
<script>
	$(document).ready(function(){

		$('#navbarSupportedContentBtn').click(function(){
			$('body').toggleClass('nav-content-toggle-tab');
		});

		$('.menu-item.dropdown').click(function(event) {
			event.stopPropagation();
			
			$('body').addClass('nav-content-toggle');
			
			$(this).find('.dropdown_menu').addClass('active-dropdown').css('display', 'block');
		});

		// Close dropdown when clicking back icon
		$('.back-icon').click(function(event) {
			event.stopPropagation();
			
			$(this).closest('.dropdown_menu').removeClass('active-dropdown').css('display', 'none');
			
			if($('.dropdown_menu.active-dropdown').length === 0) {
				$('body').removeClass('nav-content-toggle');
			}
		});

		// Close dropdown when clicking outside
		$(document).click(function(event) {
			if (!$(event.target).closest('.menu-item.dropdown').length) {
				$('.dropdown_menu').removeClass('active-dropdown').css('display', 'none');
				$('body').removeClass('nav-content-toggle');
			}
		});

		// Close when clicking cross icon
		$('.cross-icon-lft').click(function() {
			$('.dropdown_menu').removeClass('active-dropdown').css('display', 'none');
			$('body').removeClass('nav-content-toggle');
			$(".navbar-collapse").removeClass("show");
		});

	});


</script> --}}

<script>
	$(document).ready(function(){

		$('#navbarSupportedContentBtn').click(function(){
			$('body').toggleClass('nav-content-toggle-tab');
		});

		// $('.menu-item.dropdown').click(function(event) {
		// 	// if ($(event.target).closest('a').length) {
        //     // return;
		// 	// }
		// 	// event.stopPropagation();

		// 	var $currentDropdown = $(this).find('.dropdown_menu');
		// 	var isCurrentlyActive = $currentDropdown.hasClass('active-dropdown');
			
		// 	// If clicking the same dropdown that's already open, close it
		// 	if (isCurrentlyActive) {
		// 		$currentDropdown.removeClass('active-dropdown').css('display', 'none');
				
		// 		// Remove body class if no dropdowns are active
		// 		if($('.dropdown_menu.active-dropdown').length === 0) {
		// 			$('body').removeClass('nav-content-toggle');
		// 		}
		// 	} else {
		// 		// Close other dropdowns first
		// 		$('.dropdown_menu').removeClass('active-dropdown').css('display', 'none');
				
		// 		// Open the clicked dropdown
		// 		$('body').addClass('nav-content-toggle');
		// 		$currentDropdown.addClass('active-dropdown').css('display', 'block');
		// 	}
		// });

		// Close dropdown when clicking back icon
		$('.back-icon').click(function(event) {
			event.stopPropagation();
			
			$(this).closest('.dropdown_menu').removeClass('active-dropdown').css('display', 'none');
			
			if($('.dropdown_menu.active-dropdown').length === 0) {
				$('body').removeClass('nav-content-toggle');
			}
		});

		// Close dropdown when clicking outside
			// if (!$(event.target).closest('.menu-item.dropdown').length) {
			// 	$('.dropdown_menu').removeClass('active-dropdown').css('display', 'none');
			// 	$('body').removeClass('nav-content-toggle');
			// }
		});	$(document).click(function(event) {
	

		// Close when clicking cross icon
		$('.cross-icon-lft').click(function() {
			$('.dropdown_menu').removeClass('active-dropdown').css('display', 'none');
			$('body').removeClass('nav-content-toggle');
			$(".navbar-collapse").removeClass("show");
		});

	});
</script>

<script>
    $(document).ready(function() {
        $('.showUnread').on('click', function(event) {
            event.stopPropagation();
            $('.allNotification').addClass('d-none');
            $('.unRead').removeClass('d-none');
            $('.unRead').addClass('active');
        });
        $('.showAll').on('click', function(event) {
            event.stopPropagation();
            $('.allNotification').removeClass('d-none');
            $('.unRead').addClass('d-none');
            $('.allNotification').addClass('active');
        })
    });
</script>

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
	});

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

	// 	$('.back-icon .container-fluid').click(function(event) {
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