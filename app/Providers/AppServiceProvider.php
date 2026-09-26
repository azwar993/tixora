<?php

namespace App\Providers;

use App\Support\ProtectedDatabaseCommandGuard;
use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(ArtisanStarting::class, function (ArtisanStarting $event): void {
            ProtectedDatabaseCommandGuard::assertAllowed(
                $event->artisan->getName(),
                config('database.connections.' . config('database.default') . '.database'),
                env('TIXORA_EMERGENCY_OVERRIDE')
            );
        });
    }
}
