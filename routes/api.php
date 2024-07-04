<?php

use App\Http\Controllers\Api\HooksController;
use App\Http\Middleware\CheckCollabTokenMiddleware;
use App\Http\Middleware\ExceptionMiddleware;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/api/hooks',
    'middleware' => ExceptionMiddleware::class
], function () {
    Route::middleware(CheckCollabTokenMiddleware::class)->post('/collab', [HooksController::class, 'collabHook']);
});

