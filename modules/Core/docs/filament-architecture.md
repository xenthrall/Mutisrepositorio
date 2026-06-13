# Arquitectura Filament Modular

## Objetivo

El ERP utiliza una arquitectura modular basada en Filament donde cada módulo es responsable de registrar y exponer sus propios recursos administrativos.

Esta arquitectura busca:

* Reducir el acoplamiento entre módulos.
* Facilitar la escalabilidad del sistema.
* Evitar configuraciones manuales repetitivas.
* Mantener una estructura consistente en todo el ERP.
* Permitir el auto-descubrimiento de componentes Filament.

---

# Estructura General

Cada módulo puede contener una carpeta `App/Filament` donde se ubican todos los componentes relacionados con Filament.

Ejemplo:

```text
Modules/
└── HR/
    └── App/
        └── Filament/
            ├── Resources/
            ├── Pages/
            └── Widgets/
```

---

# Registro Automático de Módulos

Cada módulo que necesite integrarse con Filament debe registrar un Plugin.

Ejemplo:

```php
namespace Modules\HR\Providers;

use Modules\Core\Foundation\Filament\BaseModulePlugin;

class HRPlugin extends BaseModulePlugin
{
    protected string $module = 'HR';

    public function getId(): string
    {
        return 'h-r-module';
    }
}
```

Todos los plugins deben extender:

```php
Modules\Core\Foundation\Filament\BaseModulePlugin
```

---

# BaseModulePlugin

La clase `BaseModulePlugin` centraliza el descubrimiento automático de componentes Filament.

Implementación simplificada:

```php
abstract class BaseModulePlugin implements Plugin
{
    protected string $module;

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(...)
            ->discoverPages(...);
    }
}
```

Gracias a esta implementación, no es necesario registrar manualmente:

* Resources
* Pages

siempre que estén ubicados en las rutas esperadas.

---

# Problema de los Slugs Duplicados

Por defecto, Filament genera los slugs usando únicamente el nombre de la clase.

Ejemplo:

```php
ProductResource
```

produce:

```text
products
```

Y una página:

```php
Dashboard
```

produce:

```text
dashboard
```

Esto genera un problema cuando varios módulos contienen nombres similares.

Ejemplo:

```text
Modules/Products/.../Dashboard.php
Modules/HR/.../Dashboard.php
Modules/Tasks/.../Dashboard.php
```

Todos intentarían registrar:

```text
/ dashboard
```

produciendo conflictos de rutas.

---

# Solución Implementada

Se creó una capa de abstracción que agrega automáticamente el nombre del módulo como prefijo de cada slug.

Ejemplo:

```php
Modules\HR\App\Filament\Pages\Dashboard
```

genera:

```text
hr/dashboard
```

Mientras que:

```php
Modules\Tasks\App\Filament\Pages\Dashboard
```

genera:

```text
tasks/dashboard
```

Esto garantiza rutas únicas en todo el sistema.

---

# Trait HasModuleSlug

El comportamiento de prefijado se encuentra centralizado en:

```php
Modules\Core\Foundation\Filament\Concerns\HasModuleSlug
```

## Responsabilidades

### Obtener el nombre del módulo

A partir del namespace:

```php
Modules\HR\App\Filament\Resources\EmployeeResource
```

extrae:

```text
HR
```

y lo transforma a:

```text
hr
```

mediante:

```php
protected static function getModulePrefix(): ?string
```

---

### Agregar el prefijo al slug

Método:

```php
protected static function prependModuleToSlug(string $slug): string
```

Ejemplo:

```text
employees
```

se transforma en:

```text
hr/employees
```

---

### Prevención de duplicados

Si el slug ya contiene el prefijo:

```text
hr/employees
```

no vuelve a agregarlo.

Resultado:

```text
hr/employees
```

---

# ModuleResource

Clase base para todos los Resources del ERP.

Ubicación:

```php
Modules\Core\Foundation\Filament\ModuleResource
```

