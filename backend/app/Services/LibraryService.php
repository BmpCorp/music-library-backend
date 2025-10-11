<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Artist;
use App\Models\UserFavoriteArtist;

class LibraryService
{
    /**
     * Delete artist and all data linked to it.
     */
    public function deleteArtist(int $artistId): bool
    {
        $artist = Artist::findOrFail($artistId);

        UserFavoriteArtist::whereArtistId($artistId)->delete();
        $artist->albums()->delete();

        return $artist->delete();
    }

    /**
     * Delete album and all data linked to it.
     */
    public function deleteAlbum(int $albumId): bool
    {
        $album = Album::findOrFail($albumId);

        UserFavoriteArtist::whereLastCheckedAlbumId($albumId)->update([
            UserFavoriteArtist::LAST_CHECKED_ALBUM_ID => null,
        ]);

        return $album->delete();
    }
}
