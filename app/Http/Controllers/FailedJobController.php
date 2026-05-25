<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Mail\FailedJobAlert;

class FailedJobController extends Controller
{
    // Dashboard - list failed jobs with pagination and search
    public function index(Request $request)
    {
        $query = DB::table('failed_jobs');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('queue', 'LIKE', "%{$search}%")
                  ->orWhere('exception', 'LIKE', "%{$search}%")
                  ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by queue
        if ($request->has('queue') && $request->queue) {
            $query->where('queue', $request->queue);
        }
        
        $jobs = $query->orderBy('failed_at', 'desc')->paginate(15);
        
        // Get unique queue names for filter
        $queues = DB::table('failed_jobs')->select('queue')->distinct()->pluck('queue');
        
        return view('failed-jobs.index', compact('jobs', 'queues'));
    }

    // Retry single job
    public function retry($id)
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();

        if ($job) {
            try {
                Artisan::call('queue:retry', [
                    'id' => $job->uuid
                ]);
                return back()->with('success', 'Job #' . $id . ' retried successfully');
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to retry job: ' . $e->getMessage());
            }
        }
        
        return back()->with('error', 'Job not found');
    }
    
    // Retry all failed jobs
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
                // Log error but continue
            }
        }
        
        return back()->with('success', $retried . ' job(s) retried successfully');
    }

    // Delete single failed job
    public function delete($id)
    {
        $deleted = DB::table('failed_jobs')->where('id', $id)->delete();
        
        if ($deleted) {
            return back()->with('success', 'Job #' . $id . ' deleted successfully');
        }
        
        return back()->with('error', 'Job not found');
    }
    
    // Delete all failed jobs
    public function deleteAll()
    {
        $count = DB::table('failed_jobs')->count();
        DB::table('failed_jobs')->truncate();
        
        return back()->with('success', 'All ' . $count . ' failed job(s) deleted successfully');
    }

    // Statistics dashboard with detailed analytics
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
            
        // Stats by queue
        $queueStats = DB::table('failed_jobs')
            ->select('queue', DB::raw('count(*) as total'))
            ->groupBy('queue')
            ->orderBy('total', 'desc')
            ->get();
            
        // Daily failures for chart (last 7 days)
        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = DB::table('failed_jobs')
                ->whereDate('failed_at', $date)
                ->count();
            $dailyStats[$date] = $count;
        }
        
        // Most common error messages (top 5)
        $topErrors = DB::table('failed_jobs')
            ->select('exception')
            ->get()
            ->map(function($job) {
                // Extract first line of error message
                $error = explode("\n", $job->exception)[0];
                return substr($error, 0, 100);
            })
            ->countBy()
            ->sortDesc()
            ->take(5);
        
        return view('failed-jobs.stats', compact(
            'total', 'today', 'yesterday', 'last24', 'last7Days', 'last30Days',
            'queueStats', 'dailyStats', 'topErrors'
        ));
    }
    
    // Export failed jobs to CSV
    public function export()
    {
        $jobs = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->get();
        
        $filename = 'failed-jobs-' . now()->format('Y-m-d-His') . '.csv';
        
        $handle = fopen('php://temp', 'w');
        
        // Add CSV headers
        fputcsv($handle, ['ID', 'Queue', 'Connection', 'Failed At', 'Error Message']);
        
        // Add data rows
        foreach ($jobs as $job) {
            $errorMessage = explode("\n", $job->exception)[0];
            fputcsv($handle, [
                $job->id,
                $job->queue,
                $job->connection,
                $job->failed_at,
                substr($errorMessage, 0, 200)
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    // Search API endpoint
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
}