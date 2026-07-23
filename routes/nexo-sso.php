<?php

use App\Http\Controllers\Auth\NexoSsoController;
use Illuminate\Support\Facades\Route;

// Standalone mode: with SSO disabled these routes don't exist at all. (AC-CFG-1)
// The controller re-checks the flag per request as defense in depth.
if (! config('nexo-sso.enabled')) {
    return;
}

Route::middleware(['web', 'guest'])->group(function (): void {
    Route::get('/auth/nexo/redirect', [NexoSsoController::class, 'redirect'])->name('nexo-sso.redirect');
    Route::get('/auth/nexo/callback', [NexoSsoController::class, 'callback'])->name('nexo-sso.callback');
});
