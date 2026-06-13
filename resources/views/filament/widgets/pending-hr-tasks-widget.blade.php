<x-filament-widgets::widget>
    <div class="flex flex-col gap-4">
        @foreach ($tasks as $task)
            <x-filament::section class="border {{ $task['border_class'] }}">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

                    {{-- Icono y Texto --}}
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-full {{ $task['bg_class'] }} {{ $task['color_class'] }}">
                            @svg($task['icon'], 'w-6 h-6')
                        </div>
                        <div>
                            <h3 class="text-lg font-bold {{ $task['color_class'] }}">
                                {{ $task['title'] }}
                            </h3>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $task['description'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Botón de Acción --}}
                    <div class="shrink-0 w-full sm:w-auto mt-2 sm:mt-0">
                        <x-filament::button tag="a" :href="$task['url']" :color="$task['button_color']"
                            icon="heroicon-m-arrow-right" class="w-full sm:w-auto">
                            Revisar ahora
                        </x-filament::button>
                    </div>

                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-widgets::widget>
