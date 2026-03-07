<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Importar productos desde Excel</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="mb-4 text-sm text-gray-700">
                    Encabezados esperados: <strong>codigo, nombre, cantidad, precio</strong>.
                    Opcionales: <strong>categoria, proveedor, codigo_barras, descripcion, stock_minimo</strong>.
                </p>

                <form action="{{ route('products.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Archivo Excel</label>
                        <input type="file" name="file" class="mt-1 block w-full rounded border-gray-300" required>
                        @error('file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <button type="submit"
                            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Importar</button>
                        <a href="{{ route('products.index') }}"
                            class="ml-2 rounded bg-gray-300 px-4 py-2 text-gray-800 hover:bg-gray-400">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>