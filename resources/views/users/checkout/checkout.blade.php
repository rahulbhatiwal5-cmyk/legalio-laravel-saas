@extends('users_layout.custom_header')
@section('content')


<section class="check_out odr_sec chk_out_sec">
    <div class="container">

        <div class="listo">
            <div class="listo_img">
                <img src="{{ asset('assets/img/Check.svg') }}" alt="">
            </div>
            <div class="listo_txt">
                <h2>Your Document Is Ready</h2>
                <p class="size20">8,780 people have purchased this document</p>
                <p class="size20">
                    <span style="display:flex;align-items:center;gap:5px;">
                        <span>Rating:</span>
                        <img src="{{ asset('assets/img/stars.png') }}" alt="">
                        <span>5.0</span>
                    </span>
                </p>
            </div>
        </div>

        <div class="row prnt_row">
            <div class="col-lg-7">
    <div class="pymnt_form" id="detail-div">

        <div class="detail">
            <h6 class="b-dark">Customer Information</h6>
            @php
                $user           = auth()->user();
                $address        = $user?->addresses()->first();
                $defaultCountry = old('country') ?? ($address?->country ?? 'México');
                $countries = [
                    'México'    => 'México',    'Argentina' => 'Argentina',
                    'Colombia'  => 'Colombia',  'Chile'     => 'Chile',
                    'Perú'      => 'Perú',      'Ecuador'   => 'Ecuador',
                    'Venezuela' => 'Venezuela', 'Bolivia'   => 'Bolivia',
                    'Paraguay'  => 'Paraguay',  'Uruguay'   => 'Uruguay',
                    'España'    => 'España',
                ];
            @endphp
            <p class="size18 mb-0 small">
                You are logged in as {{ $user?->first_name }}. Not you?
                <a href="{{ route('logout') }}">Log out</a>
            </p>
            <form class="row g-3" id="userdetails">
                <div class="col-md-6">
                    <x-google-input type="text" name="fname" id="fname" label="First Name" :value="$user?->first_name" />
                    <span class="text error-msg" id="firstname-error" style="display:none;">This field is required</span>
                </div>
                <div class="col-md-6">
                    <x-google-input type="text" name="lname" id="lname" label="Last Name" :value="$user?->last_name" />
                    <span class="text error-msg" id="lastname-error" style="display:none;">This field is required</span>
                </div>
                <div style="display:none;">
                <div class="col-md-6">
                    <x-google-input type="text" name="address" id="address" label="Street Address" :value="$address?->address ?? 'Not Filled'" />
                    <span class="text error-msg" id="address-error" style="display:none;">This field is required</span>
                </div>
                <div class="col-md-6">
                    <x-google-input
                            type="text"
                            name="city"
                            id="city"
                            label="City"
                            :value="$address?->city ?? 'Not Filled'" />
                    <span class="text error-msg" id="city-error" style="display:none;">This field is required</span>
                </div>
                <div class="col-md-6">
                    <x-google-input type="text" name="state" id="state" label="State" :value="$address?->state ?? 'Not Filled'" />
                    <span class="text error-msg" id="state-error" style="display:none;">This field is required</span>
                </div>
                <div class="col-md-6">
                    <x-google-input type="number" name="postal_code" id="postal_code" label="ZIP Code" :value="$address?->postal_code ?? 1001" />
                    <span class="text error-msg" id="postal_code-error" style="display:none;">This field is required</span>
                </div>
                </div>

                <div class="col-md-12">
                    <x-google-input type="select" name="country" id="country" label="Country" :options="$countries" :value="$defaultCountry" />
                    <span class="text error-msg" id="country-error" style="display:none;">This field is required</span>
                </div>
                <div class="col-md-12" id="company_div" style="margin-top:10px;">
                    <x-google-input type="text" name="company" id="company" label="Company (optional)" :value="$address?->company" />
                </div>
            </form>
        </div>

        <div class="time-to-hideen">
            <div class="pymnt">
                <div class="p-heading">
                    <h6 class="b-dark">Payment Method</h6>
                </div>

                <div class="opt">
                    <div class="form-check active" id="paypalForm">
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="paypal">
                            <div class="d-flex align-items-center" style="width:100%;">
                                <input class="form-check-input paymentMethod me-2" name="pymnt_method" value="paypal" type="radio" id="paypal" checked>
                                <span class="fw-normal">Pay with PayPal</span>
                                <div class="card-icons d-flex align-items-center" style="margin-left:auto;">
                                    <img src="{{ asset('assets/images/paypal-svg.svg') }}" alt="PayPal" class="paypal-logo">
                                </div>
                            </div>
                        </label>
                    </div>
                    <form class="row g-3 pymnt-details" id="paypal_form"
                        action="{{ route('checkout.paypal') }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden"
                                name="plan_id"
                                id="paypal_plan_id"
                                value="{{ $plan_id ?? '' }}">
                        <input type="hidden" name="payment_method"     value="paypal">
                        <input type="hidden" name="document_id"        value="{{ $document->id ?? 'null'}}">
                        <input type="hidden" name="company"            value="">
                        <input type="hidden" name="company_2"          value="">
                        <input type="hidden" name="first_name"         value="">
                        <input type="hidden" name="last_name"          value="">
                        <input type="hidden" name="address"            value="">
                        <input type="hidden" name="city"               value="">
                        <input type="hidden" name="state"              value="">
                        <input type="hidden" name="postal_code"        value="">
                        <input type="hidden" name="country"            value="">
                        <input type="hidden" name="email"              value="">
                        <input type="hidden" name="password"           value="">
                        <input type="hidden" name="subscription_price" id="subscription_price"
                            value="{{ number_format($discount_price, 2) }}">
                        <input type="hidden" name="purchase_type" id="paypal_purchase_type" value="">
                    </form>
                </div>

                <div class="opt">
                    <div class="form-check" id="stripeForm">
                        <label class="form-check-label d-flex align-items-center justify-content-between w-100" for="cards">
                            <div class="d-flex align-items-center">
                                <input class="form-check-input paymentMethod me-2" name="pymnt_method" value="stripe" type="radio" id="cards">
                                <span class="fw-normal">Pay with Card</span>
                            </div>
                            <div class="card-icons d-flex align-items-center">
                                <img src="{{ asset('assets/images/card-img-icon.svg') }}" alt="">
                            </div>
                        </label>
                    </div>
                    <form class="row g-3 pymnt-details" id="stripe_form"
                        action="{{ route('checkout.customer') }}" method="POST">
                        @csrf
                        <input type="hidden" name="document_data" value="{{$document_data ?? null}}">
                        <div class="col-md-12">
                            <div class="floating-group">
                                <input type="text" id="name_on_card" name="name_on_card"
                                    class="form-control floating-input" placeholder=" " required>
                                <label for="name_on_card" class="floating-label">Name on Card</label>
                            </div>
                        </div>
                        <div class="col-md-12 stripe-floating">
                            <div class="stripe-input-wrapper" id="card-number-wrapper">
                                <label class="floating-label">Card Number</label>
                                <div class="stripe-element" id="card-number"></div>
                            </div>
                        </div>
                        <div class="col-md-6 stripe-floating">
                            <div class="stripe-input-wrapper" id="card-expiry-wrapper">
                                <label class="floating-label">Expiration Date</label>
                                <div class="stripe-element" id="card-expiry"></div>
                            </div>
                        </div>
                        <div class="col-md-6 stripe-floating">
                            <div class="stripe-input-wrapper" id="card-cvc-wrapper">
                                <label class="floating-label">CVC</label>
                                <div class="stripe-element" id="card-cvc"></div>
                            </div>
                        </div>
                        <input type="hidden" name="payment_method"  id="stripe_pm"       value="">
                        <input type="hidden" name="payment_intent"  id="stripe_i"        value="{{ $intent->id ?? '' }}">
                        <input type="hidden" name="payment_type"    id="payment_type"    value="">
                        <input type="hidden" name="first_name"      id="first_name"      value="">
                        <input type="hidden" name="last_name"       id="last_name"       value="">
                        <input type="hidden" name="useremail"       id="useremail"       value="">
                        <input type="hidden" name="userpassword"    id="userpassword"    value="">
                        <input type="hidden" name="interval"        id="interval"        value="monthly">
                        <input type="hidden" name="credit"          id="credit"          value="">
                        <input type="hidden" name="is_trial"        id="is_trial"        value="0">
                        <input type="hidden" name="subsc_price"     id="subsc_price"     value="{{ number_format($discount_price, 2) }}">
                        <input type="hidden" name="plan_id"         id="plan_id"         value="{{ $plan_id ?? '' }}">
                        <input type="hidden" name="is_subscription" id="is_subscription" value="{{ $is_onetime ? '0' : '1' }}">
                        <input type="hidden" name="no_of_months"    id="no_of_months"    value="{{ $no_of_months ?? $selected_months }}">
                    </form>
                    <div id="card-errors" class="error-msg mt-2" role="alert"></div>
                </div>

                <div class="form-check">
                    <input class="form-check-input chk-bx" type="checkbox" value="" id="advertising">
                    <label class="form-check-label" for="advertising">
                        I agree that my personal data may be used for marketing purposes.
                    </label>
                </div>
            </div>
        </div>

            <div class="form-check mt-3">
            <input class="form-check-input chk-bx" type="checkbox" value="" id="accept">
            <label class="form-check-label" for="accept">
                <span class="accept">I accept the</span>
                <a href="{{ url('/terms-conditions') }}" class="b-dark" target="_blank"
                    style="text-decoration:underline;">Terms and Conditions</a>.*
            </label>
        </div>

            <div class="form_btn">
                <a href="javascript:void(0)" class="cta_org size20 submit-form">
                    Place Order & Download
                </a>
            </div>
    </div>
