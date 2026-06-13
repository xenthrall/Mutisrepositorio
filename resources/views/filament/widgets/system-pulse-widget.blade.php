<x-filament-widgets::widget>
    {{-- Envolvemos en una Section para que tenga el mismo borde y fondo que el AccountWidget --}}
    <x-filament::section class="relative overflow-hidden">
        
        {{-- Blobs de luz de fondo (Efecto ambient sutil) --}}
        <div class="absolute -right-6 -top-6 h-32 w-32 animate-[pulse_6s_ease-in-out_infinite] rounded-full bg-emerald-50 dark:bg-emerald-900/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-6 -left-6 h-24 w-24 animate-[pulse_8s_ease-in-out_infinite] rounded-full bg-teal-50 dark:bg-teal-900/10 blur-xl pointer-events-none"></div>

        <div class="relative flex items-center justify-between">
            
            {{-- Lado Izquierdo: Animación y Textos --}}
            <div class="flex items-center gap-4">
                
                {{-- ANIMACIÓN MINIMALISTA (Rings & Core) --}}
                <div class="relative flex h-12 w-12 shrink-0 items-center justify-center">
                    
                    {{-- Anillo exterior: Gira a la derecha muy lento (6 segundos) --}}
                    <div class="absolute inset-0 rounded-full border-[1.5px] border-emerald-500/10 dark:border-emerald-400/10 border-t-emerald-500/80 dark:border-t-emerald-400/80 animate-[spin_6s_linear_infinite]"></div>
                    
                    {{-- Anillo interior: Gira a la izquierda un poco más rápido (4 segundos) --}}
                    <div class="absolute inset-1.5 rounded-full border-[1.5px] border-teal-500/10 dark:border-teal-400/10 border-b-teal-500/80 dark:border-b-teal-400/80 animate-[spin_4s_linear_infinite_reverse]"></div>
                    
                    {{-- Núcleo central: Efecto de respiración suave --}}
                    <div class="relative h-3 w-3 animate-[pulse_3s_ease-in-out_infinite] rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400 shadow-[0_0_12px_rgba(16,185,129,0.4)]"></div>
                </div>

                {{-- Textos de Estado --}}
                <div>
                    <h2 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                        Estado Óptimo
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Sistemas en línea y sincronizados
                    </p>
                </div>
            </div>

            {{-- Lado Derecho: Contexto (Hora y Lugar) --}}
            <div class="hidden sm:block text-right">
                <div class="flex items-center justify-end gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-1">
                    <x-heroicon-m-map-pin class="h-3.5 w-3.5" />
                    Colombia
                </div>
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ now()->setTimezone('America/Bogota')->translatedFormat('d M, Y') }}
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>