<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('assets/img/logo-legalio-mx-new.svg') }}" width="150" alt="Logo">
        <h2>Invoice</h2>
    </div>

    <p><strong>Invoice ID:</strong> {{ $invoice_id }}</p>
    <p><strong>Date:</strong> {{ $invoice_date }}</p>

    <p><strong>To:</strong> {{ $customer_name ?? 'N/A'}}<br>
    {{ $customer_address ?? 'N/A' }}<br>
    {{ $customer_email ?? 'N/A' }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items ?? [] as $item)
            <tr>
                <td>
                    @if(!empty($item['image_approaches']))
                        <!-- Try multiple approaches to display the SVG - different PDF generators may prefer different approaches -->
                        
                    
                        
                        <!-- Approach: Standard img tag with base64 -->
                        <img 
                            src="{{ $item['image_approaches']['base64'] }}" 
                            width="60" 
                            height="60"
                            style="display: inline-block; vertical-align: middle;"
                            alt="Document Image">
                            
                 
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $item['description'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>${{ number_format($item['price'], 2) }}</td>
                <td>${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="right"><strong>Grand Total</strong></td>
                <td>${{ number_format($total ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
