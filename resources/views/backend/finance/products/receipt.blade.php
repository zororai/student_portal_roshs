<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $sale->sale_number }}</title>
    <style>
        @page { size: 58mm auto; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 10px; width: 58mm; max-width: 58mm; margin: 0 auto; padding: 3mm; }
        .header { text-align: center; border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
        .header h1 { font-size: 12px; margin: 0; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 9px; }
        .details { margin-bottom: 5px; font-size: 9px; }
        .details div { margin: 2px 0; }
        .items { border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 5px 0; }
        .item { display: flex; justify-content: space-between; margin: 2px 0; font-size: 9px; }
        .totals { padding-top: 5px; }
        .total-row { display: flex; justify-content: space-between; font-size: 9px; }
        .total-row.grand { font-weight: bold; font-size: 11px; border-top: 1px solid #000; padding-top: 3px; margin-top: 3px; }
        .footer { text-align: center; margin-top: 10px; font-size: 8px; border-top: 1px dashed #000; padding-top: 5px; }
        @media print { 
            html, body { width: 58mm; max-width: 58mm; }
            body { padding: 2mm; }
        }
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
