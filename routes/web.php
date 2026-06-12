<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Marketplace homepage (category sections).
Route::get('/', [HomeController::class, 'index'])->name('home');

// Search results.
Route::get('/search', SearchController::class)->name('search');

// Static pages.
Route::get('/about',   [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact',[PageController::class, 'sendContact'])->name('contact.send');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated user's own ads. Declared before the resource route so that
// "/my-ads" is not swallowed by the "ads/{ad}" wildcard.
Route::get('/my-ads', [AdController::class, 'myAds'])
    ->middleware('auth')
    ->name('ads.myAds');

// Saved / favourite ads.
Route::middleware('auth')->group(function () {
    Route::get('/favorites',           [FavoriteController::class, 'index'])->name('favorites');
    Route::post('/ads/{ad}/favorite',  [FavoriteController::class, 'toggle'])->name('ads.favorite');
});

// User profile.
Route::middleware('auth')->group(function () {
    Route::get('/profile',          [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Full ad resource: index, create, store, show, edit, update, destroy.
// Auth on the write actions is enforced inside AdController (HasMiddleware).
Route::resource('ads', AdController::class);
Route::patch('/ads/{ad}/sold', [AdController::class, 'markSold'])->name('ads.markSold');

/*
|--------------------------------------------------------------------------
| AJAX endpoints (cascading dropdowns)
|--------------------------------------------------------------------------
*/
Route::get('/api/categories/{category}/subcategories', [AdController::class, 'getSubcategories'])
    ->name('api.subcategories');

Route::get('/api/districts/{district}/cities', [AdController::class, 'getCities'])
    ->name('api.cities');
