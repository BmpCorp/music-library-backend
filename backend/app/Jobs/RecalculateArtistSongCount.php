<?php

namespace App\Jobs;

use App\Models\Album;
use App\Models\Artist;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class RecalculateArtistSongCount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $artistId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $artist = Artist::find($this->artistId);
        if (!$artist) {
            return;
        }

        $songCount = $artist->albums()->sum(Album::SONG_COUNT);
        $artist->update([Artist::TOTAL_SONG_COUNT => $songCount]);
    }
}
