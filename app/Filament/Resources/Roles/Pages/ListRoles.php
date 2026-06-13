<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    /*
    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
    */
    
    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getTitle(): string
    {
        return 'Roles y Permisos';
    }

    public function getHeader():?view
    {
        return view('filament.resources.roles.pages.header');
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (Auth::user()?->can('roles.create')) {
            $actions[] = CreateAction::make()
                ->label('Nuevo Rol');
        }

        return $actions;
    }
}