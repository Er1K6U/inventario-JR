<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Informe de ventas por proyectos
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="p-3 rounded bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-3 rounded bg-red-100 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('clientes_credito.reportes.proyectos_ventas') }}"
                    class="grid grid-cols-1 md:grid-cols-6 gap-3">

                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                        class="border rounded px-3 py-2">

                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="border rounded px-3 py-2">

                    <select name="customer_id" class="border rounded px-3 py-2">
                        <option value="">Todos los clientes</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" @selected((string) ($filters['customerId'] ?? '') === (string) $c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="border rounded px-3 py-2">
                        <option value="">Todos los estados</option>
                        <option value="abierto" @selected(($filters['status'] ?? '') === 'abierto')>Abierto</option>
                        <option value="cerrado" @selected(($filters['status'] ?? '') === 'cerrado')>Cerrado</option>
                        <option value="anulado" @selected(($filters['status'] ?? '') === 'anulado')>Anulado</option>
                    </select>

                    <button type="submit" class="bg-blue-600 text-white rounded px-4 py-2">
                        Filtrar
                    </button>

                    <a href="{{ route('clientes_credito.reportes.proyectos_ventas.export', request()->query()) }}"
                        class="bg-green-600 text-white rounded px-4 py-2 inline-flex items-center justify-center">
                        Exportar Excel
                    </a>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <p class="text-sm text-gray-500">Total vendido</p>
                    <p class="text-2xl font-bold">
                        ${{ number_format((float) ($summary->total_sold ?? 0), 2) }}
                    </p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <p class="text-sm text-gray-500">Total pagado</p>
                    <p class="text-2xl font-bold">
                        ${{ number_format((float) ($summary->total_paid ?? 0), 2) }}
                    </p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <p class="text-sm text-gray-500">Saldo pendiente</p>
                    <p class="text-2xl font-bold">
                        ${{ number_format((float) ($summary->total_balance ?? 0), 2) }}
                    </p>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Cliente</th>
                            <th class="px-4 py-3">Proyecto</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Detalle de compra</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Pagado</th>
                            <th class="px-4 py-3">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $p)
                            <tr class="border-t align-top">
                                <td class="px-4 py-3">{{ optional($p->project_date)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">{{ $p->customer?->name }}</td>
                                <td class="px-4 py-3">{{ $p->name }}</td>
                                <td class="px-4 py-3">{{ $p->status }}</td>

                                <td class="px-4 py-3">
                                    @if($p->items && $p->items->count())
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach($p->items as $item)
                                                @php
                                                    $codigo = $item->product->code ?? ('ID-' . $item->product_id);
                                                    $nombre = $item->product->name ?? 'Producto sin nombre';
                                                @endphp
                                                <li>
                                                    <span class="font-semibold">[{{ $codigo }}]</span>
                                                    {{ $nombre }} — Cant: {{ (int) $item->quantity }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-500">Sin ítems</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">${{ number_format((float) $p->total, 2) }}</td>
                                <td class="px-4 py-3">${{ number_format((float) $p->paid, 2) }}</td>
                                <td class="px-4 py-3 font-semibold">${{ number_format((float) $p->balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                    Sin resultados para los filtros seleccionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</x-app-layout>