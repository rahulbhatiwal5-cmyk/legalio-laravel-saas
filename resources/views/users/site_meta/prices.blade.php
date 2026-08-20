@extends('users_layout.master')
@section('content')

<!-- ********(banner-sec)*********** -->

<section class="banner_sec dark inner-banner"
     style="background-image: url('{{ asset('storage/' . $data['background_image'] ?? '') }}');">
     <div class="container banner-col-width">
          <div class="row align-items-center">
               <div class="col-md-6 banner-col">
                    <div class="banner_content">
                         <h1>{{ $data['banner_title'] ?? 'Precios' }}</h1>
                         <p>
                              {{ $data['banner_description'] ?? 'Creemos en ofrecer precios claros y asequibles para soluciones legales de alta calidad. Ya
                         sea que sea un individuo, una pequeña empresa o una empresa más grande, tenemos un plan
                         diseñado para satisfacer sus necesidades específicas.' }}
                         </p>
                    </div>
               </div>
               <div class="col-md-6 banner-col">
                    <img src="{{ asset('storage/' . $data['banner_image']) }}" alt="Así funciona">
               </div>
          </div>
     </div>
</section>

<!-- table section start //////////////////////////////////////// -->


<div class="out_side_table p_120">
     <div class="container">
          <div class="row">
               <div class="insde_tab_box">
                    <div class="hed_tb">
                         <div class="tbh1">
                              {{ $data['document_heading'] ?? 'Documento' }}
                         </div>
                         <div class="tbh3">
                              {{ $data['price_heading'] ?? 'Precio' }}
                         </div>
                    </div>
                    @if(isset($document) && $document != null)
                         @foreach($document as $content)
                         <a href="" class="flex_tb">
                              <div class="tr_data">
                                   {{ $content->title ?? '' }}
                              </div>
                              <div class="sec_tbprice">
                                   ${{ $content->doc_price ?? '' }}
                              </div>
                         </a>
                         @endforeach
                    @endif
               </div>
          </div>
     </div>
</div>


@endsection