<x-filament-widgets::widget>
    <x-filament::section>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Aplicaciones
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Selecciona un módulo para comenzar a trabajar.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 md:gap-6">

            @foreach ($this->getApps() as $app)
                <a href="{{ $app['url'] }}"
                    class="relative flex flex-col items-center justify-start p-5 transition-all duration-300 ease-out bg-white dark:bg-gray-900 rounded-2xl ring-1 ring-gray-200 dark:ring-white/10 shadow-sm hover:shadow-md dark:hover:shadow-none hover:ring-primary-500/50 dark:hover:ring-primary-500/50 active:scale-95 group h-full">

                    {{-- Contenedor del Ícono: Proporciones ajustadas --}}
                    <div
                        class="relative flex items-center justify-center p-3 mb-3 transition-transform duration-300 ease-out {{ $app['bg'] }} rounded-xl group-hover:-translate-y-1">
                        <x-dynamic-component :component="$app['icon']" class="w-9 h-9 {{ $app['color'] }}" />
                    </div>

                    {{-- Título: Soporte para 2 líneas en lugar de truncar --}}
                    <h3 class="w-full text-sm font-bold text-center text-gray-900 line-clamp-2 leading-tight transition-colors group-hover:text-primary-600 dark:text-white dark:group-hover:text-primary-400"
                        title="{{ $app['name'] }}">
                        {{ $app['name'] }}
                    </h3>

                    {{-- Descripción: Ajuste de espaciado y límite de líneas --}}
                    <span
                        class="w-full mt-1.5 text-xs text-center text-gray-500 line-clamp-2 leading-snug dark:text-gray-400"
                        title="{{ $app['description'] }}">
                        {{ $app['description'] }}
                    </span>

                </a>
            @endforeach

        </div>

    </x-filament::section>
</x-filament-widgets::widget>