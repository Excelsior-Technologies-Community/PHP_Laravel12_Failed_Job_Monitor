# PHP_Laravel12_Failed_Job_Monitor


## Project Description

PHP_Laravel12_Failed_Job_Monitor is a Laravel 12 based application that demonstrates how to monitor failed queue jobs and send automatic email notifications using the Spatie Laravel Failed Job Monitor package.

This project uses Laravel’s queue system with database driver to store jobs and failed jobs. When a job fails, the system automatically detects the failure and sends an email alert containing the job name and error message.

This helps developers quickly identify and fix issues in background jobs, improving application reliability and debugging efficiency.

## Key features of this project include:

- Queue job processing using database driver  
- Automatic failed job detection  
- Email notifications for failed jobs  
- Failed job logging for debugging  
- Integration with official Spatie Failed Job Monitor package  
- Simple example job to simulate job failure  

This project is useful for learning Laravel queues, failed job handling, and real-time failure monitoring.


## Requirements

The following software and tools are required to run this project:

- PHP >= 8.2

- Composer

- Laravel 12

- MySQL Database

- XAMPP / WAMP / Laragon (Local Server)

- Web Browser (Chrome, Edge, etc.)

- Mailtrap / Gmail SMTP (for email testing)



## Technologies Used

- Laravel 12 (Framework)
- PHP 8.2+
- MySQL
- Laravel Queue (Database Driver)
- Spatie Failed Job Monitor Package
- SMTP Mail Service
- Blade Templating Engine


---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_Failed_Job_Monitor "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_Failed_Job_Monitor

```

#### Explanation:

This step creates a fresh Laravel 12 application with all required core files, folders, and dependencies.




## STEP 2: Database Setup 

### Open .env and set:

```
QUEUE_CONNECTION=database 

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_failed_job_monitor
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel12_failed_job_monitor

```


### Run command:

```
php artisan queue:table
php artisan queue:failed-table
php artisan migrate

```


#### Explanation:

This step configures the database and creates required tables (jobs and failed_jobs) to store and manage queued and failed jobs.




## STEP 3: Install Official Package

### Run:

```
composer require spatie/laravel-failed-job-monitor

```

### Publish config:

```
php artisan vendor:publish --tag=failed-job-monitor-config

```


#### Explanation:

This installs the official Spatie package and publishes the configuration file to enable failed job monitoring.




## STEP 4: Configure Failed Job Monitor

### Open: config/failed-job-monitor.php

#### Use this exact code:

```
<?php

return [

    /*
     * This class will be used to send the notification.
     */
    'notification' => \Spatie\FailedJobMonitor\Notification::class,

    /*
     * The notifiable that will receive the notification.
     */
    'notifiable' => \Spatie\FailedJobMonitor\Notifiable::class,

    /*
     * Channels via which the notification will be sent.
     */
    'channels' => ['mail'],

    /*
     * Mail settings
     */
    'mail' => [
        'to' => ['admin@gmail.com'], // change to your email
    ],

    /*
     * Slack settings (optional)
     */
    'slack' => [
        'webhook_url' => env('FAILED_JOB_SLACK_WEBHOOK_URL'),
    ],

];

```


#### Explanation:

This file controls how failed job notifications are sent, including email recipients and notification channels.




## STEP 5: Configure Mail

### Edit .env:

#### Example using Gmail SMTP:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="Failed Job Monitor"

```


#### Explanation:

This step configures Laravel to send emails using Gmail SMTP so failed job alerts can be delivered.





## STEP 6: Create Failed Job

### Create job:

```
php artisan make:job ExampleFailedJob

php artisan make:Mail FailedJobMail.php

php artisan make:Listeners FailedJobListener.php



```

### Edit: App\Jobs\ExampleFailedJob

```
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

```


#### Explanation:

This creates a job class that runs in the background using Laravel queue system.

