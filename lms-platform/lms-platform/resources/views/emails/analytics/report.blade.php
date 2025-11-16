<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .summary {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .summary h3 {
            margin-top: 0;
            color: #333;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($reportType) }} Analytics Report</h1>
        <p>{{ $period }}</p>
    </div>
    
    <div class="content">
        <p>Hello {{ $instructor->name }},</p>
        
        <p>Here's your {{ $reportType }} analytics report for {{ $period }}. Keep up the great work!</p>
        
        <div class="stats-grid">
            @if($reportType === 'earnings')
                <div class="stat-card">
                    <div class="stat-value">${{ number_format($reportData['total_earnings'] ?? 0, 2) }}</div>
                    <div class="stat-label">Total Earnings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($reportData['total_orders'] ?? 0) }}</div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${{ number_format($reportData['avg_order'] ?? 0, 2) }}</div>
                    <div class="stat-label">Average Order</div>
                </div>
            @elseif($reportType === 'visits')
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($reportData['total_views'] ?? 0) }}</div>
                    <div class="stat-label">Total Views</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($reportData['unique_visitors'] ?? 0) }}</div>
                    <div class="stat-label">Unique Visitors</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($reportData['total_clicks'] ?? 0) }}</div>
                    <div class="stat-label">Total Clicks</div>
                </div>
            @elseif($reportType === 'engagement')
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($reportData['total_engagements'] ?? 0) }}</div>
                    <div class="stat-label">Total Engagements</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($reportData['comments'] ?? 0) }}</div>
                    <div class="stat-label">Comments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($reportData['completions'] ?? 0) }}</div>
                    <div class="stat-label">Completions</div>
                </div>
            @endif
        </div>
        
        <div class="summary">
            <h3>Summary</h3>
            @if($reportType === 'earnings')
                <p>Your courses generated <strong>${{ number_format($reportData['total_earnings'] ?? 0, 2) }}</strong> in revenue this {{ strtolower($period) }}.</p>
                <p>You received <strong>{{ number_format($reportData['total_orders'] ?? 0) }}</strong> new orders with an average value of <strong>${{ number_format($reportData['avg_order'] ?? 0, 2) }}</strong>.</p>
            @elseif($reportType === 'visits')
                <p>Your courses received <strong>{{ number_format($reportData['total_views'] ?? 0) }}</strong> views from <strong>{{ number_format($reportData['unique_visitors'] ?? 0) }}</strong> unique visitors.</p>
                <p>Total clicks: <strong>{{ number_format($reportData['total_clicks'] ?? 0) }}</strong></p>
            @elseif($reportType === 'engagement')
                <p>Students engaged with your content <strong>{{ number_format($reportData['total_engagements'] ?? 0) }}</strong> times this {{ strtolower($period) }}.</p>
                <p>You received <strong>{{ number_format($reportData['comments'] ?? 0) }}</strong> comments and <strong>{{ number_format($reportData['completions'] ?? 0) }}</strong> course completions.</p>
            @endif
        </div>
        
        <div style="text-align: center;">
            <a href="{{ route('instructor.dashboard') }}" class="btn">View Full Dashboard</a>
        </div>
    </div>
    
    <div class="footer">
        <p>This is an automated report from your LMS platform.</p>
        <p>You can manage your email preferences in your account settings.</p>
    </div>
</body>
</html> 