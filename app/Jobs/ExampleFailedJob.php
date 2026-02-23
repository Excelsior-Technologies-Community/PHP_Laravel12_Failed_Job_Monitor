<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExampleFailedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // This is intentional failure for testing failed job monitoring
        throw new Exception("This job failed for testing");
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        \Log::error("ExampleFailedJob failed: " . $exception->getMessage());
    }
}