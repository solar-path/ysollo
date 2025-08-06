<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return Inertia::render('welcome', [
            'workspace' => [
                'name' => tenant('workspace_name'),
                'slug' => tenant('slug'),
            ]
        ]);
    })->name('tenant.home');
    
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('dashboard', function () {
            return Inertia::render('dashboard', [
                'workspace' => [
                    'name' => tenant('workspace_name'),
                    'slug' => tenant('slug'),
                ]
            ]);
        })->name('tenant.dashboard');
    });
    
    require __DIR__.'/auth.php';
    require __DIR__.'/settings.php';
});
