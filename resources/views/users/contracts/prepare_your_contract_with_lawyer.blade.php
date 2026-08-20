@extends('users_layout.master')
@section('content')

<div class="mobile_header_side_bar">
     <div class="header_sidebar_inner">
          <div class="sidebar_empty_space"></div>
          <div class="sidebar_header_content">
               <div class="sidebar_header_nav">
                    <div class="header_search_form">
                         <form id="header_search_form" method="POST" action="" autocomplete="off">
                         <input type="text" class="form-control" name="search" placeholder="Buscar" />
                         <button class="mobilesearch-btn" type="submit">
                              <i class="fa-solid fa-magnifying-glass"></i>
                         </button>
                         </form>
                    </div>
                    <ul class="_Ty8L2" data-qa="SidebarMenuList">
                         <li><a href="">Arrendamiento</a></li>
                         <li><a href="">Comercio</a></li>
                         <li><a href="">Consumo</a></li>
                         <li><a href="">Familia</a></li>
                         <li><a href="">Internet</a></li>
                         <li><a href="">Laboral</a></li>
                         <li><a href="">Vida diaria</a></li>
                    </ul>

                    <ul class="_Ty8L3" data-qa="SidebarMenuList">
                         <li><a href="">Así funciona</a></li>
                         <li><a href="">Preguntas</a></li>
                    </ul>

                    <div class="mobile_header_buuton">
                         <a class="cta" href="">Crear documento</a>
                    </div>
               </div>
          </div>
     </div>
     <div role="button" class="_GtzsW _Gv2_B"><div class="_NRBhu"></div></div>
     <div class="__z_48"></div>
</div>
<div class="mobile_header_side_bar_right">
     <div class="header_sidebar_inner">
          <div class="sidebar_empty_space"></div>
          <div class="sidebar_header_content">
               <div class="sidebar_header_nav">
                    <div class="header_right_sidebar_content">
                         <div>
                         <p class="logged-in">¡Bienvenido!</p>
                         <div class="mobile_right_header_button loggedin">
                              <a href="" class="cta login">Mis documentos</a>
                              <a href="" class="cta register">Configuración</a>
                              <a href="" class="cta login">Cerrar session</a>
                         </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
     <div role="button" class="_GtzsW _Gv2_B _zNNE6"><div class="_NRBhu"></div></div>
     <div class="__z_48"></div>
</div>

