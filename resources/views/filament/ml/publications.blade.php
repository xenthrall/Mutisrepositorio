<x-filament-panels::page>
    <div class="mb-4">
        <x-filament::button tag="a" href="javascript:history.back()" color="gray" icon="heroicon-m-arrow-left">
            Volver al Catálogo
        </x-filament::button>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
