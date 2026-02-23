<?php

namespace App\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FailedJobMail extends Mailable
{
    use Queueable, SerializesModels;

    public $exception;
    public $jobName;

    /**
     * Create a new message instance.
     */
    public function __construct($jobName, Exception $exception)
    {
        $this->jobName = $jobName;
        $this->exception = $exception;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Laravel Failed Job Alert')
                    ->view('emails.failed-job');
    }
}