<div class="wn_hght">
	
     <style>
          a.eco_btn {
               /* background: #002655; */
               border-radius: 80px;
               color: #002655;
               font-weight: 600;
               font-size: 18px;
               line-height: 17px;
               padding: 16px 36px;
               border: 1px solid #002655;
          }
          .post-card-sec.doc-post-card h2 {
               color: #002655;
          }
          .pop_price span {
               color: rgb(30 44 79 / 60%);
               font-size: 12px;
               font-weight: 400;
          }
          .pop_price {
               display: flex;
               align-items: baseline;
               gap: 7px;
          }
          section.topic_sl {
               background: #002655;
          }
          form#contract-dropdown-form {
               margin-top: 30px;
          }
          .chnghvr {
               background: #002655;
               color: #fff;
          }
          .gr_bg {
               background: #EDEEF1;
               /* margin-bottom: 16px; */
               display: flex;
               align-items: center;
               padding:20px;
               border-radius: 12px;
               margin: 0;
               margin-top:14px;
          }
          .gr_bg img {
               border-radius: 12px;
          }
          .topic-select {
               padding: 23px;
               border-radius: 0;
               margin-top: 50px;
          }

          .dropdown-form select#contract_names {
               padding: 15px 10px;
               width: 100%;
               font-size: 14px;
               color: #002655;
               background: #fff;
               border-radius: 5px;
               border: none;
               outline: none;
               box-shadow: none;
          }
          form#contract-dropdown-form label {
               color: #ffffff;
               font-size: 25px;
               font-weight: 500;
               line-height: 24px;
               margin-right: 25px;
               margin-bottom: 9px;
          }
          .dropdown-form .form-group {
               position: relative;
               display: block;
               height: 7em;
               line-height: 3;
               overflow: hidden;
               border-radius: 0.25em;
               padding-bottom: 10px;
          }
          .dropdown-form select#contract_names {
               padding: 17px 14px;
               font-size: 18px;
          }
          button#select-submit-button, button.new-custom-buy {
               padding: 15px 40px;
               background: #002655;
               color: #ffffff;
          }
          .post-card-sec {
               padding: 50px;
               border: 1px solid;
               border-radius: 20px;
               margin: 50px 0px;
               background:#ffffff;
          }
          .lds-dual-ring {
               display: inline-block;
               width: 30px;
               height: 30px;
          }
          .lds-dual-ring:after {
               content: " ";
               display: block;
               width: 45px;
               height: 45px;
               margin: 5px;
               border-radius: 50%;
               border: 6px solid #ffffff;
               border-color: #ffffff transparent #ffffff transparent;
               animation: lds-dual-ring 1.2s linear infinite;
          }

          p.lt_gr {
               color: #b8b2b2;
               display: flex;
               gap: 7px;
               align-items: center;
          }
          p.lt_gr img
          {
               width:50px;
          }
          p.eco_desc {
               padding: 20px;
          }
          .eco_head {
               text-align: center;
          }
          .newdocasi .temp-img img {
               width: 55px !important;
          }
          section.eco_Section {
               background: #EDEEF1;
               text-align:center;
          }

          @keyframes lds-dual-ring {
          0% {
               transform: rotate(0deg);
          }
          100% {
               transform: rotate(360deg);
          }
          }
          @media only screen and (max-width: 768px){
               .elabora-wraapp {
                    margin: 50px 0 0 0;
               }
               .dropdown-form select#contract_names {
                    width: 100%;
               }
          }
          .elabora-wraapp ul li:before {
               content: "\f058";
               font-family: "Font Awesome 6 Free";
               font-weight: 900;
               left: 35px;
               position: absolute;
          }
          p.bl_sec {
               font-size: 18px;
          }
     </style>


     <section class="elabora-section p-100">
          <div class="container">
               <div class="row">
                    <div class="col-md-5">
                         <div class="gr_bg">
                              <div class="image_ftr"><img src="{{ asset('storage/'.$image->media->file_name) }}" /></div>
                         </div>
                         <div class="heateor_sss_sharing_container heateor_sss_horizontal_sharing" data-heateor-ss-offset="0" data-heateor-sss-href="https://documentos-legales.mx/elabora-tu-contrato-con-un-abogado/">
                              <div class="heateor_sss_sharing_ul">
                              <a aria-label="Facebook" class="heateor_sss_facebook" href="{{ $data['fb_link'] ?? '' }}" title="Facebook" rel="nofollow noopener" target="_blank" style="font-size: 32px !important; box-shadow: none; display: inline-block; vertical-align: middle;">
                                   <span class="heateor_sss_svg" 
                                        style="
                                             background-color: #0765fe;
                                             width: 42px;
                                             height: 42px;
                                             border-radius: 999px;
                                             display: inline-block;
                                             opacity: 1;
                                             float: left;
                                             font-size: 32px;
                                             box-shadow: none;
                                             display: inline-block;
                                             font-size: 16px;
                                             padding: 0 4px;
                                             vertical-align: middle;
                                             background-repeat: repeat;
                                             overflow: hidden;
                                             padding: 0;
                                             cursor: pointer;
                                             box-sizing: content-box;
                                        ">
                                        <svg style="display: block; border-radius: 999px;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32">
                                             <path
                                                  fill="#1E2C4F"
                                                  d="M28 16c0-6.627-5.373-12-12-12S4 9.373 4 16c0 5.628 3.875 10.35 9.101 11.647v-7.98h-2.474V16H13.1v-1.58c0-4.085 1.849-5.978 5.859-5.978.76 0 2.072.15 2.608.298v3.325c-.283-.03-.775-.045-1.386-.045-1.967 0-2.728.745-2.728 2.683V16h3.92l-.673 3.667h-3.247v8.245C23.395 27.195 28 22.135 28 16Z"
                                             ></path>
                                        </svg>
                                   </span>
                              </a>
                              <a aria-label="Twitter" class="heateor_sss_button_twitter" href="{{ $data['twitter_link'] ?? '' }}" title="Twitter" rel="nofollow noopener" target="_blank" style="font-size: 32px !important; box-shadow: none; display: inline-block; vertical-align: middle;">
                                   <span class="heateor_sss_svg heateor_sss_s__default heateor_sss_s_twitter"
                                        style="
                                             background-color: #55acee;
                                             width: 42px;
                                             height: 42px;
                                             border-radius: 999px;
                                             display: inline-block;
                                             opacity: 1;
                                             float: left;
                                             font-size: 32px;
                                             box-shadow: none;
                                             display: inline-block;
                                             font-size: 16px;
                                             padding: 0 4px;
                                             vertical-align: middle;
                                             background-repeat: repeat;
                                             overflow: hidden;
                                             padding: 0;
                                             cursor: pointer;
                                             box-sizing: content-box;
                                        ">
                                        <svg style="display: block; border-radius: 999px;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-4 -4 39 39">
                                             <path
                                                  d="M28 8.557a9.913 9.913 0 0 1-2.828.775 4.93 4.93 0 0 0 2.166-2.725 9.738 9.738 0 0 1-3.13 1.194 4.92 4.92 0 0 0-3.593-1.55 4.924 4.924 0 0 0-4.794 6.049c-4.09-.21-7.72-2.17-10.15-5.15a4.942 4.942 0 0 0-.665 2.477c0 1.71.87 3.214 2.19 4.1a4.968 4.968 0 0 1-2.23-.616v.06c0 2.39 1.7 4.38 3.952 4.83-.414.115-.85.174-1.297.174-.318 0-.626-.03-.928-.086a4.935 4.935 0 0 0 4.6 3.42 9.893 9.893 0 0 1-6.114 2.107c-.398 0-.79-.023-1.175-.068a13.953 13.953 0 0 0 7.55 2.213c9.056 0 14.01-7.507 14.01-14.013 0-.213-.005-.426-.015-.637.96-.695 1.795-1.56 2.455-2.55z"
                                                  fill="#1E2C4F"
                                             ></path>
                                        </svg>
                                   </span>
                              </a>
                              <a aria-label="Pinterest" class="heateor_sss_button_pinterest" href="{{ $data['pinterest_link'] ?? '' }}" target="_blank" title="Pinterest" rel="nofollow noopener" style="font-size: 32px !important; box-shadow: none; display: inline-block; vertical-align: middle;">
                                   <span class="heateor_sss_svg heateor_sss_s__default heateor_sss_s_pinterest"
                                        style="
                                             background-color: #cc2329;
                                             width: 42px;
                                             height: 42px;
                                             border-radius: 999px;
                                             display: inline-block;
                                             opacity: 1;
                                             float: left;
                                             font-size: 32px;
                                             box-shadow: none;
                                             display: inline-block;
                                             font-size: 16px;
                                             padding: 0 4px;
                                             vertical-align: middle;
                                             background-repeat: repeat;
                                             overflow: hidden;
                                             padding: 0;
                                             cursor: pointer;
                                             box-sizing: content-box;
                                        ">
                                        <svg style="display: block; border-radius: 999px;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-2 -2 35 35">
                                             <path
                                                  fill="#1E2C4F"
                                                  d="M16.539 4.5c-6.277 0-9.442 4.5-9.442 8.253 0 2.272.86 4.293 2.705 5.046.303.125.574.005.662-.33.061-.231.205-.816.27-1.06.088-.331.053-.447-.191-.736-.532-.627-.873-1.439-.873-2.591 0-3.338 2.498-6.327 6.505-6.327 3.548 0 5.497 2.168 5.497 5.062 0 3.81-1.686 7.025-4.188 7.025-1.382 0-2.416-1.142-2.085-2.545.397-1.674 1.166-3.48 1.166-4.689 0-1.081-.581-1.983-1.782-1.983-1.413 0-2.548 1.462-2.548 3.419 0 1.247.421 2.091.421 2.091l-1.699 7.199c-.505 2.137-.076 4.755-.039 5.019.021.158.223.196.314.077.13-.17 1.813-2.247 2.384-4.324.162-.587.929-3.631.929-3.631.46.876 1.801 1.646 3.227 1.646 4.247 0 7.128-3.871 7.128-9.053.003-3.918-3.317-7.568-8.361-7.568z"
                                             ></path>
                                        </svg>
                                   </span>
                              </a>
                              <a aria-label="Copy Link" class="heateor_sss_button_copy_link" title="Copy Link" rel="nofollow noopener" href="" onclick="event.preventDefault()" style="font-size: 32px !important; box-shadow: none; display: inline-block; vertical-align: middle;">
                                   <span class="heateor_sss_svg heateor_sss_s__default heateor_sss_s_copy_link"
                                        style="
                                             background-color: #ffc112;
                                             width: 42px;
                                             height: 42px;
                                             border-radius: 999px;
                                             display: inline-block;
                                             opacity: 1;
                                             float: left;
                                             font-size: 32px;
                                             box-shadow: none;
                                             display: inline-block;
                                             font-size: 16px;
                                             padding: 0 4px;
                                             vertical-align: middle;
                                             background-repeat: repeat;
                                             overflow: hidden;
                                             padding: 0;
                                             cursor: pointer;
                                             box-sizing: content-box;
                                        "
                                        >
                                        <svg style="display: block; border-radius: 999px;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-4 -4 40 40">
                                             <path
                                                  fill="#1E2C4F"
                                                  d="M24.412 21.177c0-.36-.126-.665-.377-.917l-2.804-2.804a1.235 1.235 0 0 0-.913-.378c-.377 0-.7.144-.97.43.026.028.11.11.255.25.144.14.24.236.29.29s.117.14.2.256c.087.117.146.232.177.344.03.112.046.236.046.37 0 .36-.126.666-.377.918a1.25 1.25 0 0 1-.918.377 1.4 1.4 0 0 1-.373-.047 1.062 1.062 0 0 1-.345-.175 2.268 2.268 0 0 1-.256-.2 6.815 6.815 0 0 1-.29-.29c-.14-.142-.223-.23-.25-.254-.297.28-.445.607-.445.984 0 .36.126.664.377.916l2.778 2.79c.243.243.548.364.917.364.36 0 .665-.118.917-.35l1.982-1.97c.252-.25.378-.55.378-.9zm-9.477-9.504c0-.36-.126-.665-.377-.917l-2.777-2.79a1.235 1.235 0 0 0-.913-.378c-.35 0-.656.12-.917.364L7.967 9.92c-.254.252-.38.553-.38.903 0 .36.126.665.38.917l2.802 2.804c.242.243.547.364.916.364.377 0 .7-.14.97-.418-.026-.027-.11-.11-.255-.25s-.24-.235-.29-.29a2.675 2.675 0 0 1-.2-.255 1.052 1.052 0 0 1-.176-.344 1.396 1.396 0 0 1-.047-.37c0-.36.126-.662.377-.914.252-.252.557-.377.917-.377.136 0 .26.015.37.046.114.03.23.09.346.175.117.085.202.153.256.2.054.05.15.148.29.29.14.146.222.23.25.258.294-.278.442-.606.442-.983zM27 21.177c0 1.078-.382 1.99-1.146 2.736l-1.982 1.968c-.745.75-1.658 1.12-2.736 1.12-1.087 0-2.004-.38-2.75-1.143l-2.777-2.79c-.75-.747-1.12-1.66-1.12-2.737 0-1.106.392-2.046 1.183-2.818l-1.186-1.185c-.774.79-1.708 1.186-2.805 1.186-1.078 0-1.995-.376-2.75-1.13l-2.803-2.81C5.377 12.82 5 11.903 5 10.826c0-1.08.382-1.993 1.146-2.738L8.128 6.12C8.873 5.372 9.785 5 10.864 5c1.087 0 2.004.382 2.75 1.146l2.777 2.79c.75.747 1.12 1.66 1.12 2.737 0 1.105-.392 2.045-1.183 2.817l1.186 1.186c.774-.79 1.708-1.186 2.805-1.186 1.078 0 1.995.377 2.75 1.132l2.804 2.804c.754.755 1.13 1.672 1.13 2.75z"
                                             ></path>
                                        </svg>
                                   </span>
                              </a>
                              <a class="heateor_sss_more" title="More" rel="nofollow noopener" style="font-size: 32px !important; border: 0; box-shadow: none; display: inline-block !important; font-size: 16px; padding: 0 4px; vertical-align: middle; display: inline;" href="" onclick="event.preventDefault()">
                                   <span
                                        class="heateor_sss_svg"
                                        style="
                                             background-color: #ee8e2d;
                                             width: 42px;
                                             height: 42px;
                                             border-radius: 999px;
                                             display: inline-block !important;
                                             opacity: 1;
                                             float: left;
                                             font-size: 32px !important;
                                             box-shadow: none;
                                             display: inline-block;
                                             font-size: 16px;
                                             padding: 0 4px;
                                             vertical-align: middle;
                                             display: inline;
                                             background-repeat: repeat;
                                             overflow: hidden;
                                             padding: 0;
                                             cursor: pointer;
                                             box-sizing: content-box;
                                        "
                                   >
                                        <svg
                                             xmlns="http://www.w3.org/2000/svg"
                                             xmlns:xlink="http://www.w3.org/1999/xlink"
                                             viewBox="-.3 0 32 32"
                                             version="1.1"
                                             width="100%"
                                             height="100%"
                                             style="display: block; border-radius: 999px;"
                                             xml:space="preserve"
                                        >
                                             <g><path fill="#1E2C4F" d="M18 14V8h-4v6H8v4h6v6h4v-6h6v-4h-6z" fill-rule="evenodd"></path></g>
                                        </svg>
                                   </span>
                              </a>
                              </div>
                              <div class="heateorSssClear"></div>
                         </div>
                    </div>

                    <div class="col-md-7">
                         <div class="elabora-wraapp">
                              <div class="page-heading">
                              <h1>{{ $data['title_name'] ?? '' }}</h1>
                              <!-- <div class="check_with_text single_mobile_check_with_text"><img src="/wp-content/uploads/2023/05/confirmation.svg" alt="" />Para Contratos Altamente Personalizados</div> -->

                              <?php print_r($data['short_description']) ?>
                              <p></p>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </section>

     <section class="topic_sl">
          <div class="topic-select container">
               <div class="dropdown-form">
                    <form class="dropdwn-contract-form" id="contract-dropdown-form" data-gtm-form-interact-id="0">
                         <div class="drp-dwn-form">
                              <div class="form-group">
                              <label for="contract_name">Selecciona el Contrato a Elaborar con nuestro Abogado:</label><br />
                              <select name="contract_name" id="contract_names" data-gtm-form-interact-field-id="0">
                                   <option value=""> Seleccionar contrato </option>
                                   @if(isset($products) && $products != null)
                                   @foreach($products as $product)
                                        <option value="{{ $product->id ?? '' }}">{{ $product->name ?? '' }}</option>
                                   @endforeach
                                   @endif
                              </select>
                              </div>
                         </div>
                    </form>
               </div>
               <div class="response_sr">
                    <div class="post-card-sec doc-post-card"><p class="lt_gr">Para conocer los detalles y el costo, selecciona el tipo de contrato que deseas elaborar en el menú desplegable de arriba.</p></div>
               </div>
          </div>
     </section>

     <section class="selected-doc-text-sec p-100">
          <div class="container">
               <div class="selected-doc-text-content">
                    <div class="row">
                         <div class="col-lg-9 col-md-9 col-sm-12">
                              <div class="select-doc-left-content">
                              <div class="select-doc-text">
                                   <?php print_r($data['description']) ?>
                              </div>
                              </div>
                         </div>
                         <div class="col-lg-3 col-md-3 col-sm-12">
                              <div class="selected-doc-right newdocasi">
                                   <h3>Así funciona</h3>
                              @if(isset($work_sec) && $work_sec != null)
                                   @foreach($work_sec as $work)
                                   <div class="temp-box">
                                        <div class="temp-img">
                                             <img src="{{ asset('storage/'.$work->media->file_name) }}" alt="" />
                                        </div>
                                        <div class="temp-text">
                                             <div class="temp-head">
                                                  <h5>{{ $work->contract_work->header ?? '' }}</h5>
                                             </div>
                                             <div class="choose">
                                                  <small>{{ $work->contract_work->description ?? '' }}</small>
                                             </div>
                                        </div>
                                   </div>
                                   @endforeach
                              @endif
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </section>
     <section class="eco_Section p-100">
          <div class="container">
               <h2 class="eco_head">{{ $data['economical_main_heading'] ?? '' }}</h2>
               <p class="eco_desc bl_sec">
                    <?php print_r($data['economical_description']) ?>
               </p>
               <a class="eco_btn" href="{{ $data['button_link'] ?? '' }}">{{ $data['button_text'] ?? '' }}</a>
          </div>
     </section>
</div>

<script>
     $(document).ready(function(){
          $('#contract_names').on('change',function(){
               if($(this).val() !== "" && $(this).val() !== null && $(this).val() !== undefined){
                    var data = {
                         id: $(this).val(),
                         _token: "{{ csrf_token() }}"
                    }
                    $.ajax({
                         url: "{{ url('/get-contract') }}",
                         type: "post",
                         data: data,
                         dataType: "json",
                         success: function(response){
                              if(response.status == 200){
                                   var html = `<div class="post-card-sec doc-post-card">
                                                  <h2>contract lawyer 2</h2>
                                                  <h4 class="pop_price">$${response.data.regular_price}<span>pesos mexicanos</span></h4>
                                                  <p>${response.data.product_description}</p>
                                                  <a href=""><button type="button" data-pro-id="" class="chnghvr new-custom-buy cta">Seleccionar</button></a>
                                             </div>`
                                   $('.response_sr').html(html);
                              }
                         }
                    })
               }else{
                    $('.response_sr').html('<div class="post-card-sec doc-post-card"><p class="lt_gr">Para conocer los detalles y el costo, selecciona el tipo de contrato que deseas elaborar en el menú desplegable de arriba.</p></div>');
               }
               
          })
     })
</script>

@endsection