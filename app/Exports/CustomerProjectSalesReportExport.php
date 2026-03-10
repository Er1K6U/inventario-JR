<?php

namespace App\Exports;

use App\Models\CustomerProject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerProjectSalesReportExport implements FromCollection, WithHeadings
{
    public function __construct(
        private readonly ?string $from,
        private readonly ?string $to,
        private readonly ?string $customerId,
        private readonly ?string $status
    ) {
    }

    public function collection()
    {
        return CustomerProject::query()
            ->with([
                'customer:id,name',
                'items:id,customer_project_id,product_id,quantity,subtotal',
                'items.product:id,code,name',
                'payments:id,customer_project_id,amount',
            ])
            ->when($this->from, fn($q) => $q->whereDate('project_date', '>=', $this->from))
            ->when($this->to, fn($q) => $q->whereDate('project_date', '<=', $this->to))
            ->when($this->customerId, fn($q) => $q->where('customer_id', $this->customerId))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderByDesc('project_date')
            ->get()
            ->map(function ($project) {
                $detalle = $project->items->map(function ($item) {
                    $codigo = $item->product->code ?? ('ID-' . $item->product_id);
                    $nombre = $item->product->name ?? 'Producto sin nombre';
                    return '[' . $codigo . '] ' . $nombre . ' (x' . (int) $item->quantity . ')';
                })->implode(' | ');

                $total = (float) $project->items->sum('subtotal');
                $pagado = (float) $project->payments->sum('amount');
                $saldo = $total - $pagado;

                return [
                    'fecha' => optional($project->project_date)->format('Y-m-d'),
                    'cliente' => $project->customer?->name,
                    'proyecto' => $project->name,
                    'estado' => $project->status,
                    'detalle_compra' => $detalle ?: 'Sin ítems',
                    'total' => $total,
                    'pagado' => $pagado,
                    'saldo' => $saldo,
                ];
            });
    }

    public function headings(): array
    {
        return ['Fecha', 'Cliente', 'Proyecto', 'Estado', 'Detalle compra', 'Total', 'Pagado', 'Saldo'];
    }
}