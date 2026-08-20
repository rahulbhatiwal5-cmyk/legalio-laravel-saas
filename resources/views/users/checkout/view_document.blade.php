@extends('users_layout.custom_header')
@section('title', $document->title ?? 'Document Preview')
@section('content')

@php
    // Free trial check
    $is_free_trial = \App\Models\FreeTrail::where('user_id', auth()->id())
                        ->where('status', 'active')
                        ->where('end_date', '>=', now())
                        ->first();
@endphp

<style>
    /* Copy protection */
    #free_trial_preview,
    #free_trial_preview * {
        user-select: none !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        cursor: default !important;
    }

    /* Top bar */
    .free_trial_topbar {
        background: linear-gradient(135deg, rgb(1, 37, 85), #fff);
        color: #fff;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 999;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .free_trial_topbar .trial_info {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        font-weight: 500;
    }
    .free_trial_topbar .trial_badge {
        background: #fff;
        color: #fd5602;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }
    .free_trial_topbar .upgrade_btn {
        background: #fff;
        color: #fd5602;
        border: none;
        padding: 8px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .free_trial_topbar .upgrade_btn:hover {
        background: #fd5602;
        color:#fff;
    }

    /* Download blocked overlay button */
    .download_block_btn {
        display: flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        border: 1.5px solid #fd5602;
        color: #fd5602;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        margin: 16px auto;
        transition: all 0.3s ease;
    }
    .download_block_btn:hover {
        background: #fd5602;
        color: #fff;
    }

    /* Document preview container */
    .free_preview_container {
        max-width: 860px;
        margin: 30px auto;
        padding: 0 20px 60px;
    }
    .free_preview_header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .free_preview_header h1 {
        font-size: 22px;
        font-weight: 700;
        color: #002655;
    }
    .free_preview_card {
        background: #fff;
        border: 1px solid #e0e4ed;
        border-radius: 12px;
        padding: 40px 48px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        font-family: 'Times New Roman', serif;
        font-size: 15px;
        line-height: 1.8;
        color: #222;
        position: relative;
    }

    /* Trial ends timer */
.trial_ends_bar {
	background: #fd560226;
	border: 1px solid #fd560238;
	border-radius: 8px;
	padding: 10px 16px;
	margin-bottom: 20px;
	display: flex;
	align-items: center;
	gap: 8px;
	font-size: 13px;
	color: #012555;
	/* text-align: center; */
	justify-content: center;
}

    /* Upgrade modal */
    .upgrade_plan_item {
        border: 1.5px solid #e0e4ed;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .upgrade_plan_item:hover {
        border-color: #002655;
        background: #f5f7fa;
    }
    .upgrade_plan_item.selected {
        border-color: #002655;
        background: #e8ecf4;
    }
</style>

{{-- ✅ Free Trial Top Bar --}}
<div class="free_trial_topbar">
    <div class="trial_info">
        <span class="trial_badge">FREE TRIAL</span>
        <span>
            Preview mode — 
            @if($is_free_trial)
                Trial ends: <strong>{{ \Carbon\Carbon::parse($is_free_trial->end_date)->format('M d, Y') }}</strong>
            @endif
        </span>
    </div>
    <button class="upgrade_btn" onclick="$('#upgrade_modal').modal('show')">
        ⬆ Upgrade to Download
    </button>
</div>

<div class="free_preview_container">

    {{-- Trial ends warning --}}
    @if($is_free_trial)
    <div class="trial_ends_bar">
         Your free trial ends on 
        <strong>{{ \Carbon\Carbon::parse($is_free_trial->end_date)->format('F d, Y') }}</strong>.
        Upgrade now to keep access and download.
    </div>
    @endif

    {{-- Header --}}
    <div class="free_preview_header">
        <h1>{{ $document->title ?? 'Document Preview' }}</h1>
        <button class="download_block_btn" onclick="$('#upgrade_modal').modal('show')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" 
                 stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Download Document
        </button>
    </div>

    {{-- ✅ Document Content (Copy Protected) --}}
    <div class="free_preview_card" id="free_trial_preview">
        @if($contractContent)
            {!! $contractContent->html !!}
        @else
            <p style="color:#888; text-align:center;">
                Document content is being prepared. Please check back shortly.
            </p>
        @endif
    </div>

</div>

{{-- ✅ Upgrade Modal --}}
<div class="modal fade" id="upgrade_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; padding:10px;">
            <div class="modal-header border-0">
                <h5 class="modal-title" style="color:#002655; font-weight:700;">
                    Upgrade to Download
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="color:#555; font-size:14px; margin-bottom:16px;">
                    Your free trial allows previewing only. 
                    Choose a plan to download instantly.
                </p>

                @php
                    $upgrade_plans   = \App\Models\Plans::orderBy('number_of_months')->get();
                    $currency_symbol = optional(web_setting('country_currency_symbol'))->value ?? '$';
                @endphp

                <div class="upgrade_plans_list">
                    @foreach($upgrade_plans as $uplan)
                    @if($uplan->number_of_months != 24)

                    <div class="upgrade_plan_item"
                         onclick="goToUpgrade({{ $uplan->id }}, {{ $uplan->number_of_months }})">
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
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                <p style="font-size:11px; color:#aaa; text-align:center; margin-top:12px;">
                    Your free trial will be cancelled upon upgrade.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Copy Protection Script --}}
<script>
    // Block right click
    document.getElementById('free_trial_preview')
        .addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });

    // Block copy
    document.getElementById('free_trial_preview')
        .addEventListener('copy', function(e) {
            e.preventDefault();
            e.clipboardData.setData('text/plain', '');
            return false;
        });

    // Block keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        var ctrl = e.ctrlKey || e.metaKey;
        if (ctrl && ['a','c','u','s','p'].indexOf(e.key.toLowerCase()) !== -1) {
            e.preventDefault();
            return false;
        }
        if (e.key === 'F12' || (ctrl && e.shiftKey)) {
            e.preventDefault();
            return false;
        }
    });

    // Go to checkout
    function goToUpgrade(planId, months) {
        window.location.href = "{{ url('/checkout') }}"
            + '?type=sub'
            + '&plan_id='   + planId
            + '&months='    + months
            + '&document_id={{ $document->id ?? '' }}'
            + '&cancel_free_trial=1';
    }
</script>

@endsection