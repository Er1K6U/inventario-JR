<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle de usuario</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p><strong>Nombre:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Rol:</strong> {{ $user->getRoleNames()->first() ?? 'Sin rol' }}</p>
                <div class="mt-4">
                    <a href="{{ route('users.index') }}"
                        class="rounded bg-gray-300 px-4 py-2 text-gray-800 hover:bg-gray-400">Volver</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>