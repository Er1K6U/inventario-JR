@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Nombre *</label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
            class="mt-1 w-full rounded border-gray-300" required>
        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Email *</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
            class="mt-1 w-full rounded border-gray-300" required>
        @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Rol *</label>
        <select name="role" class="mt-1 w-full rounded border-gray-300" required>
            @php($selectedRole = old('role', isset($user) ? ($user->getRoleNames()->first() ?? 'Usuario') : 'Usuario'))
            <option value="Administrador" @selected($selectedRole === 'Administrador')>Administrador</option>
            <option value="Usuario" @selected($selectedRole === 'Usuario')>Usuario</option>
            <option value="Vendedor" @selected($selectedRole === 'Vendedor')>Vendedor</option>
        </select>
        @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Contraseña {{ isset($user) ? '(opcional)' : '*' }}</label>
        <input type="password" name="password" class="mt-1 w-full rounded border-gray-300" {{ isset($user) ? '' : 'required' }}>
        @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Confirmar contraseña {{ isset($user) ? '(opcional)' : '*' }}</label>
        <input type="password" name="password_confirmation" class="mt-1 w-full rounded border-gray-300" {{ isset($user) ? '' : 'required' }}>
    </div>
</div>

<div class="mt-6">
    <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Guardar</button>
    <a href="{{ route('users.index') }}"
        class="ml-2 rounded bg-gray-300 px-4 py-2 text-gray-800 hover:bg-gray-400">Cancelar</a>
</div>