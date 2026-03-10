<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proyectos de {{ $customer->name }}
            </h2>
            <a href="{{ route('clientes_credito.clientes.index') }}" class="text-sm text-blue-600 hover:underline">
                ← Volver a clientes
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3">Nuevo proyecto</h3>
                        <form method="POST" action="{{ route('clientes_credito.proyectos.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                <x-text-input name="name" placeholder="Nombre del proyecto" required />
                                <x-text-input name="project_date" type="date" required />
                                <x-text-input name="note" placeholder="Nota (opcional)" />
                                <x-primary-button>Crear</x-primary-button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-auto mb-4">
                        <table class="min-w-full">
                            <thead class="bg-gray-100 text-left">
                                <tr>
                                    <th class="px-4 py-3">Proyecto</th>
                                    <th class="px-4 py-3">Fecha</th>
                                    <th class="px-4 py-3">Estado</th>
                                    <th class="px-4 py-3">Total</th>
                                    <th class="px-4 py-3">Pagado</th>
                                    <th class="px-4 py-3">Saldo</th>
                                    <th class="px-4 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projects as $project)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">{{ $project->name }}</td>
                                        <td class="px-4 py-3">{{ optional($project->project_date)->format('Y-m-d') }}</td>
                                        <td class="px-4 py-3">{{ $project->status }}</td>
                                        <td class="px-4 py-3">${{ number_format($project->total, 2) }}</td>
                                        <td class="px-4 py-3">${{ number_format($project->paid, 2) }}</td>
                                        <td class="px-4 py-3 font-semibold">${{ number_format($project->balance, 2) }}</td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('clientes_credito.proyectos.show', $project) }}"
                                                class="text-blue-600 hover:underline">Abrir</a>
                                            @if($project->status === 'abierto')
                                                <form method="POST"
                                                    action="{{ route('clientes_credito.proyectos.close', $project) }}"
                                                    class="inline ml-2">
                                                    @csrf
                                                    <button type="submit" class="text-orange-600 hover:underline">
                                                        Cerrar
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">Sin proyectos aún.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $projects->links() }}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>