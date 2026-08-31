<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AssetCompanyChart;
use App\Filament\Widgets\AssetStats;
use App\Filament\Widgets\AssetStatusChart;
use App\Filament\Widgets\WarrantyExpiringAssets;
use App\Filament\Widgets\AssetStatusModalWidget;
use App\Filament\Widgets\AssetCompanyModalWidget;
use App\Filament\Widgets\ServiceYearChart;
use App\Filament\Widgets\ServiceYearModalWidget;
use App\Filament\Widgets\AssetJenisCompanyChart;
use App\Filament\Widgets\AssetJenisCompanyModalWidget;
use App\Filament\Widgets\AssetLocationStatusModalWidget;
use App\Filament\Widgets\AssetLocationStatusChart;
use App\Filament\Widgets\AssetDepartmentChart;
use App\Filament\Widgets\AssetDepartmentModalWidget;
use App\Filament\Widgets\SoftwareAssignmentCompanyChart;
use App\Filament\Widgets\SoftwareAssignmentCompanyModalWidget;


use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;

use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Enums\Width;



class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel

            ->default()

            ->id('admin')

            ->path('admin')


            ->login()


            ->databaseNotifications()


            ->colors([
                'primary' => Color::Amber,
            ])


            ->brandName('IT Asset Management')

            ->brandLogo(asset('images/favicon.png'))

            ->brandLogoHeight('40px')


            ->sidebarCollapsibleOnDesktop()



            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources',
            )



            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages',
            )



            ->pages([
                Dashboard::class,
            ])





            ->widgets([

                AssetStats::class,

                AssetStatusChart::class,

                AssetStatusModalWidget::class,
                AssetCompanyChart::class,

                AssetCompanyModalWidget::class,

                AssetDepartmentChart::class,
                AssetDepartmentModalWidget::class,

                

                AssetJenisCompanyChart::class,

                AssetJenisCompanyModalWidget::class,
                
                AssetLocationStatusChart::class,

                AssetLocationStatusModalWidget::class,

                ServiceYearChart::class,

                ServiceYearModalWidget::class,
                SoftwareAssignmentCompanyChart::class,

                SoftwareAssignmentCompanyModalWidget::class,

                WarrantyExpiringAssets::class,

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

            ])

            
            ->maxContentWidth(Width::Full);
    }
}