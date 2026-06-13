<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Tables\RolesTable;
use Spatie\Permission\Models\Role;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Modules\Core\App\Enums\PermissionCriticality;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model; 

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'sistema';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Roles y Permisos';

    protected static ?string $modelLabel = 'Rol';

    protected static ?string $pluralModelLabel = 'Roles';

    // =========================================================================
    // 🛡️ MÉTODOS DE AUTORIZACIÓN GRANULAR DE FILAMENT
    // =========================================================================

    // 1. Controla si el usuario puede ver la página en el menú
    public static function canAccess(): bool
    {
        return auth()->user()->can('roles.view');
    }

    // 2. Controla si aparece el botón "Crear Rol"
    public static function canCreate(): bool
    {
        return auth()->user()->can('roles.create');
    }

    // 3. Controla si aparece el botón/ícono de "Editar" en la tabla
    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('roles.edit');
    }

    // 4. Controla si aparece el botón/ícono de "Eliminar" (individual y masivo)
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('roles.delete');
    }
    
    // =========================================================================

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount([
                'permissions',
                'permissions as permissions_low_count' => fn(Builder $query) => $query->where('criticality', PermissionCriticality::LOW->value),
                'permissions as permissions_medium_count' => fn(Builder $query) => $query->where('criticality', PermissionCriticality::MEDIUM->value),
                'permissions as permissions_high_count' => fn(Builder $query) => $query->where('criticality', PermissionCriticality::HIGH->value),
                'permissions as permissions_critical_count' => fn(Builder $query) => $query->where('criticality', PermissionCriticality::CRITICAL->value),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'permissions' => Pages\ManageRolePermissions::route('/{record}/permissions'),
        ];
    }
}