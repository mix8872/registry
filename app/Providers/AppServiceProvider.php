<?php

namespace App\Providers;

use Filament\Notifications\Livewire\Notifications;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\Alignment;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        URL::forceScheme('https');
        Notifications::alignment(Alignment::Center);
    }
}
