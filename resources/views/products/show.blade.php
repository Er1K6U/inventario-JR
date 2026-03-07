<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle de producto</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p><strong>Código:</strong> {{ $product->code }}</p>
                <p><strong>Nombre:</strong> {{ $product->name }}</p>
                <p><strong>Precio:</strong> $ {{ number_format($product->price, 2) }}</p>
                <p><strong>Stock:</strong> {{ $product->stock }}</p>
                <p><strong>Stock mínimo:</strong> {{ $product->min_stock }}</p>

                <div class="mt-4">
                    <a href="{{ route('products.index') }}"
                        class="rounded bg-gray-300 px-4 py-2 text-gray-800 hover:bg-gray-400">Volver</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>