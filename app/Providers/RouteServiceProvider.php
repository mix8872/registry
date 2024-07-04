<?php

namespace App\Providers;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers\Api';

    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        Route::group(['namespace' => $this->namespace], base_path('routes/api.php'));
    }
}
