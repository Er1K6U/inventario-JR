<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $sellerId = $request->input('seller_id');

        $salesQuery = Sale::query()->with('seller.roles');

        if ($from) {
            $salesQuery->whereDate('sold_at', '>=', $from);
        }

        if ($to) {
            $salesQuery->whereDate('sold_at', '<=', $to);
        }

        if ($sellerId) {
            $salesQuery->where('seller_id', $sellerId);
        }

        $sales = $salesQuery->latest('sold_at')->paginate(15)->withQueryString();

        $salesTodayTotal = Sale::whereDate('sold_at', now()->toDateString())->sum('total');
        $salesOverallTotal = Sale::sum('total');

        $salesByRole = Sale::query()
            ->join('users', 'sales.seller_id', '=', 'users.id')
            ->join('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name as role_name', DB::raw('SUM(sales.total) as total_sales'), DB::raw('COUNT(sales.id) as total_orders'))
            ->groupBy('roles.name')
            ->orderByDesc('total_sales')
            ->get();

        $salesDetailQuery = SaleItem::query()->with([
            'sale.seller.roles',
            'product',
        ]);

        if ($from) {
            $salesDetailQuery->whereHas('sale', function ($q) use ($from) {
                $q->whereDate('sold_at', '>=', $from);
            });
        }

        if ($to) {
            $salesDetailQuery->whereHas('sale', function ($q) use ($to) {
                $q->whereDate('sold_at', '<=', $to);
            });
        }

        if ($sellerId) {
            $salesDetailQuery->whereHas('sale', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            });
        }

        $salesDetail = $salesDetailQuery
            ->latest('id')
            ->paginate(20, ['*'], 'detail_page')
            ->withQueryString();

        $inventory = Product::orderBy('name')->paginate(15, ['*'], 'inventory_page')->withQueryString();

        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
            ->where('active', true)
            ->orderBy('stock')
            ->get();

        $sellers = User::orderBy('name')->get();

        return view('reports.index', [
            'sales' => $sales,
            'salesTodayTotal' => $salesTodayTotal,
            'salesOverallTotal' => $salesOverallTotal,
            'salesByRole' => $salesByRole,
            'salesDetail' => $salesDetail,
            'inventory' => $inventory,
            'lowStockProducts' => $lowStockProducts,
            'sellers' => $sellers,
            'filters' => [
                'from' => $from,
                'to' => $to,
                'seller_id' => $sellerId,
            ],
        ]);
    }

    public function export(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $fileName = 'reporte-ventas-' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new SalesReportExport($from, $to), $fileName);
    }
}