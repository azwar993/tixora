<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CreatorActivationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EoDashboardController;
use App\Http\Controllers\EoEventController;
use App\Http\Controllers\EoSeatingController;
use App\Http\Controllers\EoTicketController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrValidationController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', [EventController::class, 'home'])->name('home');

/*
|--------------------------------------------------------------------------
| MIDTRANS WEBHOOK
|--------------------------------------------------------------------------
|
| Route ini sengaja TIDAK memakai middleware auth/admin.
| Midtrans harus bisa mengirim notification langsung ke Laravel.
|
*/

Route::post('/midtrans/notification', [
    MidtransWebhookController::class,
    'handle',
])->name('midtrans.notification');

/*
|--------------------------------------------------------------------------
| ADMIN - USERS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin/users')
    ->name('admin.users.')
    ->group(function () {

        Route::get('/', [UserController::class, 'index'])
            ->name('index');

        Route::patch('/{user}/role', [UserController::class, 'updateRole'])
            ->name('role');
    });

Route::middleware(['auth', 'eo'])
    ->prefix('eo/events/{event}/tickets')
    ->name('eo.events.tickets.')
    ->controller(EoTicketController::class)
    ->scopeBindings()
    ->group(function () {
        Route::get('/', 'index')->whereNumber('event')->name('index');
        Route::get('/create', 'create')->whereNumber('event')->name('create');
        Route::post('/', 'store')->whereNumber('event')->name('store');
        Route::get('/{ticket}/edit', 'edit')->whereNumber('event')->whereNumber('ticket')->name('edit');
        Route::match(['put', 'patch'], '/{ticket}', 'update')->whereNumber('event')->whereNumber('ticket')->name('update');
        Route::delete('/{ticket}', 'destroy')->whereNumber('event')->whereNumber('ticket')->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| ADMIN - DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', [EventController::class, 'index'])
    ->middleware(['auth', 'admin'])
    ->name('admin.dashboard');

Route::get('/eo/dashboard', [EoDashboardController::class, 'index'])
    ->middleware(['auth', 'eo'])
    ->name('eo.dashboard');

Route::middleware(['auth', 'eo'])
    ->prefix('eo/events')
    ->name('eo.events.')
    ->controller(EoEventController::class)
    ->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/', 'index')->name('index');
        Route::get('/{event}/edit', 'edit')->whereNumber('event')->name('edit');
        Route::put('/{event}', 'update')->whereNumber('event')->name('update');
        Route::patch('/{event}', 'update')->whereNumber('event')->name('update.patch');
        Route::get('/{event}/preview', 'preview')->whereNumber('event')->name('preview');
        Route::post('/{event}/submit', 'submit')->whereNumber('event')->name('submit');
    });

Route::middleware(['auth', 'eo'])
    ->prefix('eo/events/{event}')
    ->name('eo.events.')
    ->controller(EoSeatingController::class)
    ->group(function () {
        Route::get('/seating', 'index')->whereNumber('event')->name('seating.index');
        Route::post('/sections', 'storeSection')->whereNumber('event')->name('sections.store');
        Route::match(['put', 'patch'], '/sections/{section}', 'updateSection')
            ->whereNumber('event')->whereNumber('section')->name('sections.update');
        Route::delete('/sections/{section}', 'destroySection')
            ->whereNumber('event')->whereNumber('section')->name('sections.destroy');
        Route::post('/sections/{section}/seats/generate', 'generateSeats')
            ->whereNumber('event')->whereNumber('section')->name('sections.seats.generate');
        Route::delete('/sections/{section}/seats/{seat}', 'destroySeat')
            ->whereNumber('event')->whereNumber('section')->whereNumber('seat')->name('sections.seats.destroy');
    });

/*
|--------------------------------------------------------------------------
| ADMIN - ARTICLES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin/articles')
    ->name('admin.articles.')
    ->group(function () {
        Route::get('/create', [ArticleController::class, 'create'])
            ->name('create');

        Route::get('/', [ArticleController::class, 'index'])
            ->name('index');

        Route::post('/', [ArticleController::class, 'store'])
            ->name('store');

        Route::get('/{article}/edit', [ArticleController::class, 'edit'])
            ->name('edit');

        Route::put('/{article}', [ArticleController::class, 'update'])
            ->name('update');

        Route::patch('/{article}', [ArticleController::class, 'update'])
            ->name('update');

        Route::post('/{article}/publish', [ArticleController::class, 'publish'])
            ->name('publish');

        Route::delete('/{article}', [ArticleController::class, 'destroy'])
            ->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| ADMIN - ORDERS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin/orders')
    ->name('admin.orders.')
    ->group(function () {

        Route::get('/', [OrderController::class, 'index'])
            ->name('index');

        Route::get('/{order}', [OrderController::class, 'show'])
            ->name('show');
    });

/*
|--------------------------------------------------------------------------
| ADMIN - PAYMENTS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin/payments')
    ->name('admin.payments.')
    ->group(function () {

        Route::get('/', [PaymentController::class, 'index'])
            ->name('index');

        Route::get('/{payment}', [PaymentController::class, 'show'])
            ->name('show');
    });

/*
|--------------------------------------------------------------------------
| ADMIN - QR VALIDATION
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin/qr-validation')
    ->name('admin.qr-validation.')
    ->group(function () {

        Route::get('/', [QrValidationController::class, 'index'])
            ->name('index');

        Route::post(
            '/validate',
            [QrValidationController::class, 'validateTicket']
        )->name('validate');

        Route::post(
            '/{ticketInstance}/check-in',
            [QrValidationController::class, 'checkIn']
        )->name('check-in');
    });

/*
|--------------------------------------------------------------------------
| ADMIN - SEATS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin/seats')
    ->name('admin.seats.')
    ->group(function () {

        Route::get('/', [SeatController::class, 'index'])
            ->name('index');

        Route::post('/generate', [SeatController::class, 'generate'])
            ->name('generate');

        Route::post('/', [SeatController::class, 'store'])
            ->name('store');

        Route::put('/{seat}', [SeatController::class, 'update'])
            ->name('update');

        Route::delete('/{seat}', [SeatController::class, 'destroy'])
            ->name('destroy');

        Route::put(
            '/{event}/type',
            [SeatController::class, 'updateType']
        )->name('type.update');
    });

/*
|--------------------------------------------------------------------------
| ADMIN - EVENTS
|--------------------------------------------------------------------------
*/

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
| ADMIN - CATEGORIES
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
| ADMIN - TICKETS
|--------------------------------------------------------------------------
*/

