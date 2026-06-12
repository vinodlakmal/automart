<?php

use App\Http\Controllers\AdController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home -> public ad listing.
Route::get('/', [AdController::class, 'index'])->name('home');

// Authenticated user's own ads. Declared before the resource route so that
// "/my-ads" is not swallowed by the "ads/{ad}" wildcard.
Route::get('/my-ads', [AdController::class, 'myAds'])
    ->middleware('auth')
    ->name('ads.myAds');

// Full ad resource: index, create, store, show, edit, update, destroy.
// Auth on the write actions is enforced inside AdController (HasMiddleware).
Route::resource('ads', AdController::class);

/*
|--------------------------------------------------------------------------
| AJAX endpoints (cascading dropdowns)
|--------------------------------------------------------------------------
*/
Route::get('/api/categories/{category}/subcategories', [AdController::class, 'getSubcategories'])
    ->name('api.subcategories');

Route::get('/api/districts/{district}/cities', [AdController::class, 'getCities'])
    ->name('api.cities');
