<?php

namespace App\Http\Controllers;

use App\Models\CashSession;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesScannerController extends Controller
{
    public function index(Request $request): View
    {
        $cart = session('sales_cart', []);
        $total = collect($cart)->sum('subtotal');

        return view('sales.scanner', [
            'cart' => $cart,
            'total' => $total,
            'lastCode' => $request->input('code', ''),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $code = trim($request->input('code'));
        $quantity = (int) $request->input('quantity');

        $product = Product::where('code', $code)
            ->orWhere('barcode', $code)
            ->first();

        if (!$product) {
            return back()->with('error', 'Producto no encontrado para ese código.');
        }

        if (!$product->active) {
            return back()->with('error', 'El producto está inactivo.');
        }

        if ($product->stock < $quantity) {
            return back()->with('error', 'Stock insuficiente para ' . $product->name . '. Disponible: ' . $product->stock);
        }

        $cart = session('sales_cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
            $cart[$product->id]['subtotal'] = $cart[$product->id]['quantity'] * $cart[$product->id]['unit_price'];
        } else {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'photo_path' => $product->photo_path,
                'quantity' => $quantity,
                'unit_price' => (float) $product->price,
                'subtotal' => $quantity * (float) $product->price,
            ];
        }

        session(['sales_cart' => $cart]);

        return back()->with('success', 'Producto agregado al carrito.');
    }

    public function clear(): RedirectResponse
    {
        session()->forget('sales_cart');

        return back()->with('success', 'Carrito limpiado.');
    }

    public function checkout(): RedirectResponse
    {
        $cart = session('sales_cart', []);

        if (empty($cart)) {
            return back()->with('error', 'No hay productos en el carrito.');
        }

        $activeSession = CashSession::where('status', 'OPEN')->latest('opened_at')->first();
        if (!$activeSession) {
            return back()->with('error', 'Debes abrir un día de ventas antes de vender.');
        }

        DB::transaction(function () use ($cart, $activeSession) {
            $total = collect($cart)->sum('subtotal');

            $sale = Sale::create([
                'cash_session_id' => $activeSession->id,
                'seller_id' => Auth::id(),
                'sale_number' => 'V-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                'total' => $total,
                'payment_method' => 'CASH',
                'sold_at' => now(),
                'status' => 'COMPLETED',
            ]);

            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \RuntimeException('Stock insuficiente en ' . $product->name . ' durante confirmación.');
                }

                $before = $product->stock;
                $after = $before - $item['quantity'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $product->update(['stock' => $after]);

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'OUT',
                    'quantity' => -1 * (int) $item['quantity'],
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'reason' => 'Venta ' . $sale->sale_number,
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                ]);
            }
        });

        session()->forget('sales_cart');

        return redirect()->route('sales.scanner')->with('success', 'Venta confirmada correctamente.');
    }
}