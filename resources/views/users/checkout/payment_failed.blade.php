@extends('users_layout.master')
@section('content')

<section class="odr_sec">
     <div class="container">
          <div class="cnfrm-odr">
               <div class="odr_img">
                    <img src="{{ asset('assets/img/oc.png') }}" alt="order-confirmed">
               </div>
               <div class="odr_txt">
                    <h2>Failed</h2>
                    <h6>
                         Payment is failed now
                    </h6>
               </div>
               
          </div>
     </div>
</section>

<script>
     $(document).ready(function(){
          $('.generate_pdf').click(function(e){
               e.preventDefault();

               let isLogin = @json(auth()->check());
               if(isLogin){
                    let document_id = $('#document_id').val();
                    let baseUrl = "{{ url('/') }}";
                    let url = baseUrl + "/generate-pdf?id=" + document_id;
                    location.href = url;
               }
          })

          $('.go_to_dashboard').click(function(e){
               e.preventDefault();

               let isLogin = @json(auth()->check());
               if(isLogin){
                    // let url = "{{ url('/user-dashboard') }}";
                    let url = "{{ url('/account') }}";
                    location.href = url;
               }
          })
     })
</script>

@endsection
