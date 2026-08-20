@extends('users_layout.custom_header')

@section('content')
@php
  $data = $document ?? 'document';
  $is_trial = $trial ?? null;
@endphp

<section class="thank-you-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">

                <div class="thankyou-card">

                    <div class="success-icon">
                        <i class="fas fa-check"></i>
                    </div>

                    <h2 class="thankyou-title">
                        Thank You For Your Purchase!
                    </h2>

                    <p class="thankyou-text">
                        Your order has been processed successfully.
                    </p>

                    <div class="billing-box">
                        <h4>Complete Your Billing Information</h4>
                        <p class="text-muted mb-4">
                            Please provide your billing details to continue.
                        </p>

                        <form id="billingForm" method="POST" action="{{route('billing.info.save')}}">

                            @csrf
                            <input type="hidden" name="document" value="{{$data}}">
                            <input type="hidden" name="is_trial" value="{{$is_trial}}">

                            <div class="row">

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Address</label>
                                    <input type="text"
                                           name="address"
                                           id="address"
                                           class="form-control"
                                           placeholder="Enter Address">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text"
                                           name="state"
                                           id="state"
                                           class="form-control"
                                           placeholder="Enter State">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text"
                                           name="city"
                                           id="city"
                                           class="form-control"
                                           placeholder="Enter City">
                                </div>

                                    <div class="col-md-12 mb-4">

                                    <label class="form-label">Zip Code</label>
                                    <input type="number"
                                        name="zip_code"
                                        id="zip_code"
                                        class="form-control"
                                        placeholder="Enter Zip Code"
                                        step="1"
                                        min="0"
                                        required>
                                </div>

                            </div>

                            <button type="submit"                                   
                                    class="btn continue-btn w-100">
                                Continue
                            </button>

                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>

<style>

.thank-you-section{
    min-height:80vh;
    display:flex;
    align-items:center;
    background:#f7f9fc;
}

.thankyou-card{
    background:#fff;
    border-radius:15px;
    padding:40px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    text-align:center;
}

.success-icon{
    width:80px;
    height:80px;
    margin:auto;
    border-radius:50%;
    background:#002655;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:35px;
    margin-bottom:20px;
}

.thankyou-title{
    font-size:32px;
    font-weight:700;
    color:#002655;
    margin-bottom:10px;
}

.thankyou-text{
    font-size:18px;
    color:#666;
    margin-bottom:35px;
}

.billing-box{
    text-align:left;
    margin-top:20px;
}

.billing-box h4{
    color:#002655;
    font-weight:600;
}

.form-control{
    height:52px;
    border-radius:8px;
}

.continue-btn{
    background:#002655;
    border:none;
    color:#fff;
    padding:14px;
    font-size:18px;
    font-weight:600;
    border-radius:8px;
}

.continue-btn:hover{
    background:#01397a;
    color:#fff;
}

.error-msg{
    color:red;
    font-size:13px;
}

</style>



@endsection