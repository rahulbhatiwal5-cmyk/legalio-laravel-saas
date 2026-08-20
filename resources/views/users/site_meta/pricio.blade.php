@extends('users_layout.master')
@section('title',$data['meta_title'])
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<style>

    #productSelectt{
    height: 56px;
    border-radius: 12px;
    padding: 0 15px;
    border: 1px solid #d1d5db;
    }
    .select2-results__options{
        max-height: 240px !important;
        overflow-y: auto !important;
    }

    .body_sub_1 .tab-content {
        display: none;
        text-align: center;
        font-weight: 450;
        font-size: 16px;
    }
</style>

@php
    
    $show_upgrade_popup = $show_upgrade_popup ?? 0;
    $url_document_id   = request()->get('document_id', '');
    $selected_document = null;

    if ($url_document_id) {
        $selected_document = $documents->firstWhere('id', $url_document_id);
    }

    $single_doc_price = $selected_document?->doc_price ?? $default_price;
    $single_doc_slug  = $selected_document?->slug ?? '';
    $single_doc_title = $selected_document?->title ?? '';

    // ✅ Fix 1 — Paid subscription check
    $has_paid_subscription = auth()->check()
        ? \App\Models\Subscription::where('user_id', auth()->id())
              ->where('status', 'active')
              ->exists()
        : false;

    // ✅ Fix 2 — Free trial ever used check
    $has_used_free_trial = auth()->check()
        ? \App\Models\FreeTrail::where('user_id', auth()->id())
              ->exists()
        : false;
@endphp

<section class="price_banner inner-banner">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="banner_content"></div>
            </div>
            <div class="col-md-5">
                <div class="banner_img"></div>
            </div>
        </div>
    </div>
</section>

