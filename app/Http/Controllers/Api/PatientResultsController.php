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

        // Paginate orders, 10 per page
        $paginatedOrders = $patient->orders()->paginate(5);

        return response()->json([
            'patient' => new PatientResource($patient),
            'orders' => OrderResource::collection($paginatedOrders),
            'pagination' => [
                'current_page' => $paginatedOrders->currentPage(),
                'last_page' => $paginatedOrders->lastPage(),
                'per_page' => $paginatedOrders->perPage(),
                'total' => $paginatedOrders->total(),
                'next_page_url' => $paginatedOrders->nextPageUrl(),
                'prev_page_url' => $paginatedOrders->previousPageUrl(),
            ],
        ]);
    }
}
