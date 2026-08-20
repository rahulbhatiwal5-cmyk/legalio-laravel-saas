@extends('users_layout.master')
@section('content')

<section class="odr_sec">
     <div class="container">
          <div class="cnfrm-odr">
               <div class="odr_img">
                    <img src="{{ asset('assets/img/oc.png') }}" alt="order-confirmed">
               </div>
               <div class="odr_txt">
                    <h2>Gracias</h2>
                    <h6>
                         Confirmación del pedido enviada a su correo electrónico.
                    </h6>
               </div>
               <div class="odr_btn">
                    <a href="javascript:void(0);" class="cta_wyt generate_pdf">Descargar documento</a>
               </div>
               <div class="odr_btn2" style="padding:20px;">
                    <a href="javascript:void(0);" class="cta_wyt go_to_dashboard">Ir al panel</a>
               </div>
               <input type="hidden" id="document_id" value="{{ Session::get('document_id') ?? '' }}">
          </div>
     </div>
</section>

<script>
     $(document).ready(function(){
          $('.generate_pdf').click(function(e){
               e.preventDefault();
               console.log($('#document_id').val());
               
               // let isLogin = @json(auth()->check()); 
               // if(isLogin){
               //      let document_id = $('#document_id').val();
               //      let baseUrl = "{{ url('/') }}";
               //      let url = baseUrl + "/generate-pdf?id=" + document_id;
               //      location.href = url;
               // }
          })

          $('.go_to_dashboard').click(function(e){
               e.preventDefault();

               // let isLogin = @json(auth()->check()); 
               // if(isLogin){
                    // let url = "{{ url('/user-dashboard') }}";
                    let url = "{{ url('/account') }}";
                    location.href = url;
               // }
          })
     })
</script>

@endsection