<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Modules\Core\App\Models\Permission;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class ManageRolePermissions extends Page
{
    use InteractsWithRecord;

    protected static string $resource = RoleResource::class;

    protected string $view = 'filament.resources.roles.pages.manage-role-permissions';

    public array $selectedPermissions = [];

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
    
    #[Url]
    public ?string $activeTab = null;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->selectedPermissions = $this->record->permissions->pluck('name')->toArray();

        if (blank($this->activeTab)) {
            $this->activeTab = $this->modules->keys()->first();
        }
    }

    /**
     * 1. Quitamos los Breadcrumbs por completo
     */
    public function getBreadcrumbs(): array
    {
        return [];
    }

    /**
     * 2. Quitamos el Header/Heading por defecto de la pantalla
     */
    public function getHeading(): string | Htmlable
    {
        return '';
    }

    /**
     * Mantiene el título correcto en la pestaña del navegador
     */
    public function getTitle(): string | Htmlable
    {
        return "Permisos: " . ($this->record?->name ?? 'Rol');
    }

    #[Computed]
    public function modules()
    {
        return Permission::query()
            ->orderBy('module')
            ->orderBy('domain')
            ->get()
            ->groupBy(['module', 'domain']);
    }

    public function save(): void
    {
        $this->record->syncPermissions($this->selectedPermissions);

        Notification::make()
            ->success()
            ->title('Permisos actualizados')
            ->body("Los permisos para el rol {$this->record->name} se han guardado correctamente.")
            ->send();
    }
}