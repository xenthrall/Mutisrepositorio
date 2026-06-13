<x-filament-panels::page>
    <div x-data="{ activeTab: @entangle('activeTab').live }" class="space-y-6">

        {{-- Header --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/5 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-start gap-4">
                    <a
                        href="{{ \App\Filament\Resources\Roles\RoleResource::getUrl('index') }}"
                        title="Volver a la lista de roles"
                        class="mt-1 inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-50 text-gray-500 shadow-sm ring-1 ring-gray-950/10 transition hover:bg-gray-100 hover:text-gray-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:bg-white/5 dark:text-gray-400 dark:ring-white/10 dark:hover:bg-white/10 dark:hover:text-white"
                    >
                        <x-filament::icon icon="heroicon-m-arrow-left" class="h-5 w-5" />
                    </a>

                    <div class="min-w-0">
                        <h1 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">
                            Permisos del Rol:
                            <span class="text-primary-600 dark:text-primary-500">
                                {{ $this->record->name }}
                            </span>
                        </h1>

                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                            Administra permisos por módulo y dominio desde una interfaz optimizada para escritorio y móvil.
                        </p>
                    </div>
                </div>

                <div class="flex w-full lg:w-auto">
                    <x-filament::button
                        wire:click="save"
                        icon="heroicon-m-check-badge"
                        class="w-full sm:w-auto"
                    >
                        Guardar cambios
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div
            class="overflow-x-auto border-b border-gray-200 pb-px dark:border-white/10
                   [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
        >
            <div class="flex min-w-max gap-2 sm:gap-3">
                @foreach($this->modules as $moduleName => $domains)
                    <button
                        type="button"
                        x-on:click="activeTab = @js($moduleName)"
                        :class="activeTab === @js($moduleName)
                            ? 'border-primary-600 text-primary-600 dark:border-primary-500 dark:text-primary-500 bg-primary-50 dark:bg-primary-500/10'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-white/5'"
                        class="whitespace-nowrap rounded-full border px-4 py-2 text-sm font-medium transition sm:px-5"
                    >
                        {{ $moduleName }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Content --}}
        <div class="space-y-6">
            @foreach($this->modules as $moduleName => $domains)
                <div
                    x-show="activeTab === @js($moduleName)"
                    x-cloak
                    class="space-y-6"
                >
                    @foreach($domains as $domainName => $permissions)
                        <x-filament::section>
                            <x-slot name="heading">
                                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                    <span class="text-base font-semibold text-gray-950 dark:text-white">
                                        {{ $domainName }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $permissions->count() }} permiso(s)
                                    </span>
                                </div>
                            </x-slot>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach($permissions as $permission)
                                    <label
                                        class="group flex cursor-pointer items-start gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-primary-500 hover:shadow-md dark:border-white/10 dark:bg-white/5 dark:hover:border-primary-500"
                                    >
                                        <div class="mt-0.5 flex h-5 items-center">
                                            <input
                                                type="checkbox"
                                                wire:model="selectedPermissions"
                                                value="{{ $permission->name }}"
                                                class="h-4 w-4 rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800"
                                            >
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-col gap-1">
                                                <span class="text-sm font-semibold text-gray-950 transition group-hover:text-primary-600 dark:text-white">
                                                    {{ $permission->action }}
                                                </span>

                                                @if($permission->description)
                                                    <span class="text-sm leading-snug text-gray-500 dark:text-gray-400">
                                                        {{ $permission->description }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-medium {{ $permission->criticality->badgeClasses() }}">
                                                    <x-filament::icon
                                                        :icon="$permission->criticality->getIcon()"
                                                        class="mr-1 h-3 w-3"
                                                    />
                                                    {{ $permission->criticality->getLabel() }}
                                                </span>

                                                @if($permission->is_system)
                                                    <span class="inline-flex items-center rounded-full border border-purple-200 bg-purple-50 px-2.5 py-1 text-[10px] font-medium text-purple-700 dark:border-purple-500/20 dark:bg-purple-500/10 dark:text-purple-400">
                                                        Sistema
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </x-filament::section>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Bottom action bar for mobile --}}
        <div class="sticky bottom-4 z-20 lg:hidden">
            <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-lg dark:border-white/10 dark:bg-gray-950/95">
                <x-filament::button wire:click="save" icon="heroicon-m-check-badge" class="w-full">
                    Guardar permisos
                </x-filament::button>
            </div>
        </div>

        {{-- Desktop action --}}
        <div class="hidden justify-end lg:flex">
            <x-filament::button wire:click="save" size="lg" icon="heroicon-m-check-badge">
                Guardar permisos
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>