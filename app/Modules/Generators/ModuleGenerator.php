<?php

namespace App\Modules\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleGenerator
{
    protected string $name;
    protected string $modulePath;
    protected bool $withFilament = false;
    protected bool $withApi = false;

    public function __construct(string $name)
    {
        $this->name = Str::studly($name);
        $this->modulePath = config('modules.path') . '/' . $this->name;
    }

    public function withFilament(bool $value): self
    {
        $this->withFilament = $value;
        return $this;
    }

    public function withApi(bool $value): self
    {
        $this->withApi = $value;
        return $this;
    }

    public function generate(): void
    {
        // 1. Estructura y Provider base
        $this->createStructure('base');
        $this->generateFile('provider.stub', "Providers/{$this->name}ServiceProvider.php");
        $this->generateFile('web.stub', "routes/web.php");

        if ($this->withFilament) {
            $this->createStructure('filament');
            $this->generateFile('plugin.stub', "Providers/{$this->name}Plugin.php");
            
            // 🔥 Generar página y vista interactiva para Filament
            $this->generateFile('filament-page.stub', "App/Filament/Pages/{$this->name}Dashboard.php");
            $this->generateFile('filament-view.stub', "resources/views/filament/pages/dashboard.blade.php");
        }

        if ($this->withApi) {
            $this->createStructure('api');
            $this->generateFile('api.stub', "routes/api.php");
        }
    }

    protected function createStructure(string $type): void
    {
        $folders = config("modules.structure.{$type}", []);

        foreach ($folders as $folder) {
            $path = "{$this->modulePath}/{$folder}";
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
        }
    }

    protected function generateFile(string $stubName, string $destinationPath): void
    {
        $stubPath = app_path("Modules/Stubs/module/{$stubName}");
        $destination = "{$this->modulePath}/{$destinationPath}";

        if (!File::exists($stubPath)) {
            return; // O lanzar una excepción si el stub no existe
        }

        $content = File::get($stubPath);
        
        // Reemplazo dinámico actualizado para soportar {{module_lower}}
        $content = str_replace(
            ['{{module}}', '{{module_kebab}}', '{{module_lower}}'],
            [$this->name, Str::kebab($this->name), strtolower($this->name)],
            $content
        );

        // Asegurar que el directorio destino existe
        $directory = dirname($destination);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($destination, $content);
    }
}