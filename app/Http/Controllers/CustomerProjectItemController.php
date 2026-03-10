<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerProjectItemRequest;
use App\Models\CustomerProject;
use App\Models\CustomerProjectItem;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerProjectItemController extends Controller
{
    public function store(StoreCustomerProjectItemRequest $request, CustomerProject $project): RedirectResponse
    {
        if ($project->status !== 'abierto') {
            return back()->with('error', 'No puedes agregar repuestos a un proyecto cerrado.');
        }

        DB::transaction(function () use ($request, $project) {
            $query = trim((string) $request->input('product_query'));

            $product = Product::query()
                ->lockForUpdate()
                ->where('code', $query)
                ->orWhere('barcode', $query)
                ->orWhere('id', is_numeric($query) ? (int) $query : 0)
                ->first();

            if (!$product) {
                throw ValidationException::withMessages([
                    'product_query' => 'Producto no encontrado por código, barcode o ID.',
                ]);
            }

            $quantity = $request->integer('quantity');
            $unitPrice = $request->filled('unit_price')
                ? (float) $request->input('unit_price')
                : (float) ($product->sale_price ?? $product->price ?? 0);

            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'La cantidad debe ser mayor o igual a 1.',
                ]);
            }

            if ($product->stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stock insuficiente para este producto.',
                ]);
            }

            if ($unitPrice < 0) {
                throw ValidationException::withMessages([
                    'unit_price' => 'El precio no puede ser negativo.',
                ]);
            }

            CustomerProjectItem::create([
                'customer_project_id' => $project->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $quantity * $unitPrice,
                'added_by_user_id' => auth()->id(),
                'added_at' => now(),
            ]);

            $stockBefore = (int) $product->stock;
            $stockAfter = $stockBefore - $quantity;

            $product->decrement('stock', $quantity);

            InventoryMovement::create([
                'product_id' => $product->id,
                'type' => 'out',
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reason' => 'Salida por proyecto #' . $project->id,
                'user_id' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Repuesto agregado correctamente.');
    }
}