@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="nk-block-head">
          <div class="nk-block-head-content">
              <h4 class="nk-block-title">@if(isset($id)) Edit Plan @else Add Plan @endif</h4>
          </div>
      </div>
     <div class="container-fluid">
          <form id="subscription-plan" action="{{ route('admin.add.subscription.plan') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" id="plan_id" name="plan_id" value="{{ $id ?? '' }}">
               <div class="card card-bordered card-preview subsc_card">
                    <div class="card-inner">
                         <!-- <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="">Select Interval</label>
                                   <select class="form-select js-select2" id="interval" name="interval" data-search="on" data-placeholder="Select Interval">
                                        <option value="">Select</option>
                                        @if(isset($plan))
                                             @php $interval = $plan->interval ?? '' @endphp
                                             @if($interval == 'monthly')
                                             <option value="monthly" selected>Monthly</option>
                                             <option value="yearly">Yearly</option>
                                             @elseif($plan->interval == 'yearly')
                                             <option value="monthly">Monthly</option>
                                             <option value="yearly" selected>Yearly</option>
                                             @endif
                                        @else
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                        @endif
                                   </select>
                              </div>
                              <span class="text text-danger" id="error" style="display:none;">This field is required</span>
                         </div> -->
                         <!-- <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="document_count">Number of Document Credit</label>
                                   <input type="number" class="form-control" id="document_count" name="document_count" placeholder="Enter Document Count" value="{{ $plan->credit ?? '' }}">
                              </div>    
                              <span class="text text-danger" id="error_count" style="display:none;">This field is required</span>
                         </div> -->

                         <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="number_of_months">Number of Months</label>
                                   <input type="number" class="form-control" id="number_of_months" name="number_of_months" placeholder="Enter Number of Months" value="{{ $plan->number_of_months ?? '' }}">
                              </div>    
                              <span class="text text-danger" id="error_months" style="display:none;">This field is required</span>
                         </div>
                         <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="plan_amount">Price</label>
                                   <input type="number" class="form-control" id="plan_amount" name="plan_amount" placeholder="Enter Price" value="{{ $plan->price ?? '' }}">                   
                              </div>  
                              <span class="text text-danger" id="error_plan_amount" style="display:none;">This field is required</span>
                         </div>
                         <!-- <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="discount_price">Discount Price</label>
                                   <input type="number" class="form-control" id="discount_price" name="discount_price" placeholder="Enter Discount Price" value="{{ $plan->discount_price ?? '' }}">                   
                              </div>  
                              <span class="text text-danger" id="error_plan_amount" style="display:none;">This field is required</span>
                         </div>                    -->
                    </div>
               </div>
               <div class="mt-3">
                    <button class="btn btn-primary submitform" type="button">@if(isset($id)) Update  @else Save @endif</button>
               </div>
          </form>
     </div>
</div>


<script>

     $(document).ready(function() {
          // Attach input listeners once
          $('#interval').on('change', function() {
               $('#error').hide();
          });
          
          $('#document_count').on('input', function() {
               $('#error_count').hide();
          });
          $('#plan_amount').on('input', function() {
               $('#error_plan_amount').hide();
          });

          $('.submitform').click(function(e) {
               e.preventDefault(); // Prevent default form submission

               var interval = $('#interval').val();
               var document_count = $('#document_count').val();
               var plan_amount = $('#plan_amount').val();

               var isValid = true;

               if (interval === '') {
                    $('#error').show();
                    isValid = false;
               } else {
                    $('#error').hide();
               }

               if (document_count === '') {
                    $('#error_count').show();
                    isValid = false;
               } else {
                    $('#error_count').hide();
               }

               if (plan_amount === '') {
                    $('#error_plan_amount').show();
                    isValid = false;
               } else {
                    $('#error_plan_amount').hide();
               }

               // Only submit the form if all fields are valid
               if (isValid) {
                    $('#subscription-plan').submit();
               }
          });
     });

</script>

@endsection
