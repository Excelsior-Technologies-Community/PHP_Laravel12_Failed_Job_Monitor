<!DOCTYPE html>
<html>
<head>
    <title>Failed Jobs Dashboard</title>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid #e1e4e8;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #6c757d;
            font-size: 14px;
        }
        
        /* Stats Bar */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
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
            font-size: 32px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }
        
        /* Toolbar */
        .toolbar {
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border: 1px solid #e1e4e8;
        }
        
        .search-box {
            flex: 1;
            min-width: 250px;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #007bff;
        }
        
        .filter-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .filter-group select, .filter-group button {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            font-size: 14px;
            cursor: pointer;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Table */
        .table-container {
            background: white;
            border-radius: 12px;
            overflow-x: auto;
            border: 1px solid #e1e4e8;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            background: #6c757d;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .error-message {
            max-width: 400px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #dc3545;
            font-family: monospace;
            font-size: 12px;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 25px;
            flex-wrap: wrap;
        }
        
        .pagination a, .pagination span {
            padding: 8px 15px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
        }
        
        .pagination .active span {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state h3 {
            margin-bottom: 10px;
            color: #495057;
        }
        
        @media (max-width: 768px) {
            .stats-bar {
                grid-template-columns: 1fr;
            }
            
            .toolbar {
                flex-direction: column;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            td, th {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    
    <!-- Header -->
    <div class="header">
        <h1>Failed Jobs Monitor</h1>
        <p>Track and manage failed queue jobs in real-time</p>
    </div>
    
    <!-- Stats Bar -->
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-number">{{ $jobs->total() }}</div>
            <div class="stat-label">Total Failed Jobs</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ \Illuminate\Support\Facades\DB::table('failed_jobs')->whereDate('failed_at', now()->toDateString())->count() }}</div>
            <div class="stat-label">Failed Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ \Illuminate\Support\Facades\DB::table('failed_jobs')->where('failed_at', '>=', now()->subDay())->count() }}</div>
            <div class="stat-label">Last 24 Hours</div>
        </div>
    </div>
    
    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif
    
    <!-- Toolbar -->
    <div class="toolbar">
        <form method="GET" action="/failed-jobs" class="search-box">
            <input type="text" name="search" placeholder="Search by ID, queue, or error message..." value="{{ request('search') }}">
        </form>
        
        <div class="filter-group">
            <form method="GET" action="/failed-jobs" id="filter-form">
                <select name="queue" onchange="this.form.submit()">
                    <option value="">All Queues</option>
                    @foreach($queues as $queue)
                        <option value="{{ $queue }}" {{ request('queue') == $queue ? 'selected' : '' }}>
                            {{ $queue }}
                        </option>
                    @endforeach
                </select>
            </form>
            
            <a href="/failed-jobs/export" class="btn btn-secondary btn-sm">Export CSV</a>
            
            <form method="POST" action="/failed-jobs/retry-all" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Retry all failed jobs?')">Retry All</button>
            </form>
            
            <form method="POST" action="/failed-jobs/delete-all" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete ALL failed jobs? This action cannot be undone.')">Delete All</button>
            </form>
            
            <a href="/failed-jobs/stats" class="btn btn-primary btn-sm">View Statistics</a>
        </div>
    </div>
    
    <!-- Jobs Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Queue</th>
                    <th>Connection</th>
                    <th>Error Message</th>
                    <th>Failed At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td><span class="badge">{{ $job->queue }}</span></td>
                    <td>{{ $job->connection }}</td>
                    <td class="error-message" title="{{ $job->exception }}">
                        {{ substr($job->exception, 0, 100) }}{{ strlen($job->exception) > 100 ? '...' : '' }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($job->failed_at)->format('Y-m-d H:i:s') }}</td>
                    <td class="action-buttons">
                        <form method="POST" action="/failed-jobs/retry/{{ $job->id }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Retry</button>
                        </form>
                        
                        <form method="POST" action="/failed-jobs/delete/{{ $job->id }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this job?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <h3>No failed jobs found</h3>
                                <p>All your queue jobs are running smoothly</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($jobs->hasPages())
        <div class="pagination">
            {{ $jobs->appends(request()->query())->links() }}
        </div>
    @endif
    
</div>

</body>
</html>