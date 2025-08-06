<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {


        Route::get('/', function () {
            return Inertia::render('welcome');
        })->name('home');

        Route::middleware(['auth', 'verified'])->group(function () {
            Route::get('dashboard', function () {
                return redirect()->route('workspaces.index');
            })->name('dashboard');
            
            Route::get('workspaces', [App\Http\Controllers\WorkspaceController::class, 'index'])->name('workspaces.index');
            Route::get('workspaces/{tenant}', [App\Http\Controllers\WorkspaceController::class, 'show'])->name('workspaces.show');
            Route::post('workspaces/{tenant}/switch', [App\Http\Controllers\WorkspaceController::class, 'switchWorkspace'])->name('workspaces.switch');
        });

    });
}


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
