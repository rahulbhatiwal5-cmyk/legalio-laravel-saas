
@extends('users_layout.master')
@section('title',$data['meta_title'])
@section('content')

<section class="price_banner inner-banner">
     <div class="container">
          <div class="row align-items-center">
               <div class="col-md-7">
                    <div class="banner_content">
                    </div>
               </div>
               <div class="col-md-5">
                    <div class="banner_img">
                    </div>
               </div>
          </div>
    </div>
</section>
<section class="sub_payment p_120">
     <div class="conatiner_payment">
          <div class="row align-items-center">
               <div class="col-lg-6 rec_price_card">
                    <div class="sub_payment_1">
                         <div class="header_sub_1">
                              {{-- <p>{{ $data['subscription_title'] ?? 'Mejor oferta' }}</p> --}}
                              <p>{{ 'Best Offer' }}</p>
                         </div>

                         <div class="inner_part_sub_paymem">
                              <div class="Subscrip subsc_heding">
                                   <p>
                                        {{-- {{ $data['subscription_heading'] ?? 'Suscripción' }}  --}}
                                        {{-- {{ 'Unlimited Contracts Subscription' }}  --}}
                                        {{ 'Unlimited Contracts' }}
                                        <!-- <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="_G_eWZ _dnXfu"><path d="M7.206 5.265C7.09 5.405 7 5.65 7 6v.5H5V6c0-.649.16-1.404.67-2.015C6.204 3.342 7.01 3 8 3s1.795.342 2.33.985c.51.61.67 1.366.67 2.015 0 .763-.155 1.367-.467 1.862-.287.455-.665.737-.904.916L9.6 8.8c-.264.198-.366.286-.442.406a.997.997 0 00-.131.395C8.993 9.82 8.82 10 8.6 10H7.4a.377.377 0 01-.385-.4c.046-.579.196-1.057.452-1.462.287-.455.665-.737.904-.916L8.4 7.2c.264-.198.366-.286.442-.406C8.904 6.695 9 6.487 9 6c0-.351-.09-.596-.206-.735C8.705 5.158 8.51 5 8 5s-.705.158-.794.265z" fill="#696969"></path><path d="M7.951 13a1 1 0 100-2 1 1 0 000 2z" fill="#696969"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M8 16A8 8 0 108 0a8 8 0 000 16zm0-2A6 6 0 108 2a6 6 0 000 12z" fill="#696969"></path></svg> -->
                                        {{-- <span class="recommended">{{ $data['recommended_text'] ?? 'RECOMENDADO' }}</span> --}}
                                        <span class="recommended">{{ 'RECOMMENDED' }}</span>
                                        <div class="svg_pointer_box">
                                             Full access to all documents. Cancel anytime.
                                        </div>
                                   </p>
                                   <div class="discreption">
                                        <!-- <p>{{ $data['subscription_description'] ?? 'Save with a Suscripción' }}</p> -->
                                   </div>
                              </div>
                              <div class="body_sub_1 sub_content">
                                   <!-- <div class="tabs">
                                        <div class="tab active" data-tab="monthly">{{ $data['monthly_text'] ?? 'Mensual' }}</div>
                                        <div class="tab" data-tab="upfront">
                                             {{ $data['yearly_text'] ?? 'Anual Upfront' }}
                                             <span class="save">{{ $data['ahorra_text'] ?? 'ahorra 19%' }}</span>
                                        </div> -->
                                        <!-- <div class="tab" data-tab="monthly2">Month-to-Month</div> -->
                                   <!-- </div> -->
                                   <div class="tab-content active" id="monthly">
                                        <div class="image_p">
                                             <!-- @if($monthly_plans->isEmpty())
                                                  <p style="text-align:center;">No Plans Available</p>
                                             @else
                                             <div class="plan-selector">
                                                  @foreach($monthly_plans as $m_plan)
                                                       @if($loop->first)
                                                       <div class="plan-option active" data-count="{{ $m_plan->credit ?? '' }}" data-price="{{ $m_plan->price ?? '' }}" data-month="{{ $m_plan->price ?? '' }}">{{ $m_plan->credit ?? '' }}</div>
                                                       @else
                                                       <div class="plan-option" data-count="{{ $m_plan->credit ?? '' }}" data-price="{{ $m_plan->price ?? '' }}" data-month="{{ $m_plan->price ?? '' }}">{{ $m_plan->credit ?? '' }}</div>
                                                       @endif
                                                  @endforeach
                                             </div> -->

                                             <div class="price_main">
                                                  <div class="plan_perid">
                                                       <label id="period">Period</label>
                                                       <select class="form-select" id="months" name="months">
                                                            @foreach($plans as $plan)
                                                                 @if($plan->number_of_months == 24)
                                                                 <option value="{{ $plan->number_of_months ?? '' }}" data-id="{{ $plan->id ?? '' }}" data-price="{{ $plan->price ?? '' }}" selected>
                                                                      {{ $plan->number_of_months }} {{ $plan->number_of_months == 1 ? 'month' : 'months' }}
                                                                 </option>
                                                                 @else
                                                                 <option value="{{ $plan->number_of_months ?? '' }}" data-id="{{ $plan->id ?? '' }}" data-price="{{ $plan->price ?? '' }}">
                                                                      {{ $plan->number_of_months }} {{ $plan->number_of_months == 1 ? 'month' : 'months' }}
                                                                 </option>
                                                                 @endif
                                                            @endforeach
                                                       </select>
                                                  </div>
     
                                                  {{-- <div class="note">{{ $data['subscription_note'] ?? 'Las descargas no utilizadas se acumulan' }}</div> --}}
                                                  {{-- <div class="price imagePrice" id="current_price">$ {{ number_format($discount_price,2 ?? '') }} <span>/{{ $data['per_month_text'] ?? 'al mes' }}</span></div>
                                                  <div class="price imagePrice" id="old_price">$ {{ $month_price ?? '' }} /{{ $data['per_month_text'] ?? 'al mes' }}</div> --}}
                                                  <div class="price_image_wrapper">
                                                       <div class="price imagePrice" id="current_price">{{ $currency_symbol }} {{ number_format($discount_price,2 ?? '') }}<span>/month</span></div>
                                                       <div class="price imagePrice" id="old_price">{{ $currency_symbol }} {{ $month_price ?? '' }}<span>/month</span></div>
                                                  </div>
                                             </div>
                                             <div class="check_list">
                                                  <ul class="unlmted_pts">
                                                       
                                                       <li>
                                                            <img src="{{ asset('assets/img/check.png') }}">
                                                            <span class="chk_list_heading" >All-Inclusive Access:</span>
                                                            All documents included, instant downloads (PDF, Word, Pages), unlimited editing, always updated with latest legal changes.
                                                       </li>
                                                  </ul>
                                             </div>
                                             <div class="btn-holder">
                                                  {{-- <button class="cta_org">{{ $data['subscription_btn_text'] ?? 'Suscribirse ahora' }}</button> --}}
                                                  <button class="cta_org">{{ 'Subscribe Now' }}</button>

                                             </div>
                                             <!-- @endif -->
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
               <div class="col-lg-6 othr_price_card">
                    <div class="sub_payment_1 sub_payment_2">
                         <div class="inner_part_sub_paymem">
                              <div class="Subscrip">
                                   <p>
                                        {{-- {{ $data['one_time_heading'] ?? 'Pago único' }} --}}
                                        {{ 'Single Document Purchase' }}
                                        <!-- <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="_G_eWZ _dnXfu"><path d="M7.206 5.265C7.09 5.405 7 5.65 7 6v.5H5V6c0-.649.16-1.404.67-2.015C6.204 3.342 7.01 3 8 3s1.795.342 2.33.985c.51.61.67 1.366.67 2.015 0 .763-.155 1.367-.467 1.862-.287.455-.665.737-.904.916L9.6 8.8c-.264.198-.366.286-.442.406a.997.997 0 00-.131.395C8.993 9.82 8.82 10 8.6 10H7.4a.377.377 0 01-.385-.4c.046-.579.196-1.057.452-1.462.287-.455.665-.737.904-.916L8.4 7.2c.264-.198.366-.286.442-.406C8.904 6.695 9 6.487 9 6c0-.351-.09-.596-.206-.735C8.705 5.158 8.51 5 8 5s-.705.158-.794.265z" fill="#696969"></path><path d="M7.951 13a1 1 0 100-2 1 1 0 000 2z" fill="#696969"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M8 16A8 8 0 108 0a8 8 0 000 16zm0-2A6 6 0 108 2a6 6 0 000 12z" fill="#696969"></path></svg> -->
                                        <div class="svg_pointer_box">
                                             Pay once, download instantly. No recurring fees.
                                        </div>
                                   </p>
                                   <div class="discreption">
                                        <!-- <p>{{ $data['one_time_description'] ?? 'No Suscripción' }}</p> -->
                                   </div>
                              </div>
                              <div class="body_sub_2 body_sub_1">
                                   <div class="tab-content active" id="monthly_4">
                                        <div class="image_p">
                                             <div class="single_price_wrapper">
                                                  <div class="product_div">
                                                       <div class="form-group">
                                                            <select class="form-select productSelect" id="productSelect">
                                                                 @if($documents->isEmpty())
                                                                      <option value="">No documents available</option>
                                                                 @endif
                                                                 @foreach($documents as $document)
                                                                      @if($loop->first)
                                                                           <option value="{{ $document->id ?? '' }}" data-slug="{{ $document->slug ?? '' }}" selected>{{ $document->title ?? '' }}</option>
                                                                      @else
                                                                           <option value="{{ $document->id ?? '' }}" data-slug="{{ $document->slug ?? '' }}">{{ $document->title ?? '' }}</option>
                                                                      @endif
                                                                 @endforeach
                                                            </select>
                                                       </div>
                                                  </div>
                                                  {{-- <div class="note">{{ $data['one_time_price_note'] ?? 'Sin cobros recurrentes. Solo pagas por un documento.' }}</div> --}}
                                                  {{-- single document price section --}}
                                                  <div class="price document_price" id="imagePrice">{{ $currency_symbol }} {{ number_format($default_price, 2) }}</div>
                                                  {{-- @if(is_null($first_document_price))
                                                       <div class="price document_price" id="imagePrice">$ {{ number_format($default_price, 2) }}</div>
                                                  @else
                                                       <div class="price document_price" id="imagePrice">$ {{ number_format($first_document_price, 2) }}</div>
                                                  @endif --}}
                                             </div>
                                             {{-- single document price section --}}
                                             {{-- <div class="price document_price" id="imagePrice">$ {{ number_format($default_price, 2) }}</div> --}}
                                             {{-- @if(is_null($first_document_price))
                                                  <div class="price document_price" id="imagePrice">$ {{ number_format($default_price, 2) }}</div>
                                             @else
                                                  <div class="price document_price" id="imagePrice">$ {{ number_format($first_document_price, 2) }}</div>
                                             @endif --}}
                                             <div class="one_check_list">
                                                  <ul class="unlmted_pts">
                                                       {{-- <li><img src="{{ asset('assets/img/check.png') }}">Full access to your selected document</li>
                                                       <li><img src="{{ asset('assets/img/check.png') }}">Instant download in PDF, Word & Pages</li>
                                                       <li><img src="{{ asset('assets/img/check.png') }}">1 online document edit included</li>
                                                       <li><img src="{{ asset('assets/img/cross.png') }}">No future legal updates</li>
                                                       <li><img src="{{ asset('assets/img/cross.png') }}">No access to other documents</li> --}}
                                                       <li>
                                                            <img src="{{ asset('assets/img/cross.png') }}">
                                                            <span class="chk_list_heading" >No Full Access: </span>
                                                            Does not include other documents, unlimited editing, or future legal updates.
                                                       </li>
                                                  </ul>
                                             </div>
                                             <div class="btn-holder crear_documento">
                                                  <button class="cta_org" onclick="goToDetailPage()">{{ 'Create Document' }}</button>
                                             </div>
                                             
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</section>

