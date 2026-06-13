<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Role;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected array $permissionsToSync = [];

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (Auth::user()?->can('roles.delete')) {
            $actions[] = DeleteAction::make()
                ->disabled(function (Role $record) {
                    return $record->users()->exists();
                })
                ->tooltip(function (Role $record) {
                    if ($record->users()->exists()) {
                        return 'No se puede eliminar porque hay usuarios usando este rol';
                    }
                    return null;
                })
                ->before(function (Role $record, DeleteAction $action) {
                    if ($record->users()->exists()) {
                        Notification::make()
                            ->title('No se puede eliminar el rol')
                            ->body('Este rol está asignado a uno o más usuarios en el sistema. Debe quitarle este rol a esos usuarios antes de poder eliminarlo.')
                            ->danger()
                            ->duration(8000)
                            ->send();

                        $action->cancel();
                    }
                });
        }

        return $actions;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $nested = $data['role_permissions'] ?? [];

        // 1. Obtenemos los permisos que el formulario envió (forzados a String para evitar errores)
        $submittedPermissions = collect($nested)
            ->flatMap(fn($arr) => is_array($arr) ? $arr : [])
            ->filter()
            ->values()
            ->map(fn($id) => (string) $id)
            ->toArray();

        // 2. lista estricta de permisos sensibles
        $sensitivePermissionNames = [
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign_permissions',
            'ml.settings.manage',
            'drive.settings.manage',
            'utils.backups.manage'
        ];
        //si el rol es SUper Admin definimos una lista de permisos sensibles más amplia
        if ($this->record->name === 'SUPER ADMIN') {
            $sensitivePermissionNames = [
                'roles.view',
                'roles.create',
                'roles.edit',
                'roles.delete',
                'roles.assign_permissions',
                'utils.backups.manage'
            ];
        }

        // 3. Obtenemos los IDs reales de esos permisos en la BD
        $sensitiveIds = Permission::whereIn('name', $sensitivePermissionNames)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        // 4. APLICAMOS LA PROTECCIÓN DEL BACKEND
        if ($this->record->name === 'SUPER ADMIN') {
            // Si es Super Admin, unimos lo enviado + los sensibles (así garantizamos que nunca los pierda)
            $this->permissionsToSync = array_unique(array_merge($submittedPermissions, $sensitiveIds));
        } else {
            // Si NO es Super Admin, le restamos los IDs sensibles a lo que haya enviado
            $this->permissionsToSync = array_diff($submittedPermissions, $sensitiveIds);
        }

        // 5. Limpiamos el array temporal de Filament para no causar error en SQLite
        unset($data['role_permissions']);
        return $data;
    }

    protected function afterSave(): void
    {
        if (! empty($this->permissionsToSync)) {
            $permissions = Permission::whereIn('id', $this->permissionsToSync)->get();
            $this->record->syncPermissions($permissions);
        } else {
            $this->record->syncPermissions([]);
        }
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $ids = $this->record->permissions()->pluck('id')->toArray();

        $grouped = Permission::whereIn('id', $ids)
            ->get()
            ->groupBy('group')
            ->map(fn($items) => $items->pluck('id')->map(fn($id) => (string) $id)->toArray()) // Forzamos a string aquí también
            ->toArray();

        $data['role_permissions'] = $grouped;

        return $data;
    }
}
