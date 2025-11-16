<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Earnings Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .summary h3 {
            margin-top: 0;
            color: #333;
        }
        .total {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Earnings Report</h1>
        <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
        <p>Instructor: {{ auth()->user()->name }}</p>
    </div>

    <div class="summary">
        <h3>Summary</h3>
        <p><strong>Total Earnings:</strong> <span class="total">${{ number_format($totalEarnings, 2) }}</span></p>
        <p><strong>Total Orders:</strong> {{ $earnings->sum('order_count') }}</p>
        <p><strong>Average Order Value:</strong> ${{ $earnings->sum('order_count') > 0 ? number_format($totalEarnings / $earnings->sum('order_count'), 2) : '0.00' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Course Title</th>
                <th>Earnings ($)</th>
                <th>Orders</th>
                <th>Payment Type</th>
            </tr>
        </thead>
        <tbody>
            @forelse($earnings as $earning)
            <tr>
                <td>{{ $earning->date->format('M d, Y') }}</td>
                <td>{{ $earning->course ? $earning->course->course_title : 'N/A' }}</td>
                <td>${{ number_format($earning->total_earnings, 2) }}</td>
                <td>{{ $earning->order_count }}</td>
                <td>{{ ucfirst($earning->payment_type ?? 'N/A') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No earnings data available</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html> 