<section class="faq_sec p_120" style="background-color:#EDEEF1;">
     <div class="help_last_sec">
          <div class="container">
               <div class="help_main_faq">
               <div class="help_faq">
                    <h2 class="b-dark">
                         {{ $data['faq_heading'] ?? 'Frequently Asked Questions' }}
                    </h2>
                    <p>{{ $data['faq_description'] ?? '' }}</p>
               </div>
               <div class="accordion accordion-flush" id="accordionExample">
                    @if(isset($price_faq) && $price_faq != null)
                    @foreach($price_faq as $faq)
                    <div class="accordion-item">
                         <h6 class="accordion-header" id="heading{{ $loop->iteration ?? '' }}">
                              <button class="{{ $loop->first ? 'accordion-button':'accordion-button collapsed' }}" type="button" data-bs-toggle="collapse"
                                   data-bs-target="#collapse{{ $loop->iteration ?? '' }}" aria-expanded="{{ $loop->first ? 'true':'false' }}" aria-controls="collapse{{ $loop->iteration ?? '' }}">
                                   {{ $faq->question ?? '' }}
                              </button>
                         </h6>
                         <div id="collapse{{ $loop->iteration ?? '' }}" class="{{ $loop->first ? 'accordion-collapse collapse show':'accordion-collapse collapse' }}" aria-labelledby="heading{{ $loop->iteration ?? '' }}"
                              data-bs-parent="#accordionExample">
                              <div class="accordion-body">
                                   <?php
                                   $answer = strip_tags($faq->answer);
                                   print_r($answer);
                                   ?>
                              </div>
                         </div>
                    </div>
                    @endforeach
                    @endif
                    <div class="faq-view-more">
                         {{-- <a href="{{ url('/preguntas-frecuentes') }}" class="cta_org">Ver más</a> --}}
                         <a href="{{ url('/faq') }}" class="cta_org">Ver más</a>
                    </div>
               </div>
               </div>
          </div>
     </div>
