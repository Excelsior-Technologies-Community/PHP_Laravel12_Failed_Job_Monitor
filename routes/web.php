<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\ExampleFailedJob;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/fail-job', function () {

    ExampleFailedJob::dispatch();

    return "Failed job dispatched!";
});



