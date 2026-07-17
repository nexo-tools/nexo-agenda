<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->middleware('throttle:10,1');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:20,1');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:5,1')->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:5,1')->name('password.store');
});

Route::get('t/{token}', [BookingManagementController::class, 'show'])->name('booking.manage');

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::prefix('app')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('services', ServiceController::class)->except(['show']);
        Route::resource('professionals', ProfessionalController::class)->except(['show', 'create']);
        Route::post('professionals/{professional}/absences', [AbsenceController::class, 'store'])
            ->name('professionals.absences.store');
        Route::delete('absences/{absence}', [AbsenceController::class, 'destroy'])->name('absences.destroy');
    });
});

// Public booking pages — the slug catch-all must stay at the very end.
Route::scopeBindings()->group(function () {
    Route::get('{business:slug}/reservar/{service}', [PublicBookingController::class, 'professional'])->name('public.professional');
    Route::get('{business:slug}/reservar/{service}/horarios', [PublicBookingController::class, 'times'])->name('public.times');
    Route::get('{business:slug}/reservar/{service}/datos', [PublicBookingController::class, 'form'])->name('public.form');
    Route::post('{business:slug}/reservar/{service}', [PublicBookingController::class, 'store'])
        ->middleware('throttle:15,1')->name('public.store');
});
Route::get('{business:slug}', [PublicBookingController::class, 'business'])->name('public.business');
