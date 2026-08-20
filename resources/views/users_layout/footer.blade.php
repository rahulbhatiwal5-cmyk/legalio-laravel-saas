@php
$keys = [
'footer_logo',
'footer_copyright',
'footer_text',
'documentos_footer',
'negocios_y_comercio_footer',
'vida_personal_footer',
'laboral_y_cumplimiento_footer',
'tecnologia_y_consumo_footer',
'informacion_footer',
'sobre_nosotros_footer',
'precios_footer',
'contacto_footer',
'facturacion_footer',
'ayuda_footer',
'centro_de_ayuda_footer',
'asi_funciona_footer',
'preguntas_frecuentes_footer',
'legal_footer',
'terminos_y_condiciones_footer',
'aviso_de_privacidad_footer',
'aviso_legal_footer',
];
$setting = App\Models\MetaData::whereIn('key', $keys)->get()->keyBy('key');

$data = [
'footer_logo' => str_replace('public/', '', $setting['footer_logo']->file_path ?? null),
'footer_copyright' => $setting['footer_copyright']->value ?? null,
'footer_text' => $setting['footer_text']->value ?? null,
'documentos_footer' => $setting['documentos_footer']->value ?? null,
'negocios_y_comercio_footer' => $setting['negocios_y_comercio_footer']->value ?? null,
'vida_personal_footer' => $setting['vida_personal_footer']->value ?? null,
'laboral_y_cumplimiento_footer' => $setting['laboral_y_cumplimiento_footer']->value ?? null,
'tecnologia_y_consumo_footer' => $setting['tecnologia_y_consumo_footer']->value ?? null,
'informacion_footer' => $setting['informacion_footer']->value ?? null,
'sobre_nosotros_footer' => $setting['sobre_nosotros_footer']->value ?? null,
'precios_footer' => $setting['precios_footer']->value ?? null,
'contacto_footer' => $setting['contacto_footer']->value ?? null,
'facturacion_footer' => $setting['facturacion_footer']->value ?? null,
'ayuda_footer' => $setting['ayuda_footer']->value ?? null,
'centro_de_ayuda_footer' => $setting['centro_de_ayuda_footer']->value ?? null,
'asi_funciona_footer' => $setting['asi_funciona_footer']->value ?? null,
'preguntas_frecuentes_footer' => $setting['preguntas_frecuentes_footer']->value ?? null,
'legal_footer' => $setting['legal_footer']->value ?? null,
'terminos_y_condiciones_footer' => $setting['terminos_y_condiciones_footer']->value ?? null,
'aviso_de_privacidad_footer' => $setting['aviso_de_privacidad_footer']->value ?? null,
'aviso_legal_footer' => $setting['aviso_legal_footer']->value ?? null,

];

$keyslink = [
'pinterest_link',
'twitter_link' ,
'fb_link',
'instagram_link'
];

$resultslink = App\Models\Setting::whereIn('key', $keyslink)->get()->keyBy('key');

$datalink= [

'pinterest_link' => $resultslink['pinterest_link']->value ?? null,
'twitter_link' => $resultslink['twitter_link']->value ?? null,
'fb_link' => $resultslink['fb_link']->value ?? null,
'instagram_link' => $resultslink['instagram_link']->value ?? null,

];

$languages=App\Models\Language::all();

$document_categories = App\Models\DocumentCategory::where('is_deleted',0)->get();

@endphp




