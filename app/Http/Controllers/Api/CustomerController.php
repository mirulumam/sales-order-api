<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * GET /api/customers
     *
     * [Optional] query params:
     *   ?search=keyword    – filter by customer name or phone
     *   ?per_page=15       – items per page (default 15)
     */
    public function index(Request $request): JsonResponse
    {
        $customers = Customer::query()
            ->when($request->search, fn ($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
            )
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => CustomerResource::collection($customers),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
            ],
        ]);
    }

    /**
     * GET /api/customers/{id}
     */
    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new CustomerResource($customer),
        ]);
    }
}
