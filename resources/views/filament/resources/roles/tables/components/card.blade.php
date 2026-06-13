@php
    use Modules\Core\App\Enums\PermissionCriticality;

    $record = $getRecord();
@endphp

<div
    class="h-full rounded-2xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-primary-500 hover:shadow-md dark:border-white/10 dark:bg-white/5">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="truncate text-base font-semibold text-gray-950 dark:text-white">
                {{ $record->name }}
            </h3>
        </div>

        <span
            class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-1 text-xs font-medium text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
            {{ $record->permissions_count ?? 0 }} permisos
        </span>
    </div>

    <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 text-center dark:border-white/10 dark:bg-white/5">
            <div class="text-lg font-bold text-gray-950 dark:text-white">
                {{ $record->permissions_low_count ?? 0 }}
            </div>
            <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                {{ PermissionCriticality::LOW->getLabel() }}
            </div>
        </div>

        <div
            class="rounded-xl border border-blue-200 bg-blue-50 p-3 text-center dark:border-blue-500/20 dark:bg-blue-500/10">
            <div class="text-lg font-bold text-blue-700 dark:text-blue-400">
                {{ $record->permissions_medium_count ?? 0 }}
            </div>
            <div class="mt-1 text-[11px] text-blue-700 dark:text-blue-400">
                {{ PermissionCriticality::MEDIUM->getLabel() }}
            </div>
        </div>

        <div
            class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-center dark:border-amber-500/20 dark:bg-amber-500/10">
            <div class="text-lg font-bold text-amber-700 dark:text-amber-400">
                {{ $record->permissions_high_count ?? 0 }}
            </div>
            <div class="mt-1 text-[11px] text-amber-700 dark:text-amber-400">
                {{ PermissionCriticality::HIGH->getLabel() }}
            </div>
        </div>

        <div
            class="rounded-xl border border-red-200 bg-red-50 p-3 text-center dark:border-red-500/20 dark:bg-red-500/10">
            <div class="text-lg font-bold text-red-700 dark:text-red-400">
                {{ $record->permissions_critical_count ?? 0 }}
            </div>
            <div class="mt-1 text-[11px] text-red-700 dark:text-red-400">
                {{ PermissionCriticality::CRITICAL->getLabel() }}
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4 dark:border-white/10">

        <span class="text-xs text-gray-500 dark:text-gray-400">
            Toca para administrar permisos
        </span>

        <span class="text-sm font-medium text-primary-600 dark:text-primary-400">
            Administrar →
        </span>

    </div>
</div>
