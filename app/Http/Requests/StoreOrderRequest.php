<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Auth already handled by middleware
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.*.qty.min' => 'Qty setiap item harus lebih besar dari 0.',
            'items.*.product_id.exists' => 'Produk tidak ditemukan.',
            'customer_id.exists' => 'Customer tidak ditemukan.',
        ];
    }
}
