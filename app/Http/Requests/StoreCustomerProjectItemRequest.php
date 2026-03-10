<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreCustomerProjectItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_query' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function passedValidation(): void
    {
        $query = trim((string) $this->input('product_query'));

        $product = Product::query()
            ->where('code', $query)
            ->orWhere('barcode', $query)
            ->orWhere('id', is_numeric($query) ? (int) $query : 0)
            ->first();

        if (!$product) {
            throw ValidationException::withMessages([
                'product_query' => 'Producto no encontrado por código, barcode o ID.',
            ]);
        }

        if ($product->stock < $this->integer('quantity')) {
            throw ValidationException::withMessages([
                'quantity' => 'Stock insuficiente para este producto.',
            ]);
        }
    }
}