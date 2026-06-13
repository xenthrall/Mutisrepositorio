# 🏗️ Guía de Desarrollo Modular

Esta guía describe la arquitectura modular de nuestra aplicación. El objetivo de esta estructura es mantener el código desacoplado, escalable y fácil de mantener, organizando la lógica por dominio de negocio (ej. Recursos Humanos, Inventario, Académico) en lugar de agrupar por tipo de archivo.

---

## 🚀 1. Creación de un Nuevo Módulo

Para garantizar la consistencia en todo el proyecto, **nunca crees las carpetas manualmente**. Utiliza nuestro generador interactivo, el cual preparará toda la estructura base, los Service Providers y registrará el módulo en el sistema.

Ejecuta el siguiente comando en tu terminal:

```bash
php artisan module:make
```

El asistente te preguntará el nombre del módulo (ej. `Inventory`, `Invoicing`) y te permitirá seleccionar las características que necesitas:

- **Filament PHP**: Genera la estructura y el Plugin para paneles de administración.
- **APIs**: Prepara controladores de recursos, Requests y el archivo `api.php`.
- **Livewire**: Configura el soporte, los namespaces y vistas para componentes reactivos.

También puedes usar flags directamente:

```bash
php artisan module:make Inventory --filament --livewire
```

---

## 📂 2. Estructura del Módulo

Una vez generado, tu módulo vivirá en la carpeta `modules/TuModulo`. La anatomía estándar es la siguiente:

```
modules/Inventory/
├── App/
│   ├── Console/         # Comandos Artisan específicos del módulo
│   ├── Filament/        # Resources, Pages y Widgets de Filament
│   ├── Http/            # Controllers, Requests y Resources
│   ├── Livewire/        # Clases de componentes Livewire
│   ├── Models/          # Modelos Eloquent del dominio
│   └── Services/        # Lógica de negocio (Actions/Services)
├── Database/
│   ├── Migrations/      # Migraciones exclusivas de este módulo
│   └── Seeders/
├── Providers/
│   ├── InventoryServiceProvider.php  # Registra rutas, vistas y configs
│   └── InventoryPlugin.php           # (Opcional) Registra el módulo en Filament
├── config/
│   └── config.php       # Configuración específica del módulo
├── resources/
│   ├── lang/            # Archivos de traducción
│   └── views/           # Vistas Blade (incluyendo livewire/)
└── routes/
    ├── api.php
    └── web.php
```

---

## 🧠 3. Comportamiento Automático (El "Core")

Nuestra arquitectura base (`ModuleServiceProvider` y `BaseModulePlugin`) hace mucho del trabajo pesado por ti. Es importante que conozcas qué ocurre "mágicamente" en el fondo para evitar configuraciones duplicadas:

### Rutas Automáticas

- **Web (`routes/web.php`)**: Se cargan automáticamente bajo el middleware `web`.
- **API (`routes/api.php`)**: Se cargan bajo el middleware `api` y automáticamente reciben un prefijo y un nombre.

> **Ejemplo:** Una ruta `/products` en el módulo `Inventory` será accesible públicamente en `/api/inventory/products` y su nombre de ruta empezará con `api.inventory.`.

### Vistas y Componentes Livewire

- Las vistas se registran usando el nombre del módulo en minúscula como namespace.
- Para retornar una vista normal: `return view('inventory::index');`
- Para usar un componente Livewire en Blade: `@livewire('inventory::mi-componente')`

### Migraciones y Comandos

- Cualquier archivo de migración colocado en `Database/Migrations` se ejecutará automáticamente cuando corras `php artisan migrate`. No necesitas referenciarlas en el proyecto principal.
- Cualquier comando creado dentro de `App/Console` es descubierto y registrado automáticamente en Artisan.

### Filament PHP

Si habilitaste Filament, el archivo `TuModuloPlugin.php` extiende de `BaseModulePlugin`. Este se encarga de escanear automáticamente las carpetas `App/Filament/Resources` y `App/Filament/Pages`.

> **Regla de oro:** Simplemente crea tu Resource con los comandos de Filament dentro de la carpeta de tu módulo, y aparecerá en el panel sin necesidad de registrarlo manualmente.

---

## 💡 4. Mejores Prácticas y Reglas de Oro

Para mantener la base de código limpia y evitar que nuestro sistema se convierta en un monolito acoplado, sigue estas reglas estrictamente:

### A. Aislamiento de Módulos (Cero Acoplamiento Estricto)

Un módulo no debe depender directamente de la base de datos o modelos de otro módulo si no es absolutamente necesario.

| ❌ Mal | ✅ Bien |
|--------|---------|
| El módulo `Invoicing` hace un `User::where('id', ...)->update(...)` directamente al modelo del módulo `Core` o `HR`. | El módulo `Invoicing` dispara un evento o usa una clase Service pública que expone el módulo de `HR` para actualizar datos de un usuario. |

### B. "Fat Models & Services, Skinny Controllers"

Mantén los controladores y los componentes de Livewire limpios. Su única responsabilidad debe ser recibir el `Request`, llamar a una clase de servicio o modelo, y retornar una respuesta.

Si el proceso involucra más de 3 pasos lógicos (ej. Crear usuario, asignar rol, enviar email de bienvenida), envuélvelo en una clase dentro de `App/Services/`.

### C. Cuidado con los Namespaces

PHP y Composer son estrictos con mayúsculas y minúsculas (especialmente en entornos de producción Linux).

- Asegúrate siempre de que tus clases comiencen con `namespace Modules\NombreDelModulo\App\...`.
- Si un comando de Laravel te genera un archivo en la carpeta global `app/`, muévelo manualmente a tu módulo y actualiza su `namespace`.

### D. Centralización de Dependencias Frontend

Si tu módulo requiere librerías JS o CSS específicas (como librerías de gráficos para Filament o Livewire), intenta inyectarlas usando el stack de Blade o regístralas a través del método `boot()` del Plugin de Filament del módulo, para no sobrecargar el bundle global de la aplicación.
