<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', [EventController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard');

Route::post('/admin/events', [EventController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('admin.events.store');

Route::put('/admin/events/{event}', [EventController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.events.update');

Route::delete('/admin/events/{event}', [EventController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.events.destroy');

Route::post('/admin/events/{event}/approve', [EventController::class, 'approve'])
    ->middleware(['auth', 'admin'])
    ->name('admin.events.approve');

Route::post('/admin/events/{event}/reject', [EventController::class, 'reject'])
    ->middleware(['auth', 'admin'])
    ->name('admin.events.reject');


/*
|--------------------------------------------------------------------------
| CATEGORY
|--------------------------------------------------------------------------
*/

Route::post('/admin/categories', [CategoryController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.store');

Route::put('/admin/categories/{category}', [CategoryController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.update');

Route::delete('/admin/categories/{category}', [CategoryController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.categories.destroy');


/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

require __DIR__.'/auth.php';