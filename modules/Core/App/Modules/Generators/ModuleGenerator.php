<?php

namespace Modules\Core\App\Modules\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleGenerator
{
    protected string $name;
    protected string $modulePath;
    protected bool $withFilament = false;
    protected bool $withApi = false;
    protected bool $withLivewire = false;

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

    public function withLivewire(bool $value): self
    {
        $this->withLivewire = $value;
        return $this;
    }

    public function generate(): void
    {
        $this->createStructure('base');
        
        $providerStub = $this->withLivewire ? 'provider-livewire.stub' : 'provider.stub';
        $this->generateFile($providerStub, "Providers/{$this->name}ServiceProvider.php");
        
        $this->generateFile('web.stub', "routes/web.php");

        if ($this->withFilament) {
            $this->createStructure('filament');
            $this->generateFile('plugin.stub', "Providers/{$this->name}Plugin.php");
            $this->generateFile('filament-page.stub', "App/Filament/Pages/{$this->name}Dashboard.php");
            $this->generateFile('filament-view.stub', "resources/views/filament/pages/dashboard.blade.php");
        }

        if ($this->withApi) {
            $this->createStructure('api');
            $this->generateFile('api.stub', "routes/api.php");
        }

        if ($this->withLivewire) {
            $this->createStructure('livewire');
            $this->generateFile('livewire-component.stub', "App/Livewire/ExampleComponent.php");
            $this->generateFile('livewire-view.stub', "resources/views/livewire/example-component.blade.php");
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
        $stubPath = base_path("modules/Core/App/Modules/Stubs/module/{$stubName}");

        $destination = "{$this->modulePath}/{$destinationPath}";

        if (!File::exists($stubPath)) {
            throw new \Exception("El stub no fue encontrado en: {$stubPath}");
        }

        $content = File::get($stubPath);

        $content = str_replace(
            ['{{module}}', '{{module_kebab}}', '{{module_lower}}'],
            [$this->name, Str::kebab($this->name), strtolower($this->name)],
            $content
        );

        $directory = dirname($destination);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($destination, $content);
    }
}