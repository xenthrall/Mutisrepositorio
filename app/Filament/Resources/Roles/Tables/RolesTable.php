<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Model $record): string => RoleResource::getUrl('permissions', [
                'record' => $record,
            ]))
            ->emptyStateHeading('No hay roles creados')
            ->emptyStateDescription('Crea un rol para empezar a asignarle permisos.')
            ->emptyStateIcon('heroicon-o-shield-check')
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                View::make('filament.resources.roles.tables.components.card'),
            ])
            ->defaultSort('name');
    }
}