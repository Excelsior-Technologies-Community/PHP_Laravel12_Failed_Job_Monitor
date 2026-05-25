<!DOCTYPE html>
<html>
<head>
    <title>Job Statistics - Failed Jobs Monitor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #e1e4e8;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #e1e4e8;
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }
        
        .stat-card.total .stat-number { color: #dc3545; }
        .stat-card.today .stat-number { color: #ffc107; }
        .stat-card.yesterday .stat-number { color: #fd7e14; }
        .stat-card.week .stat-number { color: #28a745; }
        .stat-card.month .stat-number { color: #17a2b8; }
        
        .section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #e1e4e8;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1a1a2e;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f2f5;
        }
        
        .queue-list {
            list-style: none;
        }
        
        .queue-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .queue-name {
            font-weight: 500;
            color: #495057;
        }
        
        .queue-count {
            background: #dc3545;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .daily-chart {
            margin-top: 20px;
        }
        
        .chart-bar {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            gap: 10px;
        }
        
        .chart-label {
            width: 100px;
            font-size: 12px;
            color: #6c757d;
        }
        
        .bar-container {
            flex: 1;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            height: 30px;
        }
        
        .bar-fill {
            background: #dc3545;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 10px;
            color: white;
            font-size: 12px;
            font-weight: 500;
            transition: width 0.3s;
        }
        
        .error-list {
            list-style: none;
        }
        
        .error-item {
            padding: 12px;
            background: #f8f9fa;
            margin-bottom: 10px;
            border-radius: 8px;
            border-left: 3px solid #dc3545;
        }
        
        .error-text {
            color: #495057;
            font-size: 13px;
            font-family: monospace;
        }
        
        .error-count {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            margin-top: 8px;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-label {
                width: 80px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    
    <a href="/failed-jobs" class="back-link">← Back to Dashboard</a>
    
    <div class="header">
        <h1>Failed Jobs Statistics</h1>
        <p>Analytics and insights about your failed queue jobs</p>
    </div>
    
    <!-- Overview Stats -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-number">{{ $total }}</div>
            <div class="stat-label">Total Failed Jobs</div>
        </div>
        <div class="stat-card today">
            <div class="stat-number">{{ $today }}</div>
            <div class="stat-label">Failed Today</div>
        </div>
        <div class="stat-card yesterday">
            <div class="stat-number">{{ $yesterday ?? 0 }}</div>
            <div class="stat-label">Failed Yesterday</div>
        </div>
        <div class="stat-card week">
            <div class="stat-number">{{ $last7Days }}</div>
            <div class="stat-label">Last 7 Days</div>
        </div>
        <div class="stat-card month">
            <div class="stat-number">{{ $last30Days }}</div>
            <div class="stat-label">Last 30 Days</div>
        </div>
    </div>
    
    <!-- Failures by Queue -->
    <div class="section">
        <div class="section-title">Failures by Queue</div>
        @if($queueStats->count() > 0)
            <div class="queue-list">
                @foreach($queueStats as $queue)
                    <div class="queue-item">
                        <span class="queue-name">{{ $queue->queue }}</span>
                        <span class="queue-count">{{ $queue->total }} failures</span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: #6c757d;">No data available</p>
        @endif
    </div>
    
    <!-- Daily Failure Trend -->
    <div class="section">
        <div class="section-title">Daily Failure Trend (Last 7 Days)</div>
        @if(count($dailyStats) > 0)
            <div class="daily-chart">
                @foreach($dailyStats as $date => $count)
                    @php
                        $maxCount = max($dailyStats) ?: 1;
                        $percentage = ($count / $maxCount) * 100;
                    @endphp
                    <div class="chart-bar">
                        <div class="chart-label">{{ \Carbon\Carbon::parse($date)->format('M d') }}</div>
                        <div class="bar-container">
                            <div class="bar-fill" style="width: {{ $percentage }}%;">
                                @if($count > 0) {{ $count }} @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: #6c757d;">No data available for the last 7 days</p>
        @endif
    </div>
    
    <!-- Most Common Errors -->
    @if(isset($topErrors) && $topErrors->count() > 0)
        <div class="section">
            <div class="section-title">Most Common Error Messages</div>
            <div class="error-list">
                @foreach($topErrors as $error => $count)
                    <div class="error-item">
                        <div class="error-text">{{ $error }}</div>
                        <span class="error-count">Occurred {{ $count }} times</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
</div>

</body>
</html>