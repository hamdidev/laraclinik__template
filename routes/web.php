<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\Socialite\ProviderCallbackController;
use App\Http\Controllers\Socialite\ProviderRedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// admin
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard.index');
})->middleware(['auth:admin', 'verified'])->name('admin.dashboard');
// Route::middleware('auth:admin')->get('/admin/dashboard')->name('admin.dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// socialite
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
    ->name('social.callback');
Route::get('/auth/username', [SocialAuthController::class, 'showUsernameForm'])
    ->name('social.username.form');

Route::post('/auth/username', [SocialAuthController::class, 'handleUsername'])
    ->name('social.username.store');

Route::post('/auth/check-username', [SocialAuthController::class, 'checkUsername'])
    ->name('social.check.username');


require __DIR__ . '/auth.php';
