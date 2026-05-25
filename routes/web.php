<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\ExampleFailedJob;
use App\Http\Controllers\FailedJobController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/fail-job', function () {
    ExampleFailedJob::dispatch();
    return "Failed job dispatched!";
});

Route::get('/failed-jobs', [FailedJobController::class, 'index']);
Route::post('/failed-jobs/retry/{id}', [FailedJobController::class, 'retry']);
Route::get('/failed-jobs/stats', [FailedJobController::class, 'stats']);
Route::delete('/failed-jobs/delete/{id}', [FailedJobController::class, 'delete']);
Route::delete('/failed-jobs/delete-all', [FailedJobController::class, 'deleteAll']);
Route::post('/failed-jobs/retry-all', [FailedJobController::class, 'retryAll']);
Route::get('/failed-jours/export', [FailedJobController::class, 'export']);
Route::get('/failed-jobs/search', [FailedJobController::class, 'search']);