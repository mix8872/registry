<?php

namespace Modules\Finance\Providers;

use App\Observers\HasOwnerObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Finance\Models\FinanceEconomy;
use Modules\Finance\Models\FinanceRes;
use Modules\Finance\Models\FinanceSpentFact;
use Modules\Finance\Observers\FinanceEconomyObserver;
use Modules\Finance\Observers\FinanceSpentFactObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    public function boot()
    {
        FinanceEconomy::observe([FinanceEconomyObserver::class, HasOwnerObserver::class]);
        FinanceSpentFact::observe(FinanceSpentFactObserver::class);
    }

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
        //
    }
}