</div>

            <div class="col-lg-5">
                <div class="doc_ready">

                    <div class="form_btn">
                        <a href="javascript:void(0)" class="cta_org size20 submit-form">Place Order & Download</a>
                    </div>

        
                    @if(!empty($document))

                    <div class="carta-poder">
                        <div class="carta-detail">
                            <div class="carta_img">
                                <img src="{{ $document->document_image ?? $document_image }}"
                                    alt="carta-poder">
                            </div>

                            <div class="carta_txt">
                                <h6>{{ $title ?? '' }}</h6>

                                <p class="size18 mb-0">
                                    Instant download in PDF, DOCX (Word), and Pages
                                </p>
                            </div>
                        </div>
                    </div>

                    @endif

                   
                    @if($is_sub_24)
                    <div class="sitio carta-poder locked_plan_card">    
                        <div class="plan-content">
                            <div class="subsc_content">
                                <div class="subsc_heding">
                                    <span class="plan-title">Unlimited Contracts</span>
                                    <span class="recommended">RECOMMENDED</span>
                                </div>
                            </div>
                            <div class="renew_txt">
                                Renews at {{ $currency_symbol }} {{ number_format($discount_price, 2) }}/mo
                                for 2 years. Cancel anytime.
                            </div>
                            <div class="subsc_plans">
                                <div class="col-md-6 plan_perid">
                                    <label>Period</label>
                                    <div class="locked_period_txt">24 months</div>
                                    <input type="hidden" id="months" value="24">
                                </div>
                                <div class="col-md-6 plan_price">
                                    <div class="prc_txt">
                                        <span class="new" id="new_prc">
                                            {{ $currency_symbol }} {{ number_format($discount_price, 2) }}/month
                                        </span>
                                        <span class="old" id="old_prc">
                                            {{ $currency_symbol }} {{ '49.90' }}/month
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="check_list">
                                <ul class="unlmted_pts">
                                    <li>
                                        <img src="{{ asset('assets/img/check.png') }}">
                                        <span class="chk_list_heading">All-Inclusive Access:</span>
                                        All documents, instant downloads (PDF, Word, Pages),
                                        unlimited editing, always updated.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @elseif($is_sub_12)
                    <div class="sitio carta-poder opn_wth_js_us">
                        <div class="opt">
                            <div class="form-check">
                                <input class="form-check-input pay_type" type="radio"
                                    value="recurring" id="subscription" name="pay_type" checked>
                            </div>
                        </div>
                        <div class="plan-content">
                            <div class="subsc_content">
                                <div class="subsc_heding">
                                    <span class="plan-title">Unlimited Contracts</span>
                                    <span class="recommended">RECOMMENDED</span>
                                </div>
                            </div>
                            <div class="renew_txt upsell_sub_note" id="renew_text">
                                Renews at {{ $currency_symbol }} {{ '9.90' }}/mo
                                for 1 year. Cancel anytime.
                            </div>
                            <div class="subsc_plans">
                                <div class="col-md-4 plan_perid">
                                    <label>Period</label>
                                    <div class="locked_period_txt">12 months</div>
                                    <input type="hidden" id="months" value="12">
                                </div>
                                <div class="col-md-4 plan_dis">
                                    <div class="save_txt"></div>
                                </div>
                                <div class="col-md-4 plan_price">
                                    <div class="prc_txt">
                                        <span class="new" id="new_prc">
                                            {{ $currency_symbol }} {{ '9.90' }}/month
                                        </span>
                                        <span class="old" id="old_prc">
                                            {{ $currency_symbol }} {{ '49.90' }}/month
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="check_list">
                                <ul class="unlmted_pts">
                                    <li>
                                        <img src="{{ asset('assets/img/check.png') }}">
                                        <span class="chk_list_heading">All-Inclusive Access:</span>
                                        All documents, instant downloads, unlimited editing, always updated.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                   

                   
                    @elseif($is_free)
                    <div class="sitio carta-poder free_plan_card opn_wth_js_us">
                        <div class="opt">
                            <div class="form-check">
                                <input class="form-check-input pay_type" type="radio"
                                    value="free" id="free_trial_radio" name="pay_type" checked>
                            </div>
                        </div>
                        <div class="plan-content">
                            <div class="subsc_content">
                                <div class="subsc_heding">
                                    <span class="plan-title">Trial Access</span>
                                </div>
                            </div>
                            <div class="upsell_sub_note">Full access for 7 days, then $49.90/month.</div>
                            <div class="free_price_display">$0.00 <span>/ 7 days</span></div>
                            <div class="check_list">
                                <ul class="unlmted_pts">
                                    <li>
                                        <img src="{{ asset('assets/img/check.png') }}">
                                        Get complete viewing access during your trial.
                                    </li>
                                    <li>
                                        <img src="{{ asset('assets/img/check.png') }}">
                                        Cancel anytime before trial ends — no charge.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="sitio carta-poder upsell_sub_card opn_wth_js_us">
                        <div class="opt">
                            <div class="form-check">
                                <input class="form-check-input pay_type" type="radio"
                                    value="recurring" id="subscription" name="pay_type">
                            </div>
                        </div>
                        <div class="plan-content">
                            <div class="subsc_content">
                                <div class="subsc_heding">
                                    <span class="plan-title">Or Subscribe &amp; Save</span>
                                </div>
                               
                                <p class="upsell_sub_note">Skip the trial-get unlimited access right away.</p>

                            </div>
                            <div class="subsc_plans">
                                <div class="col-md-8 plan_price plan-left-sde">
                                    <div class="prc_txt">
                                        <span class="new" id="new_prc">
                                            {{ $currency_symbol }} {{ '9.90' }}/month
                                        </span>
                                        <span class="old" id="old_prc">
                                            {{ $currency_symbol }} {{ '49.90' }}/month
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4 plan_perid">
                                    <label>Period</label>
                                    
                                    <select class="form-select" id="months" name="months">
                                                @foreach($plans as $plan)

                                                    @if($plan->number_of_months != 24)

                                                        <option
                                                            value="{{ $plan->number_of_months }}"
                                                            data-id="{{ $plan->id }}"
                                                            data-price="{{ $plan->price }}"
                                                            {{ $plan->number_of_months == 12 ? 'selected' : '' }}>

                                                            {{ $plan->number_of_months }}
                                                            {{ $plan->number_of_months == 1 ? 'month' : 'months' }}

                                                        </option>

                                                    @endif

                                                @endforeach
                                            </select>
                                </div>
                               
                            </div>
                            <div class="check_list">
                                <ul class="unlmted_pts">
                                    <li>
                                        <img src="{{ asset('assets/img/check.png') }}">
                                        <span class="chk_list_heading">All-Inclusive Access:</span>
                                        All documents, instant downloads, unlimited editing, always updated.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @elseif($is_onetime)
                    <div class="sitio carta-poder opn_wth_js_us">
                        <div class="opt">
                            <div class="form-check">
                                <input class="form-check-input pay_type" type="radio"
                                    value="one-time" id="payperuse" name="pay_type" checked>
                            </div>
                        </div>
                        <div class="plan-content">
                            <div class="pay_per-content">
                                <div class="plan-title">Single Document Purchase</div>
                                <div class="price imagePrice">{{ $currency_symbol }} {{ $price }}</div>
                            </div>
                            <div class="usage-note">Pay once, download instantly. No recurring fees.</div>
                            <div class="one_check_list">
                                <ul class="unlmted_pts">
                                    <li>
                                        <img src="{{ asset('assets/img/cross.png') }}">
                                        <span class="chk_list_heading">No Full Access: </span>
                                        Does not include other documents, unlimited editing, or future legal updates.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="sitio carta-poder upsell_sub_card opn_wth_js_us">
                        <div class="opt">
                            <div class="form-check">
                                <input class="form-check-input pay_type" type="radio"
                                    value="recurring" id="subscription" name="pay_type">
                            </div>
                        </div>
                        <div class="plan-content">
                            <div class="subsc_content">
                                <div class="subsc_heding">
                                    <span class="plan-title">Unlimited Contracts</span>
                                    <span class="recommended">RECOMMENDED</span>
                                </div>
                                <p class="upsell_sub_note">Get all documents for less than this single purchase.</p>
                            </div>
                            <div class="subsc_plans">
                                <div class="col-md-4 plan_perid">
                                    <label>Period</label>
                                    <select class="form-select" id="months" name="months">
                                        @foreach($plans as $plan)
                                                    @if($plan->number_of_months != 24)

                                            <option value="{{ $plan->number_of_months }}"
                                                data-id="{{ $plan->id }}"
                                                data-price="{{ $plan->price }}"
                                                {{ $plan->number_of_months == 24 ? 'selected' : '' }}>
                                                {{ $plan->number_of_months }}
                                                {{ $plan->number_of_months == 1 ? 'month' : 'months' }}
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 plan_dis">
                                    <div class="save_txt"></div>
                                </div>
                                <div class="col-md-4 plan_price">
                                    <div class="prc_txt">
                                        <span class="new" id="new_prc">
                                            {{ $currency_symbol }} {{ '49.90' }}/month
                                        </span>
                                        <span class="old" id="old_prc">
                                            {{ $currency_symbol }} {{ '49.90' }}/month
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="check_list">
                                <ul class="unlmted_pts">
                                    <li>
                                        <img src="{{ asset('assets/img/check.png') }}">
                                        <span class="chk_list_heading">All-Inclusive Access:</span>
                                        All documents, instant downloads, unlimited editing, always updated.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @else
                    <div class="sitio carta-poder opn_wth_js_us">
                        <div class="opt">
                            <div class="form-check">
                                <input class="form-check-input pay_type" type="radio"
                                    value="recurring" id="subscription" name="pay_type" checked>
                            </div>
                        </div>
                        <div class="plan-content">
                            <div class="subsc_content">
                                <div class="subsc_heding">
                                    <span class="plan-title">Unlimited Contracts</span>
                                    <span class="recommended">RECOMMENDED</span>
                                </div>
                            </div>
                            <div class="renew_txt upsell_sub_note" id="renew_text">
                                Renews at {{ $currency_symbol }} {{ number_format($discount_price, 2) }}/mo
                                . Cancel anytime.
                            </div>
                            <div class="subsc_plans">
                                <div class="col-md-4 plan_perid">
                                    <label id="period">Period</label>
                                    <select class="form-select" id="months" name="months">
                                        @foreach($plans as $plan)
                                                    @if($plan->number_of_months != 24)

                                            <option value="{{ $plan->number_of_months }}"
                                                data-id="{{ $plan->id }}"
                                                data-price="{{ $plan->price }}"
                                                {{ $plan->number_of_months == $no_of_months ? 'selected' : '' }}>
                                                {{ $plan->number_of_months }}
                                                {{ $plan->number_of_months == 1 ? 'month' : 'months' }}
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 plan_dis">
                                    <div class="save_txt"></div>
                                </div>
                                <div class="col-md-4 plan_price">
                                    <div class="prc_txt">
                                        <span class="new" id="new_prc">
                                            {{ $currency_symbol }} {{ '49.90' }}/month
                                        </span>
                                        <span class="old" id="old_prc">
                                            {{ $currency_symbol }} {{ '49.90' }}/month
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="check_list">
                                <ul class="unlmted_pts">
                                    <li>
                                        <img src="{{ asset('assets/img/check.png') }}">
                                        <span class="chk_list_heading">All-Inclusive Access:</span>
                                        All documents, instant downloads, unlimited editing, always updated.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @if(!empty($document))

                    <div class="sitio carta-poder opn_wth_js_us">
                        <div class="opt">
                            <div class="form-check">
                                <input class="form-check-input pay_type" type="radio"
                                    value="one-time" id="payperuse" name="pay_type">
                            </div>
                        </div>
                        <div class="plan-content">
                            <div class="pay_per-content">
                                <div class="plan-title">Single Document Purchase</div>
                                <div class="price imagePrice">{{ $currency_symbol }} {{ $price }}</div>
                            </div>
                            <div class="usage-note">Pay once, download instantly. No recurring fees.</div>
                            <div class="one_check_list">
                                <ul class="unlmted_pts">
                                    <li>
                                        <img src="{{ asset('assets/img/cross.png') }}">
                                        <span class="chk_list_heading">No Full Access: </span>
                                        Does not include other documents, unlimited editing, or future legal updates.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    @endif

                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .upsell_sub_note {
        font-size: 18px;
        font-weight: 600;
    }
    .locked_plan_card { border: 2px solid #002655 !important; }
    .locked_period_txt {
        font-size: 14px; font-weight: 500; color: #002655;
        padding: 6px 10px; background: #e8ecf4;
        border-radius: 6px; display: inline-block; margin-top: 4px;
    }
    .free_plan_card { border: 1px dashed #aaa !important; opacity: 0.85; }
    .free_plan_card:has(input:checked) { opacity: 1; border: 2px solid #002655 !important; }
    .free_price_display { font-size: 22px; font-weight: 600; color: #002655; margin: 8px 0; }
    .free_price_display span { font-size: 14px; font-weight: 400; color: #666; }
    .upsell_sub_card { border: 1px dashed #aaa !important; opacity: 0.85; }
    .upsell_sub_card:has(input:checked) { opacity: 1; border: 2px solid #002655 !important; }
    .upsell_sub_note { color: #555; margin: 4px 0 8px; }
</style>

<script src="https://js.stripe.com/v3/"></script>
<script>
    window.currencySymbol = @json($currency_symbol);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    $('.paymentMethod').on('change', function () {
        const val = $(this).val();
        $('#paypalForm, #stripeForm').removeClass('active');
        if (val === 'paypal') { $('#paypalForm').addClass('active'); $('#paypal').prop('checked', true); }
        if (val === 'stripe') { $('#stripeForm').addClass('active'); $('#cards').prop('checked', true); }
    });
    $('.form-check').on('click', function () {
        $(this).find('.paymentMethod').prop('checked', true).trigger('change');
    });

    $('.pay_type').on('change', function () {

    const val = $(this).val();

    if (val === 'free') {

        $('#is_trial').val(1);
        $('#is_subscription').val(1);

    }
    else if (val === 'recurring') {

        $('#is_trial').val(0);
        $('#is_subscription').val(1);

    }
    else if (val === 'one-time') {

        $('#is_trial').val(0);
        $('#is_subscription').val(0);

    }
    $('.pay_type:checked').trigger('change');

});

    $('#months').on('change', function () {
        const selectedMonths = $(this).val();
        const selectedOption = $(this).find('option:selected');
        const planId         = selectedOption.data('id');
        const price          = selectedOption.data('price');

        $('#no_of_months').val(selectedMonths);
        $('#plan_id').val(planId);

        $.ajax({
            url: "{{ route('user.get.price') }}",
            type: 'POST',
            dataType: 'json',
            data: {
                number_of_months: selectedMonths,
                plan_id:          planId,
                price:            price,
                _token:           "{{ csrf_token() }}"
            },
            success: function (response) {
                if (response && response.success) {
                    const total_saving   = parseFloat(response.total_savings).toFixed(2);
                    const discount_price = parseFloat(response.discount_price).toFixed(2);
                    const basePrice      = parseFloat(response.price).toFixed(2);

                    if (selectedMonths == 1) {
                        $('.save_txt').hide();
                        $('#new_prc').text(currencySymbol + ' ' + basePrice + '/month');
                        $('#old_prc').text('');
                        $('#renew_text').text('Renews at ' + currencySymbol + ' ' + 'basePrice' + '/mo. Cancel anytime.');
                        $('#subsc_price').val(basePrice);
                    } else if (selectedMonths == 12) {
                        $('.save_txt').show();
                        $('#saveText').text('SAVE ' + currencySymbol + ' ' + total_saving);
                        $('#new_prc').text(currencySymbol + ' ' + discount_price + '/month');
                        $('#old_prc').text(currencySymbol + ' ' + '49.90' + '/month');
                        $('#renew_text').text('Renews at ' + currencySymbol + ' ' + discount_price + '/mo for 1 year. Cancel anytime.');
                        $('#subsc_price').val(discount_price);
                    } else if (selectedMonths == 24) {
                        $('.save_txt').show();
                        $('#saveText').text('SAVE ' + currencySymbol + ' ' + total_saving);
                        $('#new_prc').text(currencySymbol + ' ' + discount_price + '/month');
                        $('#old_prc').text(currencySymbol + ' ' + '49.90' + '/month');
                        $('#renew_text').text('Renews at ' + currencySymbol + ' ' + discount_price + '/mo for 2 years. Cancel anytime.');
                        $('#subsc_price').val(discount_price);
                    }
                    $('#subscription').prop('checked', true).trigger('change');
                }
            }
        });
    });
});



$('.terms_conditions').hide();
var secretKey    = `{{ $public_key }}`;
var clientSecret = `{{ $clientSecret }}`;
const stripe     = Stripe(secretKey);
const elements   = stripe.elements();
const cardNumber = elements.create('cardNumber', { placeholder: '' });
const cardExpiry = elements.create('cardExpiry', { placeholder: '' });
const cardCvc    = elements.create('cardCvc',    { placeholder: '' });
cardNumber.mount('#card-number');
cardExpiry.mount('#card-expiry');
cardCvc.mount('#card-cvc');

function bindFloatingLabel(element, wrapperId) {
    const wrapper = document.getElementById(wrapperId);
    element.on('focus',  () => wrapper.classList.add('is-focused'));
    element.on('blur',   () => wrapper.classList.remove('is-focused'));
    element.on('change', (e) => {
        e.empty === false
            ? wrapper.classList.add('is-filled')
            : wrapper.classList.remove('is-filled');
    });
}   
bindFloatingLabel(cardNumber, 'card-number-wrapper');
bindFloatingLabel(cardExpiry, 'card-expiry-wrapper');
bindFloatingLabel(cardCvc,    'card-cvc-wrapper');

const cardErrors = document.getElementById('card-errors');

let trialConfirmed = false;

function validateForm() {
    const first_name  = $('#fname').val();
    const last_name   = $('#lname').val();
    const company     = $('#company').val();
    const address     = $('#address').val();
    const city        = $('#city').val();
    const state       = $('#state').val();
    const postal_code = $('#postal_code').val();
    const country     = $('#country').val();
    let   isValid     = true;

    const fields = [
        { id: 'fname',       errId: 'firstname-error' },
        { id: 'lname',       errId: 'lastname-error'  },
        { id: 'address',     errId: 'address-error'   },
        { id: 'city',        errId: 'city-error'      },
        { id: 'state',       errId: 'state-error'     },
        { id: 'postal_code', errId: 'postal_code-error' },
        { id: 'country',     errId: 'country-error'   },
    ];

    fields.forEach(f => {
        if (!$('#' + f.id).val()) {
            $('#' + f.errId).show();
            $('#' + f.id).addClass('invalid');
            isValid = false;
        } else {
            $('#' + f.errId).hide();
            $('#' + f.id).removeClass('invalid');
        }
        $('#' + f.id).on('input', function () {
            $(this).removeClass('invalid');
            $('#' + f.errId).hide();
        });
    });

    if (!isValid) {
        $('html, body').animate({ scrollTop: $(".prnt_row").offset().top }, 100);
        $('.submit-form').text("Place Order & Download").prop('disabled', false);
        return { status: false };
    }
    if (!$('#accept').is(':checked')) {
        $('.terms_conditions').show();
        return { status: false };
    }
    $('.terms_conditions').hide();
    return { status: true, data: { first_name, last_name, company, address, city, state, postal_code, country } };
}

   
function InitPaypal() {
    if (!$('#accept').is(':checked')) { $('.terms_conditions').show(); return; }
    const res = validateForm();
    if (!res.status) return;
    const payType = $('input[name="pay_type"]:checked').val();
    if (!payType) { alert('Please select a payment type'); return; }
    $('#paypal_purchase_type').val(payType);
    $('#paypal_form input[name="first_name"]').val(res.data.first_name);
    $('#paypal_form input[name="last_name"]').val(res.data.last_name);
    $('#paypal_form input[name="address"]').val(res.data.address);
    $('#paypal_form input[name="city"]').val(res.data.city);
    $('#paypal_form input[name="state"]').val(res.data.state);
    $('#paypal_form input[name="postal_code"]').val(res.data.postal_code);
    $('#paypal_form input[name="country"]').val(res.data.country);
    $('#paypal_form').submit();
}

$('.submit-form').on('click', async (e) => {

     const payType = $('input[name="pay_type"]:checked').val();

    if (payType === 'free' && !trialConfirmed) {
        e.preventDefault();

        const trialModal = new bootstrap.Modal(
            document.getElementById('trialModal')
        );

        trialModal.show();
        return;
    }
    e.preventDefault();
    const payment_method  = $('input[name="pymnt_method"]:checked').val();
    const is_advertising  = $('#advertising').is(':checked') ? 1 : 0;
    $('#payment_type').val(payment_method);

    if (payment_method === 'stripe') {
        const res = validateForm();
        if (!res.status) return;
        $('.submit-form').text("Hold On").prop('disabled', true);
        const payType = $('input[name="pay_type"]:checked').val();

                if (payType === 'free') {
                    $('#is_trial').val(1);
                    $('#is_subscription').val(1);
                } else if (payType === 'recurring') {
                    $('#is_trial').val(0);
                    $('#is_subscription').val(1);
                } else {
                    $('#is_trial').val(0);
                    $('#is_subscription').val(0);
                }

        const plan_id         = $('#plan_id').val();
        const is_subscription = $('#is_subscription').val();
        const no_of_months    = $('#no_of_months').val();

        let price = 0;
        if (is_subscription == '1') {
            price = parseFloat($('#subsc_price').val() || 0);
        } else {
            price = {{ $price ?? '0' }};
        }

        const { paymentMethod, error: pmError } = await stripe.createPaymentMethod({
            type: 'card', card: cardNumber,
            billing_details: {
                name:    $('#name_on_card').val(),
                email:   `{{ auth()->user()->email }}`,
                address: {
                    line1: res.data.address, city: res.data.city,
                    state: res.data.state,   postal_code: res.data.postal_code,
                    country: 'MX'
                }
            }
        });

        if (pmError) {
            $('.submit-form').text("Place Order & Download").prop('disabled', false);
            cardErrors.textContent = pmError.message;
            return;
        }

        const orderData = {
            ...res.data,
            email:           `{{ auth()->user()->email }}`,
            is_advertising,
            is_subscription,
            is_trial: $('#is_trial').val(),
            plan_id,
            no_of_months,
            payment_method:  paymentMethod.id,
            payment_intent:  `{{ $intent->id ?? '' }}`,
            price:           price * 100,
            description:     "Order for Document ID: {{ $document->id ?? 'null' }}",
            document_id:     {{ $document->id ?? 'null'}},
            _token:          '{{ csrf_token() }}'
        };

        if (is_subscription == '1') {
            const resp = await fetch('{{ route('user.create_subscription') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            });
            const data = await resp.json();
            if (!data.success) {
                $('.submit-form').text("Place Order & Download").prop('disabled', false);
                cardErrors.textContent = data.message || 'Subscription failed.';
                return;
            }

            const cs = data.payment_intent_client_secret;
                if (data.is_trial === true) {

                    $('#stripe_pm').val(paymentMethod.id);
                    $('#stripe_i').val('trial');
                    $('#stripe_form').submit();

                }
            if (data.subscription_status === 'incomplete' && cs) {
                const { paymentIntent, error } = await stripe.confirmCardPayment(cs);
                if (error) {
                    cardErrors.textContent = error.message;
                    $('.submit-form').text("Place Order & Download").prop('disabled', false);
                    return;
                }
                if (paymentIntent.status === 'succeeded') {
                    $('#stripe_pm').val(paymentMethod.id);
                    $('#stripe_i').val(paymentIntent.id);
                    $('#stripe_form').submit();
                }
            } else if (data.subscription_status === 'active') {
                $('#stripe_pm').val(paymentMethod.id);
                $('#stripe_i').val(data.payment_intent_id);
                $('#stripe_form').submit();
            }
        } else {
            const resp = await fetch('{{ route('user.place_order') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            });
            const orderResp = await resp.json();
            if (!resp.ok || !orderResp.success) {
                cardErrors.textContent = orderResp.data;
                return;
            }
            const { paymentIntent, error: piError } = await stripe.confirmCardPayment(clientSecret, {
                payment_method: paymentMethod.id
            });
            if (piError) { cardErrors.textContent = piError.message; return; }
            if (paymentIntent.status === 'succeeded') {
                $('#stripe_pm').val(paymentMethod.id);
                $('#stripe_form').submit();
            }
        }

    } else if (payment_method === 'paypal') {
        $('.submit-form').text("Processing...").prop('disabled', true);
        try { InitPaypal(); }
        catch (err) {
            $('.submit-form').text("Place Order & Download").prop('disabled', false);
        }
    } else {
        alert("Something went wrong.");
    }
    
});

$(document).on('click', '#agreeTrialBtn', function () {
    trialConfirmed = true;

    const modalEl = document.getElementById('trialModal');
    const modal = bootstrap.Modal.getInstance(modalEl);

    if (modal) {
        modal.hide();
    }

    $('.submit-form').first().trigger('click');
});


</script>

<div class="modal fade" id="trialModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Free Trial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>
                    Start your 7-day free trial. After the trial ends, you'll be charged
                    <strong>$49.90/month</strong>, and your subscription will automatically
                    convert to a paid plan with access to all premium features and benefits.
                    
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="button" class="btn btn-primary"
                    id="agreeTrialBtn">
                    Agree
                </button>
            </div>
        </div>
    </div>
</div>

@endsection