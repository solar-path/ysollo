<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


// Central application routes (no domain restriction needed - middleware handles tenancy)
Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::get('debug-info', function () {
    return response()->json([
        'central_domains' => config('tenancy.central_domains'),
        'current_domain' => request()->getHost(),
        'full_url' => request()->fullUrl(),
        'environment' => config('app.env'),
    ]);
})->name('debug');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return redirect()->route('workspaces.index');
    })->name('dashboard');
    
    Route::get('workspaces', [App\Http\Controllers\WorkspaceController::class, 'index'])->name('workspaces.index');
    Route::get('workspaces/{tenant}', [App\Http\Controllers\WorkspaceController::class, 'show'])->name('workspaces.show');
    Route::post('workspaces/{tenant}/switch', [App\Http\Controllers\WorkspaceController::class, 'switchWorkspace'])->name('workspaces.switch');
    
    // Development tools
    if (app()->environment('local')) {
        Route::get('dev/telescope', function () {
            return redirect('/telescope');
        })->name('dev.telescope');
        
        Route::get('dev', function () {
            return Inertia::render('dev-tools');
        })->name('dev.tools');
    }
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
