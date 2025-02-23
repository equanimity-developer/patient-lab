<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PatientAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|date',
        ]);

        $patient = Patient::whereRaw(
            "BINARY CONCAT(name, surname) = ?",
            [$request->login]
        )->first();

        if (!$patient || $patient->date_of_birth->format('Y-m-d') !== $request->password) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = auth('api')->login($patient);

        return response()->json([
            'token' => $token,
        ]);
    }
}
