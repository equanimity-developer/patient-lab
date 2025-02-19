<?php

declare(strict_types=1);

use App\Http\Controllers\Api\PatientAuthController;

Route::post('/login', [PatientAuthController::class, 'login']);
