<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ventas por escáner</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="rounded border border-green-300 bg-green-100 p-3 text-green-800">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="rounded border border-red-300 bg-red-100 p-3 text-red-800">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('sales.scanner.add') }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium">Código o código de barras</label>
                        <input id="scan-code" type="text" name="code" value="{{ $lastCode }}"
                            class="mt-1 w-full rounded border-gray-300" autocomplete="off" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Cantidad</label>
                        <input type="number" min="1" name="quantity" value="1"
                            class="mt-1 w-full rounded border-gray-300" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Agregar</button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Carrito</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Imagen</th>
                                <th class="border px-3 py-2 text-left">Código</th>
                                <th class="border px-3 py-2 text-left">Nombre</th>
                                <th class="border px-3 py-2 text-left">Cantidad</th>
                                <th class="border px-3 py-2 text-left">Precio</th>
                                <th class="border px-3 py-2 text-left">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cart as $item)
                            <tr>
                                <td class="border px-3 py-2">
                                    @php($photo = $item['photo_path'] ?? null)
                                    @if ($photo)
                                        <img src="{{ asset('storage/' . ltrim($photo, '/')) }}" alt="{{ $item['name'] }}"
                                            class="h-12 w-12 object-cover rounded border">
                                    @else
                                        <div
                                            class="h-12 w-12 rounded border bg-gray-100 flex items-center justify-center text-[10px] text-gray-500">
                                            SIN FOTO
                                        </div>
                                    @endif
                                </td>
                                <td class="border px-3 py-2">{{ $item['code'] }}</td>
                                <td class="border px-3 py-2">{{ $item['name'] }}</td>
                                <td class="border px-3 py-2">{{ $item['quantity'] }}</td>
                                <td class="border px-3 py-2">$ {{ number_format($item['unit_price'], 2) }}</td>
                                <td class="border px-3 py-2">$ {{ number_format($item['subtotal'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td class="border px-3 py-2" colspan="6">No hay productos en el carrito.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <p class="text-lg font-semibold">Total: $ {{ number_format($total, 2) }}</p>

                    <div class="flex gap-2">
                        <form action="{{ route('sales.scanner.clear') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600">Limpiar</button>
                        </form>

                        <form action="{{ route('sales.scanner.checkout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="rounded bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">Confirmar
                                venta</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('scan-code');
            if (input) {
                input.focus();
                setInterval(() => {
                    if (document.activeElement !== input) {
                        input.focus();
                    }
                }, 500);
            }
        });
    </script>
</x-app-layout>