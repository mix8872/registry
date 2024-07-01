<?php

namespace App\Providers;

use App\Models\Container;
use App\Models\Project;
use App\Models\Repository;
use App\Models\Server;
use App\Observers\HasOwnerObserver;
use App\Observers\RepositoryObserver;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Project::observe(HasOwnerObserver::class);
        Server::observe(HasOwnerObserver::class);
        Repository::observe(HasOwnerObserver::class);
        Repository::observe(RepositoryObserver::class);
        Container::observe(HasOwnerObserver::class);
    }
}
