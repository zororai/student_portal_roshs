<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $sale->sale_number }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; max-width: 300px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0; }
        .header p { margin: 5px 0; }
        .details { margin-bottom: 10px; }
        .items { border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 10px 0; }
        .item { display: flex; justify-content: space-between; margin: 5px 0; }
        .totals { padding-top: 10px; }
        .total-row { display: flex; justify-content: space-between; }
        .total-row.grand { font-weight: bold; font-size: 14px; border-top: 1px solid #000; padding-top: 5px; margin-top: 5px; }
        .footer { text-align: center; margin-top: 20px; font-size: 10px; }
        @media print { body { max-width: 100%; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>ROSHS</h1>
        <p>Receipt</p>
        <p>{{ $sale->sale_number }}</p>
    </div>

    <div class="details">
        <div>Date: {{ $sale->sale_date->format('d/m/Y H:i') }}</div>
        <div>Cashier: {{ $sale->seller->name ?? 'N/A' }}</div>
        @if($sale->customer_name)
        <div>Customer: {{ $sale->customer_name }}</div>
        @endif
        @if($sale->customer_phone)
        <div>Phone: {{ $sale->customer_phone }}</div>
        @endif
    </div>

    <div class="items">
        @foreach($sale->items as $item)
        <div class="item">
            <span>{{ $item->product_name }}</span>
            <span>{{ $item->quantity }} x ${{ number_format($item->unit_price, 2) }}</span>
        </div>
        <div class="item" style="padding-left: 20px;">
            <span></span>
            <span>${{ number_format($item->total_price, 2) }}</span>
        </div>
        @endforeach
    </div>

    <div class="totals">
        <div class="total-row grand">
            <span>TOTAL:</span>
            <span>${{ number_format($sale->total_amount, 2) }}</span>
        </div>
        <div class="total-row">
            <span>Paid ({{ ucfirst($sale->payment_method) }}):</span>
            <span>${{ number_format($sale->amount_paid, 2) }}</span>
        </div>
        <div class="total-row">
            <span>Change:</span>
            <span>${{ number_format($sale->change_given, 2) }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
