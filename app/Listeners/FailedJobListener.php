<?php

namespace App\Listeners;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Mail;
use App\Mail\FailedJobMail;

class FailedJobListener
{
    /**
     * Handle the event.
     */
    public function handle(JobFailed $event): void
    {
        $jobName = $event->job->resolveName();
        $exception = $event->exception;

        Mail::to('admin@gmail.com')->send(
            new FailedJobMail($jobName, $exception)
        );
    }
}
