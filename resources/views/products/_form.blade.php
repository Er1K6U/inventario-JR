@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Código *</label>
        <input type="text" name="code" value="{{ old('code', $product->code ?? '') }}"
            class="mt-1 w-full rounded border-gray-300" required>
        @error('code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Código de barras</label>
        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}"
            class="mt-1 w-full rounded border-gray-300">
        @error('barcode') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Nombre *</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
            class="mt-1 w-full rounded border-gray-300" required>
        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium">Descripción</label>
        <textarea name="description"
            class="mt-1 w-full rounded border-gray-300">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Precio *</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price ?? 0) }}"
            class="mt-1 w-full rounded border-gray-300" required>
        @error('price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Stock *</label>
        <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock ?? 0) }}"
            class="mt-1 w-full rounded border-gray-300" required>
        @error('stock') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Stock mínimo *</label>
        <input type="number" min="0" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 0) }}"
            class="mt-1 w-full rounded border-gray-300" required>
        @error('min_stock') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Activo *</label>
        <select name="active" class="mt-1 w-full rounded border-gray-300" required>
            <option value="1" @selected(old('active', $product->active ?? 1) == 1)>Sí</option>
            <option value="0" @selected(old('active', $product->active ?? 1) == 0)>No</option>
        </select>
        @error('active') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Categoría</label>
        <select name="category_id" class="mt-1 w-full rounded border-gray-300">
            <option value="">-- Seleccionar --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium">Proveedor</label>
        <select name="supplier_id" class="mt-1 w-full rounded border-gray-300">
            <option value="">-- Seleccionar --</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id ?? '') == $supplier->id)>
                    {{ $supplier->name }}
                </option>
            @endforeach
        </select>
        @error('supplier_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="md:col-span-2">
    <label class="block text-sm font-medium">Foto del producto</label>
    <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" class="mt-1 w-full rounded border-gray-300">
    @error('photo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

    @if(!empty($product?->photo_path))
        <div class="mt-2">
            <img src="{{ asset('storage/' . ltrim($product->photo_path, '/')) }}" alt="Foto actual"
                class="h-20 w-20 rounded border object-cover">
        </div>
    @endif
</div>

<div class="mt-6">
    <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Guardar</button>
    <a href="{{ route('products.index') }}"
        class="ml-2 rounded bg-gray-300 px-4 py-2 text-gray-800 hover:bg-gray-400">Cancelar</a>
</div>