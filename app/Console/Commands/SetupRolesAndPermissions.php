<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission; 
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SetupRolesAndPermissions extends Command
{
    protected $signature = 'app:setup-roles-and-permissions';

    protected $description = 'Sincroniza roles y permisos base del sistema y módulos, actualizando metadatos, jerarquía y criticidad.';

    public function handle()
    {
        $this->info('🔐 Iniciando sincronización de roles y permisos con nueva jerarquía...');

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Cache::forget('spatie.permission.cache');

        // 1. Permisos Base (Estructura Jerárquica: Módulo -> Categoría -> Permisos)
        $permissionsData = [
            'CORE' => [
                'Gestión de Roles' => [
                    [
                        'name' => 'roles.view',
                        'action' => 'Ver listado de roles',
                        'description' => 'Visualizar los roles existentes en el sistema.',
                        'is_system' => true,
                        'criticality' => 'medium',
                        'roles' => ['SUPER ADMIN'],
                    ],
                    [
                        'name' => 'roles.create',
                        'action' => 'Crear roles',
                        'description' => 'Crear nuevos roles para el sistema.',
                        'is_system' => true,
                        'criticality' => 'high',
                        'roles' => ['SUPER ADMIN'],
                    ],
                    [
                        'name' => 'roles.edit',
                        'action' => 'Editar nombre del rol',
                        'description' => 'Modificar la información básica del rol.',
                        'is_system' => true,
                        'criticality' => 'high',
                        'roles' => ['SUPER ADMIN'],
                    ],
                    [
                        'name' => 'roles.delete',
                        'action' => 'Eliminar roles',
                        'description' => 'Borrar roles del sistema permanentemente.',
                        'is_system' => true,
                        'criticality' => 'critical',
                        'roles' => ['SUPER ADMIN'],
                    ],
                    [
                        'name' => 'roles.assign_permissions',
                        'action' => 'Asignar / quitar permisos a un rol',
                        'description' => 'Modificar las capacidades de acceso de un rol específico.',
                        'is_system' => true,
                        'criticality' => 'critical',
                        'roles' => ['SUPER ADMIN'],
                    ],
                ],
                'Gestión de Usuarios' => [
                    [
                        'name' => 'users.view',
                        'action' => 'Ver listado de usuarios',
                        'description' => 'Acceder al directorio global de usuarios activos.',
                        'is_system' => false,
                        'criticality' => 'low',
                        'roles' => ['SUPER ADMIN', 'ADMIN'],
                    ],
                    [
                        'name' => 'users.manage_roles',
                        'action' => 'Asignar / cambiar rol del usuario',
                        'description' => 'Elevar o reducir los privilegios de un usuario.',
                        'is_system' => true,
                        'criticality' => 'high',
                        'roles' => ['SUPER ADMIN', 'ADMIN'],
                    ],
                    [
                        'name' => 'users.update_credentials',
                        'action' => 'Cambiar correo o restablecer contraseña',
                        'description' => 'Modificar credenciales de acceso de cualquier usuario.',
                        'is_system' => true,
                        'criticality' => 'critical',
                        'roles' => ['SUPER ADMIN'],
                    ],
                ],
            ]
        ];

        $this->info('🔍 Buscando permisos en los módulos...');

        $moduleFiles = glob(base_path('[Mm]odules/*/config/permissions.php')) ?: [];

        foreach ($moduleFiles as $file) {
            $modulePermissions = require $file;

            if (!is_array($modulePermissions)) {
                continue;
            }

            // Fusión profunda: Módulo -> Categoría
            foreach ($modulePermissions as $moduleName => $categories) {
                if (!isset($permissionsData[$moduleName])) {
                    $permissionsData[$moduleName] = [];
                }

                foreach ($categories as $categoryName => $perms) {
                    if (!isset($permissionsData[$moduleName][$categoryName])) {
                        $permissionsData[$moduleName][$categoryName] = [];
                    }

                    $permissionsData[$moduleName][$categoryName] = array_merge(
                        $permissionsData[$moduleName][$categoryName],
                        $perms
                    );
                }
            }
        }

        $defaultRoles = [
            'SUPER ADMIN',
            'ADMIN',
        ];

        $currentPermissionNames = [];
        $rolePermissions = [];

        DB::transaction(function () use (
            $permissionsData,
            $defaultRoles,
            &$currentPermissionNames,
            &$rolePermissions
        ) {
            $this->info('⚙ Creando o actualizando permisos definidos en código...');

            // Recorrido de 3 niveles: Módulo -> Categoría -> Permiso
            foreach ($permissionsData as $moduleName => $categories) {
                foreach ($categories as $categoryName => $perms) {
                    foreach ($perms as $perm) {
                        if (!isset($perm['name'], $perm['action'])) {
                            $this->warn("Permiso inválido omitido en la categoría [{$categoryName}] del módulo [{$moduleName}]");
                            continue;
                        }

                        $permission = Permission::updateOrCreate(
                            [
                                'name' => $perm['name'],
                                'guard_name' => 'web',
                            ],
                            [
                                'module' => $moduleName,
                                'domain' => $categoryName,
                                'action' => $perm['action'],
                                'description' => $perm['description'] ?? 'Permite ' . strtolower($perm['action']),
                                'criticality' => $perm['criticality'] ?? 'low',
                                'is_system' => $perm['is_system'] ?? false,
                                'group' => "{$moduleName} - {$categoryName}", 
                            ]
                        );

                        $currentPermissionNames[] = $permission->name;

                        foreach (($perm['roles'] ?? []) as $roleName) {
                            $rolePermissions[$roleName][] = $permission->name;
                        }
                    }
                }
            }

            $currentPermissionNames = array_values(array_unique($currentPermissionNames));

            $this->info('🧹 Buscando permisos obsoletos para eliminarlos de la base de datos...');

            $permissionsToDelete = Permission::query()
                ->where('guard_name', 'web')
                ->whereNotIn('name', $currentPermissionNames)
                ->get();

            if ($permissionsToDelete->isNotEmpty()) {
                $permissionIds = $permissionsToDelete->pluck('id')->all();

                DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
                DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
                Permission::whereIn('id', $permissionIds)->delete();

                $this->warn('⚠ Se eliminaron ' . $permissionsToDelete->count() . ' permisos obsoletos.');
            } else {
                $this->info('✔ No hay permisos obsoletos para eliminar.');
            }

            $this->info('👥 Verificando roles base...');

            foreach ($defaultRoles as $roleName) {
                Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);
            }

            $this->info('🔗 Asignando permisos base a roles sin borrar permisos manuales...');

            foreach ($rolePermissions as $roleName => $assignedPermissions) {
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);

                $assignedPermissions = array_values(array_unique($assignedPermissions));

                if (!empty($assignedPermissions)) {
                    $role->givePermissionTo($assignedPermissions);
                }
            }

            $this->info('✔ Sincronización interna completada.');
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Cache::forget('spatie.permission.cache');

        $this->info('✅ Proceso finalizado correctamente. Toda la jerarquía y metadatos están actualizados en BD.');

        return self::SUCCESS;
    }
}