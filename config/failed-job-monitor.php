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