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
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function passedValidation(): void
    {
        $product = Product::find($this->integer('product_id'));

        if (!$product) {
            return;
        }

        if ($product->stock < $this->integer('quantity')) {
            throw ValidationException::withMessages([
                'quantity' => 'Stock insuficiente para este producto.',
            ]);
        }
    }
}