<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\PatientResource;
use Illuminate\Http\JsonResponse;

class PatientResultsController extends Controller
{
    public function index(): JsonResponse
    {
        $patient = auth()->user();
        $patient->load('orders.results');

        return response()->json([
            'patient' => new PatientResource($patient),
            'orders' => OrderResource::collection($patient->orders),
        ]);
    }
}
