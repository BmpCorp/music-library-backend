<?php

namespace App\Listeners;

use App\Events\Base\AlbumEvent;
use App\Jobs\RecalculateArtistSongCount;

class UpdateArtistStats
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(AlbumEvent $event): void
    {
        RecalculateArtistSongCount::dispatch($event->artistId);
    }
}
