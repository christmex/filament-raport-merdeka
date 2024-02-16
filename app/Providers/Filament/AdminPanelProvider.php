<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use App\Filament\Pages\Backups;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Filament\Pages\Auth\EditProfile;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use FilipFonal\FilamentLogManager\FilamentLogManager;
use Illuminate\Routing\Middleware\SubstituteBindings;
use App\Http\Middleware\EnsureApplicationAlreadySetup;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->defaultThemeMode(ThemeMode::Dark)
            ->colors([
                // 'danger' => Color::Rose,
                'danger' => '#e63946',
                'gray' => Color::Gray,
                // 'info' => Color::Blue,
                'info' => '#457b9d',
                'primary' => '#E65C00',
                'success' => Color::Emerald,
                // 'warning' => Color::Orange,
                'warning' => '#ffb703',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->sidebarFullyCollapsibleOnDesktop()
            ->navigationGroups([
                'Main Configuration',
                'Configuration',
                'Master',
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
            ->authMiddleware([
                Authenticate::class,
                EnsureApplicationAlreadySetup::class
            ])
            ->brandLogo(asset('logo_basic_digital.svg'))
            // ->brandLogo(asset('logo_filamic.png'))
            ->brandLogoHeight('1rem')
            ->profile(EditProfile::class)
            // ->topNavigation()
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            // ->unsavedChangesAlerts()
            ->plugins([
                FilamentJobsMonitorPlugin::make()
                ->label('Job')
                ->pluralLabel('Jobs')
                ->enableNavigation(true)
                ->navigationIcon('heroicon-o-cpu-chip')
                ->navigationGroup('Settings')
                ->navigationSort(5)
                ->navigationCountBadge(true)
                ->enablePruning(true)
                ->pruningRetention(7),
                FilamentLogManager::make(),
                FilamentSpatieLaravelBackupPlugin::make()
                ->usingPage(Backups::class),
                // FilamentShieldPlugin::make()
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('SD 1')
                    ->url('https://pr-sd1.sekolahbasic.sch.id/')
                    ->icon('heroicon-o-academic-cap'),
                MenuItem::make()
                    ->label('SD 2')
                    ->url('https://pr-sd2.sekolahbasic.sch.id/')
                    ->icon('heroicon-o-academic-cap'),
                MenuItem::make()
                    ->label('SMP 1')
                    ->url('https://pr-smp1.sekolahbasic.sch.id/')
                    ->icon('heroicon-o-academic-cap'),
                MenuItem::make()
                    ->label('SMP 2')
                    ->url('https://pr-smp2.sekolahbasic.sch.id/')
                    ->icon('heroicon-o-academic-cap'),
                MenuItem::make()
                    ->label('SMA 1')
                    ->url('https://pr-sma1.sekolahbasic.sch.id/')
                    ->icon('heroicon-o-academic-cap'),
                MenuItem::make()
                    ->label('SMA 2')
                    ->url('https://pr-sma2.sekolahbasic.sch.id/')
                    ->icon('heroicon-o-academic-cap'),
            ])
            ->breadcrumbs(false)
            ->font('Sansation')
            ;
    }
}
