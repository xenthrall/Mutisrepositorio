<?php

namespace Modules\Core\App\Console;

use Illuminate\Console\Command;
use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\info;

class ErpSetup extends Command
{
    protected $signature = 'erp:setup';
    protected $description = 'Configura el proyecto ERP con una interfaz interactiva';

    public function handle()
    {
        intro('🛠️  Asistente de Configuración del ERP');

        do {
            $option = select(
                label: '¿Qué acción deseas realizar?',
                options: [
                    'init'        => '🚀 Ejecutar configuración inicial',
                    'permissions' => '🔐 Sincronizar roles y permisos (Spatie)',
                    'optimize'    => '⚡ Optimizar proyecto (Caché)',
                    'exit'        => '🚪 Salir'
                ],
                default: 'exit'
            );

            match ($option) {
                'init'        => $this->configuracionInicial(),
                'permissions' => $this->sincronizarPermisos(),
                'optimize'    => $this->optimizarProyecto(),
                'exit'        => outro('👋 ¡Hasta luego, Papu! Que el código te acompañe.'),
            };

        } while ($option !== 'exit');
    }

    private function configuracionInicial()
    {
        info('Iniciando el proceso de configuración...');

        // 1. Archivo .env
        $this->components->task('Configurando archivo .env', function () {
            if (!file_exists(base_path('.env'))) {
                return copy(base_path('.env.example'), base_path('.env'));
            }
            return true; // Retorna true si ya existe para marcar la tarea como exitosa
        });

        // 2. APP KEY
        if (confirm('¿Deseas generar la APP_KEY?', default: true)) {
            $this->components->task('Generando APP_KEY', fn() => $this->callSilently('key:generate') === 0);
        }

        // 3. MIGRACIONES
        if (confirm('¿Deseas ejecutar las migraciones en la base de datos?', default: true)) {
            $this->components->task('Ejecutando migraciones', fn() => $this->callSilently('migrate', ['--force' => true]) === 0);

            // Permisos
            if (confirm('¿Deseas sincronizar los permisos ahora?', default: true)) {
                $this->components->task('Sincronizando permisos', fn() => $this->callSilently('app:setup-roles-and-permissions') === 0);
            }

            // Seeders
            if (confirm('¿Deseas ejecutar los seeders?', default: true)) {
                $seeder = text(
                    label: '¿Qué seeder deseas ejecutar?',
                    placeholder: 'DatabaseSeeder',
                    default: 'DatabaseSeeder'
                );

                $this->components->task("Ejecutando seeder: {$seeder}", function () use ($seeder) {
                    return $this->callSilently('db:seed', [
                        '--class' => $seeder,
                        '--force' => true
                    ]) === 0;
                });
            }
        }

        // 4. STORAGE LINK
        $this->components->task('Verificando enlace de almacenamiento (Storage Link)', function () {
            if (!file_exists(public_path('storage'))) {
                return $this->callSilently('storage:link') === 0;
            }
            return true;
        });

        outro('🎉 ¡Configuración inicial completada con éxito!');
    }

    private function sincronizarPermisos()
    {
        $this->components->task('Sincronizando roles y permisos', function () {
            return $this->callSilently('app:setup-roles-and-permissions') === 0;
        });
        
        info('✅ Permisos actualizados correctamente.');
    }

    private function optimizarProyecto()
    {
        $this->components->task('Limpiando y optimizando cachés del sistema', function () {
            $this->callSilently('optimize:clear');
            return $this->callSilently('optimize') === 0;
        });
        
        info('🚀 Proyecto optimizado para máximo rendimiento.');
    }
}