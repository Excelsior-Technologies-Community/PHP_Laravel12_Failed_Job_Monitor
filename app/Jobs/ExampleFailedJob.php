<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExampleFailedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function handle()
    {
        throw new \Exception('This is a simulated job failure for testing purposes. Time: ' . now());
    }
}