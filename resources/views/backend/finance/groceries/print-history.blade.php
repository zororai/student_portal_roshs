<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocery History - {{ $student->user->name ?? $student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #666;
        }
        .student-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .student-info h2 {
            font-size: 14px;
            margin-bottom: 8px;
        }
        .student-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
        }
        .student-info-grid span {
            font-size: 11px;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
        }
        .summary-card {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .summary-card .label {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
        }
        .summary-card .value {
            font-size: 16px;
            font-weight: bold;
        }
        .summary-card.green .value { color: #16a34a; }
        .summary-card.red .value { color: #dc2626; }
        .summary-card.blue .value { color: #2563eb; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-green { color: #16a34a; }
        .text-red { color: #dc2626; }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-blue { background: #dbeafe; color: #2563eb; }
        .badge-yellow { background: #fef3c7; color: #d97706; }
        .missing-items {
            font-size: 10px;
            color: #dc2626;
            margin-top: 3px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        @media print {
            body { padding: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Print
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 5px;">
            Close
        </button>
    </div>

    <div class="header">
        <h1>STUDENT GROCERY HISTORY</h1>
        <p>Generated on {{ date('F d, Y \a\t h:i A') }}</p>
    </div>

    <div class="student-info">
        <h2>Student Information</h2>
        <div class="student-info-grid">
            <span><strong>Name:</strong> {{ $student->user->name ?? $student->name }}</span>
            <span><strong>Roll Number:</strong> {{ $student->roll_number ?? 'N/A' }}</span>
            <span><strong>Class:</strong> {{ $student->class->class_name ?? 'N/A' }}</span>
            <span><strong>Type:</strong> {{ ucfirst($student->student_type ?? 'Day') }}</span>
            <span><strong>Parent:</strong> {{ $student->parent->user->name ?? 'N/A' }}</span>
            <span><strong>Phone:</strong> {{ $student->parent->user->phone ?? 'N/A' }}</span>
        </div>
    </div>

    <div class="summary">
        <div class="summary-card blue">
            <div class="label">Total Terms</div>
            <div class="value">{{ count($historyData) }}</div>
        </div>
        <div class="summary-card green">
            <div class="label">Items Provided</div>
            <div class="value">{{ $totalProvidedItems }} items</div>
        </div>
        <div class="summary-card {{ ($totalOwedItems ?? 0) > 0 ? 'red' : 'green' }}">
            <div class="label">Outstanding Items</div>
            <div class="value">{{ $totalOwedItems ?? 0 }} items</div>
        </div>
    </div>

    @if(count($historyData) > 0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Term/Year</th>
                <th>Submitted</th>
                <th>Total Items</th>
                <th>Provided</th>
                <th>Missing</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($historyData as $index => $data)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ ucfirst($data['term']) }} {{ $data['year'] }}</td>
                <td>
                    @if($data['response']->submitted_at)
                        {{ $data['response']->submitted_at->format('M d, Y') }}
                    @else
                        <span style="color: #999;">Not submitted</span>
                    @endif
                </td>
                <td class="text-center">{{ $data['total_items'] }} items</td>
                <td class="text-center text-green">{{ $data['provided_count'] }} items</td>
                <td class="text-center {{ ($data['owed_count'] ?? 0) > 0 ? 'text-red' : 'text-green' }}">
                    {{ $data['owed_count'] }} items
                    @if(count($data['missing_items'] ?? []) > 0)
                        <div class="missing-items">{{ implode(', ', $data['missing_items']) }}</div>
                    @endif
                </td>
                <td class="text-center">
                    @if($data['response']->acknowledged)
                        <span class="badge badge-green">Acknowledged</span>
                    @elseif($data['response']->submitted)
                        <span class="badge badge-blue">Submitted</span>
                    @else
                        <span class="badge badge-yellow">Pending</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 30px; color: #666;">
        <p>No grocery history found for this student.</p>
    </div>
    @endif

    <div class="footer">
        <p>ROSHS Student Portal - Grocery Management System</p>
    </div>
</body>
</html>
