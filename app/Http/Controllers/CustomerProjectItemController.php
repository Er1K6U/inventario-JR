<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerProjectItemRequest;
use App\Models\CustomerProject;
use App\Models\CustomerProjectItem;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CustomerProjectItemController extends Controller
{
    public function store(StoreCustomerProjectItemRequest $request, CustomerProject $project): RedirectResponse
    {
        if ($project->status !== 'abierto') {
            return back()->with('error', 'No puedes agregar repuestos a un proyecto cerrado.');
        }

        DB::transaction(function () use ($request, $project) {
            $product = Product::query()->lockForUpdate()->findOrFail($request->integer('product_id'));
            $quantity = $request->integer('quantity');
            $unitPrice = $request->filled('unit_price')
                ? (float) $request->input('unit_price')
                : (float) $product->sale_price;

            if ($product->stock < $quantity) {
                abort(422, 'Stock insuficiente para este producto.');
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

            $product->decrement('stock', $quantity);

            InventoryMovement::create([
                'product_id' => $product->id,
                'type' => 'out',
                'quantity' => $quantity,
                'reason' => 'Salida por proyecto #' . $project->id,
                'user_id' => auth()->id(),
            ]);
        });

        return back()->with('success', 'Repuesto agregado correctamente.');
    }
}