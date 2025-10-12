<?php

namespace App\Providers;

use App\Events\AlbumCreated;
use App\Events\AlbumDeleted;
use App\Events\AlbumUpdated;
use App\Listeners\UpdateArtistStats;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(AlbumCreated::class, UpdateArtistStats::class);
        Event::listen(AlbumUpdated::class, UpdateArtistStats::class);
        Event::listen(AlbumDeleted::class, UpdateArtistStats::class);
    }
}
