<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reportes</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">Ventas del día</h3>
                <p class="mt-2 text-2xl font-bold text-emerald-700">$ {{ number_format($salesTodayTotal, 2) }}</p>
            </div>
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">Ventas totales acumuladas</h3>
                <p class="mt-2 text-2xl font-bold text-blue-700">$ {{ number_format($salesOverallTotal, 2) }}</p>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Filtro de ventas</h3>

                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-sm font-medium">Desde</label>
                        <input type="date" name="from" value="{{ $filters['from'] }}"
                            class="mt-1 w-full rounded border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Hasta</label>
                        <input type="date" name="to" value="{{ $filters['to'] }}"
                            class="mt-1 w-full rounded border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Vendedor</label>
                        <select name="seller_id" class="mt-1 w-full rounded border-gray-300">
                            <option value="">-- Todos --</option>
                            @foreach ($sellers as $seller)
                                <option value="{{ $seller->id }}" @selected((string) $filters['seller_id'] === (string) $seller->id)>
                                    {{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Filtrar</button>
                        <a href="{{ route('reports.index') }}"
                            class="rounded bg-gray-400 px-4 py-2 text-white hover:bg-gray-500">Limpiar</a>
                    </div>
                </form>
                <div class="mt-3">
                    <a href="{{ route('reports.export', ['from' => $filters['from'], 'to' => $filters['to']]) }}"
                        class="inline-block rounded bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">
                        Exportar Excel (rango actual)
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Ventas (listado)</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">N° Venta</th>
                                <th class="border px-3 py-2 text-left">Fecha</th>
                                <th class="border px-3 py-2 text-left">Vendedor</th>
                                <th class="border px-3 py-2 text-left">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sales as $sale)
                                <tr>
                                    <td class="border px-3 py-2">{{ $sale->sale_number }}</td>
                                    <td class="border px-3 py-2">{{ $sale->sold_at?->format('Y-m-d H:i:s') }}</td>
                                    <td class="border px-3 py-2">{{ $sale->seller->name ?? 'N/A' }}</td>
                                    <td class="border px-3 py-2">$ {{ number_format($sale->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="border px-3 py-2" colspan="4">No hay ventas para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $sales->links() }}</div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Ventas por rol</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Rol</th>
                                <th class="border px-3 py-2 text-left">Órdenes</th>
                                <th class="border px-3 py-2 text-left">Total ventas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($salesByRole as $row)
                                <tr>
                                    <td class="border px-3 py-2">{{ $row->role_name }}</td>
                                    <td class="border px-3 py-2">{{ $row->total_orders }}</td>
                                    <td class="border px-3 py-2">$ {{ number_format($row->total_sales, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="border px-3 py-2" colspan="3">No hay datos por rol.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Detalle de productos vendidos (quién vendió qué)</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Fecha</th>
                                <th class="border px-3 py-2 text-left">Vendedor</th>
                                <th class="border px-3 py-2 text-left">Rol</th>
                                <th class="border px-3 py-2 text-left">Código</th>
                                <th class="border px-3 py-2 text-left">Producto</th>
                                <th class="border px-3 py-2 text-left">Cantidad</th>
                                <th class="border px-3 py-2 text-left">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($salesDetail as $detail)
                                <tr>
                                    <td class="border px-3 py-2">{{ $detail->sale?->sold_at?->format('Y-m-d H:i:s') }}</td>
                                    <td class="border px-3 py-2">{{ $detail->sale?->seller?->name ?? 'N/A' }}</td>
                                    <td class="border px-3 py-2">
                                        {{ $detail->sale?->seller?->getRoleNames()?->first() ?? 'Sin rol' }}
                                    </td>
                                    <td class="border px-3 py-2">{{ $detail->product?->code }}</td>
                                    <td class="border px-3 py-2">{{ $detail->product?->name }}</td>
                                    <td class="border px-3 py-2">{{ $detail->quantity }}</td>
                                    <td class="border px-3 py-2">$ {{ number_format($detail->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="border px-3 py-2" colspan="7">No hay detalle de ventas para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $salesDetail->links() }}</div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Inventario actual</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Código</th>
                                <th class="border px-3 py-2 text-left">Nombre</th>
                                <th class="border px-3 py-2 text-left">Stock</th>
                                <th class="border px-3 py-2 text-left">Stock mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inventory as $product)
                                <tr>
                                    <td class="border px-3 py-2">{{ $product->code }}</td>
                                    <td class="border px-3 py-2">{{ $product->name }}</td>
                                    <td class="border px-3 py-2">{{ $product->stock }}</td>
                                    <td class="border px-3 py-2">{{ $product->min_stock }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="border px-3 py-2" colspan="4">No hay productos para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $inventory->links() }}</div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3 text-red-700">Alerta de stock bajo</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-red-50">
                            <tr>
                                <th class="border px-3 py-2 text-left">Código</th>
                                <th class="border px-3 py-2 text-left">Nombre</th>
                                <th class="border px-3 py-2 text-left">Stock</th>
                                <th class="border px-3 py-2 text-left">Mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStockProducts as $product)
                                <tr>
                                    <td class="border px-3 py-2">{{ $product->code }}</td>
                                    <td class="border px-3 py-2">{{ $product->name }}</td>
                                    <td class="border px-3 py-2">{{ $product->stock }}</td>
                                    <td class="border px-3 py-2">{{ $product->min_stock }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="border px-3 py-2" colspan="4">No hay productos en stock bajo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>