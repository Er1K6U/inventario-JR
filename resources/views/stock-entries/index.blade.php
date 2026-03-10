<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ingreso mercancía (Escáner)') }}
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

                    <form method="POST" action="{{ route('stock-entries.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="code" :value="__('Código (escáner)')" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
                                :value="old('code')" required autofocus autocomplete="off" />
                        </div>

                        <div>
                            <x-input-label for="quantity" :value="__('Cantidad ingresada')" />
                            <x-text-input id="quantity" name="quantity" type="number" min="1" step="1"
                                class="mt-1 block w-full" :value="old('quantity', 1)" required />
                        </div>

                        <div>
                            <x-input-label for="reason" :value="__('Motivo')" />
                            <x-text-input id="reason" name="reason" type="text" class="mt-1 block w-full"
                                :value="old('reason')" required />
                        </div>

                        <div class="flex items-center gap-3">
                            <x-primary-button>
                                {{ __('Registrar ingreso') }}
                            </x-primary-button>

                            <a href="{{ route('stock-entries.report') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Ver reporte') }}
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>