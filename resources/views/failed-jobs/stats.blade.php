<!DOCTYPE html>
<html>
<head>
    <title>Job Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h2 class="mb-4">📊 Failed Job Statistics</h2>

    <a href="/failed-jobs" class="btn btn-secondary mb-3">
        ← Back to Dashboard
    </a>

    <div class="row">

        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body text-center">
                    <h5>Total Failed</h5>
                    <h2>{{ $total }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body text-center">
                    <h5>Today</h5>
                    <h2>{{ $today }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body text-center">
                    <h5>Last 24 Hours</h5>
                    <h2>{{ $last24 }}</h2>
                </div>
            </div>
        </div>

    </div>

</div>

</body>
</html>