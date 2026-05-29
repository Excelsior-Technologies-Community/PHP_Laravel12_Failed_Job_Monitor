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
Route::get('/failed-jobs/stats', [FailedJobController::class, 'stats']);
Route::get('/failed-jobs/export', [FailedJobController::class, 'export']);
Route::get('/failed-jobs/search', [FailedJobController::class, 'search']);
Route::get('/failed-jobs/{id}/retry', [FailedJobController::class, 'retry']);
Route::get('/failed-jobs/{id}/delete', [FailedJobController::class, 'delete']);
Route::get('/failed-jobs/retry-all', [FailedJobController::class, 'retryAll']);
Route::get('/failed-jobs/delete-all', [FailedJobController::class, 'deleteAll']);

Route::post('/failed-jobs/bulk', [FailedJobController::class, 'bulkAction']);
Route::post('/failed-jobs/auto-clean', [FailedJobController::class, 'autoClean']);
Route::post('/failed-jobs/auto-retry-engine', [FailedJobController::class, 'autoRetryEngine']);