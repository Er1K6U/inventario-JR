<?php

namespace App\Http\Controllers;

use App\Exports\CustomerProjectSalesReportExport;
use App\Models\Customer;
use App\Models\CustomerProject;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CustomerProjectReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $customerId = $request->input('customer_id');
        $status = $request->input('status');

        $baseQuery = CustomerProject::query()
            ->with([
                'customer:id,name',
                'items:id,customer_project_id,product_id,quantity,subtotal',
                'items.product:id,code,name',
                'payments:id,customer_project_id,amount',
            ])
            ->when($from, fn($q) => $q->whereDate('project_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('project_date', '<=', $to))
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest('project_date');

        $projects = (clone $baseQuery)->paginate(20)->withQueryString();

        $allFiltered = (clone $baseQuery)->get();

        $totalSold = $allFiltered->sum(fn($p) => (float) $p->items->sum('subtotal'));
        $totalPaid = $allFiltered->sum(fn($p) => (float) $p->payments->sum('amount'));
        $totalBalance = $totalSold - $totalPaid;

        $summary = (object) [
            'total_sold' => $totalSold,
            'total_paid' => $totalPaid,
            'total_balance' => $totalBalance,
        ];

        $customers = Customer::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('reports.customer-project-sales', [
            'projects' => $projects,
            'customers' => $customers,
            'summary' => $summary,
            'filters' => compact('from', 'to', 'customerId', 'status'),
        ]);
    }

    public function export(Request $request)
    {
        $filename = 'reporte_proyectos_ventas_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new CustomerProjectSalesReportExport(
                $request->input('from'),
                $request->input('to'),
                $request->input('customer_id'),
                $request->input('status')
            ),
            $filename
        );
    }
}