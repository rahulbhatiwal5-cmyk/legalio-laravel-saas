<style>
    .tableBorder {
        border: 1px solid black !important;
    }
</style>
<div class="invoice-print">
    <div class="invoice-wrap">
        <div class="invoice-brand text-center">
            <img src="{{ asset('assets/img/logo-legalio-mx-new.svg') }}" srcset="{{ asset('assets/img/logo-legalio-mx-new.svg') }} 2x" alt="">
            <h4 class="mb-0 fw-bold text-dark mt-3">Invoice</h4>
        </div>

        <div class="invoice-head">
            <div class="invoice-desc">
                <ul class="list-plain">
                    <li class="invoice-id"><span>Invoice ID :</span> <span>{{ $invoice_id ?? '000XXX' }}</span></li>
                    <li class="invoice-date"><span>Date :</span>
                        <span>{{ $invoice_date ?? now()->format('d M, Y') }}</span></li>
                    <li class="invoice-date"><span>To :</span> <span>{{ $customer_name ?? 'Customer Name' }}</span></li>
                    <li class="invoice-date"><span>{!! nl2br(e($customer_address ?? 'Customer Address')) !!}</span><span></span></li>
                    <li class="invoice-date"><span>{{ $customer_email ?? 'N/A' }}</span><span></span></li>
                </ul>
            </div>
        </div>

        <div class="invoice-bills">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr class="tableBorder">
                            <th class="w-150px tableBorder">Image</th>
                            <th class="w-60 tableBorder">Description</th>
                            <th class="tableBorder">Qty</th>
                            <th class="tableBorder">Price</th>
                            <th class="tableBorder">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items ?? [] as $item)
                            <tr class="tableBorder">
                                <td class="tableBorder">
                                    @if (!empty($item['image']))
                                        <img src="{{ asset($item['image']) }}" alt="Document Image"
                                            style="width: 60px; height: auto;">
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="tableBorder">{{ $item['description'] }}</td>
                                <td class="tableBorder">{{ $item['quantity'] }}</td>
                                <td class="tableBorder">${{ number_format($item['price'], 2) }}</td>
                                <td class="tableBorder">${{ number_format($item['price'] * $item['quantity'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="tableBorder">
                        <tr>
                            <td colspan="2">Grand Total</td>
                            <td colspan="2"></td>
                            <td class="tableBorder">${{ number_format($total ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div><!-- .invoice-bills -->

    </div><!-- .invoice-wrap -->
</div><!-- .invoice -->
{{--  --}}