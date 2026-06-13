<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-full items-start relative">

        {{-- ========================================================= --}}
        {{-- PANEL IZQUIERDO: TICKET DE EGRESO (Columnas 1-5)          --}}
        {{-- ========================================================= --}}
        <div class="lg:col-span-5 flex flex-col gap-4 bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-5 sticky top-6">

            {{-- Header: Proveedor / Beneficiario (opcional) --}}
            <div class="flex flex-col gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Proveedor / Beneficiario <span class="text-gray-400 font-normal">(opcional)</span></label>

                @if($this->selectedPartner)
                    <div class="bg-warning-50/50 dark:bg-warning-900/10 p-4 rounded-xl border border-warning-100 dark:border-warning-800/30 relative transition-all">
                        <button type="button" wire:click="clearPartner" class="absolute top-3 right-3 text-gray-400 hover:text-danger-500 transition-colors" title="Cambiar tercero">
                            <x-heroicon-m-x-circle class="w-6 h-6"/>
                        </button>
                        <h4 class="font-bold text-lg text-warning-700 dark:text-warning-400 mb-3 pr-8">
                            {{ $this->selectedPartner->full_name }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <x-heroicon-m-identification class="w-4 h-4 text-warning-500/70"/>
                                <span class="font-medium text-gray-900 dark:text-gray-200">
                                    {{ $this->selectedPartner->document_type instanceof \UnitEnum ? $this->selectedPartner->document_type->getLabel() : $this->selectedPartner->document_type }}
                                </span>
                                <span>{{ $this->selectedPartner->document_number }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-heroicon-m-phone class="w-4 h-4 text-warning-500/70"/>
                                <span>{{ $this->selectedPartner->phone ?: 'No registrado' }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div x-data="{ open: false }" class="relative w-full">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <x-heroicon-m-magnifying-glass class="w-5 h-5 text-gray-400"/>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="searchPartner" @focus="open = true" @click.away="open = false" placeholder="Buscar proveedor por nombre o documento..." class="w-full pl-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-warning-500 focus:border-warning-500 block p-3 shadow-sm transition-colors">
                        </div>
                        @if(strlen($searchPartner) >= 2)
                            <div x-show="open" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden">
                                <ul class="max-h-64 overflow-y-auto custom-scrollbar">
                                    @forelse($this->filteredPartners as $partner)
                                        <li wire:click="selectPartner({{ $partner->id }}); open = false" class="p-3 hover:bg-warning-50 dark:hover:bg-warning-500/10 cursor-pointer border-b border-gray-100 dark:border-gray-700/50 transition-colors flex flex-col gap-1">
                                            <div class="font-bold text-gray-900 dark:text-white">{{ $partner->full_name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                                <x-heroicon-m-identification class="w-3 h-3"/> {{ $partner->document_number }}
                                            </div>
                                        </li>
                                    @empty
                                        <li class="p-4 text-sm text-gray-500 dark:text-gray-400 text-center">No se encontró ningún tercero.</li>
                                    @endforelse
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Categoría seleccionada --}}
            <div class="flex flex-col gap-2">
                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Categoría de Gasto</label>
                @if($this->selectedCategory)
                    <div class="bg-warning-50 dark:bg-warning-500/10 border border-warning-200 dark:border-warning-500/20 rounded-xl p-3 flex items-center gap-3">
                        <x-heroicon-m-tag class="w-5 h-5 text-warning-600 dark:text-warning-400"/>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $this->selectedCategory->name }}</p>
                            @if($this->selectedCategory->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $this->selectedCategory->description }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Seleccione una categoría del catálogo →</p>
                @endif
            </div>

            {{-- Detalle del egreso --}}
            <div class="flex flex-col gap-4 flex-1">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Concepto <span class="text-danger-500">*</span></label>
                    <input type="text" wire:model="description" placeholder="Ej: Compra de papelería, pago de servicios..." class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-warning-500 focus:border-warning-500 block p-3 shadow-sm">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Monto <span class="text-danger-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" wire:model="amount" class="w-full pl-7 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-warning-500 focus:border-warning-500 block p-3 shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Fecha <span class="text-danger-500">*</span></label>
                        <input type="date" wire:model="expense_date" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-warning-500 focus:border-warning-500 block p-3 shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Footer: Totales y acciones --}}
            <div class="border-t border-gray-100 dark:border-gray-800 pt-4 mt-2">
                <div class="flex justify-between items-end mb-4">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">TOTAL EGRESO:</span>
                    <span class="text-3xl font-black text-danger-600 dark:text-danger-400">${{ number_format((float) $amount, 2) }}</span>
                </div>

                @if($isDraft)
                    <div class="grid grid-cols-2 gap-3">
                        <button wire:click="saveExpense" class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold py-3 px-4 rounded-xl transition-colors">
                            Guardar Borrador
                        </button>
                        <button wire:click="openPaymentModal" class="bg-danger-600 hover:bg-danger-500 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-lg shadow-danger-500/30 flex justify-center items-center gap-2">
                            <x-heroicon-s-banknotes class="w-5 h-5"/> CONFIRMAR
                        </button>
                    </div>
                @else
                    <div class="bg-success-50 dark:bg-success-500/10 border border-success-200 dark:border-success-500/20 rounded-xl p-4 text-center">
                        <x-heroicon-m-check-circle class="w-8 h-8 text-success-600 mx-auto mb-2"/>
                        <p class="font-bold text-success-700 dark:text-success-400">Egreso confirmado</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">EG-{{ str_pad($record->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- PANEL DERECHO: CATÁLOGO DE CATEGORÍAS (Columnas 6-12)       --}}
        {{-- ========================================================= --}}
        <div class="lg:col-span-7 flex flex-col gap-4">

            <div class="bg-white dark:bg-gray-900 rounded-xl p-2 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 flex items-center gap-2">
                <x-heroicon-m-magnifying-glass class="w-5 h-5 text-gray-400 ml-2"/>
                <input type="text" wire:model.live.debounce.300ms="searchCategory" placeholder="Buscar categoría de gasto..." class="w-full border-none bg-transparent focus:ring-0 text-gray-900 dark:text-white placeholder-gray-400">
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($this->categories as $category)
                    <div wire:click="selectCategory({{ $category->id }})" class="bg-white dark:bg-gray-900 border rounded-xl p-4 cursor-pointer transition-all group flex flex-col justify-between h-32 relative overflow-hidden {{ $expense_category_id == $category->id ? 'border-warning-500 ring-2 ring-warning-500' : 'border-gray-200 dark:border-gray-800 hover:border-warning-500 hover:ring-1 hover:ring-warning-500' }}">
                        <x-heroicon-o-tag class="w-16 h-16 absolute -bottom-2 -right-2 text-gray-50 dark:text-gray-800/50 group-hover:text-warning-50 transition-colors pointer-events-none"/>
                        <div class="z-10">
                            <h4 class="font-bold text-gray-900 dark:text-white text-sm line-clamp-2 leading-tight">{{ $category->name }}</h4>
                        </div>
                        <div class="z-10 mt-2">
                            @if($category->description)
                                <span class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">{{ $category->description }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL DE CONFIRMACIÓN DE EGRESO                           --}}
        {{-- ========================================================= --}}
        @if($isPaymentModalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/70 backdrop-blur-sm transition-opacity">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden ring-1 ring-gray-950/5 dark:ring-white/10" @click.away="$wire.closePaymentModal()">

                    <div class="bg-gray-50 dark:bg-gray-800/50 p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Confirmar Egreso</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">EG-{{ str_pad($record->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <button wire:click="closePaymentModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            <x-heroicon-m-x-mark class="w-6 h-6"/>
                        </button>
                    </div>

                    <div class="p-6 space-y-5">

                        <div class="bg-danger-50 dark:bg-danger-500/10 rounded-xl p-4 flex justify-between items-center border border-danger-100 dark:border-danger-500/20">
                            <span class="text-danger-700 dark:text-danger-400 font-semibold">Total a Desembolsar</span>
                            <span class="text-3xl font-black text-danger-700 dark:text-danger-400">${{ number_format((float) $amount, 2) }}</span>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Origen de Fondos (Caja/Banco) <span class="text-danger-500">*</span></label>
                                <select wire:model="payment_journal_id" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-warning-500 focus:border-warning-500 block p-2.5 shadow-sm">
                                    <option value="">Seleccione de dónde saldrá el dinero...</option>
                                    @foreach($this->journals as $journal)
                                        <option value="{{ $journal->id }}">{{ $journal->name }} (Saldo: ${{ number_format((float) $journal->balance, 0) }}) — {{ $journal->type instanceof \UnitEnum ? $journal->type->getLabel() : $journal->type }}</option>
                                    @endforeach
                                </select>
                                @error('payment_journal_id') <span class="text-danger-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Monto a Desembolsar</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" wire:model="payment_amount" class="w-full pl-7 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-warning-500 focus:border-warning-500 block p-2.5 shadow-sm">
                                </div>
                                @error('payment_amount') <span class="text-danger-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Referencia / Memo</label>
                                <input type="text" wire:model="payment_memo" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-warning-500 focus:border-warning-500 block p-2.5 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/50 p-6 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
                        <button wire:click="closePaymentModal" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancelar
                        </button>
                        <button wire:click="confirmPayment" class="px-5 py-2.5 text-sm font-bold text-white bg-danger-600 hover:bg-danger-500 rounded-lg shadow-md transition-colors flex items-center gap-2">
                            <x-heroicon-s-check-circle class="w-5 h-5"/> Confirmar Egreso
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
