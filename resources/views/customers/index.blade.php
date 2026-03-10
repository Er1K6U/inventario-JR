<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clientes Crédito') }}
        </h2>
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
                        <h3 class="text-lg font-semibold mb-3">Nuevo cliente</h3>
                        <form method="POST" action="{{ route('clientes_credito.clientes.store') }}" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                <x-text-input name="name" placeholder="Nombre del cliente" class="md:col-span-3"
                                    required />
                                <x-primary-button>Guardar</x-primary-button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-100 text-left">
                                <tr>
                                    <th class="px-4 py-3">Cliente</th>
                                    <th class="px-4 py-3">Proyectos</th>
                                    <th class="px-4 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">{{ $customer->name }}</td>
                                        <td class="px-4 py-3">{{ $customer->projects_count }}</td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('clientes_credito.proyectos.index', $customer) }}"
                                                class="text-blue-600 hover:underline">
                                                Ver proyectos
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">Sin clientes aún.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $customers->links() }}</div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>