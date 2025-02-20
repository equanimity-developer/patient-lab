<?php

declare(strict_types=1);

use App\Http\Controllers\Api\PatientAuthController;
use App\Http\Controllers\Api\PatientResultsController;

Route::post('/login', [PatientAuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/results', [PatientResultsController::class, 'index']);
});
