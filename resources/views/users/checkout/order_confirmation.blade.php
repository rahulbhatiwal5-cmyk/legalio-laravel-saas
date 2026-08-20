@extends('users_layout.master') 
@section('content')

@php
    $is_free_trial = \App\Models\Subscription::where('user_id', auth()->id())
                        ->where('status', 'trialing')
                    //     ->where('end_date', '>=', now())
                        ->exists();
                        
    $document_id = Session::get('document_id') ?? '';
    $document    = $document_id ? \App\Models\Document::find($document_id) : null;
    
@endphp

<section class="odr_sec">
     <div class="container">
          <div class="cnfrm-odr">
               <div class="odr_img">
                    <img src="{{ asset('assets/img/oc.png') }}" alt="order-confirmed">
               </div>
               <div class="odr_txt">
                    <h2>Gracias</h2>
                    <h6>Confirmación del pedido enviada a su correo electrónico.</h6>
               </div>

     

               {{-- ✅ View Document Button (Free Trial) --}}
               @if($is_free_trial && $document)
               <div class="odr_btn">
                    <a href="{{ route('free.trial.view', $document->slug) }}"
                       class="cta_wyt"
                       style="background:#012555; border-color:#012555; color:#fff;">
                        View Document
                    </a>
               </div>
               @endif
               <br>

               {{-- ✅ Download Button (dono ke liye) --}}
               <div class="odr_btn">
                    <a href="javascript:void(0);" 
                       class="cta_wyt {{ $is_free_trial ? 'free_trial_download_btn' : 'generate_pdf' }}">
                       Descargar documento
                    </a>
               </div>

               <div class="odr_btn2" style="padding:20px;">
                    <a href="javascript:void(0);" class="cta_wyt go_to_dashboard">
                         Ir al panel
                    </a>
               </div>

               <input type="hidden" id="document_id" value="{{ $document_id }}">
          </div>
     </div>
</section>

{{-- ✅ Upgrade Modal (Free Trial Download Block) --}}
@if($is_free_trial)
<div class="modal fade" id="upgrade_modal" tabindex="-1">
     <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content" style="border-radius:16px; padding:10px;">
               <div class="modal-header border-0">
                    <h5 class="modal-title" style="color:#002655; font-weight:700;">
                         Download Not Available
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
               </div>
               <div class="modal-body">
                    <p style="color:#555; font-size:14px;">
                         During your free trial, downloading is not available.
                         Choose a plan to download your document instantly.
                    </p>

                    {{-- Plans --}}
                    @php
                         $upgrade_plans = \App\Models\Plans::orderBy('number_of_months')->get();
                         $currency_symbol = optional(web_setting('country_currency_symbol'))->value ?? '$';
                    @endphp

                    <div class="upgrade_plans_list">
                         @foreach($upgrade_plans as $uplan)
                                             @if($uplan->number_of_months != 24)

                         <div class="upgrade_plan_item" 
                              onclick="goToUpgrade({{ $uplan->id }}, {{ $uplan->number_of_months }})"
                              style="border:1.5px solid #e0e4ed; border-radius:10px; 
                                     padding:14px 16px; margin-bottom:10px; 
                                     cursor:pointer; transition:all 0.2s;">
                              <div style="display:flex; justify-content:space-between; align-items:center;">
                                   <div>
                                        <div style="font-weight:600; color:#002655; font-size:15px;">
                                             {{ $uplan->number_of_months }} 
                                             {{ $uplan->number_of_months == 1 ? 'Month' : 'Months' }}
                                             
                                        </div>
                                        <div style="font-size:12px; color:#888;">
                                             Cancel anytime
                                        </div>
                                        
                                   </div>
                                   <div style="font-weight:700; color:#002655; font-size:18px;">
                                        {{ $currency_symbol }}{{ number_format($uplan->price, 2) }}/mo

                                   </div>
                                   @endif
                              </div>
                         </div>
                         @endforeach
                    </div>
               </div>
          </div>
     </div>
</div>
@endif

<script>
$(document).ready(function(){

     // ── Download PDF (Paid user) ──
     $('.generate_pdf').click(function(e){
          e.preventDefault();
          let isLogin = @json(auth()->check()); 
          if(isLogin){
               let document_id = $('#document_id').val();
               let url = "{{ url('/') }}/generate-pdf?id=" + document_id;
               location.href = url;
          }
     });

     // ── Download click (Free Trial) → Modal dikhao ──
     $('.free_trial_download_btn').click(function(e){
          e.preventDefault();
          $('#upgrade_modal').modal('show');
     });

     // ── Dashboard ──
     $('.go_to_dashboard').click(function(e){
          e.preventDefault();
          let isLogin = @json(auth()->check()); 
          if(isLogin){
               location.href = "{{ url('/account') }}";
          }
     });

     // ── Hover effect on plan cards ──
     $('.upgrade_plan_item').hover(
          function(){ $(this).css({'border-color':'#002655', 'background':'#f5f7fa'}); },
          function(){ $(this).css({'border-color':'#e0e4ed', 'background':'#fff'}); }
     );
});

// ── Go to checkout with selected plan ──
function goToUpgrade(planId, months) {
     let document_id = $('#document_id').val();
     window.location.href = "{{ url('/checkout') }}"
          + '?type=sub'
          + '&plan_id=' + planId
          + '&months='  + months
          + '&document_id=' + document_id
          + '&cancel_free_trial=1';
}
</script>

@endsection