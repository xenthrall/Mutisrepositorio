<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full items-start relative">

        {{-- Badge de estado / número de factura --}}
        <div class="lg:col-span-12 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 bg-primary-50 dark:bg-primary-500/10 text-primary-700 dark:text-primary-400 font-bold text-sm px-3 py-1.5 rounded-lg ring-1 ring-primary-200 dark:ring-primary-500/20">
                    <x-heroicon-m-receipt-percent class="w-4 h-4"/>
                    {{ $record->number }}
                </span>
                @if($record->status)
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1.5 rounded-lg
                        @if($record->status === \Modules\Financial\App\Enums\InvoiceStatusEnum::Paid) bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400
                        @elseif($record->status === \Modules\Financial\App\Enums\InvoiceStatusEnum::Cancelled) bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400
                        @elseif($record->status === \Modules\Financial\App\Enums\InvoiceStatusEnum::Quotation) bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400
                        @else bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400
                        @endif">
                        {{ $record->status->getLabel() }}
                    </span>
                @endif
            </div>
            @if($isLocked)
                <p class="text-sm text-gray-500 dark:text-gray-400">Solo lectura — factura procesada</p>
            @endif
        </div>
        
        {{-- ========================================================= --}}
        {{-- PANEL IZQUIERDO: EL TICKET DE VENTA (Columnas 1-5)        --}}
        {{-- ========================================================= --}}
        <div class="lg:col-span-5 flex flex-col gap-4 bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-5 sticky top-6">
            
            {{-- Header: Selección de Cliente Inteligente --}}
            <div class="flex flex-col gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Cliente a Facturar</label>

                @if($this->selectedPartner)
                    <div class="bg-primary-50/50 dark:bg-primary-900/10 p-4 rounded-xl border border-primary-100 dark:border-primary-800/30 relative transition-all">
                        @unless($isLocked)
                            <button type="button" wire:click="clearPartner" class="absolute top-3 right-3 text-gray-400 hover:text-danger-500 transition-colors" title="Cambiar cliente">
                                <x-heroicon-m-x-circle class="w-6 h-6"/>
                            </button>
                        @endunless
                        <h4 class="font-bold text-lg text-primary-700 dark:text-primary-400 mb-3 pr-8">
                            {{ $this->selectedPartner->full_name }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <x-heroicon-m-identification class="w-4 h-4 text-primary-500/70"/>
                                <span class="font-medium text-gray-900 dark:text-gray-200">
                                    {{ $this->selectedPartner->document_type instanceof \UnitEnum ? $this->selectedPartner->document_type->getLabel() : $this->selectedPartner->document_type }}
                                </span>
                                <span>{{ $this->selectedPartner->document_number }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-heroicon-m-phone class="w-4 h-4 text-primary-500/70"/>
                                <span>{{ $this->selectedPartner->phone ?: 'No registrado' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-heroicon-m-envelope class="w-4 h-4 text-primary-500/70"/>
                                <span class="truncate" title="{{ $this->selectedPartner->email }}">{{ $this->selectedPartner->email ?: 'No registrado' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-heroicon-m-map-pin class="w-4 h-4 text-primary-500/70"/>
                                <span class="truncate" title="{{ $this->selectedPartner->address }}">{{ $this->selectedPartner->address ?: 'No registrada' }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div x-data="{ open: false }" class="relative w-full">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <x-heroicon-m-magnifying-glass class="w-5 h-5 text-gray-400"/>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="searchPartner" @focus="open = true" @click.away="open = false" placeholder="Buscar por nombre o documento..." class="w-full pl-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-3 shadow-sm transition-colors">
                        </div>
                        @if(strlen($searchPartner) >= 2)
                            <div x-show="open" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden">
                                <ul class="max-h-64 overflow-y-auto custom-scrollbar">
                                    @forelse($this->filteredPartners as $partner)
                                        <li wire:click="selectPartner({{ $partner->id }}); open = false" class="p-3 hover:bg-primary-50 dark:hover:bg-primary-500/10 cursor-pointer border-b border-gray-100 dark:border-gray-700/50 transition-colors flex flex-col gap-1">
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $partner->full_name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                                <x-heroicon-m-identification class="w-3 h-3"/> {{ $partner->document_number }}
                                            </div>
                                        </li>
                                    @empty
                                        <li class="p-4 text-sm text-gray-500 dark:text-gray-400 text-center flex flex-col items-center gap-2">
                                            No se encontró ningún cliente.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Body: Líneas de la factura --}}
            <div class="flex-1 overflow-y-auto min-h-[300px] max-h-[50vh] pr-2 custom-scrollbar space-y-3">
                @forelse($lines as $index => $line)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700/50">
                        <div class="flex-1">
                            <p class="font-bold text-sm text-gray-900 dark:text-white truncate max-w-[200px]">{{ $line['name'] }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <input type="number" step="0.01" wire:model.live.debounce.300ms="lines.{{ $index }}.quantity" wire:change="updateLine({{ $index }})" @disabled($isLocked) class="w-20 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md py-1 px-2 focus:ring-primary-500 focus:border-primary-500 disabled:opacity-60">
                                <span class="text-xs text-gray-500">x</span>
                                <input type="number" step="0.01" wire:model.live.debounce.300ms="lines.{{ $index }}.unit_price" wire:change="updateLine({{ $index }})" @disabled($isLocked) class="w-24 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-md py-1 px-2 focus:ring-primary-500 focus:border-primary-500 disabled:opacity-60">
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="font-bold text-gray-900 dark:text-white">${{ number_format($line['subtotal'], 2) }}</span>
                            @unless($isLocked)
                                <button wire:click="removeLine({{ $index }})" class="text-danger-500 hover:text-danger-700 p-1 bg-danger-50 dark:bg-danger-500/10 rounded-md transition-colors">
                                    <x-heroicon-o-trash class="w-4 h-4"/>
                                </button>
                            @endunless
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 gap-2 py-10">
                        <x-heroicon-o-shopping-cart class="w-12 h-12 opacity-50"/>
                        <p class="text-sm">El ticket está vacío</p>
                    </div>
                @endforelse
            </div>

            {{-- Footer: Totales y Botón Pagar --}}
            <div class="border-t border-gray-100 dark:border-gray-800 pt-4 mt-2">
                <div class="flex justify-between items-end mb-4">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">TOTAL:</span>
                    <span class="text-3xl font-black text-primary-600 dark:text-primary-400">${{ number_format($totalAmount, 2) }}</span>
                </div>
                @if($isLocked)
                    <div class="bg-success-50 dark:bg-success-500/10 border border-success-200 dark:border-success-500/20 rounded-xl p-4 text-center">
                        <x-heroicon-m-check-circle class="w-8 h-8 text-success-600 mx-auto mb-2"/>
                        <p class="font-bold text-success-700 dark:text-success-400">{{ $record->status->getLabel() }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total cobrado: ${{ number_format((float) $record->total_amount, 2) }}</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        <button wire:click="saveInvoice" class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold py-3 px-4 rounded-xl transition-colors">
                            Guardar Borrador
                        </button>
                        <button wire:click="openPaymentModal" class="bg-success-600 hover:bg-success-500 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-lg shadow-success-500/30 flex justify-center items-center gap-2">
                            <x-heroicon-s-banknotes class="w-5 h-5"/> PAGAR
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- PANEL DERECHO: CATÁLOGO DE PRODUCTOS (Columnas 6-12)      --}}
        {{-- ========================================================= --}}
        <div class="lg:col-span-7 flex flex-col gap-4">
            
            <div class="bg-white dark:bg-gray-900 rounded-xl p-2 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 flex items-center gap-2">
                <x-heroicon-m-magnifying-glass class="w-5 h-5 text-gray-400 ml-2"/>
                <input type="text" wire:model.live.debounce.300ms="searchProduct" placeholder="Buscar producto o servicio..." class="w-full border-none bg-transparent focus:ring-0 text-gray-900 dark:text-white placeholder-gray-400">
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($this->products as $product)
                    <div
                        @unless($isLocked) wire:click="addProduct({{ $product->id }})" @endunless
                        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 transition-all group flex flex-col justify-between h-32 relative overflow-hidden {{ $isLocked ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:border-primary-500 hover:ring-1 hover:ring-primary-500' }}"
                    >
                        <x-heroicon-o-cube class="w-16 h-16 absolute -bottom-2 -right-2 text-gray-50 dark:text-gray-800/50 group-hover:text-primary-50 transition-colors pointer-events-none"/>
                        <div class="z-10">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $product->code ?? 'Sin código' }}</p>
                            <h4 class="font-bold text-gray-900 dark:text-white text-sm line-clamp-2 leading-tight">{{ $product->name }}</h4>
                        </div>
                        <div class="z-10 mt-2">
                            <span class="inline-block bg-primary-50 dark:bg-primary-500/10 text-primary-700 dark:text-primary-400 font-black text-sm px-2 py-1 rounded-md">
                                ${{ number_format($product->price, 0) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- VENTANA MODAL DE PAGO (Controlada por Livewire)           --}}
        {{-- ========================================================= --}}
        @if($isPaymentModalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/70 backdrop-blur-sm transition-opacity">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden ring-1 ring-gray-950/5 dark:ring-white/10" @click.away="$wire.closePaymentModal()">
                    
                    {{-- Modal Header --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Procesar Pago</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Factura {{ $record->number }}</p>
                        </div>
                        <button wire:click="closePaymentModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <x-heroicon-m-x-mark class="w-6 h-6"/>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-5">
                        
                        {{-- Monto a cobrar (Resaltado) --}}
                        <div class="bg-primary-50 dark:bg-primary-500/10 rounded-xl p-4 flex justify-between items-center border border-primary-100 dark:border-primary-500/20">
                            <span class="text-primary-700 dark:text-primary-400 font-semibold">Total a Cobrar</span>
                            <span class="text-3xl font-black text-primary-700 dark:text-primary-400">${{ number_format($totalAmount, 2) }}</span>
                        </div>

                        {{-- Formulario de Pago --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Método de Ingreso (Caja/Banco) <span class="text-danger-500">*</span></label>
                                <select wire:model="payment_journal_id" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 shadow-sm">
                                    <option value="">Seleccione a dónde ingresará el dinero...</option>
                                    @foreach($this->journals as $journal)
                                        <option value="{{ $journal->id }}">{{ $journal->name }} (Saldo: ${{ number_format((float) $journal->balance, 0) }}) — {{ $journal->type instanceof \UnitEnum ? $journal->type->getLabel() : $journal->type }}</option>
                                    @endforeach
                                </select>
                                @error('payment_journal_id') <span class="text-danger-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monto Recibido</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" wire:model="payment_amount" class="w-full pl-7 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 shadow-sm">
                                </div>
                                @error('payment_amount') <span class="text-danger-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                @if((float)$payment_amount > $totalAmount)
                                    <p class="text-success-600 text-xs mt-1">Cambio a devolver: <strong>${{ number_format((float)$payment_amount - $totalAmount, 2) }}</strong></p>
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Referencia / Memo</label>
                                <input type="text" wire:model="payment_memo" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 shadow-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 p-6 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
                        <button wire:click="closePaymentModal" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancelar
                        </button>
                        <button wire:click="confirmPayment" class="px-5 py-2.5 text-sm font-bold text-white bg-success-600 hover:bg-success-500 rounded-lg shadow-md transition-colors flex items-center gap-2">
                            <x-heroicon-s-check-circle class="w-5 h-5"/> Confirmar y Pagar
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>