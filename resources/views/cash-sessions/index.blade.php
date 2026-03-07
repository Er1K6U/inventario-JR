<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Apertura y cierre de día</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="rounded border border-green-300 bg-green-100 p-3 text-green-800">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="rounded border border-red-300 bg-red-100 p-3 text-red-800">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Estado actual</h3>

                @if ($activeSession)
                    <p><strong>Estado:</strong> Abierto</p>
                    <p><strong>Apertura:</strong> {{ $activeSession->opened_at?->format('Y-m-d H:i:s') }}</p>
                    <p><strong>Abierto por:</strong> {{ $activeSession->opener->name ?? 'N/A' }}</p>

                    <form action="{{ route('cash-sessions.close') }}" method="POST" class="mt-4">
                        @csrf
                        <label class="block text-sm font-medium">Nota de cierre</label>
                        <textarea name="closing_note" class="mt-1 w-full rounded border-gray-300"></textarea>
                        <button type="submit" class="mt-3 rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">Cerrar
                            día</button>
                    </form>
                @else
                    <p><strong>Estado:</strong> No hay día abierto.</p>

                    <form action="{{ route('cash-sessions.open') }}" method="POST" class="mt-4">
                        @csrf
                        <label class="block text-sm font-medium">Nota de apertura</label>
                        <textarea name="opening_note" class="mt-1 w-full rounded border-gray-300"></textarea>
                        <button type="submit"
                            class="mt-3 rounded bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">Abrir día</button>
                    </form>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-3">Últimos días</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Estado</th>
                                <th class="border px-3 py-2 text-left">Apertura</th>
                                <th class="border px-3 py-2 text-left">Cierre</th>
                                <th class="border px-3 py-2 text-left">Usuario apertura</th>
                                <th class="border px-3 py-2 text-left">Usuario cierre</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lastSessions as $session)
                                <tr>
                                    <td class="border px-3 py-2">{{ $session->status }}</td>
                                    <td class="border px-3 py-2">{{ $session->opened_at?->format('Y-m-d H:i:s') }}</td>
                                    <td class="border px-3 py-2">{{ $session->closed_at?->format('Y-m-d H:i:s') ?? '-' }}
                                    </td>
                                    <td class="border px-3 py-2">{{ $session->opener->name ?? 'N/A' }}</td>
                                    <td class="border px-3 py-2">{{ $session->closer->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="border px-3 py-2" colspan="5">No hay registros aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>