</section>
<script>
    window.currencySymbol = @json($currency_symbol);
</script>
<script>
     function goToDetailPage(){
          var selectedDocument = document.querySelector('.productSelect').selectedOptions[0].getAttribute('data-slug');
          // console.log(selectedDocument);
          if (selectedDocument) {
               var slug = selectedDocument;
               var url = "{{ route('get.document', ':slug') }}".replace(':slug', slug);
               window.location.href = url;
          } else {
               alert('Please select a document first.');
          }
     }
</script>
<script>
     $(document).ready(function() {
          $('#menuToggle').click(function() {
               $('#sidebar').toggleClass('open');
          });
     });

     $(document).ready(function() {
          // Delegate event for all .productSelect dropdowns
          $('.productSelect').change(function() {
               var $this = $(this);
               var selectedValue = $this.val();
               var parentTab = $this.closest('.tab-content'); // Get parent .tab-content

               if (selectedValue) {
                    $.ajax({
                         url: "{{ route('document.price') }}",
                         type: 'POST',
                         dataType: 'json',
                         data: {
                              id: selectedValue,
                              _token: '{{ csrf_token() }}'
                         },
                         success: function(data) {
                              // Update image price text node
                              parentTab.find('.document_price').contents().filter(function() {
                                   return this.nodeType === 3;
                              }).first().replaceWith(currencySymbol + ' ' + parseFloat(data.doc_price).toFixed(2) + ' ');


                              // Update monthly price
                              // parentTab.find('.monthly').text('$' + data.monthly_price + ' /month');
                         },
                         error: function(xhr, status, error) {
                              console.error('Error fetching price:', error);
                         }
                    });
               } else {
                    // Reset values if no selection
                    parentTab.find('.document_price').contents().filter(function() {
                         return this.nodeType === 3;
                    }).first().replaceWith('$0.00 ');

                    parentTab.find('.monthly').text('$0.00 /al mes');
               }
          });
     });
