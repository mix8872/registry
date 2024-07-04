<?php

namespace App\Providers;

use Filament\Notifications\Livewire\Notifications;
use Illuminate\Support\Facades\Http;
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
        Http::macro('ipa', function ($jar) {
            // Set headers for the search API request
            return Http::withOptions([
                'ssl_key' => [config('services.ipa.ca_cert')],
                'cookies' => $jar
            ])->withHeaders([
                'referer' => config('services.ipa.host') . '/ipa'
            ]);
        });
    }
}