Route::post('/admin/tickets', [TicketController::class, 'store'])
    ->middleware(['auth', 'admin'])
    ->name('admin.tickets.store');

Route::put('/admin/tickets/{ticket}', [TicketController::class, 'update'])
    ->middleware(['auth', 'admin'])
    ->name('admin.tickets.update');

Route::delete('/admin/tickets/{ticket}', [TicketController::class, 'destroy'])
    ->middleware(['auth', 'admin'])
    ->name('admin.tickets.destroy');

/*
|--------------------------------------------------------------------------
| PUBLIC - ARTICLES
|--------------------------------------------------------------------------
*/

Route::get('/articles', [ArticleController::class, 'publicIndex'])
    ->name('articles.index');

Route::get('/articles/{article}', [ArticleController::class, 'show'])
    ->name('articles.show');

/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

Route::get(
    '/events/{event}',
    [EventController::class, 'show']
)->name('events.show');

Route::middleware(['auth', 'verified'])
    ->group(function () {

        /*
        |----------------------------------------------------------------------
        | CHECKOUT
        |----------------------------------------------------------------------
        */

        Route::post(
            '/checkout',
            [CheckoutController::class, 'store']
        )->name('checkout.store');

        /*
        |----------------------------------------------------------------------
        | SIMULATE PAYMENT
        |----------------------------------------------------------------------
        |
        | Untuk testing lokal.
        | Nanti jalur utama akan memakai Midtrans webhook.
        |
        */

        Route::post(
            '/checkout/{order}/simulate-payment',
            [CheckoutController::class, 'simulatePayment']
        )->name('checkout.simulate-payment');

        /*
        |----------------------------------------------------------------------
        | USER DASHBOARD
        |----------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [EventController::class, 'userDashboard']
        )->name('dashboard');
    });

Route::post('/creator/activate', [CreatorActivationController::class, 'activate'])
    ->middleware('auth')
    ->name('creator.activate');

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
