@extends('user_dashboard_layout.master')
@section('content')

@php
    $user         = auth()->user();
    $subscription = \App\Models\Subscription::where('user_id', $user->id)
                        ->whereIn('status', ['active', 'trialing'])
                        ->latest()
                        ->first();

    $freeTrial    = \App\Models\FreeTrail::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->where('end_date', '>=', now())
                        ->first();

    $plan         = $subscription?->plan;
    $currency     = optional(web_setting('country_currency_symbol'))->value ?? '$';

    // All subscriptions history
    $allSubscriptions = \App\Models\Subscription::where('user_id', $user->id)
                            ->with('plan')
                            ->latest()
                            ->get();
@endphp

<style>
    .sub_detail_card {
        background: #fff;
        border: 1.5px solid #e0e4ed;
        border-radius: 14px;
        padding: 24px 28px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .sub_detail_card .card_heading {
        font-size: 16px;
        font-weight: 700;
        color: #002655;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sub_status_badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status_active    { background: #e8f8f0; color: #fd5602; }
   .status_trialing {
	background: #fd56022b;
	color: #012555;
}
    .status_cancelled { background: #fde8e8; color: #002655; }
    .status_paused    { background: #e8ecf4; color: #002655; }
    .status_expired   { background: #f0f0f0; color: #888; }

    .sub_info_row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f2f5;
        font-size: 14px;
    }
    .sub_info_row:last-child { border-bottom: none; }
    .sub_info_label { color: #888; font-weight: 500; }
    .sub_info_value { color: #002655; font-weight: 600; }

    /* Free Trial Card */
    .free_trial_card {
        border: 2px solid #fd5602 !important;
    }
    .free_trial_card .card_heading { color: #fd5602; }
    .trial_progress_bar {
        height: 8px;
        background: #e0f5ee;
        border-radius: 10px;
        margin: 12px 0;
        overflow: hidden;
    }
    .trial_progress_fill {
        height: 100%;
        background: linear-gradient(90deg, #fd5602, #fff);
        border-radius: 10px;
        transition: width 0.5s;
    }

    /* Cancel button */
    .cancel_sub_btn {
        background: transparent;
        border: 1.5px solid #fd5602;
        color: #fd5602;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .cancel_sub_btn:hover {
        background: #fd5602;
        color: #fff;
    }

    /* Upgrade button */
    .upgrade_sub_btn {
        background: #002655;
        border: none;
        color: #fff;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .upgrade_sub_btn:hover {
        background: #001a3d;
        color: #fff;
    }

    /* History table */
    .sub_history_table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .sub_history_table th {
        background: #f5f7fa;
        color: #002655;
        font-weight: 600;
        padding: 10px 14px;
        text-align: left;
        border-bottom: 2px solid #e0e4ed;
    }
    .sub_history_table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f0f2f5;
        color: #444;
        vertical-align: middle;
    }
    .sub_history_table tr:last-child td { border-bottom: none; }
    .sub_history_table tr:hover td { background: #f9fafb; }

    /* No subscription */
    .no_sub_box {
        text-align: center;
        padding: 40px 20px;
        color: #888;
    }
    .no_sub_box h3 { color: #002655; margin-bottom: 10px; }
    .no_sub_box p  { font-size: 14px; margin-bottom: 20px; }

    /* Confirm modal */
    .confirm_modal_body {
        text-align: center;
        padding: 10px 0;
    }
    .confirm_modal_body .warning_icon {
        font-size: 48px;
        margin-bottom: 12px;
    }
    .confirm_modal_body h5 {
        color: #002655;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .confirm_modal_body p {
        color: #666;
        font-size: 14px;
        margin-bottom: 0;
    }
</style>

<div class="user_info">
    <div class="uer_nm">
        <h2>Subscription Details</h2>
    </div>

    <div class="scroll_div">

        {{-- ══════════════════════════════════
             FREE TRIAL CARD
        ══════════════════════════════════ --}}
        @if($freeTrial)
        @php
            $trialStart    = \Carbon\Carbon::parse($freeTrial->start_date);
            $trialEnd      = \Carbon\Carbon::parse($freeTrial->end_date);
            $totalDays     = $trialStart->diffInDays($trialEnd);
            $usedDays      = $trialStart->diffInDays(now());
            $remainingDays = max(0, $trialEnd->diffInDays(now()));
            $progressPct   = $totalDays > 0 
                             ? min(100, round(($usedDays / $totalDays) * 100)) 
                             : 100;
        @endphp
        <div class="sub_detail_card free_trial_card">
            <div class="card_heading">
                <span>Free Trial Active</span>
                <span class="sub_status_badge status_trialing">TRIAL</span>
            </div>

            <div class="sub_info_row">
                <span class="sub_info_label">Trial Started</span>
                <span class="sub_info_value">
                    {{ \Carbon\Carbon::parse($freeTrial->start_date)->format('M d, Y') }}
                </span>
            </div>
            <div class="sub_info_row">
                <span class="sub_info_label">Trial Ends</span>
                <span class="sub_info_value" style="color:#fd5602;">
                    {{ \Carbon\Carbon::parse($freeTrial->end_date)->format('M d, Y') }}
                </span>
            </div>
            <div class="sub_info_row">
                <span class="sub_info_label">Days Remaining</span>
                <span class="sub_info_value">{{ $remainingDays }} days</span>
            </div>

            {{-- Progress Bar --}}
            <div style="margin-top:12px;">
                <div style="display:flex; justify-content:space-between; 
                            font-size:12px; color:#888; margin-bottom:4px;">
                    <span>Day {{ $usedDays }}</span>
                    <span>Day {{ $totalDays }}</span>
                </div>
                <div class="trial_progress_bar">
                    <div class="trial_progress_fill" 
                         style="width:{{ $progressPct }}%"></div>
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:16px; flex-wrap:wrap;">
                <a href="{{ url('/pricing') }}" class="upgrade_sub_btn">
                    ⬆ Upgrade Now
                </a>
                <button class="cancel_sub_btn" 
                        onclick="$('#cancel_trial_modal').modal('show')">
                    Cancel Trial
                </button>
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════
             ACTIVE SUBSCRIPTION CARD
        ══════════════════════════════════ --}}
        @if($subscription)
        @php
            $statusClass = match($subscription->status) {
                'active'    => 'status_active',
                'trialing'  => 'status_trialing',
                'cancel',
                'canceled'  => 'status_cancelled',
                'paused',
                'suspended' => 'status_paused',
                default     => 'status_expired',
            };
            $gateway = $subscription->stripe_subscription_id ? 'Stripe' : 'PayPal';
        @endphp

        <div class="sub_detail_card">
            <div class="card_heading">
                <span>Current Subscription</span>
                <span class="sub_status_badge {{ $statusClass }}">
                    {{ ucfirst($subscription->status) }}
                </span>
            </div>

            <div class="sub_info_row">
                <span class="sub_info_label">Plan</span>
                <span class="sub_info_value">
                    {{ $plan->number_of_months ?? '-' }}
                    {{ ($plan->number_of_months ?? 0) == 1 ? 'Month' : 'Months' }}
                    Plan
                </span>
            </div>
            {{-- <div class="sub_info_row">
                <span class="sub_info_label">Price</span>
                <span class="sub_info_value">
                    {{ $currency }}{{ number_format($plan->price ?? 0, 2) }}/month
                </span>
            </div> --}}
                        <div class="sub_info_row">
                <span class="sub_info_label">Price</span>
                <span class="sub_info_value">
                    @if(optional($plan)->price === null)
                        {{ $currency }}0.00/week
                    @else
                        {{ $currency }}{{ number_format($plan->price, 2) }}/month
                    @endif
                </span>
            </div>
            {{-- <div class="sub_info_row">
                <span class="sub_info_label">Payment Gateway</span>
                <span class="sub_info_value">{{ $gateway }}</span>
            </div> --}}
            <div class="sub_info_row">
                <span class="sub_info_label">Start Date</span>
                <span class="sub_info_value">
                    {{ $subscription->start_date 
                       ? \Carbon\Carbon::parse($subscription->start_date)->format('M d, Y') 
                       : '-' }}
                </span>
            </div>
            <div class="sub_info_row">
                <span class="sub_info_label">Current Period</span>
                <span class="sub_info_value">
                    {{ $subscription->current_period_start_date 
                       ? \Carbon\Carbon::parse($subscription->current_period_start_date)->format('M d, Y') 
                       : '-' }}
                    →
                    {{ $subscription->current_period_end_date 
                       ? \Carbon\Carbon::parse($subscription->current_period_end_date)->format('M d, Y') 
                       : '-' }}
                </span>
            </div>

            @if($subscription->is_paused)
            <div class="sub_info_row">
                <span class="sub_info_label">Paused Until</span>
                <span class="sub_info_value" style="color:#856404;">
                    {{ $subscription->pause_end_at 
                       ? \Carbon\Carbon::parse($subscription->pause_end_at)->format('M d, Y') 
                       : 'Indefinite' }}
                </span>
            </div>
            @endif

            <div class="sub_info_row">
                <span class="sub_info_label">Subscription ID</span>
                <span class="sub_info_value" style="font-size:12px; color:#888;">
                    {{ $subscription->stripe_subscription_id 
                       ?? $subscription->paypal_subscription_id 
                       ?? '-' }}
                </span>
            </div>

            {{-- Cancel button (sirf active subscription pe) --}}
            @if(in_array($subscription->status, ['active', 'trialing']))
            <div style="margin-top:16px;">
                <button class="cancel_sub_btn" 
                        id="cancel_subscription_btn"
                        data-id="{{ $subscription->id }}"
                        data-gateway="{{ $gateway }}"
                        onclick="$('#cancel_sub_modal').modal('show')">
                    Cancel Subscription
                </button>
            </div>
            @endif
        </div>

        @else
        {{-- No active subscription --}}
        @if(!$freeTrial)
        <div class="sub_detail_card">
            <div class="no_sub_box">
                <div style="font-size:48px; margin-bottom:12px;">📄</div>
                <h3>No Active Subscription</h3>
                <p>You don't have any active subscription right now.</p>
                <a href="{{ url('/pricing') }}" class="upgrade_sub_btn">
                    View Plans
                </a>
            </div>
        </div>
        @endif
        @endif

        {{-- ══════════════════════════════════
             SUBSCRIPTION HISTORY
        ══════════════════════════════════ --}}
        @if($allSubscriptions->count() > 0)
        <div class="sub_detail_card">
            <div class="card_heading">
                <span>Subscription History</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="sub_history_table">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Gateway</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allSubscriptions as $sub)
                        @php
                            $subPlan = $sub->plan;
                            $subGateway = $sub->stripe_subscription_id ? 'Stripe' : 'PayPal';
                            $subStatusClass = match($sub->status) {
                                'active'    => 'status_active',
                                'trialing'  => 'status_trialing',
                                'cancel',
                                'canceled'  => 'status_cancelled',
                                'paused'    => 'status_paused',
                                default     => 'status_expired',
                            };
                        @endphp
                        <tr>
                            <td>
                                {{ $subPlan->number_of_months ?? '-' }}
                                {{ ($subPlan->number_of_months ?? 0) == 1 ? 'Month' : 'Months' }}
                            </td>
                            <td>{{ $subGateway }}</td>
                            <td>
                                <span class="sub_status_badge {{ $subStatusClass }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $sub->start_date 
                                   ? \Carbon\Carbon::parse($sub->start_date)->format('M d, Y') 
                                   : '-' }}
                            </td>
                            <td>
                                {{ $sub->end_date 
                                   ? \Carbon\Carbon::parse($sub->end_date)->format('M d, Y') 
                                   : '-' }}
                            </td>
                            <td>
                                {{ $currency }}{{ number_format($subPlan->price ?? 0, 2) }}/mo
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>{{-- end scroll_div --}}
</div>

{{-- ══════════════════════════════════
     CANCEL SUBSCRIPTION MODAL
══════════════════════════════════ --}}
<div class="modal fade" id="cancel_sub_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; padding:10px;">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" 
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body confirm_modal_body">
                <div class="warning_icon"></div>
                <h5>Cancel Subscription?</h5>
                <p>
                    Are you sure you want to cancel your subscription?
                    You will lose access at the end of your current billing period.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" 
                        data-bs-dismiss="modal"
                        style="border-radius:8px; padding:8px 24px;">
                    Keep Subscription
                </button>
                <button type="button" 
                        class="cancel_sub_btn" 
                        id="confirm_cancel_btn"
                        style="padding:8px 24px;">
                    Yes, Cancel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════
     CANCEL FREE TRIAL MODAL
══════════════════════════════════ --}}
<div class="modal fade" id="cancel_trial_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; padding:10px;">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" 
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body confirm_modal_body">
                <div class="warning_icon"></div>
                <h5>Cancel Free Trial?</h5>
                <p>
                    Are you sure you want to cancel your free trial?
                    You will lose preview access immediately.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" 
                        data-bs-dismiss="modal"
                        style="border-radius:8px; padding:8px 24px;">
                    Keep Trial
                </button>
                <button type="button" 
                        class="cancel_sub_btn" 
                        id="confirm_cancel_trial_btn">
                    Yes, Cancel Trial
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {

    // ── Cancel Subscription ──
    $('#confirm_cancel_btn').on('click', function() {
        let subscriptionId = $('#cancel_subscription_btn').data('id');
        let btn = $(this);

        btn.text('Cancelling...').prop('disabled', true);

        $.ajax({
            url: "{{ route('subscription.cancel') }}",
            type: 'POST',
            data: {
                subscription_id: subscriptionId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#cancel_sub_modal').modal('hide');

                if (response.success) {
                    Swal.fire({
                        icon:  'success',
                        title: 'Cancelled!',
                        text:  response.message ?? 
                               'Your subscription has been cancelled.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon:  'error',
                        title: 'Error',
                        text:  response.message ?? 
                               'Something went wrong. Please try again.',
                    });
                    btn.text('Yes, Cancel').prop('disabled', false);
                }
            },
            error: function() {
                Swal.fire({
                    icon:  'error',
                    title: 'Error',
                    text:  'Something went wrong. Please try again.',
                });
                btn.text('Yes, Cancel').prop('disabled', false);
            }
        });
    });

    // ── Cancel Free Trial ──
    $('#confirm_cancel_trial_btn').on('click', function() {
        let btn = $(this);
        btn.text('Cancelling...').prop('disabled', true);

        $.ajax({
            url: "{{ route('free_trial.cancel') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#cancel_trial_modal').modal('hide');

                if (response.success) {
                    Swal.fire({
                        icon:  'success',
                        title: 'Trial Cancelled!',
                        text:  'Your free trial has been cancelled.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon:  'error',
                        title: 'Error',
                        text:  response.message ?? 'Something went wrong.',
                    });
                    btn.text('Yes, Cancel Trial').prop('disabled', false);
                }
            },
            error: function() {
                Swal.fire({
                    icon:  'error',
                    title: 'Error',
                    text:  'Something went wrong. Please try again.',
                });
                btn.text('Yes, Cancel Trial').prop('disabled', false);
            }
        });
    });
});
</script>

@endsection