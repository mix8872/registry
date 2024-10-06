<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Widgets\ContainersOverview;
use App\Filament\Widgets\ProjectsOverview;
use App\Filament\Widgets\RepositoriesOverview;
use App\Filament\Widgets\ServersOverview;
use App\Models\User;
use BezhanSalleh\FilamentShield\FilamentShield;
use Coolsam\Modules\ModulesPlugin;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use DutchCodingCompany\FilamentSocialite\Provider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('/' . config('filament.admin_address'))
            ->path('/' . config('filament.admin_address'))
            ->login(Login::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->favicon(asset('images/registry_logo.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->widgets([
//                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
                ContainersOverview::class,
                RepositoriesOverview::class,
                ProjectsOverview::class,
                ServersOverview::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->plugin(ModulesPlugin::make())
            ->plugin(
                FilamentSocialitePlugin::make()
                    // (required) Add providers corresponding with providers in `config/services.php`.
                    ->providers([
                        Provider::make('authentik')
                            ->label('GRCH')
//                            ->icon('fab-gitlab')
                            ->color(Color::hex('#2f2a6b'))
                            ->outlined(false)
                            ->stateless(false)
//                            ->scopes(['...'])
//                            ->with(['...']),
                    ])
                    ->registration(true)
                    ->createUserUsing(function (string $provider, \SocialiteProviders\Manager\OAuth2\User $oauthUser, FilamentSocialitePlugin $plugin) {
                        $query = (new User())->query();
                        $userObj = $query->create([
                            'name' => $oauthUser->getName(),
                            'email' => $oauthUser->getEmail(),
                            'password' => Hash::make($oauthUser->token)
                        ]);

                        foreach ($oauthUser->attributes['groups'] as $role) {
                            FilamentShield::createRole($role);
                        }
                        $userObj->syncRoles(...$oauthUser->attributes['groups']);

                        return $userObj;
                    })
//                    ->socialiteUserModelClass(SocialiteUser::class)
            )
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
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->topNavigation();
    }
}
