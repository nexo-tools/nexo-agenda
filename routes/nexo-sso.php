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
    Route::get('/auth/nexo/silent', [NexoSsoController::class, 'silent'])->name('nexo-sso.silent');
    Route::get('/auth/nexo/callback', [NexoSsoController::class, 'callback'])->name('nexo-sso.callback');
});

// RP-initiated (central) logout. Point this tool's logout button here when SSO
// is enabled: it ends the local session AND the Nexo ID session. POST + CSRF,
// mirroring the framework's own logout. (AC-LOGOUT-1)
Route::middleware(['web', 'auth'])->group(function (): void {
    Route::post('/auth/nexo/logout', [NexoSsoController::class, 'logout'])->name('nexo-sso.logout');
});
