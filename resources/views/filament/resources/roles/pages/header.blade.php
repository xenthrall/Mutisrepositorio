<div
    class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5"
>
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

        <div class="flex items-start gap-4">

            <div
                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400"
            >
                <x-filament::icon
                    icon="heroicon-o-shield-check"
                    class="h-8 w-8"
                />
            </div>

            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Roles y Permisos
                </h1>

                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                    Gestiona los perfiles de acceso del sistema, asigna permisos
                    y controla los niveles de seguridad de cada rol.
                </p>
            </div>

        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">

            <div class="rounded-2xl border border-gray-200 p-4 dark:border-white/10">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Roles
                </div>

                <div class="mt-1 text-2xl font-bold text-gray-950 dark:text-white">
                    {{ \Spatie\Permission\Models\Role::count() }}
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 p-4 dark:border-white/10">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Permisos
                </div>

                <div class="mt-1 text-2xl font-bold text-gray-950 dark:text-white">
                    {{ \Modules\Core\App\Models\Permission::count() }}
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 p-4 dark:border-white/10 col-span-2 sm:col-span-1">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Módulos
                </div>

                <div class="mt-1 text-2xl font-bold text-gray-950 dark:text-white">
                    {{ \Modules\Core\App\Models\Permission::distinct('module')->count('module') }}
                </div>
            </div>

        </div>

    </div>
</div>