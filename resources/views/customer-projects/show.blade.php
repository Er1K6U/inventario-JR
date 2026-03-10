<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Proyecto: {{ $project->name }}
                </h2>
                <p class="text-gray-600">Cliente: {{ $project->customer->name }}</p>
            </div>

            <a href="{{ route('clientes_credito.proyectos.index', $project->customer_id) }}"
                class="text-sm text-blue-600 hover:underline">
                ← Volver a proyectos
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Resumen --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-gray-500 text-sm">Total</p>
                            <p class="text-2xl font-bold">${{ number_format($project->total, 2) }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-gray-500 text-sm">Pagado</p>
                            <p class="text-2xl font-bold text-green-700">${{ number_format($project->paid, 2) }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-gray-500 text-sm">Saldo</p>
                            <p class="text-2xl font-bold text-orange-700">${{ number_format($project->balance, 2) }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        {{-- Agregar repuesto --}}
                        <div class="bg-gray-50 p-6 rounded">
                            <h3 class="text-lg font-semibold mb-3">Agregar repuesto</h3>
                            <p class="text-sm text-gray-500 mb-3">Puedes escribir código (ej: JR0911), barcode o ID.</p>

                            <form method="POST" action="{{ route('clientes_credito.items.store', $project) }}"
                                class="space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <input type="text" name="product_query" value="{{ old('product_query') }}"
                                            placeholder="Código, barcode o ID (ej: JR0911)"
                                            class="w-full border rounded px-3 py-2" required>
                                        @error('product_query')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <input type="number" name="quantity" value="{{ old('quantity') }}"
                                            placeholder="Cantidad" min="1" class="w-full border rounded px-3 py-2"
                                            required>
                                        @error('quantity')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <input type="number" name="unit_price" value="{{ old('unit_price') }}"
                                            placeholder="Precio (opcional)" step="0.01" min="0"
                                            class="w-full border rounded px-3 py-2">
                                        @error('unit_price')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded">
                                    Agregar ítem
                                </button>
                            </form>

                            <h4 class="font-semibold mt-6 mb-2">Ítems</h4>
                            <div class="overflow-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-100 text-left">
                                        <tr>
                                            <th class="px-3 py-2">Producto</th>
                                            <th class="px-3 py-2">Cant.</th>
                                            <th class="px-3 py-2">P. Unit.</th>
                                            <th class="px-3 py-2">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($project->items as $item)
                                            <tr class="border-t">
                                                <td class="px-3 py-2">{{ $item->product->name ?? ('#' . $item->product_id) }}
                                                </td>
                                                <td class="px-3 py-2">{{ $item->quantity }}</td>
                                                <td class="px-3 py-2">${{ number_format((float) $item->unit_price, 2) }}</td>
                                                <td class="px-3 py-2">${{ number_format((float) $item->subtotal, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-3 py-4 text-center text-gray-500">Sin ítems aún.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Registrar abono --}}
                        <div class="bg-gray-50 p-6 rounded">
                            <h3 class="text-lg font-semibold mb-3">Registrar abono</h3>

                            <form method="POST" action="{{ route('clientes_credito.pagos.store', $project) }}"
                                class="space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div>
                                        <input type="number" name="amount" value="{{ old('amount') }}" step="0.01"
                                            min="0.01" placeholder="Monto" class="w-full border rounded px-3 py-2"
                                            required>
                                        @error('amount')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <input type="date" name="payment_date" value="{{ old('payment_date') }}"
                                            class="w-full border rounded px-3 py-2" required>
                                        @error('payment_date')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <input type="text" name="note" value="{{ old('note') }}"
                                            placeholder="Nota (opcional)" class="w-full border rounded px-3 py-2">
                                        @error('note')
                                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded">
                                    Registrar abono
                                </button>
                            </form>

                            <h4 class="font-semibold mt-6 mb-2">Pagos</h4>
                            <div class="overflow-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-100 text-left">
                                        <tr>
                                            <th class="px-3 py-2">Fecha</th>
                                            <th class="px-3 py-2">Monto</th>
                                            <th class="px-3 py-2">Nota</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($project->payments as $payment)
                                            <tr class="border-t">
                                                <td class="px-3 py-2">
                                                    {{ optional($payment->payment_date)->format('Y-m-d') }}</td>
                                                <td class="px-3 py-2">${{ number_format((float) $payment->amount, 2) }}</td>
                                                <td class="px-3 py-2">{{ $payment->note }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-3 py-4 text-center text-gray-500">Sin abonos aún.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>{{-- grid --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>