This job intentionally throws an exception to simulate a failed job for testing monitoring and email notifications.





### FailedJobMail.php

#### location: app/Mail/FailedJobMail.php

```
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

```


#### Explanation:

This creates a Mailable class used to send email alerts when a job fails.






### FailedJobListener.php

#### location: app/Listeners/FailedJobListener.php

```
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

```

#### Replace:

```
admin@gmail.com

```
with your email.


#### Explanation:

This listener listens for job failure events and sends email notifications.






## STEP 7: Email Blade File

### location: resources/views/emails/failed-job.blade.php

```
<!DOCTYPE html>
<html>

<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#dc3545; padding:20px; text-align:center; color:#ffffff;">
                            <h2 style="margin:0;">🚨 Laravel Failed Job Alert</h2>
                            <p style="margin:5px 0 0 0; font-size:12px;">Action Required</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:25px;">

                            <p style="font-weight:bold; margin-bottom:5px;">Job Name:</p>
                            <div style="background:#f8f9fa; padding:10px; border-radius:5px; margin-bottom:15px;">
                                {{ $jobName }}
                            </div>

                            <p style="font-weight:bold; margin-bottom:5px;">Error Message:</p>
                            <div
                                style="background:#fff3f3; border-left:5px solid #dc3545; padding:15px; border-radius:5px; color:#721c24;">
                                {{ $exception->getMessage() }}
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="text-align:center; padding:15px; font-size:12px; color:#888; background:#f8f9fa;">
                            This is an automated message from Laravel Failed Job Monitor.<br>
                            Please check your logs and fix the issue.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>

```


#### Explanation:

This file defines the email template that displays job name and error message.





## STEP 8: Dispatch Job Route

### Edit: routes/web.php

```
use App\Jobs\ExampleFailedJob;

Route::get('/fail-job', function () {

    ExampleFailedJob::dispatch();

    return "Failed job dispatched!";
});

```

#### Explanation:

This route dispatches the job to the queue when accessed in browser.





## STEP 9: Run Queue Worker

### Terminal 1:

```
php artisan queue:work database --tries=1

```

### Terminal 2:

```
php artisan serve

```

### Open browser:

```
http://127.0.0.1:8000/fail-job

```


#### Explanation:

This command starts the queue worker which processes jobs and detects failures.




## So you can see this type Output:


### Failed Job Dispatch Confirmation (Browser Output):


<img width="1919" height="816" alt="Screenshot 2026-02-23 115113" src="https://github.com/user-attachments/assets/e71eea2b-ca12-42a2-b040-79c8a20607f2" />


### Failed Job Email Notification Received:


<img width="1518" height="832" alt="Screenshot 2026-02-23 124842" src="https://github.com/user-attachments/assets/f4351abb-f0a9-4c71-8acb-a28b6d15e0a9" />



---

# Project Folder Structure:

```
PHP_Laravel12_Failed_Job_Monitor/
│
├── app/
│   │
│   ├── Jobs/
│   │   └── ExampleFailedJob.php
│   │
│   ├── Listeners/
│   │   └── FailedJobListener.php
│   │
│   ├── Mail/
│   │   └── FailedJobMail.php
│   │
│   └── Models/
│       └── User.php
│
├── bootstrap/
│   └── app.php
│
├── config/
│   │
│   ├── app.php
│   ├── database.php
│   ├── mail.php
│   ├── queue.php
│   └── failed-job-monitor.php
│
├── database/
│   │
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   └── xxxx_xx_xx_xxxxxx_create_failed_jobs_table.php
│   │
│   └── seeders/
│       └── DatabaseSeeder.php
│
├── resources/
│   │
│   └── views/
│       │
│       └── emails/
│           └── failed-job.blade.php
│
├── routes/
│   └── web.php
│
├── storage/
│   └── logs/
│       └── laravel.log
│
├── vendor/
│
├── .env
├── artisan
├── composer.json
└── README.md

```
