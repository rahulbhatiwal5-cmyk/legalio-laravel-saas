@extends('user_dashboard_layout.master')
@section('content')
    <div class="uer_nm">
        <h1>
            Receipts & Invoices
        </h1>
    </div>
    <div class="scroll_div">
        <div class="main_ryt">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="invoice-upd">Order</th>
                            <th class="invoice-upd">Date</th>
                            <th class="invoice-upd">Status</th>
                            <th class="invoice-upd">Product</th>
                            <th class="invoice-upd">Payment Method</th>
                            <th class="invoice-upd">Total</th>
                            <th class="invoice-upd">Invoice</th>
                            <th class="invoice-upd">Receipt</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach( $invoices as $order)
                            <tr>
                                <td class="invo-data">{{ $loop->iteration + $invoices->firstItem() - 1 }}</td>
                                <td class="invo-data">{{ $order->created_at?->format('d/m/Y') }}</td>
                                <td class="invo-data">{{ $order->status ?? '' }}</td>
                                <td class="invo-data">{{ $order->document?->title ?? 'N/A' }}</td>
                                <td class="invo-data">{{ $order->transaction->type ?? ''  }}</td>
                                <td class="invo-data">{{ '$'.$order->amount ?? ''  }}</td>
                                {{-- <td><a href="javascript:void(0)">Factura</a></td> --}}
                                <td class="invo-data"><a href="{{ route('user.order.invoice', ['id' => $order->order_num])}}">Factura</a></td>
                                <td class="invo-data"><a href="{{ route('download.PDF', ['id' => $order->id]) }}">Recibo</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($invoices->count() > 0)
            <div class="pagination-container">
                {{ $invoices->links('pagination::bootstrap-4') }}
            </div>
        @else
            <div class="no-records">
                <p>No invoices found.</p>
            </div>
        @endif
        </div>
    </div>
@endsection
