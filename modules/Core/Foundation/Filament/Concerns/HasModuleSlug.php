<?php

namespace Modules\Core\Foundation\Filament\Concerns;

use Illuminate\Support\Str;

/**
 * Agrega automáticamente un prefijo basado en el módulo al slug generado
 * por Filament.
 *
 * Este trait está diseñado para aplicaciones modulares donde múltiples
 * módulos pueden contener Resources o Pages con nombres similares.
 *
 * Problema:
 * Filament genera los slugs utilizando principalmente el nombre de la
 * clase y su ubicación dentro del namespace. En una arquitectura modular,
 * esto puede provocar colisiones de rutas cuando distintos módulos
 * exponen recursos conceptualmente iguales.
 *
 * Ejemplos:
 *
 * Modules\HR\App\Filament\Resources\Employees\EmployeeResource
 * -> employees
 *
 * Modules\Customers\App\Filament\Resources\Employees\EmployeeResource
 * -> employees
 *
 * Ambos recursos terminarían compartiendo el mismo slug.
 *
 * Solución:
 * El trait detecta automáticamente el nombre del módulo a partir del
 * namespace y lo utiliza como prefijo:
 *
 * HR -> hr/employees
 * Customers -> customers/employees
 * MercadoLibre -> mercado-libre/ml-publications
 *
 * También evita duplicar el prefijo cuando el slug ya comienza con el
 * nombre del módulo:
 *
 * products
 * -> products
 *
 * en lugar de:
 *
 * products/products
 */
trait HasModuleSlug
{
    /**
     * Obtiene el nombre del módulo a partir del namespace de la clase.
     *
     * Ejemplos:
     *
     * Modules\HR\App\Filament\Resources\EmployeeResource
     * -> hr
     *
     * Modules\MercadoLibre\App\Filament\Pages\MlDashboard
     * -> mercado-libre
     *
     * @return string|null Prefijo normalizado del módulo o null si no se
     * encuentra un namespace compatible.
     */
    protected static function getModulePrefix(): ?string
    {
        preg_match(
            '/Modules\\\\([^\\\\]+)/',
            static::class,
            $matches
        );

        return isset($matches[1])
            ? Str::kebab($matches[1])
            : null;
    }

    /**
     * Agrega el prefijo del módulo al slug recibido.
     *
     * Si el slug ya contiene dicho prefijo, se devuelve sin modificaciones.
     *
     * Ejemplos:
     *
     * hr + employees
     * -> hr/employees
     *
     * products + products
     * -> products
     *
     * mercado-libre + ml-publications
     * -> mercado-libre/ml-publications
     *
     * @param string $slug Slug original generado por Filament.
     *
     * @return string Slug final con aislamiento por módulo.
     */
    protected static function prependModuleToSlug(string $slug): string
    {
        $module = static::getModulePrefix();

        if (! $module) {
            return $slug;
        }

        $segments = explode('/', $slug);

        if (($segments[0] ?? null) === $module) {
            return $slug;
        }

        return "{$module}/{$slug}";
    }
}