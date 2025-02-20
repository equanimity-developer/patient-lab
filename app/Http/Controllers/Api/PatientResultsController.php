<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Result;
use Illuminate\Http\JsonResponse;

class PatientResultsController extends Controller
{
    public function index(): JsonResponse
    {
        $patient = auth()->user();
        $patient->load('orders.results');


        return response()->json([
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->name,
                'surname' => $patient->surname,
                'sex' => $patient->sex,
                'birthDate' => $patient->date_of_birth->format('Y-m-d'),
            ],
            'orders' => $patient->orders->map(function ($order) {
                return [
                    'orderId' => $order->id,
                    'results' => $order->results->map(function ($result) {
                        return [
                            'name' => $result->test_name,
                            'value' => $result->test_value,
                            'reference' => $result->test_reference,
                        ];
                    }),
                ];
            }),
        ]);
    }
}
