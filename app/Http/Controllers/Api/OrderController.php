<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) { }

    /**
     * GET /api/orders
     *
     * [Optional]
     * query params:
     *   ?status=1|2|3
     *   ?per_page=15       – items per page (default 15)
     * 
     * 1 => Draft
     * 2 => Submitted
     * 3 => Cancelled
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['customer', 'creator'])
            ->when($request->status, function ($q, $s) {
                $status = Order::STATUS_MAP[$s] ?? null;
                $q->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * GET /api/orders/{id}
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['customer', 'creator', 'details.product']);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * POST /api/orders
     *
     * Body:
     * {
     *   "customer_id": {id},
     *   "items": [
     *     { "product_id": {product_id_1}, "qty": {qty_1} },
     *     { "product_id": {product_id_2}, "qty": {qty_2} }
     *   ]
     * }
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createDraft(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Order draft berhasil dibuat',
                'data' => new OrderResource($order),
            ], 201);
        } catch (OrderException $e) {
            return $this->businessError($e);
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    /**
     * PATCH /api/orders/submit
     * 
     * Body:
     * {
     *     "order_id": {id}
     * }
     */
    public function submit(Request $request): JsonResponse
    {
        $request->validate(['order_id' => ['required', 'integer', 'exists:orders,id']]);

        $order = Order::findOrFail($request->order_id);

        try {
            $order = $this->orderService->submit($order);
            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat.',
                'data' => new OrderResource($order),
            ]);
        } catch (OrderException $e) {
            return $this->businessError($e);
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    /**
     * PATCH /api/orders/cancel
     * 
     * Body:
     * {
     *     "order_id": {id}
     * }
     */
    public function cancel(Request $request): JsonResponse
    {
        $request->validate(['order_id' => ['required', 'integer', 'exists:orders,id']]);

        $order = Order::findOrFail($request->order_id);

        try {
            $order = $this->orderService->cancel($order);
            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibatalkan. Stok produk telah dikembalikan.',
                'data' => new OrderResource($order),
            ]);
        } catch (OrderException $e) {
            return $this->businessError($e);
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    private function businessError(OrderException $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], $e->getCode());
    }

    private function serverError(Throwable $e): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan pada server.',
            'detail' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}
