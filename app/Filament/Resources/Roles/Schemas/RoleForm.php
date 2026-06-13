<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Grid;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        $permissions = Permission::query()
            ->get()
            ->groupBy('group');

        // 1. Array base para cualquier rol (incluyendo roles nuevos)
        $protectedPermissionNames = [
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign_permissions',
            'ml.settings.manage',
            'drive.settings.manage',
            'utils.backups.manage'
        ];

        // 2. Obtenemos el ID de la ruta (si estamos editando)
        $roleId = request()->route('record');

        // 3. Validamos si hay un ID, buscamos el rol y aplicamos reglas específicas
        if ($roleId) {
            $role = Role::find($roleId);

            if ($role && $role->name === 'SUPER ADMIN') {
                $protectedPermissionNames = [
                    'roles.view',
                    'roles.create',
                    'roles.edit',
                    'roles.delete',
                    'roles.assign_permissions',
                    'ml.settings.manage',
                    'drive.settings.manage',
                    'utils.backups.manage'
                ];
            }
        }

        $protectedIds = Permission::whereIn('name', $protectedPermissionNames)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        $leftColumn = [];
        $rightColumn = [];
        $iteration = 0;

        foreach ($permissions as $group => $perms) {
            $section = Section::make($group)
                ->collapsed(false)
                ->schema([
                    CheckboxList::make("role_permissions.{$group}")
                        ->options($perms->pluck('action', 'id'))
                        ->columns(2)
                        //->searchable()
                        ->bulkToggleable()
                        ->descriptions($perms->pluck('description', 'id'))
                        //->disableOptionWhen(fn (string $value): bool => in_array($value, $protectedIds))
                        ->disableOptionWhen(function (string $value, $record) use ($protectedIds) {
                            if ($record?->name === 'SUPER ADMIN') {
                                return false;
                            }
                            return in_array($value, $protectedIds);
                        })
                        ->hiddenLabel(),
                ]);

            if ($iteration % 2 === 0) {
                $leftColumn[] = $section;
            } else {
                $rightColumn[] = $section;
            }
            $iteration++;
        }

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre del Rol')
                    ->disabled(fn($record) => $record?->name === 'SUPER ADMIN')
                    ->required(),


                Grid::make(2)
                    ->schema([
                        Group::make($leftColumn),
                        Group::make($rightColumn),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
