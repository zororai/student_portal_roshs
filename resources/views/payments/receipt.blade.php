<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        .receipt-details {
            margin-top: 20px;
        }
        .receipt-details p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h2>Receipt for Payment</h2>

    <div class="receipt-details">
        <p><strong>Student:</strong> {{ $payment->student->name }}</p>
        <p><strong>Fee Category:</strong> {{ $payment->feeCategory->name }}</p>
        <p><strong>Amount Paid:</strong> ${{ $payment->amount }}</p>
        <p><strong>Status:</strong> {{ ucfirst($payment->status) }}</p>
        <p><strong>Date:</strong> {{ $payment->created_at->format('Y-m-d H:i') }}</p>
    </div>
</body>
</html>