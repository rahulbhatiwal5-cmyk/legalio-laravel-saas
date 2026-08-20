@extends('user_dashboard_layout.master')
<style>
.doc-title {
    padding: 21px 87px 1px;
}

img.order-img {
    max-width: 20%;
}

.billing-inform {
    margin: 10px 0px 43px 73px;
}
.order-title{
    font-size: 20px;
}
</style>
@section('content')
    {{-- <link rel="stylesheet" href="{{ asset('admin-theme/assets/css/dashlite.css') }}?time={{ time() }}"> --}}

    <div class="nk-content">
        <div class="container-fluid">
            <div class="row main_section_div">
                <div class="col col-md-12 doc-left-content">
                    <div class="doc-top-butns2 mt-0">
                        <div class="form-group d-flex justify-content-between">
                            <b><h4>Order Details</h4></b>
                        </div>
                    </div>
                    <div class="card card-bordered card-preview mt-4">
                        <div class="card-inner">
                            <div class="col-md-12 doc-title">
                                <div class="form-group ord-det">
                                    <label class="form-label" for="title">
                                        <b>
                                            <h3 class="order-title">Order Detail <span class="required">#{{ $order->order_num ?? '' }}</span></h3>
                                        </b>
                                    </label>

                                    @if ($payment->type == 'stripe')
                                        <p>Payment via Credit Card
                                            <span
                                                class="card_id">({{ $paymentIntent->payment_method ?? 'No record found' }})</span>.
                                            </br>
                                            Created on <span class="date">{{ $formattedDate ?? '' }}</span> @ <span
                                                class="time">{{ $order->created_at->format('H:i') }}</span>.
                                            </br>
                                            Paid on <span class="date">{{ $formattedDate ?? '' }}</span> @ <span
                                                class="time">{{ $order->transactions->created_at->format('H:i') }}</span>.
                                        </p>
                                    @elseif($payment->type == 'paypal')
                                        <p>Payment via PayPal
                                            <span class="card_id">({{ $paymentMethod ?? 'No record found' }})</span>.
                                            </br>
                                            Created on <span class="date">{{ $formattedDate ?? '' }}</span> @ <span
                                                class="time">{{ $order->created_at->format('H:i') }}</span>.
                                            </br>
                                            Paid on <span class="date">{{ $formattedDate ?? '' }}</span> @ <span
                                                class="time">{{ $order->transactions->created_at->format('H:i') }}</span>.
                                        </p>
                                    @else
                                        <p>Payment Method:
                                            <span class="card_id">No record found</span>.
                                            Created on <span class="date">{{ $formattedDate ?? '' }}</span> @ <span
                                                class="time">{{ $order->created_at->format('H:i') }}</span>.
                                            Paid on <span class="date">{{ $formattedDate ?? '' }}</span> @ <span
                                                class="time">{{ $order->transactions->created_at->format('H:i') }}</span>.
                                        </p>
                                    @endif
                                </div>

                            </div>
                            {{-- @endif --}}
                            <div class="col-md-12 mt-2 billing-inform">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-sm">

                                            <h5 class="mt-2">Customer Information
                                                <em class="icon ni ni-cross" type="button" id="cross_btn_2"
                                                    style="display: none;"></em>
                                            </h5>
                                            <div class="customer_detail mt-1 mb-1">
                                                <b>First Name:</b><span>{{ $order->user->first_name ?? '' }}{{ $order->user->last_name ?? '' }}</span><br>
                                                <b>Last Name:</b><span>{{ $order->user->last_name ?? '' }}</span><br>
                                                <b>Email:</b><span>{{ $order->user->email ?? '' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm">
                                            <div class="d-flex justify-content-between">
                                                <h5 class="mt-2">Billing Information <em class="icon ni ni-cross"
                                                        type="button" id="cross_btn" style="display: none;"></em></h5>
                                            </div>
                                            <div id="billing_detail" class="billing_detail mt-1">
                                                <b>Company:</b> <span>{{ $userAddress->company ?? '' }}</span><br>
                                                <b>Address:</b> <span>{{ $userAddress->address ?? '' }}</span><br>
                                                <b>City:</b> <span>{{ $userAddress->city ?? '' }}</span><br>
                                                <b>ZIP:</b><span>{{ $userAddress->postal_code ?? '' }}</span><br>
                                                <b>State:</b><span>{{ $userAddress->state ?? '' }}</span><br>
                                                <b>Country:</b><span>{{ $userAddress->country ?? '' }}</span><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($order->order_type == 'one_time')
                                <hr>
                                <div class="d-flex justify-content-between align-items-center" class="recipt-heading">
                                    <h5 class="mt-4">Receipt</h5>
                                    <a href="javascript:void(0)" class="btn btn-lg btn-white show-invoice-modal"
                                        data-order-id="{{ $order->id }}">
                                        <em class="icon ni ni-printer-fill">download</em>
                                        {{-- <em class="icon ni ni-printer-fill"><i class="bi bi-printer-fill"></i></em> --}}

                                    </a>

                                    <!-- Invoice Modal -->
                                    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog"
                                        aria-labelledby="invoiceModalTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="invoiceModalTitle">Invoice Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="invoice-container">
                                                        <!-- Placeholder while loading -->
                                                        <div class="d-flex justify-content-center my-5">
                                                            <div class="spinner-border" role="status">
                                                                <span class="sr-only">Loading...</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>

                                                    <button type="button" class="btn btn-primary print-modal-invoice"
                                                        disabled>
                                                        <em class="icon ni ni-printer-fill"></em> Print
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hidden div for printing -->
                                    <div id="printArea" style="display: none;"></div>
                                </div>
                            @endif

                            <hr>
                            @if ($order->order_type == 'one_time')
                                <div class="col-md-12 mt-2">
                                    <div class="invoice-bills">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="w-150px">Image</th>
                                                        <th class="w-60"></th>
                                                        <th>Price</th>
                                                        <th></th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <tr>
                                                        <td>
                                                            <?php
                                                            $image_path = getStorageFilepath($order->document->document_image ?? '');
                                                            $quantity = 1;
                                                            $doc_price = $order->document->doc_price ?? 0;
                                                            $default_price = web_setting('default_document_price');
                                                            
                                                            if ($doc_price) {
                                                                $price = $doc_price;
                                                            } else {
                                                                $price = $default_price->value ?? 199;
                                                            }
                                                            
                                                            $amount = $price * $quantity;
                                                            ?>
                                                            <img src="{{ $image_path }}" alt="" class="order-img">
                                                        </td>
                                                        <td></td>
                                                        <td>${{ number_format($price, 2) }}</td>
                                                        <td></td>
                                                        <td>${{ number_format($amount, 2) }}</td>
                                                    </tr>

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td colspan="2">Subtotal</td>
                                                        <td>${{ $amount ?? '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td colspan="2">TAX</td>
                                                        <td>$00.00</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td colspan="2">Grand Total</td>
                                                        <td>${{ $amount ?? '' }}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            @elseif($order->order_type == 'subscription')
                                <div class="col-md-12 mt-2">
                                    <h5>Subscription Details</h5>
                                    <div class="subscription-bills">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Credit</th>
                                                        <th>Interval</th>
                                                        <th>Start Date</th>
                                                        <th>Next Invoice Date</th>
                                                        <th>Status</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                
                                                <tbody>
                                                    <tr>
                                                        <?php
                                                            $quantity = 1;
                                                            $amount = $order->amount;
                                                            $interval = $subscription?->plan?->interval;
                                                            $start_date = Carbon\Carbon::parse($subscription?->current_period_start_date);
                                                            $startDate = $start_date->translatedFormat('F d, Y');
                                                            $end_date = Carbon\Carbon::parse($subscription?->current_period_end_date);
                                                            $endDate = $end_date->translatedFormat('F d, Y');
                                                            $credit = web_setting('fair_use_document_limit')->value;
                                                        ?>
                                                        <td>{{ $credit ?? '' }}</td>
                                                        <td>{{ $interval ?? '' }}</td>
                                                        <td>{{ $startDate ?? '' }}</td>
                                                        <td>{{ $endDate ?? '' }}</td>
                                                        <td>{{ $subscription?->status ?? '' }}</td>
                                                        <td>${{ number_format($amount, 2) }}/@if ($interval == 'monthly')
                                                                month
                                                            @elseif($interval == 'yearly')
                                                                year
                                                            @endif
                                                        </td>
                                                    </tr>

                                                </tbody>

                                            </table>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        
    </div>

    <script>
        $(document).ready(function() {
            $('#dropdownMenuButton').click(function() {
                $('.download-options').toggle();
            })
        })
    </script>

    <script>
        let currentOrderId = null;

        $(document).ready(function() {
            // Open modal and load invoice content
            $('.show-invoice-modal').on('click', function() {
                currentOrderId = $(this).data('order-id');

                $('#invoiceModal').modal('show');

                $('.invoice-container').html(`
                    <div class="d-flex justify-content-center my-5">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                `);

                $('.print-modal-invoice').prop('disabled', true);

                $.ajax({
                    url: "{{ route('order.invoice.content', ['id' => ':id']) }}".replace(
                        ':id', currentOrderId),
                    type: 'GET',
                    success: function(response) {
                        $('.invoice-container').html(response);
                        $('.print-modal-invoice').prop('disabled', false);
                    },
                    error: function() {
                        $('.invoice-container').html(
                            '<div class="alert alert-danger">Failed to load invoice</div>');
                        $('.print-modal-invoice').prop('disabled', true);
                    }
                });
            });

            // Print invoice (PDF download)
            $('.print-modal-invoice').on('click', function() {
                if (!currentOrderId) {
                    alert('Order ID not found.');
                    return;
                }

                const url = `/account/orders/${currentOrderId}/download-invoice`;
                window.open(url, '_blank'); // Trigger Laravel PDF response
            });
        });
    </script>
@endsection
