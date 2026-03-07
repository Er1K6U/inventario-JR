<?php

namespace App\Exports;

use App\Models\SaleItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(
        private readonly ?string $from,
        private readonly ?string $to,
    ) {
    }

    public function headings(): array
    {
        return [
            'Fecha venta',
            'Número venta',
            'Vendedor',
            'Rol',
            'Código producto',
            'Producto',
            'Cantidad',
            'Precio unitario',
            'Subtotal',
        ];
    }

    public function collection(): Collection
    {
        $query = SaleItem::query()->with([
            'sale.seller.roles',
            'product',
        ]);

        if ($this->from) {
            $query->whereHas('sale', function ($q) {
                $q->whereDate('sold_at', '>=', $this->from);
            });
        }

        if ($this->to) {
            $query->whereHas('sale', function ($q) {
                $q->whereDate('sold_at', '<=', $this->to);
            });
        }

        return $query
            ->orderByDesc('id')
            ->get()
            ->map(function (SaleItem $item) {
                $seller = $item->sale?->seller;
                $role = $seller?->getRoleNames()?->first() ?? 'Sin rol';

                return collect([
                    optional($item->sale?->sold_at)?->format('Y-m-d H:i:s'),
                    $item->sale?->sale_number,
                    $seller?->name,
                    $role,
                    $item->product?->code,
                    $item->product?->name,
                    $item->quantity,
                    $item->unit_price,
                    $item->subtotal,
                ]);
            });
    }
}