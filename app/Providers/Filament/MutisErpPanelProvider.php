<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\File;


class MutisErpPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('mutis-erp')
            ->path('mutis-erp')
            ->viteTheme('resources/css/filament/mutis-erp/theme.css')
            ->login()
            ->profile()
            ->colors([
                'primary' => Color::Indigo,
                'gray'    => Color::Slate,
                'danger'  => Color::Red,
                'info'    => Color::Cyan, 
                'success' => Color::Teal,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->plugins($this->getModulePlugins())
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Gestión Académica')
                    ->icon('heroicon-o-user-group')
                    ->collapsed(), 
                NavigationGroup::make()
                    ->label('Recursos Humanos')
                    ->icon('heroicon-o-briefcase')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label('sistema')
                    ->icon('heroicon-o-cog')
                    ->collapsed(),


            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            //->topNavigation()
            ->sidebarCollapsibleOnDesktop()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function getModulePlugins(): array
    {
        $plugins = [];
        $modulesPath = base_path('modules');

        if (File::exists($modulesPath)) {
            foreach (File::directories($modulesPath) as $modulePath) {
                $moduleName = basename($modulePath);

                if ($moduleName === 'Customers') {
                    continue;
                }

                $pluginClass = "Modules\\{$moduleName}\\Providers\\{$moduleName}Plugin";

                if (class_exists($pluginClass)) {
                    $plugins[] = $pluginClass::make();
                }
            }
        }

        return $plugins;
    }
    
}
