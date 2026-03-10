<?php

namespace App\Exports;

use App\Models\StockEntry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockEntriesExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected $from,
        protected $to,
        protected $productId,
        protected $userId,
        protected $role,
    ) {
    }

    public function collection()
    {
        $query = StockEntry::query()->with(['product', 'user.roles']);

        if (!empty($this->from)) {
            $query->whereDate('entered_at', '>=', $this->from);
        }

        if (!empty($this->to)) {
            $query->whereDate('entered_at', '<=', $this->to);
        }

        if (!empty($this->productId)) {
            $query->where('product_id', $this->productId);
        }

        if (!empty($this->userId)) {
            $query->where('user_id', $this->userId);
        }

        if (!empty($this->role)) {
            $query->whereHas('user.roles', function ($q) {
                $q->where('name', $this->role);
            });
        }

        return $query->latest('entered_at')->get()->map(function (StockEntry $entry) {
            return [
                'fecha_hora' => optional($entry->entered_at)->format('Y-m-d H:i:s'),
                'codigo_producto' => $entry->product?->code,
                'producto' => $entry->product?->name,
                'cantidad' => $entry->quantity,
                'stock_antes' => $entry->stock_before,
                'stock_despues' => $entry->stock_after,
                'motivo' => $entry->reason,
                'usuario' => $entry->user?->email,
                'rol' => $entry->user?->roles?->pluck('name')->join(', '),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha/Hora',
            'Código',
            'Producto',
            'Cantidad',
            'Stock antes',
            'Stock después',
            'Motivo',
            'Usuario (correo)',
            'Rol',
        ];
    }
}