<?php

namespace App\Services;

use App\Exceptions\OrderException;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{

    /**
     * Create a new Draft order with its detail lines.
     *
     * @param  array{customer_id: int, items: [{product_id: int, qty: int}]}  $data
     */
    public function createDraft(array $data, User $user): Order
    {
        $customer = Customer::findOrFail($data['customer_id']);

        return DB::transaction(function () use ($data, $user, $customer) {
            $order = Order::create([
                'order_no' => $this->generateOrderNo(),
                'customer_id' => $customer->id,
                'status' => Order::STATUS_DRAFT,
                'total_amount' => 0,
                'created_by' => $user->id,
            ]);

            $total = $this->syncItems($order, $data['items']);

            $order->update(['total_amount' => $total]);

            return $order->load(['customer', 'creator', 'details.product']);
        });
    }

    /**
     * Submit a Draft order, decrementing product stock.
     * Uses SELECT ... FOR UPDATE to prevent race conditions.
     */
    public function submit(Order $order): Order
    {
        if (! $order->isDraft()) {
            throw new OrderException(
                'Hanya order berstatus Draft yang dapat dilanjutkan',
                422
            );
        }

        if ($order->details->isEmpty()) {
            throw new OrderException('Order tidak memiliki item.', 422);
        }

        return DB::transaction(function () use ($order) {
            foreach ($order->details as $detail) {
                $product = Product::lockForUpdate()->findOrFail($detail->product_id);

                if (! $product->hasEnoughStock($detail->qty)) {
                    throw new OrderException(
                        "Stok produk \"{$product->name}\" tidak mencukupi",
                        422
                    );
                }

                $product->decrement('stock', $detail->qty);
            }

            $order->update(['status' => Order::STATUS_SUBMITTED]);

            return $order->fresh(['customer', 'creator', 'details.product']);
        });
    }

    /**
     * Cancel a Submitted order, restoring product stock.
     */
    public function cancel(Order $order): Order
    {
        if (! $order->isSubmitted()) {
            throw new OrderException(
                'Hanya order berstatus Submitted yang dapat dibatalkan',
                422
            );
        }

        return DB::transaction(function () use ($order) {
            foreach ($order->details as $detail) {
                Product::lockForUpdate()->findOrFail($detail->product_id)
                    ->increment('stock', $detail->qty);
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);

            return $order->fresh(['customer', 'creator', 'details.product']);
        });
    }

    /**
     * Insert/replace detail lines for an order and return the new total_amount.
     *
     * @param  [{product_id: int, qty: int}]  $items
     */
    private function syncItems(Order $order, array $items): float
    {
        $order->details()->delete();

        $total = 0;

        foreach ($items as $item) {
            if ((int) $item['qty'] <= 0) {
                throw new OrderException('Qty setiap item harus lebih besar dari 0.', 422);
            }

            $product = Product::find($item['product_id']);
            if (! $product) {
                throw new OrderException(
                    "Produk dengan id {$item['product_id']} tidak ditemukan.",
                    422
                );
            }

            $subtotal = $product->price * $item['qty'];
            $total += $subtotal;

            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'qty' => $item['qty'],
                'price' => $product->price,
                'subtotal' => $subtotal,
            ]);
        }

        return $total;
    }

    /**
     * Generate a unique order number: ORD-YYYYMMDD-XXXX
     */
    private function generateOrderNo(): string
    {
        $prefix = 'ORD-' . now()->format('Ymd') . '-';
        $last = Order::where('order_no', 'like', $prefix . '%')
            ->orderByDesc('order_no')
            ->value('order_no');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