Implementación:

```php
abstract class ModuleResource extends Resource
{
    use HasModuleSlug;

    public static function getDefaultSlug(): string
    {
        return static::prependModuleToSlug(
            parent::getDefaultSlug()
        );
    }
}
```

---

## Uso Correcto

En lugar de:

```php
use Filament\Resources\Resource;

class EmployeeResource extends Resource
{
}
```

debe utilizarse:

```php
use Modules\Core\Foundation\Filament\ModuleResource;

class EmployeeResource extends ModuleResource
{
}
```

---

## Resultado

Filament:

```text
employees
```

ERP:

```text
hr/employees
```

---

# ModulePage

Clase base para páginas independientes del ERP.

Ubicación:

```php
Modules\Core\Foundation\Filament\ModulePage
```

Implementación:

```php
abstract class ModulePage extends Page
{
    use HasModuleSlug;

    public static function getDefaultSlug(): string
    {
        return static::prependModuleToSlug(
            parent::getDefaultSlug()
        );
    }
}
```

---

## Uso Correcto

En lugar de:

```php
use Filament\Pages\Page;

class HrDashboard extends Page
{
}
```

utilizar:

```php
use Modules\Core\Foundation\Filament\ModulePage;

class HrDashboard extends ModulePage
{
}
```

---

## Resultado

Filament:

```text
hr-dashboard
```

ERP:

```text
hr/hr-dashboard
```

---

# Diferencia entre ModulePage y Resource Pages

Es importante entender esta diferencia.

## Páginas Independientes

Son páginas registradas directamente por Filament.

Ejemplos:

```text
Dashboard
Settings
Metrics
ToolsHub
GoogleDriveSettings
```

Estas deben extender:

```php
ModulePage
```

---

## Resource Pages

Son páginas internas asociadas a un Resource.

Ejemplos:

```text
CreateEmployee
EditEmployee
ViewEmployee
ManageEmployeeDocuments
```

Estas NO generan sus propias rutas.

La ruta es controlada por el Resource padre.

Ejemplo:

```php
EmployeeResource
```

define:

```text
hr/employees
```

y las páginas cuelgan automáticamente de esa ruta:

```text
hr/employees
hr/employees/create
hr/employees/{record}/edit
hr/employees/{record}
```

---

## Regla

Nunca extender:

```php
ModulePage
```

para páginas que pertenezcan a un Resource.

Deben seguir extendiendo:

```php
Filament\Resources\Pages\Page
```

o cualquiera de las clases derivadas:

```php
CreateRecord
EditRecord
ViewRecord
ManageRecords
```

---

# Convenciones del Proyecto

## Resources

Siempre:

```php
class EmployeeResource extends ModuleResource
{
}
```

---

## Pages Independientes

Siempre:

```php
class HrDashboard extends ModulePage
{
}
```

---

## Resource Pages

Siempre:

```php
class ViewEmployee extends ViewRecord
{
}
```

o

```php
class ManageEmployeeDocuments extends Page
{
}
```

si pertenecen al Resource.

---

# Beneficios de esta Arquitectura

* Eliminación de conflictos de rutas.
* Convención única para todos los módulos.
* Menor configuración manual.
* Auto-descubrimiento completo.
* Escalabilidad para nuevos módulos.
* Mayor mantenibilidad del ERP.
* Menor riesgo de errores al incorporar nuevos desarrolladores.

---

# Checklist para Nuevos Módulos

Al crear un nuevo módulo:

* Crear un Plugin que extienda `BaseModulePlugin`.
* Registrar el Plugin en el Panel correspondiente.
* Extender `ModuleResource` para todos los Resources.
* Extender `ModulePage` para páginas independientes.
* Mantener las Resource Pages usando las clases estándar de Filament.
* Respetar la estructura `App/Filament`.

Si se siguen estas convenciones, el módulo quedará integrado automáticamente al ecosistema Filament del ERP.
