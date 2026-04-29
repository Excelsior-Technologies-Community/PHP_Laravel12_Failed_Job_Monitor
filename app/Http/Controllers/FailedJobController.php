<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class FailedJobController extends Controller
{
    // Dashboard - list failed jobs
    public function index()
    {
        $jobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'asc')
            ->get();

        return view('failed-jobs.index', compact('jobs'));
    }

    // Retry job
    public function retry($id)
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();

        if ($job) {
            Artisan::call('queue:retry', [
                'id' => $job->uuid
            ]);
        }

        return back()->with('success', 'Job retried successfully');
    }

    // Statistics dashboard
    public function stats()
    {
        $total = DB::table('failed_jobs')->count();

        $today = DB::table('failed_jobs')
            ->whereDate('failed_at', now()->toDateString())
            ->count();

        $last24 = DB::table('failed_jobs')
            ->where('failed_at', '>=', now()->subDay())
            ->count();

        return view('failed-jobs.stats', compact('total', 'today', 'last24'));
    }
}
