<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usuarios</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 rounded border border-green-300 bg-green-100 p-3 text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-4">
                    <a href="{{ route('users.create') }}"
                        class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Nuevo usuario</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Nombre</th>
                                <th class="border px-3 py-2 text-left">Email</th>
                                <th class="border px-3 py-2 text-left">Rol</th>
                                <th class="border px-3 py-2 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="border px-3 py-2">{{ $user->name }}</td>
                                    <td class="border px-3 py-2">{{ $user->email }}</td>
                                    <td class="border px-3 py-2">{{ $user->getRoleNames()->first() ?? 'Sin rol' }}</td>
                                    <td class="border px-3 py-2">
                                        <a class="text-blue-600 hover:underline"
                                            href="{{ route('users.edit', $user) }}">Editar</a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('¿Eliminar este usuario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="ml-2 text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="border px-3 py-2" colspan="4">No hay usuarios.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>