<section class="sub_payment p_120">
    <div class="conatiner_payment">
        <div class="row align-items-stretch">

            {{-- ===================== CARD 3: SINGLE DOCUMENT ===================== --}}

             <div class="col-lg-4 price-card-commn othr_price_card">
                <div class="sub_payment_1 sub_payment_2 h-100">
                    <div class="inner_part_sub_paymem">
                        <div class="Subscrip">
                            <p>
                                {{ 'Single Document Purchase' }}
                                <div class="svg_pointer_box"></div>
                            </p>
                            <div class="discreption"></div>
                        </div>

                        <div class="body_sub_2 body_sub_1">
                            <div class="tab-content active" id="monthly_4">
                                <div class="image_p">

                                    @if($selected_document)
                                    {{-- ── User came from a specific document ── --}}
                                    <div class="single_price_wrapper">
                                        <div class="single_doc_display">
                                            <div class="single_doc_title">
                                                <img src="{{ asset('assets/img/doc_icon.svg') }}"
                                                     onerror="this.style.display='none'"
                                                     style="width:18px;margin-right:6px;">
                                                {{ $single_doc_title }}
                                            </div>
                                        </div>
                                        <div class="price document_price  " id="imagePrice">
                                            {{ $currency_symbol }} {{ number_format($single_doc_price, 2) }}
                                        </div>
                                    </div>

                                    @else
                                    {{-- ── No document_id — show dropdown ── --}}
                                    <div class="single_price_wrapper">
                                        <div class="product_div">
                                            <div class="form-group">
                                                {{-- <select class="form-select productSelect" id="productSelectt">
                                                    @if($documents->isEmpty())
                                                        <option value="">No documents available</option>
                                                    @endif
                                                    @foreach($documents as $document)
                                                        <option
                                                            value="{{ $document->id ?? '' }}"
                                                            data-slug="{{ $document->slug ?? '' }}"
                                                            {{ $loop->first ? 'selected' : '' }}>
                                                            {{ $document->title ?? '' }}
                                                        </option>
                                                    @endforeach
                                                </select> --}}

                                                <select class="form-select productSelect" id="productSelectt">
                                                    @if($documents->isEmpty())
                                                        <option value="">No documents available</option>
                                                    @endif

                                                    @foreach($documents as $document)
                                                        <option
                                                            value="{{ $document->id ?? '' }}"
                                                            data-slug="{{ $document->slug ?? '' }}"
                                                            {{ $loop->first ? 'selected' : '' }}>

                                                            {{ $document->title ?? '' }}

                                                        </option>
                                                    @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="price document_price " id="imagePrice">
                                            {{ $currency_symbol }} {{ number_format($default_price, 2) }}
                                        </div>
                                    </div>
                                    @endif

                                    <div class="one_check_list">
                                        <ul class="unlmted_pts">
                                            <li>
                                                <img src="{{ asset('assets/img/cross.png') }}">
                                                <span class="chk_list_heading">No Full Access: </span>
                                                Does not include other documents, unlimited editing,
                                                or future legal updates.
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="btn-holder crear_documento">
                                        {{-- ✅ Fix 3 — Document detail page pe jao --}}
                                        <button class="cta_org"  id="onetime-btn">
                                            {{ 'Create Document' }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== CARD 1: SUBSCRIPTION ===================== --}}

            <div class="col-lg-4 price-card-commn rec_price_card">
                <div class="sub_payment_1 h-100">
                    <div class="header_sub_1">
                        <p>{{ 'Best Offer' }}</p>
                    </div>
                    <div class="inner_part_sub_paymem">
                        <div class="Subscrip subsc_heding">
                            <p>
                                {{ 'Unlimited Contracts' }}
                                <span class="recommended">{{ 'RECOMMENDED' }}</span>
                                <div class="svg_pointer_box">
                                    Full access to all documents. Cancel anytime.
                                </div>
                            </p>
                            <div class="discreption"></div>
                        </div>
                        <div class="body_sub_1 sub_content">
                            <div class="tab-content active" id="monthly">
                                <div class="image_p">
                                    <div class="price_main">
                                        <div class="plan_perid">
                                            <label id="period">Period</label>
                                            {{-- <select class="form-select" id="months" name="months">
                                                @foreach($plans as $plan)
                                                    <option
                                                        value="{{ $plan->number_of_months ?? '' }}"
                                                        data-id="{{ $plan->id ?? '' }}"
                                                        data-price="{{ $plan->price ?? '' }}"
                                                        {{ $plan->number_of_months == 12 ? 'selected' : '' }}>
                                                        {{ $plan->number_of_months }}
                                                        {{ $plan->number_of_months == 1 ? 'month' : 'months' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            
                                            --}}

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
                                        <div class="price_image_wrapper">
                                            <div class="price imagePrice" id="current_price">
                                                {{ $currency_symbol }} {{ '9.90' }}<span>/month</span>
                                            </div>
                                            <div class="price imagePrice" id="old_price">
                                                {{ $currency_symbol }} 49.90<span>/month</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="check_list">
                                        {{-- <ul class="unlmted_pts">
                                            <li>
                                                <img src="{{ asset('assets/img/check.png') }}">
                                                <span class="chk_list_heading">All-Inclusive Access:</span>
                                                All documents included, instant downloads (PDF, Word, Pages),
                                                unlimited editing, always updated with latest legal changes.
                                            </li>
                                        </ul> --}}
                                        <ul class="unlmted_pts">
                                            <li>
                                                <img src="{{ asset('assets/img/check.png') }}">
                                                All documents included with your subscription.
                                            </li>

                                            <li>
                                                <img src="{{ asset('assets/img/check.png') }}">
                                                Instant downloads in PDF, Word, and Pages formats.
                                            </li>

                                            <li>
                                                <img src="{{ asset('assets/img/check.png') }}">
                                                Unlimited editing and document customization.
                                            </li>

                                            <li>
                                                <img src="{{ asset('assets/img/check.png') }}">
                                                Always updated with the latest legal changes.
                                            </li>
                                        </ul>
                                                                            </div>
                                    <div class="btn-holder">
                                        <button class="cta_org" id="subscribe-btn">
                                            {{ 'Subscribe Now' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           

            {{-- ===================== CARD 2: FREE 1 WEEK TRIAL ===================== --}}
            {{-- ✅ Fix 2: Sirf tab dikhao jab free trial kabhi use nahi hua --}}
            @if(!$has_used_free_trial)
            <div class="col-lg-4  price-card-commn free_trial_price_card">
                <div class="sub_payment_1 sub_payment_free h-100">
                    {{-- <div class="header_sub_1 header_free">
                        <p>{{ '1 Week Free' }}</p>
                    </div> --}}
                    <div class="inner_part_sub_paymem">
                        <div class="Subscrip subsc_heding">
                            <p>
                                {{ 'Trial Access' }}
                                <span class="recommended recommended_free">{{ 'TRY FREE' }}</span>
                                <div class="svg_pointer_box">
                                    Full access for 7 days.
                                </div>
                            </p>
                            <div class="discreption"></div>
                        </div>
                        <div class="body_sub_1 sub_content">
                            <div class="tab-content active" id="free_trial_tab">
                                <div class="image_p">
                                    <div class="price_main">
                                        <div class="free_trial_price_block">
                                            <div class="price imagePrice ">
                                                $0.00 <span>/ 7 days</span>
                                            </div>
                                            <div class="free_then_text">
                                                Then choose a plan or cancel — no charge.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="check_list">
                                        {{-- <ul class="unlmted_pts">
                                            <li>
                                                <img src="{{ asset('assets/img/check.png') }}">
                                                <span class="chk_list_heading">Full Access for 7 Days:</span>
                                                Experience full contract access with secure viewing—upgrade
                                                anytime to download and use without limits.
                                            </li>
                                           
                                        </ul> --}}
                                        <ul class="unlmted_pts">
                                            <li>
                                                <img src="{{ asset('assets/img/check.png') }}">
                                                Full access to all available contracts for 7 days.
                                            </li>

                                            <li>
                                                <img src="{{ asset('assets/img/check.png') }}">
                                                Secure viewing and editing of legal documents.
                                            </li>

                                            <li>
                                                <img src="{{ asset('assets/img/check.png') }}">
                                                Explore premium features before upgrading.
                                            </li>

                                            <li>
                                                <img src="{{ asset('assets/img/check.png') }}">
                                                Upgrade anytime for unlimited downloads and usage.
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="btn-holder">
                                        <button class="cta_org " id="free-trial-btn">
                                            {{ 'Start Free Trial' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

           

        </div>{{-- end .row --}}
    </div>
</section>

{{-- ===================== FAQ SECTION ===================== --}}
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
                                    <button
                                        class="{{ $loop->first ? 'accordion-button' : 'accordion-button collapsed' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $loop->iteration ?? '' }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                        aria-controls="collapse{{ $loop->iteration ?? '' }}">
                                        {{ $faq->question ?? '' }}
                                    </button>
                                </h6>
                                <div id="collapse{{ $loop->iteration ?? '' }}"
                                    class="{{ $loop->first ? 'accordion-collapse collapse show' : 'accordion-collapse collapse' }}"
                                    aria-labelledby="heading{{ $loop->iteration ?? '' }}"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <?php echo strip_tags($faq->answer); ?>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div class="faq-view-more">
                        <a href="{{ url('/faq') }}" class="cta_org">Ver más</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================== ALREADY SUBSCRIBED MODAL ===================== --}}
<div class="modal fade" id="already_subscribed_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; padding:10px;">
            <div class="modal-header border-0">
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align:center; padding:20px 30px;">
                <div style="font-size:48px; margin-bottom:12px;">✅</div>
                <h5 style="color:#002655; font-weight:700; margin-bottom:8px;">
                    You Already Have a Subscription!
                </h5>
                <p style="color:#666; font-size:14px; margin-bottom:20px;">
                    You already have an active subscription.
                    To change your plan, please cancel your
                    current subscription first.
                </p>
                <a href="{{ route('subscription.details') }}"
                   class="cta_org"
                   style="display:inline-block; padding:10px 24px;
                          border-radius:8px; text-decoration:none;">
                    Go to Subscription Details
                </a>
            </div>
        </div>
    </div>
</div>



{{-- ===================== STYLES ===================== --}}
<style>
    
    /* Free trial card */
    .header_free      { background: #fd5602 !important; }
    /* .sub_payment_free { border: 2px solid #fd5602!important; } */
    .recommended_free { background: #002655!important; color: #fff !important; }
    .free_price_main  { color: #fd5602!important; }
    .free_then_text   {
        font-size: 13px; color: #555;
        margin-top: 4px; margin-bottom: 12px; line-height: 1.5;
    }
    .
     { background: #fd5602 !important; border-color: #fd5602 !important; }
    .cta_free_trial:hover { background: #d14803 !important; border-color: #d14803 !important; }

    /* Single document display */
    .single_doc_display {
        background: #f5f7fa;
        border: 1px solid #e0e4ed;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 12px;
    }
    .single_doc_title {
        font-size: 14px;
        font-weight: 500;
        color: #002655;
        display: flex;
        align-items: center;
        line-height: 1.4;
    }

    @media (max-width: 991px) {
        .rec_price_card,
        .free_trial_price_card,
        .othr_price_card { margin-bottom: 24px; }
    }
</style>

{{-- ===================== SCRIPTS ===================== --}}
<script>
    window.currencySymbol = @json($currency_symbol);
</script>

<script>
    $(document).ready(function () {

        $('#menuToggle').click(function () {
            $('#sidebar').toggleClass('open');
        });

        /* ── Single doc dropdown price fetch ── */
        $('.productSelect').change(function () {
            var $this       = $(this);
            var selectedVal = $this.val();
            var parentTab   = $this.closest('.tab-content');
            if (selectedVal) {
                $.ajax({
                    url: "{{ route('document.price') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: { id: selectedVal, _token: '{{ csrf_token() }}' },
                    success: function (data) {
                        parentTab.find('.document_price').contents().filter(function () {
                            return this.nodeType === 3;
                        }).first().replaceWith(
                            currencySymbol + ' ' +
                            parseFloat(data.doc_price).toFixed(2) + ' '
                        );
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching price:', error);
                    }
                });
            } else {
                parentTab.find('.document_price').contents().filter(function () {
                    return this.nodeType === 3;
                }).first().replaceWith('$0.00 ');
            }
        });

        /* ── Subscription plan price on months dropdown change ── */
        $('#months').on('change', function () {
            var selectedMonths = $(this).val();
            var selectedOption = $(this).find('option:selected');
            var planId         = selectedOption.data('id');
            var price          = selectedOption.data('price');
            $.ajax({
                url: "{{ route('get.plan.price') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    number_of_months: selectedMonths,
                    plan_id: planId,
                    price: price,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response && response.success) {
                        var discount_price = parseFloat(response.discount_price).toFixed(2);
                        var basePrice      = parseFloat(response.price).toFixed(2);
                        if (selectedMonths == 1) {
                            $('#old_price').hide();
                            $('#current_price').html(
                                currencySymbol + ' ' + basePrice + '<span>/month</span>'
                            );
                        } else {
                            $('#old_price').show();
                            $('#current_price').html(
                                currencySymbol + ' ' + discount_price + '<span>/month</span>'
                            );
                            $('#old_price').html(
                                currencySymbol + ' 49.90<span>/month</span>'
                            );
                        }
                    }
                }
            });
        });

        /* ── Subscribe Now ── */
        $('#subscribe-btn').on('click', function () {

            // ✅ Fix 1 — Already paid subscription check
            @if(auth()->check() && $has_paid_subscription)
                $('#already_subscribed_modal').modal('show');
                return;
            @endif

            var selectedMonths = $('#months').val();
            var planId         = $('#months').find('option:selected').data('id');
            var documentId     = "{{ $url_document_id }}";
            window.location.href = "{{ url('/checkout') }}"
                + '?type=sub'
                + '&plan_id='     + planId
                + '&months='      + selectedMonths
                + '&document_id=' + documentId;
        });

        /* ── Free Trial ── */
        // $('#free-trial-btn').on('click', function () {
        //     var documentId = "{{ $url_document_id }}";
        //     window.location.href = "{{ url('/checkout') }}"
        //         + '?type=free'
        //         + '&document_id=' + documentId;
        // });

        $('#free-trial-btn').on('click', function () {

            var documentId = "{{ $url_document_id }}";

            // ✅ If document exists
            if(documentId){

                window.location.href =
                    "{{ url('/checkout') }}"
                    + '?type=free'
                    + '&document_id=' + documentId;

            } else {

                // ✅ Direct pricing page access
                window.location.href =
                    "{{ url('/checkout') }}"
                    + '?type=free';

            }

        });

        /* ── Single Document ── */

        // $('#onetime-btn').on('click', function () {
        //     @if($selected_document)
        //         window.location.href = "{{ url('document/' . ($selected_document->slug ?? '')) }}";
        //     @else
        //         var selectedSlug = document.querySelector('.productSelect')
        //                             ?.selectedOptions[0]
        //                             ?.getAttribute('data-slug');
        //         if (selectedSlug) {
        //             window.location.href = "{{ url('document/') }}/" + selectedSlug;
        //         } else {
        //             alert('Please select a document first.');
        //         }
        //     @endif
        // });
        $('#productSelectt').select2({
    width: '100%'
});

        $('#onetime-btn').on('click', function () {

            var documentId = "{{ $url_document_id }}";

            if(documentId){

                window.location.href = "{{ url('/checkout') }}"
                    + '?type=onetime'
                    + '&document_id=' + documentId;

            } else {

                var selectedSlug = document.querySelector('.productSelect')
                                    ?.selectedOptions[0]
                                    ?.getAttribute('data-slug');

                if (selectedSlug) {

                    window.location.href =
                        "{{ url('document') }}/" + selectedSlug;

                } else {

                    alert('Please select a document first.');

                }
            }
        });

    });
</script>
    {{-- ===================== TRIAL EXPIRED POPUP ===================== --}}
@if($show_upgrade_popup)
<div class="modal fade" id="trial_expired_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; padding:10px;">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="text-align:center; padding:10px 30px 24px;">

                {{-- Icon --}}
                {{-- <div style="font-size:54px; margin-bottom:12px;"></div> --}}

                {{-- Heading --}}
                <h4 style="color:#002655; font-weight:700; margin-bottom:10px;">
                    Your Free Trial Has Ended
                </h4>

                {{-- Subtext --}}
                <p style="color:#666; font-size:14px; line-height:1.7; margin-bottom:6px;">
                    We hope you enjoyed exploring our documents
                    during your <strong>7-day free trial!</strong>
                </p>
                <p style="color:#666; font-size:14px; line-height:1.7; margin-bottom:20px;">
                    To <strong>download, edit, and access all documents</strong>
                    without limits, choose a plan below. Cancel anytime.
                </p>

                {{-- Features list --}}
                <div style="background:#f5f7fa; border-radius:10px;
                            padding:14px 20px; margin-bottom:22px;
                            text-align:left;">
                    <div style="display:flex; align-items:center; gap:8px;
                                font-size:13px; color:#002655; margin-bottom:10px;">
                        <span style="color:#1D9E75; font-size:16px; flex-shrink:0;">✅</span>
                        Unlimited document downloads (PDF, Word, Pages)
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;
                                font-size:13px; color:#002655; margin-bottom:10px;">
                        <span style="color:#1D9E75; font-size:16px; flex-shrink:0;">✅</span>
                        Access to all legal documents instantly
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;
                                font-size:13px; color:#002655; margin-bottom:10px;">
                        <span style="color:#1D9E75; font-size:16px; flex-shrink:0;">✅</span>
                        Always updated with latest legal changes
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;
                                font-size:13px; color:#002655;">
                        <span style="color:#1D9E75; font-size:16px; flex-shrink:0;">✅</span>
                        Unlimited editing — cancel anytime
                    </div>
                </div>

                {{-- CTA Button --}}
                <a href="#"
                   onclick="$('#trial_expired_modal').modal('hide'); return false;"
                   class="cta_org"
                   style="display:block; width:100%; padding:13px;
                          border-radius:10px; font-size:15px; font-weight:600;
                          text-decoration:none; margin-bottom:12px;
                          box-sizing:border-box;">
                    Choose a Plan Now
                </a>

                {{-- Skip --}}
                <button type="button"
                        data-bs-dismiss="modal"
                        style="background:none; border:none; color:#aaa;
                               font-size:12px; cursor:pointer;
                               text-decoration:underline; padding:0;">
                    Maybe later
                </button>

            </div>
        </div>
    </div>
</div>

{{-- ✅ Auto open --}}
<script>
    $(document).ready(function() {
        $('#trial_expired_modal').modal('show');
    });
</script>
@endif
@endsection