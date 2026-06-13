<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Permission;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected array $permissionsToSync = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $nested = $data['role_permissions'] ?? [];

        // 1. Extraemos los permisos que el formulario intentó enviar (forzados a String)
        $submittedPermissions = collect($nested)
            ->flatMap(fn ($arr) => is_array($arr) ? $arr : [])
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

        $sensitiveIds = Permission::whereIn('name', $sensitivePermissionNames)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        $roleName = strtoupper(trim($data['name'] ?? ''));

        if ($roleName === 'SUPER ADMIN') {
            $this->permissionsToSync = array_unique(array_merge($submittedPermissions, $sensitiveIds));
        } else {
            // Si es un rol común, eliminamos cualquier intento de asignar un permiso sensible
            $this->permissionsToSync = array_diff($submittedPermissions, $sensitiveIds);
        }

        // 5. Borramos el campo falso para evitar el error de sql
        unset($data['role_permissions']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if (! empty($this->permissionsToSync)) {
            $permissions = Permission::whereIn('id', $this->permissionsToSync)->get();
            $this->record->syncPermissions($permissions);
        }
    }
}