<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Esta acción requiere credenciales de un usuario con rol <strong>Administrador</strong>.
    </div>

    @if (session('warning'))
        <div class="mb-4 rounded border border-yellow-300 bg-yellow-100 p-3 text-yellow-800">
            {{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin-approval.verify') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email administrador')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña administrador')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Confirmar autorización
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>