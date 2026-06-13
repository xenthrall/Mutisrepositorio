<?php

namespace Modules\Core\App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Modules\Core\App\Modules\Generators\ModuleGenerator;

use function Laravel\Prompts\text;
use function Laravel\Prompts\multiselect;

class MakeModuleCommand extends Command
{
    protected $signature = 'module:make 
                            {name? : El nombre del módulo}
                            {--filament : Generar estructura para Filament PHP}
                            {--api : Generar estructura para APIs}
                            {--livewire : Generar estructura para componentes Livewire}
                            {--simple : Crear un módulo básico sin extras}';

    protected $description = 'Genera un nuevo módulo estructurado y escalable de forma interactiva';

    public function handle()
    {
        $name = $this->argument('name') ?? text(
            label: '¿Cuál es el nombre de tu nuevo módulo?',
            placeholder: 'Ej: Inventory, Sales, HR...',
            required: 'El nombre del módulo es obligatorio.'
        );

        $this->components->info("Iniciando la creación del módulo [{$name}]...");

        $hasFlags = $this->option('filament') || $this->option('api') || $this->option('livewire') || $this->option('simple');
        $selectedFeatures = [];

        if (! $hasFlags) {
            $selectedFeatures = multiselect(
                label: '¿Qué características deseas incluir en este módulo?',
                options: [
                    'filament' => 'Filament PHP (Paneles e Interfaz)',
                    'api' => 'Estructura para APIs (Rutas, Requests, Resources)',
                    'livewire' => 'Livewire (Componentes reactivos e Interfaz)',
                ],
                hint: 'Usa ↑/↓ para navegar, ESPACIO para seleccionar y ENTER para confirmar.'
            );
        }

        $withFilament = $this->option('filament') || in_array('filament', $selectedFeatures);
        $withApi = $this->option('api') || in_array('api', $selectedFeatures);
        $withLivewire = $this->option('livewire') || in_array('livewire', $selectedFeatures);

        $generator = new ModuleGenerator($name);

        $generator
            ->withFilament($withFilament)
            ->withApi($withApi)
            ->withLivewire($withLivewire)
            ->generate();

        $this->components->info("✅ Estructura del módulo {$name} creada correctamente.");
        
        $this->components->task('Registrando módulo en el sistema', function () {
            Artisan::call('module:cache');
        });

        $this->newLine();
        $this->info("🚀 ¡Módulo listo para usarse! Las rutas y el ServiceProvider ya están activos.");
    }
}