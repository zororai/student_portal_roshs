<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocery Stock Report - {{ ucfirst(str_replace('_', ' ', $term)) }} {{ $year }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; line-height: 1.4; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .header h2 { font-size: 18px; font-weight: normal; color: #666; }
        .header p { font-size: 14px; color: #888; margin-top: 5px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px 12px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: 600; text-transform: uppercase; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 600; }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .text-blue { color: #2563eb; }
        .text-purple { color: #9333ea; }
        .totals-row { background-color: #f9fafb; font-weight: 600; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; }
        .signatures { display: flex; justify-content: space-between; margin-top: 60px; }
        .signature-box { width: 200px; text-align: center; }
        .signature-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; }
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print" style="margin-bottom: 20px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                Print Report
            </button>
            <a href="{{ route('admin.grocery-stock.index') }}" style="padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px; display: inline-block;">
                Back to Stock
            </a>
        </div>

        <div class="header">
            <h1>ROSHS</h1>
            <h2>Grocery Stock Report</h2>
            <p>{{ ucfirst(str_replace('_', ' ', $term)) }} {{ $year }}</p>
        </div>

        <div class="meta">
            <span>Generated: {{ now()->format('F d, Y H:i') }}</span>
            <span>Total Items: {{ $stockItems->count() }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 25%;">Item Name</th>
                    <th style="width: 10%;">Unit</th>
                    <th class="text-right" style="width: 12%;">Balance B/F</th>
                    <th class="text-right" style="width: 12%;">Received</th>
                    <th class="text-right" style="width: 12%;">Usage</th>
                    <th class="text-right" style="width: 12%;">Bad Stock</th>
                    <th class="text-right" style="width: 12%;">Closing</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalBalanceBf = 0;
                    $totalReceived = 0;
                    $totalUsage = 0;
                    $totalBadStock = 0;
                    $totalClosing = 0;
                @endphp
                @foreach($stockItems as $index => $item)
                @php
                    $totalBalanceBf += $item->balance_bf;
                    $totalReceived += $item->received;
                    $totalUsage += $item->usage;
                    $totalBadStock += $item->bad_stock;
                    $totalClosing += $item->closing_balance;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $item->name }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="text-right text-purple">{{ number_format($item->balance_bf, 2) }}</td>
                    <td class="text-right text-green">{{ number_format($item->received, 2) }}</td>
                    <td class="text-right text-blue">{{ number_format($item->usage, 2) }}</td>
                    <td class="text-right text-red">{{ number_format($item->bad_stock, 2) }}</td>
                    <td class="text-right font-bold {{ $item->closing_balance < 0 ? 'text-red' : '' }}">{{ number_format($item->closing_balance, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <td colspan="3" class="text-right font-bold">TOTALS:</td>
                    <td class="text-right font-bold text-purple">{{ number_format($totalBalanceBf, 2) }}</td>
                    <td class="text-right font-bold text-green">{{ number_format($totalReceived, 2) }}</td>
                    <td class="text-right font-bold text-blue">{{ number_format($totalUsage, 2) }}</td>
                    <td class="text-right font-bold text-red">{{ number_format($totalBadStock, 2) }}</td>
                    <td class="text-right font-bold">{{ number_format($totalClosing, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p><strong>Legend:</strong></p>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li><span class="text-purple">Balance B/F</span> - Balance Brought Forward from previous term</li>
                <li><span class="text-green">Received</span> - Stock received from students</li>
                <li><span class="text-blue">Usage</span> - Stock used/consumed</li>
                <li><span class="text-red">Bad Stock</span> - Spoiled/damaged stock written off</li>
            </ul>
        </div>

        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line">Prepared By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Verified By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Approved By</div>
            </div>
        </div>
    </div>
</body>
</html>
