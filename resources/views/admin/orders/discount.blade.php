@extends('admin_layout.master')
@section('content')

<div class="nk-content">
     <div class="nk-block-head">
          <div class="nk-block-head-content">
              <h4 class="nk-block-title">Discount</h4>
          </div>
      </div>
     <div class="container-fluid">
          <form id="discount" action="{{ route('admin.add.discount.process') }}" method="post" enctype="multipart/form-data">
               @csrf
               <input type="hidden" id="id" name="id" value="{{ $id ?? '' }}">
               <div class="card card-bordered card-preview subsc_card">
                    <div class="card-inner">
                         <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="discount_name">Discount Name</label>
                                   <input type="text" class="form-control" id="discount_name" name="discount_name" placeholder="Enter Discount Name" value="{{ $discount->name ?? '' }}">
                              </div>    
                              <span class="text text-danger" id="error_count" style="display:none;">This field is required</span>
                         </div>
                         <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="discount_percent">Percentage</label>
                                   <input type="number" class="form-control" id="discount_percent" name="discount_percent" placeholder="Enter Percent" value="{{ $discount->percentage ?? '' }}">
                              </div>    
                              <span class="text text-danger" id="percent_error" style="display:none;">This field is required</span>
                         </div>
                         <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="start_date">Start Date</label>
                                   <input type="date" class="form-control" id="start_date" name="start_date" placeholder="Enter Start Date" value="{{ isset($discount->start_date) ? $discount->start_date : '' }}">                   
                              </div>  
                              <span class="text text-danger" id="start_date_error" style="display:none;">This field is required</span>
                         </div>
                         <div class="col-md-8 mt-2">
                              <div class="form-group">
                                   <label class="form-label" for="end_date">End Date</label>
                                   <input type="date" class="form-control" id="end_date" name="end_date" placeholder="Enter End Date" value="{{ isset($discount->end_date) ? $discount->end_date : '' }}">                   
                              </div>  
                              <span class="text text-danger" id="end_date_error" style="display:none;">This field is required</span>
                         </div>               
                    </div>
               </div>
               <div class="mt-3">
                    <button class="btn btn-primary submitform" type="submit">@if(isset($id)) Update  @else Save @endif</button>
               </div>
          </form>
     </div>
</div>


<script>
    
     $(document).ready(function() {
          const $startDate = $('#start_date');
          const $endDate = $('#end_date');
          const today = new Date().toISOString().split('T')[0];
          $startDate.attr('min', today);

          if(!$startDate.val()){
               $startDate.val(today);
          }

          if(!$endDate.val()){
               const tomorrow = new Date();
               tomorrow.setDate(tomorrow.getDate() + 1);
               const tomorrowStr = tomorrow.toISOString().split('T')[0];
               $endDate.val(tomorrowStr);
               $endDate.attr('min', tomorrowStr);
          }else{
               const startDateVal = new Date($startDate.val());
               const minEndDate = new Date(startDateVal);
               minEndDate.setDate(startDateVal.getDate() + 1);
               const minEndDateStr = minEndDate.toISOString().split('T')[0];
               $endDate.attr('min', minEndDateStr);
          }

          $startDate.on('change', function() {
               const startDate = new Date($(this).val());
               if(!isNaN(startDate)){
                    const nextDay = new Date(startDate);
                    nextDay.setDate(startDate.getDate() + 1);
                    const minEndDate = nextDay.toISOString().split('T')[0];
                    $endDate.attr('min', minEndDate);

                    if(new Date($endDate.val()) < nextDay){
                         $endDate.val(minEndDate);
                    }
               }
          });
     });

</script>



@endsection
