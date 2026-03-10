<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reporte de ingresos de mercancía') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('stock-entries.report') }}"
                        class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <x-input-label for="from" :value="__('Desde')" />
                            <x-text-input id="from" name="from" type="date" class="mt-1 block w-full"
                                :value="$filters['from']" />
                        </div>

                        <div>
                            <x-input-label for="to" :value="__('Hasta')" />
                            <x-text-input id="to" name="to" type="date" class="mt-1 block w-full"
                                :value="$filters['to']" />
                        </div>

                        <div>
                            <x-input-label for="product_id" :value="__('Producto')" />
                            <select id="product_id" name="product_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Todos --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @selected((string) $filters['product_id'] === (string) $product->id)>
                                        {{ $product->code }} - {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="user_id" :value="__('Usuario')" />
                            <select id="user_id" name="user_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Todos --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected((string) $filters['user_id'] === (string) $user->id)>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="role" :value="__('Rol')" />
                            <x-text-input id="role" name="role" type="text" class="mt-1 block w-full"
                                :value="$filters['role']" placeholder="Administrador / Vendedor" />
                        </div>

                        <div class="md:col-span-5 flex items-center gap-3">
                            <x-primary-button>{{ __('Filtrar') }}</x-primary-button>

                            <a href="{{ route('stock-entries.export', ['from' => $filters['from'], 'to' => $filters['to'], 'product_id' => $filters['product_id'], 'user_id' => $filters['user_id'], 'role' => $filters['role']]) }}"
                                class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500">
                                {{ __('Exportar Excel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Fecha/Hora</th>
                                <th class="px-3 py-2 text-left">Código</th>
                                <th class="px-3 py-2 text-left">Producto</th>
                                <th class="px-3 py-2 text-right">Cantidad</th>
                                <th class="px-3 py-2 text-right">Stock antes</th>
                                <th class="px-3 py-2 text-right">Stock después</th>
                                <th class="px-3 py-2 text-left">Motivo</th>
                                <th class="px-3 py-2 text-left">Usuario</th>
                                <th class="px-3 py-2 text-left">Rol</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($entries as $entry)
                                <tr>
                                    <td class="px-3 py-2">{{ optional($entry->entered_at)->format('Y-m-d H:i:s') }}</td>
                                    <td class="px-3 py-2">{{ $entry->product?->code }}</td>
                                    <td class="px-3 py-2">{{ $entry->product?->name }}</td>
                                    <td class="px-3 py-2 text-right">{{ $entry->quantity }}</td>
                                    <td class="px-3 py-2 text-right">{{ $entry->stock_before }}</td>
                                    <td class="px-3 py-2 text-right">{{ $entry->stock_after }}</td>
                                    <td class="px-3 py-2">{{ $entry->reason }}</td>
                                    <td class="px-3 py-2">{{ $entry->user?->email }}</td>
                                    <td class="px-3 py-2">{{ $entry->user?->roles?->pluck('name')->join(', ') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-3 py-4 text-center text-gray-500">No hay registros.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $entries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>