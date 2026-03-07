<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Productos</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="jr-card">
                @if (session('success'))
                    <div class="mb-4 rounded border border-green-300 bg-green-100 p-3 text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div class="flex gap-2">
                        <a href="{{ route('products.create') }}"
                            class="jr-btn-primary">
                            Nuevo producto
                        </a>
                        <a href="{{ route('products.import.create') }}"
                            class="rounded bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">
                            Importar Excel
                        </a>
                    </div>

                    <form method="GET" action="{{ route('products.index') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            placeholder="Buscar por nombre o código" class="w-72 rounded border-gray-300">
                        <button type="submit"
                            class="rounded bg-gray-700 px-4 py-2 text-white hover:bg-gray-800">Buscar</button>
                        <a href="{{ route('products.index') }}"
                            class="rounded bg-gray-300 px-4 py-2 text-gray-800 hover:bg-gray-400">Limpiar</a>
                    </form>
                </div>

                <div class="overflow-x-auto">
                        <table class="jr-table min-w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Código</th>
                                <th class="border px-3 py-2 text-left">Nombre</th>
                                <th class="border px-3 py-2 text-left">Precio</th>
                                <th class="border px-3 py-2 text-left">Stock</th>
                                <th class="border px-3 py-2 text-left">Activo</th>
                                <th class="border px-3 py-2 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td class="border px-3 py-2">{{ $product->code }}</td>
                                    <td class="border px-3 py-2">{{ $product->name }}</td>
                                    <td class="border px-3 py-2">$ {{ number_format($product->price, 2) }}</td>
                                    <td class="border px-3 py-2">{{ $product->stock }}</td>
                                    <td class="border px-3 py-2">{{ $product->active ? 'Sí' : 'No' }}</td>
                                    <td class="border px-3 py-2">
                                        <a class="text-blue-600 hover:underline"
                                            href="{{ route('products.edit', $product) }}">Editar</a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('¿Eliminar este producto?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="ml-2 text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="border px-3 py-2" colspan="6">No hay productos aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>