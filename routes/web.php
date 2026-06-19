<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Redirect root ─────────────────────────────────────────────────────────
Route::get('/', fn () => redirect('/dashboard'));

// ── Auth (guests only) ────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ── Logout ────────────────────────────────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Protected routes ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard (Livewire full-page component)
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Instagram OAuth
    Route::get('/auth/instagram',          [SocialAuthController::class, 'redirectToInstagram'])->name('auth.instagram');
    Route::get('/auth/instagram/callback', [SocialAuthController::class, 'handleInstagramCallback'])->name('auth.instagram.callback');

    // Snapchat OAuth
    Route::get('/auth/snapchat',           [SocialAuthController::class, 'redirectToSnapchat'])->name('auth.snapchat');
    Route::get('/auth/snapchat/callback',  [SocialAuthController::class, 'handleSnapchatCallback'])->name('auth.snapchat.callback');
});