<footer @if(request()->segment(1)=='order-confirmation' || request()->segment(1)=='checkout' ) class = {{'hide_footer'}} @endif >
    <div class="outer_foot_bg dark">
        <div class="container">

            <div class="in1_foot ">
                <div class="row">
                    <div class="col-lg-3 col-md-8">
                        <div class="fot_logo">
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('storage/' . $data['footer_logo']) }}" alt="">
                            </a>
                            {{-- <p class="logo-text">Líder en documentos legales <br> por más de 5 años</p> --}}
                            <p class="logo-text">{{$data['footer_text'] ?? ''}}</p>

                            <div class="social_foot">
                                <ul class="soc_icon_fot">
                                    <li class="soc_icon_li">
                                        <a href="{{ $datalink['fb_link'] ?? '' }}" class="fot_icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                                <!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                <path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z" /></svg>
                                        </a>
                                    </li>
                                    <li class="soc_icon_li">
                                        <a href="{{ $datalink['instagram_link'] ?? '' }}" class="fot_icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                <path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z" /></svg>
                                        </a>

                                    </li>
                                    <li class="soc_icon_li">
                                        <a href="{{ $datalink['pinterest_link'] ?? '' }}" class="fot_icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                                <!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                <path d="M204 6.5C101.4 6.5 0 74.9 0 185.6 0 256 39.6 296 63.6 296c9.9 0 15.6-27.6 15.6-35.4 0-9.3-23.7-29.1-23.7-67.8 0-80.4 61.2-137.4 140.4-137.4 68.1 0 118.5 38.7 118.5 109.8 0 53.1-21.3 152.7-90.3 152.7-24.9 0-46.2-18-46.2-43.8 0-37.8 26.4-74.4 26.4-113.4 0-66.2-93.9-54.2-93.9 25.8 0 16.8 2.1 35.4 9.6 50.7-13.8 59.4-42 147.9-42 209.1 0 18.9 2.7 37.5 4.5 56.4 3.4 3.8 1.7 3.4 6.9 1.5 50.4-69 48.6-82.5 71.4-172.8 12.3 23.4 44.1 36 69.3 36 106.2 0 153.9-103.5 153.9-196.8C384 71.3 298.2 6.5 204 6.5z" /></svg>
                                        </a>
                                    </li>
                                    <li class="soc_icon_li">
                                        <a href="{{ $datalink['twitter_link'] ?? '' }}" class="fot_icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                                <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" /></svg>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="foot_sec">
                            <div class="foot_h">
                                <h5>{{ $data['documentos_footer'] ?? '' }}</h5>
                            </div>
                            <ul class="foot_ul">
                                <li class="foot_li"><a href="{{ url('/business-commerce') }}">{{ $data['negocios_y_comercio_footer'] ?? '' }}</a></li>
                                {{-- <li class="foot_li"><a href="{{ url('/negocios-y-comercio') }}">{{ $data['negocios_y_comercio_footer'] ?? '' }}</a></li> --}}
                                <li class="foot_li"><a href="{{ url('/personal-life') }}">{{ $data['vida_personal_footer'] ?? '' }}</a></li>
                                {{-- <li class="foot_li"><a href="{{ url('/vida-personal') }}">{{ $data['vida_personal_footer'] ?? '' }}</a></li> --}}
                                <li class="foot_li"><a href="{{ url('/employment-compliance') }}">{{ $data['laboral_y_cumplimiento_footer'] ?? '' }}</a></li>
                                {{-- <li class="foot_li"><a href="{{ url('/laboral-y-cumplimiento') }}">{{ $data['laboral_y_cumplimiento_footer'] ?? '' }}</a></li> --}}
                                <li class="foot_li"><a href="{{ url('/technology-consumer') }}">{{ $data['tecnologia_y_consumo_footer'] ?? '' }}</a></li>
                                {{-- <li class="foot_li"><a href="{{ url('/tecnologia-y-consumo') }}">{{ $data['tecnologia_y_consumo_footer'] ?? '' }}</a></li> --}}
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <div class="foot_sec">
                            <div class="foot_h">
                                <h5>{{ $data['informacion_footer'] ?? '' }}</h5>
                            </div>
                            <ul class="foot_ul">
                                {{-- <li class="foot_li"><a href="{{ url('/sobre-nosotros') }}">{{ $data['sobre_nosotros_footer'] ?? '' }}</a></li> --}}
                                <li class="foot_li"><a href="{{ url('/about-legalio') }}">{{ $data['sobre_nosotros_footer'] ?? '' }}</a></li>
                                {{-- <li class="foot_li"><a href="{{ url('/precios') }}">{{ $data['precios_footer'] ?? '' }}</a></li> --}}
                                <li class="foot_li"><a href="{{ url('/pricing') }}">{{ $data['precios_footer'] ?? '' }}</a></li>
                                {{-- <li class="foot_li"><a href="{{ url('/contacto') }}">{{ $data['contacto_footer'] ?? '' }}</a></li> --}}
                                {{-- <li class="foot_li"><a href="#">{{ $data['facturacion_footer'] ?? '' }}</a></li> --}}
                                <li class="foot_li"><a href="{{ url('/how-it-works') }}">{{ $data['asi_funciona_footer'] ?? '' }}</a></li>
                                <li class="foot_li"><a href="{{ url('/faq') }}">{{ $data['preguntas_frecuentes_footer'] ?? '' }}</a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- <div class="col-lg-2 col-md-6">
							<div class="foot_sec">
								<div class="foot_h">
									<h5>{{ $data['ayuda_footer'] ?? '' }}</h5>
                </div>
                <ul class="foot_ul">
                    <li class="foot_li"><a href="{{ url('/centro-de-ayuda') }}">{{ $data['centro_de_ayuda_footer'] ?? '' }}</a></li>
                    <li class="foot_li"><a href="{{ url('/help') }}">{{ $data['centro_de_ayuda_footer'] ?? '' }}</a></li>
                    <li class="foot_li"><a href="{{ url('/asi-funciona') }}">{{ $data['asi_funciona_footer'] ?? '' }}</a></li>
                    <li class="foot_li"><a href="{{ url('/how-it-works') }}">{{ $data['asi_funciona_footer'] ?? '' }}</a></li>
                    <li class="foot_li"><a href="{{ url('/preguntas-frecuentes') }}">{{ $data['preguntas_frecuentes_footer'] ?? '' }}</a></li>
                    <li class="foot_li"><a href="{{ url('/faq') }}">{{ $data['preguntas_frecuentes_footer'] ?? '' }}</a></li>
                </ul>
            </div>
        </div> --}}

        <div class="col-lg-2 col-md-6">
            <div class="foot_sec">
                <div class="foot_h">
                    <h5>{{ $data['legal_footer'] ?? '' }}</h5>
                </div>
                <ul class="foot_ul">
                    {{-- <li class="foot_li"><a href="{{ url('/terminos-y-condiciones') }}">{{ $data['terminos_y_condiciones_footer'] ?? '' }}</a></li> --}}
                    <li class="foot_li"><a href="{{ url('/terms-conditions') }}">{{ $data['terminos_y_condiciones_footer'] ?? '' }}</a></li>
                    {{-- <li class="foot_li"><a href="{{ url('/aviso-de-privacidad') }}">{{ $data['aviso_de_privacidad_footer'] ?? '' }}</a></li> --}}
                    <li class="foot_li"><a href="{{ url('/privacy-policy') }}">{{ $data['aviso_de_privacidad_footer'] ?? '' }}</a></li>
                    <li class="foot_li"><a href="{{ url('/contact') }}">{{ $data['contacto_footer'] ?? '' }}</a></li>
                    {{-- <li class="foot_li"><a href="{{ url('/aviso-legal') }}">{{ $data['aviso_legal_footer'] ?? '' }}</a></li> --}}
                </ul>
            </div>
        </div>

    </div>
    </div>
    <div class="foot_end_box home_page_footer ">
        <div class="reserve_box">
            
           © {{ date('Y') }} Legalio. All rights reserved.
            <!-- Copyright © 2020-2024 Legalio. Todos los derechos reservados. -->

        </div>

        {{-- <div class="reserve_box">
					<div class="select-menu active">
						<div class="select-btn ">
							<button class="sBtn-text">México - Español</button>
							<i class="fa fa-chevron-down"></i>
						</div>

						<div class="cus_dropdown_menu options">
							<div class="container">
								<h2>Choose your Country/Region</h2>
								<div class="cus_m_wrapper mst-ly-cnt"> --}}
        {{-- @foreach ($languages as $language )
									<a class="cus_dropdown-item " href="">{{ $language->country }} - {{ $language->name }} </a>
        @endforeach --}}
        {{-- <a class="cus_dropdown-item " href="">Spain - Spanish</a>
									<a class="cus_dropdown-item " href="">Australia - English</a>
									<a class="cus_dropdown-item " href="">Brasil - Português</a>
									<a class="cus_dropdown-item " href="">Canada - English</a>
									<a class="cus_dropdown-item " href="">Canada - Français</a>
									<a class="cus_dropdown-item " href="">Colombia - Español</a>
									<a class="cus_dropdown-item " href="">Deutschland - Deutsch</a>
									<a class="cus_dropdown-item " href="">España - Español</a>
									<a class="cus_dropdown-item " href="">Estados Unidos - Español</a>
									<a class="cus_dropdown-item " href="">France - Français</a>
									<a class="cus_dropdown-item " href="">Hong Kong - English</a>
									<a class="cus_dropdown-item " href="">India - English</a>
									<a class="cus_dropdown-item " href="">Ireland - English</a>
									<a class="cus_dropdown-item " href="">Israel - English</a>
									<a class="cus_dropdown-item " href="">Svizzera - Italiano</a>
									<a class="cus_dropdown-item " href="">Malaysia - English</a>
									<a class="cus_dropdown-item " href="">Mexico - Spanish</a>
									<a class="cus_dropdown-item " href="">New Zealand - English</a>
									<a class="cus_dropdown-item " href="">Österreich - Deutsch</a>
									<a class="cus_dropdown-item " href="">Pakistan - English</a>
									<a class="cus_dropdown-item " href="">Perú - Español</a>
									<a class="cus_dropdown-item " href="">Philippines - English</a>
									<a class="cus_dropdown-item " href="">Polska - Polski</a>
									<a class="cus_dropdown-item " href="">Portuguese - Portugal</a>
									<a class="cus_dropdown-item " href="">Schweiz - Deutsch</a>
									<a class="cus_dropdown-item " href="">Singapore - English</a>
									<a class="cus_dropdown-item " href="">South Africa - English</a>
									<a class="cus_dropdown-item " href="">Suisse - Français</a>
									<a class="cus_dropdown-item " href="">Türkiye - Türkçe</a>
									<a class="cus_dropdown-item " href="">United Arab Emirates - English</a>
									<a class="cus_dropdown-item " href="">United Kingdom - English</a>
									<a class="cus_dropdown-item  selected" href="">United States - English</a>
									<a class="cus_dropdown-item " href="">Venezuela - Español</a>
								</div>
							</div>
						</div>

					</div>
				</div> --}}
    </div>
    </div>
    </div>

    {!! getMetadata('end_of_footer') !!}
</footer>


<script>
    document.addEventListener("DOMContentLoaded", () => {

        const items = document.querySelectorAll(".foot_sec");

        items.forEach(sec => {

            const ul = sec.querySelector(".foot_ul");

            sec.addEventListener("click", e => {

                if (e.target.closest("a")) return;

                const isOpen = sec.classList.contains("f_open_section");

                items.forEach(s => {
                    s.classList.remove("f_open_section");
                    const u = s.querySelector(".foot_ul");
                    u.style.maxHeight = null;
                });

                if (!isOpen) {
                    sec.classList.add("f_open_section");
                    ul.style.maxHeight = ul.scrollHeight + "px";
                }

            });
        });

    });

</script>
