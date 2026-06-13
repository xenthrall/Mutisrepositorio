<x-filament-panels::page>
    <div class="flex flex-col gap-6" x-data="{ activeTab: @entangle('activeTab') }">

        {{-- ========================================================= --}}
        {{-- FILTROS ELEGANTES + RANGOS RÁPIDOS                        --}}
        {{-- ========================================================= --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-5">
                <div class="flex items-center gap-2">
                    <x-heroicon-m-funnel class="w-5 h-5 text-primary-500"/>
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Filtros de Análisis</h3>
                </div>
                
                {{-- Rangos rápidos --}}
                <div class="flex flex-wrap gap-2">
                    @foreach(['today' => 'Hoy', 'week' => 'Semana', 'month' => 'Mes', 'quarter' => 'Trimestre', 'year' => 'Año'] as $key => $label)
                        <button 
                            wire:click="setRange('{{ $key }}')"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg transition-all
                            {{ $startDate === now()->{'startOf' . ucfirst($key === 'today' ? 'Day' : ($key === 'week' ? 'Week' : ($key === 'month' ? 'Month' : ($key === 'quarter' ? 'Quarter' : 'Year'))))}()->format('Y-m-d') ? 'bg-primary-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                    <button wire:click="resetFilters" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-danger-600 transition-all flex items-center gap-1">
                        <x-heroicon-m-arrow-path class="w-3 h-3"/>
                        Limpiar
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Fecha Inicio</label>
                    <input type="date" wire:model.live="startDate" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-primary-500 focus:border-primary-500 p-2.5 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Fecha Fin</label>
                    <input type="date" wire:model.live="endDate" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-primary-500 focus:border-primary-500 p-2.5 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Diario (Caja/Banco)</label>
                    <select wire:model.live="filterJournalId" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-primary-500 focus:border-primary-500 p-2.5 text-sm transition-all">
                        <option value="">Todas las cuentas...</option>
                        @foreach(\Modules\Financial\App\Models\Journal::where('is_active', true)->get() as $journal)
                            <option value="{{ $journal->id }}">{{ $journal->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wider">Cliente / Proveedor</label>
                    <select wire:model.live="filterPartnerId" class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-xl focus:ring-primary-500 focus:border-primary-500 p-2.5 text-sm transition-all">
                        <option value="">Todos los terceros...</option>
                        @foreach(\Modules\Core\App\Models\Partner::orderBy('first_name')->get() as $partner)
                            <option value="{{ $partner->id }}">{{ $partner->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- PESTAÑAS (TABS)                                           --}}
        {{-- ========================================================= --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="border-b border-gray-100 dark:border-gray-800">
                <nav class="flex space-x-1 p-2" aria-label="Tabs">
                    @foreach([
                        'overview' => ['label' => 'Resumen General', 'icon' => 'heroicon-m-chart-pie'],
                        'invoices' => ['label' => 'Facturas', 'icon' => 'heroicon-m-document-text'],
                        'transactions' => ['label' => 'Transacciones', 'icon' => 'heroicon-m-arrows-right-left'],
                        'analytics' => ['label' => 'Análisis Avanzado', 'icon' => 'heroicon-m-chart-bar'],
                    ] as $key => $tab)
                        <button 
                            @click="activeTab = '{{ $key }}'"
                            wire:click="$set('activeTab', '{{ $key }}')"
                            class="group flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 relative"
                            :class="activeTab === '{{ $key }}' 
                                ? 'bg-primary-50 dark:bg-primary-500/10 text-primary-700 dark:text-primary-400' 
                                : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'">
                            @svg($tab['icon'], 'w-4 h-4')
                            {{ $tab['label'] }}
                            @if($key === 'invoices' && $this->overdueCount > 0)
                                <span class="bg-danger-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $this->overdueCount }}</span>
                            @endif
                            <span 
                                x-show="activeTab === '{{ $key }}'"
                                x-transition
                                class="absolute bottom-0 left-2 right-2 h-0.5 bg-primary-500 rounded-full">
                            </span>
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="p-6">

                {{-- ===================== TAB: OVERVIEW ===================== --}}
                <div x-show="activeTab === 'overview'" x-transition.opacity.duration.200ms>
                    
                    {{-- KPI Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        {{-- Ingresos --}}
                        <div class="bg-gradient-to-br from-success-50 to-white dark:from-success-500/5 dark:to-gray-900/50 rounded-2xl p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 relative overflow-hidden group hover:shadow-md transition-all">
                            <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-all">
                                <x-heroicon-s-arrow-down-left class="w-20 h-20 text-success-500"/>
                            </div>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="bg-success-100 dark:bg-success-500/20 p-2 rounded-lg">
                                    <x-heroicon-o-banknotes class="w-5 h-5 text-success-600 dark:text-success-400"/>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300">Ingresos (Cobros)</h3>
                            </div>
                            <p class="text-3xl font-black text-gray-900 dark:text-white mt-2">${{ number_format($this->totalCollected, 2) }}</p>
                            <p class="text-xs text-success-600 dark:text-success-400 mt-1 font-medium">Tasa de cobro: {{ $this->collectionRate }}%</p>
                        </div>

                        {{-- Egresos --}}
                        <div class="bg-gradient-to-br from-danger-50 to-white dark:from-danger-500/5 dark:to-gray-900/50 rounded-2xl p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 relative overflow-hidden group hover:shadow-md transition-all">
                            <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-all">
                                <x-heroicon-s-arrow-up-right class="w-20 h-20 text-danger-500"/>
                            </div>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="bg-danger-100 dark:bg-danger-500/20 p-2 rounded-lg">
                                    <x-heroicon-o-credit-card class="w-5 h-5 text-danger-600 dark:text-danger-400"/>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300">Egresos (Gastos)</h3>
                            </div>
                            <p class="text-3xl font-black text-gray-900 dark:text-white mt-2">${{ number_format($this->totalExpenses, 2) }}</p>
                            <p class="text-xs text-danger-600 dark:text-danger-400 mt-1 font-medium">
                                {{ $this->totalCollected > 0 ? round(($this->totalExpenses / $this->totalCollected) * 100, 1) : 0 }}% de los ingresos
                            </p>
                        </div>

                        {{-- Flujo Neto --}}
                        <div class="bg-gradient-to-br {{ $this->netCashFlow >= 0 ? 'from-primary-50 to-white dark:from-primary-500/5' : 'from-danger-50 to-white dark:from-danger-500/5' }} dark:to-gray-900/50 rounded-2xl p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 relative overflow-hidden group hover:shadow-md transition-all">
                            <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-all">
                                <x-heroicon-s-scale class="w-20 h-20 {{ $this->netCashFlow >= 0 ? 'text-primary-500' : 'text-danger-500' }}"/>
                            </div>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="{{ $this->netCashFlow >= 0 ? 'bg-primary-100 dark:bg-primary-500/20' : 'bg-danger-100 dark:bg-danger-500/20' }} p-2 rounded-lg">
                                    <x-heroicon-o-chart-bar class="w-5 h-5 {{ $this->netCashFlow >= 0 ? 'text-primary-600 dark:text-primary-400' : 'text-danger-600 dark:text-danger-400' }}"/>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300">Flujo de Caja Neto</h3>
                            </div>
                            <p class="text-3xl font-black {{ $this->netCashFlow >= 0 ? 'text-primary-600 dark:text-primary-400' : 'text-danger-600 dark:text-danger-400' }} mt-2">
                                ${{ number_format($this->netCashFlow, 2) }}
                            </p>
                            <p class="text-xs {{ $this->netCashFlow >= 0 ? 'text-primary-600 dark:text-primary-400' : 'text-danger-600 dark:text-danger-400' }} mt-1 font-medium">
                                {{ $this->netCashFlow >= 0 ? 'Superávit' : 'Déficit' }} en el período
                            </p>
                        </div>

                        {{-- Cuentas por Cobrar --}}
                        <div class="bg-gradient-to-br from-warning-50 to-white dark:from-warning-500/5 dark:to-gray-900/50 rounded-2xl p-6 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 relative overflow-hidden group hover:shadow-md transition-all">
                            <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-all">
                                <x-heroicon-s-clock class="w-20 h-20 text-warning-500"/>
                            </div>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="bg-warning-100 dark:bg-warning-500/20 p-2 rounded-lg">
                                    <x-heroicon-o-document-text class="w-5 h-5 text-warning-600 dark:text-warning-400"/>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300">Cuentas por Cobrar</h3>
                            </div>
                            <p class="text-3xl font-black text-gray-900 dark:text-white mt-2">${{ number_format($this->totalPending, 2) }}</p>
                            @if($this->overdueInvoices > 0)
                                <p class="text-xs text-danger-600 dark:text-danger-400 mt-1 font-medium flex items-center gap-1">
                                    <x-heroicon-m-exclamation-triangle class="w-3 h-3"/>
                                    ${{ number_format($this->overdueInvoices, 2) }} vencidas
                                </p>
                            @endif
                        </div>
                    </div>

                    
                </div>

                {{-- ===================== TAB: INVOICES ===================== --}}
                <div x-show="activeTab === 'invoices'" x-transition.opacity.duration.200ms class="hidden" :class="{'hidden': activeTab !== 'invoices'}">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Nº Factura</th>
                                    <th class="px-6 py-4 font-semibold">Cliente</th>
                                    <th class="px-6 py-4 font-semibold">Fecha</th>
                                    <th class="px-6 py-4 font-semibold">Vencimiento</th>
                                    <th class="px-6 py-4 font-semibold text-right">Total</th>
                                    <th class="px-6 py-4 font-semibold text-right">Pagado</th>
                                    <th class="px-6 py-4 font-semibold text-right">Saldo</th>
                                    <th class="px-6 py-4 font-semibold text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse($this->invoices as $invoice)
                                    @php
                                        $isOverdue = $invoice->status !== 'cancelled' && $invoice->amount_due > 0 && \Carbon\Carbon::parse($invoice->due_date)->isPast();
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors {{ $isOverdue ? 'bg-danger-50/30 dark:bg-danger-500/5' : '' }}">
                                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $invoice->number }}</td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $invoice->partner->full_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 {{ $isOverdue ? 'text-danger-600 dark:text-danger-400 font-semibold' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}
                                            @if($isOverdue)
                                                <span class="text-[10px] bg-danger-100 dark:bg-danger-500/20 text-danger-700 dark:text-danger-400 px-1.5 py-0.5 rounded ml-1">Vencida</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-white">${{ number_format($invoice->total_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-right text-success-600 dark:text-success-400">${{ number_format($invoice->total_amount - $invoice->amount_due, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-bold {{ $invoice->amount_due > 0 ? 'text-warning-600 dark:text-warning-400' : 'text-gray-400' }}">
                                            ${{ number_format($invoice->amount_due, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($invoice->status->value === 'paid')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-success-500"></div> Pagada
                                                </span>
                                            @elseif($invoice->status->value === 'pending')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-warning-100 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-warning-500"></div> Pendiente
                                                </span>
                                            @elseif($invoice->status->value === 'cancelled')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-gray-400"></div> Cancelada
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                                                    {{ $invoice->status->value }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                            <x-heroicon-o-document-magnifying-glass class="w-12 h-12 mx-auto mb-3 opacity-50"/>
                                            No hay facturas en el período seleccionado
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $this->invoices->links() }}
                    </div>
                </div>

                {{-- ===================== TAB: TRANSACTIONS ===================== --}}
                <div x-show="activeTab === 'transactions'" x-transition.opacity.duration.200ms class="hidden" :class="{'hidden': activeTab !== 'transactions'}">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Tipo</th>
                                    <th class="px-6 py-4 font-semibold">Fecha</th>
                                    <th class="px-6 py-4 font-semibold">Referencia</th>
                                    <th class="px-6 py-4 font-semibold">Tercero</th>
                                    <th class="px-6 py-4 font-semibold">Caja/Banco</th>
                                    <th class="px-6 py-4 font-semibold text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse($this->transactions as $tx)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                                        <td class="px-6 py-4">
                                            @if($tx->type === 'in')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400 ring-1 ring-success-600/20">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-success-500"></div> Ingreso
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400 ring-1 ring-danger-600/20">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-danger-500"></div> Egreso
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($tx->date)->format('d M, Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300 max-w-xs truncate" title="{{ $tx->reference }}">
                                            {{ $tx->reference }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 truncate max-w-[150px]">
                                            {{ $tx->partner }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-md">
                                                {{ $tx->journal }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold {{ $tx->type === 'in' ? 'text-success-600 dark:text-success-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $tx->type === 'in' ? '+' : '-' }} ${{ number_format($tx->amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                            <x-heroicon-o-document-magnifying-glass class="w-12 h-12 mx-auto mb-3 opacity-50"/>
                                            No hay transacciones registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $this->transactions->links() }}
                    </div>
                </div>

                {{-- ===================== TAB: ANALYTICS ===================== --}}
                <div x-show="activeTab === 'analytics'" x-transition.opacity.duration.200ms class="hidden" :class="{'hidden': activeTab !== 'analytics'}">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Top Clientes --}}
                        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <x-heroicon-m-trophy class="w-5 h-5 text-warning-500"/>
                                Top 5 Clientes por Ingresos
                            </h3>
                            <div class="h-64 w-full" wire:ignore>
                                <canvas id="topPartnersChart"></canvas>
                            </div>
                        </div>

                        {{-- Estado de Facturas --}}
                        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-5">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <x-heroicon-m-clipboard-document-list class="w-5 h-5 text-primary-500"/>
                                Estado de Facturas
                            </h3>
                            <div class="space-y-4">
                                @php $statusData = $this->invoiceStatusData; @endphp
                                @foreach([
                                    ['key' => 'paid', 'label' => 'Pagadas', 'color' => 'bg-success-500', 'text' => 'text-success-600'],
                                    ['key' => 'pending', 'label' => 'Pendientes', 'color' => 'bg-warning-500', 'text' => 'text-warning-600'],
                                    ['key' => 'overdue', 'label' => 'Vencidas', 'color' => 'bg-danger-500', 'text' => 'text-danger-600'],
                                    ['key' => 'cancelled', 'label' => 'Canceladas', 'color' => 'bg-gray-400', 'text' => 'text-gray-500'],
                                ] as $item)
                                    @php 
                                        $value = $statusData[$item['key']] ?? 0;
                                        $total = array_sum($statusData);
                                        $percent = $total > 0 ? round(($value / $total) * 100, 1) : 0;
                                    @endphp
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $item['label'] }}</span>
                                            <span class="font-bold {{ $item['text'] }}">${{ number_format($value, 2) }} ({{ $percent }}%)</span>
                                        </div>
                                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2.5">
                                            <div class="{{ $item['color'] }} h-2.5 rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- CHARTS INITIALIZATION (Chart.js ya está en Filament v3)   --}}
    {{-- ========================================================= --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            const initCharts = () => {
                const dailyData = @js($this->dailyFlowData);
                const expenseData = @js($this->expensesByCategoryData);
                const topPartners = @js($this->topPartnersData);

                // 1. Main Line Chart
                const ctxMain = document.getElementById('mainChart');
                if (ctxMain) {
                    if (window.mainChartInstance) window.mainChartInstance.destroy();
                    window.mainChartInstance = new Chart(ctxMain, {
                        type: 'line',
                        data: {
                            labels: dailyData.labels,
                            datasets: [
                                {
                                    label: 'Ingresos',
                                    data: dailyData.income,
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 3,
                                    pointHoverRadius: 6
                                },
                                {
                                    label: 'Egresos',
                                    data: dailyData.expense,
                                    borderColor: '#ef4444',
                                    backgroundColor: 'rgba(239, 68, 68, 0.05)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 3,
                                    pointHoverRadius: 6
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { intersect: false, mode: 'index' },
                            plugins: {
                                legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8 } },
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                    padding: 12,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: (context) => {
                                            return context.dataset.label + ': $' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2});
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(156, 163, 175, 0.1)' },
                                    ticks: { callback: (value) => '$' + value.toLocaleString() }
                                },
                                x: {
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                }

                // 2. Expense Doughnut Chart
                const ctxExp = document.getElementById('expenseChart');
                if (ctxExp) {
                    if (window.expenseChartInstance) window.expenseChartInstance.destroy();
                    window.expenseChartInstance = new Chart(ctxExp, {
                        type: 'doughnut',
                        data: {
                            labels: expenseData.labels,
                            datasets: [{
                                data: expenseData.values,
                                backgroundColor: expenseData.colors,
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { size: 11 } } }
                            }
                        }
                    });
                }

                // 3. Top Partners Bar Chart
                const ctxTop = document.getElementById('topPartnersChart');
                if (ctxTop) {
                    if (window.topChartInstance) window.topChartInstance.destroy();
                    window.topChartInstance = new Chart(ctxTop, {
                        type: 'bar',
                        data: {
                            labels: topPartners.labels,
                            datasets: [{
                                label: 'Total Pagado',
                                data: topPartners.values,
                                backgroundColor: '#6366f1',
                                borderRadius: 6,
                                barThickness: 30
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(156, 163, 175, 0.1)' },
                                    ticks: { callback: (value) => '$' + value.toLocaleString() }
                                },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            };

            initCharts();

            // Re-init on Livewire updates
            Livewire.hook('morph.updated', () => {
                setTimeout(initCharts, 100);
            });
        });
    </script>
    @endpush
</x-filament-panels::page>