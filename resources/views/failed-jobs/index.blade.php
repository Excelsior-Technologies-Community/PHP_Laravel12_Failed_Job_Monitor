<!DOCTYPE html>
<html>
<head>
    <title>Failed Jobs Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6fb;
        }

        .page-title {
            font-weight: 700;
        }

        .card-box {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .table thead {
            background: #111827;
            color: #fff;
        }

        .table tbody tr:hover {
            background: #f1f5f9;
        }

        .error-box {
            max-width: 450px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-modern {
            border-radius: 8px;
            padding: 6px 14px;
        }
    </style>
</head>

<body>

<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title">🚨 Failed Jobs Monitor</h2>
            <small class="text-muted">Real-time job failure tracking system</small>
        </div>

        <a href="/failed-jobs/stats" class="btn btn-primary btn-modern">
            📊 View Stats
        </a>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="card card-box">
        <div class="card-body p-0">

            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Queue</th>
                        <th>Error Message</th>
                        <th>Failed At</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>#{{ $job->id }}</td>

                        <td>
                            <span class="badge bg-dark">
                                {{ $job->queue }}
                            </span>
                        </td>

                        <td class="error-box" title="{{ $job->exception }}">
                            {{ $job->exception }}
                        </td>

                        <td>
                            <span class="text-muted">
                                {{ $job->failed_at }}
                            </span>
                        </td>

                        <td>
                            <form method="POST" action="/failed-jobs/retry/{{ $job->id }}">
                                @csrf
                                <button class="btn btn-success btn-sm btn-modern">
                                    🔄 Retry
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            🎉 No failed jobs found
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

</body>
</html>