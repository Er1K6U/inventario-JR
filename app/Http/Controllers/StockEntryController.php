<?php

namespace App\Http\Controllers;

use App\Exports\StockEntriesExport;
use App\Http\Requests\StoreStockEntryRequest;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockEntryController extends Controller
{
    public function index(): View
    {
        return view('stock-entries.index');
    }

    public function store(StoreStockEntryRequest $request): RedirectResponse
    {
        $product = Product::query()
            ->where('code', $request->string('code')->toString())
            ->first();

        if (!$product) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'No existe un producto con ese código.']);
        }

        $quantity = (int) $request->input('quantity');
        $stockBefore = (int) $product->stock;
        $stockAfter = $stockBefore + $quantity;

        DB::transaction(function () use ($product, $quantity, $stockBefore, $stockAfter, $request): void {
            StockEntry::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reason' => $request->string('reason')->toString(),
                'entered_at' => now(),
            ]);

            $product->update([
                'stock' => $stockAfter,
            ]);
        });

        return back()->with('success', 'Ingreso registrado correctamente.');
    }

    public function report(Request $request): View
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $productId = $request->input('product_id');
        $userId = $request->input('user_id');
        $role = $request->input('role');

        $entriesQuery = StockEntry::query()
            ->with(['product', 'user.roles']);

        if ($from) {
            $entriesQuery->whereDate('entered_at', '>=', $from);
        }

        if ($to) {
            $entriesQuery->whereDate('entered_at', '<=', $to);
        }

        if ($productId) {
            $entriesQuery->where('product_id', $productId);
        }

        if ($userId) {
            $entriesQuery->where('user_id', $userId);
        }

        if ($role) {
            $entriesQuery->whereHas('user.roles', function ($q) use ($role): void {
                $q->where('name', $role);
            });
        }

        $entries = $entriesQuery
            ->latest('entered_at')
            ->paginate(20)
            ->withQueryString();

        $products = Product::query()->orderBy('name')->get(['id', 'name', 'code']);
        $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);

        return view('stock-entries.report', [
            'entries' => $entries,
            'products' => $products,
            'users' => $users,
            'filters' => [
                'from' => $from,
                'to' => $to,
                'product_id' => $productId,
                'user_id' => $userId,
                'role' => $role,
            ],
        ]);
    }

    public function export(Request $request)
    {
        $fileName = 'ingresos-mercancia-' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new StockEntriesExport(
            $request->input('from'),
            $request->input('to'),
            $request->input('product_id'),
            $request->input('user_id'),
            $request->input('role')
        ), $fileName);
    }
}