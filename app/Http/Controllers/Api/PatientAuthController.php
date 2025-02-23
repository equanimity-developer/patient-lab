<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PatientLoginRequest;

class PatientAuthController extends Controller
{
    public function login(PatientLoginRequest $request): JsonResponse
    {
        $patient = Patient::whereRaw(
            "BINARY CONCAT(name, surname) = ?",
            [$request->login]
        )->first();

        if (!$patient || $patient->date_of_birth->format('Y-m-d') !== $request->password) {
            return response()->json([
                'message' => __('auth.invalid_credentials')
            ], 401);
        }

        $token = auth('api')->login($patient);

        return response()->json([
            'token' => $token,
        ]);
    }
}