</script>

<script>
     $(document).ready(function(){
          $('#months').on('change', function(){
               let selectedMonths = $(this).val();
               let selectedOption = $(this).find('option:selected');

               let planId = selectedOption.data('id');
               let price = selectedOption.data('price');

               var data = {
                    number_of_months: selectedMonths,
                    plan_id: planId,
                    price: price,
                    _token: "{{ csrf_token() }}"
               }

               $.ajax({
                    url: "{{ route('get.plan.price') }}",
                    type: "post",
                    data: data,
                    dataType: "json",
                    success: function(response){
                         if(response){
                              if(response.success == true){
                                   var discount_price = parseFloat(response.discount_price).toFixed(2);
                                   var price = parseFloat(response.price).toFixed(2);
                                   if(selectedMonths == 1){
                                        $('#old_price').hide();
                                        $('#current_price').html(currencySymbol + ' ' + price + '<span>/month</span>');
                                   }else if(selectedMonths == 12){
                                        $('#old_price').show();
                                        $('#current_price').html(currencySymbol + ' ' + discount_price + '<span>/month</span>');
                                        $('#old_price').html(currencySymbol + ' ' + price + '<span>/month</span>');
                                   }else if(selectedMonths == 24){
                                        $('#old_price').show();
                                        $('#current_price').html(currencySymbol + ' ' + discount_price + '<span>/month</span>');
                                        $('#old_price').html(currencySymbol + ' ' + price + '<span>/month</span>');
                                   }
                              }
                         }
                    }
               })
          });
     })
</script>
@endsection
