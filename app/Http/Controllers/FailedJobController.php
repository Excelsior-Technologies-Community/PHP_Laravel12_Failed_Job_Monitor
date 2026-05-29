<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Mail\FailedJobAlert;
use Carbon\Carbon;

class FailedJobController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('failed_jobs');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('queue', 'LIKE', "%{$search}%")
                    ->orWhere('exception', 'LIKE', "%{$search}%")
                    ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('queue') && $request->queue) {
            $query->where('queue', $request->queue);
        }

        if ($request->filled('queue_filter')) {
            $query->where('queue', $request->queue_filter);
        }

        if ($request->filled('connection_filter')) {
            $query->where('connection', $request->connection_filter);
        }

        $jobs = $query->orderBy('failed_at', 'desc')->paginate(15);

        $queues = DB::table('failed_jobs')->select('queue')->distinct()->pluck('queue');
        $connections = DB::table('failed_jobs')->distinct()->pluck('connection');

        return view('failed-jobs.index', compact('jobs', 'queues', 'connections'));
    }

    public function retry($id)
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();

        if ($job) {
            try {
                Artisan::call('queue:retry', [
                    'id' => $job->uuid,
                ]);
                return back()->with('success', 'Job #' . $id . ' retried successfully');
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to retry job: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Job not found');
    }

    public function retryAll()
    {
        $jobs = DB::table('failed_jobs')->get();

        if ($jobs->isEmpty()) {
            return back()->with('error', 'No failed jobs to retry');
        }

        $retried = 0;
        foreach ($jobs as $job) {
            try {
                Artisan::call('queue:retry', ['id' => $job->uuid]);
                $retried++;
            } catch (\Exception $e) {
            }
        }

        return back()->with('success', $retried . ' job(s) retried successfully');
    }

    public function delete($id)
    {
        $deleted = DB::table('failed_jobs')->where('id', $id)->delete();

        if ($deleted) {
            return back()->with('success', 'Job #' . $id . ' deleted successfully');
        }

        return back()->with('error', 'Job not found');
    }

    public function deleteAll()
    {
        $count = DB::table('failed_jobs')->count();
        DB::table('failed_jobs')->truncate();

        return back()->with('success', 'All ' . $count . ' failed job(s) deleted successfully');
    }

    public function stats()
    {
        $total = DB::table('failed_jobs')->count();

        $today = DB::table('failed_jobs')
            ->whereDate('failed_at', now()->toDateString())
            ->count();

        $yesterday = DB::table('failed_jobs')
            ->whereDate('failed_at', now()->subDay()->toDateString())
            ->count();

        $last24 = DB::table('failed_jobs')
            ->where('failed_at', '>=', now()->subDay())
            ->count();

        $last7Days = DB::table('failed_jobs')
            ->where('failed_at', '>=', now()->subDays(7))
            ->count();

        $last30Days = DB::table('failed_jobs')
            ->where('failed_at', '>=', now()->subDays(30))
            ->count();

        $queueStats = DB::table('failed_jobs')
            ->select('queue', DB::raw('count(*) as total'))
            ->groupBy('queue')
            ->orderBy('total', 'desc')
            ->get();

        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = DB::table('failed_jobs')
                ->whereDate('failed_at', $date)
                ->count();
            $dailyStats[$date] = $count;
        }

        $topErrors = DB::table('failed_jobs')
            ->select('exception')
            ->get()
            ->map(function ($job) {
                $exceptionText = is_object($job) ? $job->exception : ($job['exception'] ?? '');
                $lines = explode("\n", $exceptionText);
                $firstLine = $lines[0] ?? '';
                return substr($firstLine, 0, 100);
            })
            ->countBy()
            ->sortDesc()
            ->take(5);

        return view('failed-jobs.stats', compact(
            'total',
            'today',
            'yesterday',
            'last24',
            'last7Days',
            'last30Days',
            'queueStats',
            'dailyStats',
            'topErrors'
        ));
    }

    public function export()
    {
        $jobs = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->get();

        $filename = 'failed-jobs-' . now()->format('Y-m-d-His') . '.csv';

        $handle = fopen('php://temp', 'w');

        fputcsv($handle, ['ID', 'Queue', 'Connection', 'Failed At', 'Error Message']);

        foreach ($jobs as $job) {
            $lines = explode("\n", $job->exception);
            $firstLine = $lines[0] ?? '';
            fputcsv($handle, [
                $job->id,
                $job->queue,
                $job->connection,
                $job->failed_at,
                substr($firstLine, 0, 200),
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function search(Request $request)
    {
        $search = $request->get('q');

        $jobs = DB::table('failed_jobs')
            ->where('id', 'LIKE', "%{$search}%")
            ->orWhere('queue', 'LIKE', "%{$search}%")
            ->orWhere('exception', 'LIKE', "%{$search}%")
            ->orderBy('failed_at', 'desc')
            ->paginate(15);

        return view('failed-jobs.index', compact('jobs'));
    }

    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids) || empty($action)) {
            return back()->with('error', 'No jobs selected or action not specified.');
        }

        if ($action === 'retry') {
            foreach ($ids as $id) {
                $job = DB::table('failed_jobs')->where('id', $id)->first();
                if ($job) {
                    Artisan::call('queue:retry', ['id' => $job->uuid]);
                }
            }
            return back()->with('success', count($ids) . ' jobs sent for retry.');
        }

        if ($action === 'delete') {
            DB::table('failed_jobs')->whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' jobs deleted successfully.');
        }

        return back();
    }

    public function autoClean(Request $request)
    {
        $days = $request->input('days', 30);
        $deleted = DB::table('failed_jobs')
            ->where('failed_at', '<', Carbon::now()->subDays($days))
            ->delete();

        return back()->with('success', $deleted . ' old logs cleaned up.');
    }

    public function autoRetryEngine()
    {
        $failedJobs = DB::table('failed_jobs')
            ->where('failed_at', '>=', Carbon::now()->subMinutes(15))
            ->get();

        foreach ($failedJobs as $job) {
            if (str_contains($job->exception, 'Timeout') || str_contains($job->exception, 'MaxAttemptsExceededException')) {
                Artisan::call('queue:retry', ['id' => $job->uuid]);
            }
        }

        return back()->with('success', 'Auto-retry rule engine executed successfully.');
    }
}