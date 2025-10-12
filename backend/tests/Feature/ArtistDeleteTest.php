<?php

namespace Feature;

use App\Models\Album;
use App\Models\Artist;
use App\Models\User;
use App\Models\UserFavoriteArtist;
use App\Services\LibraryService;
use Tests\TestCase;

class ArtistDeleteTest extends TestCase
{
    private User $user;
    private Artist $artist;
    private Album $album;

    private LibraryService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new LibraryService();

        $this->user = User::factory(1)->create()->first();
        $this->artist = Artist::factory(1)->create()->first();
        $this->album = Album::factory(1)->for($this->artist)->create()->first();

        UserFavoriteArtist::create([
            UserFavoriteArtist::USER_ID => $this->user->id,
            UserFavoriteArtist::ARTIST_ID => $this->artist->id,
            UserFavoriteArtist::LAST_CHECKED_ALBUM_ID => $this->album->id,
        ]);
    }

    protected function tearDown(): void
    {
        UserFavoriteArtist::whereUserId($this->user->id)->delete();
        $this->album->forceDelete();
        $this->artist->forceDelete();
        $this->user->forceDelete();

        parent::tearDown();
    }

    public function test_album_delete(): void
    {
        $result = $this->service->deleteAlbum($this->album->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted($this->album);
        $this->assertDatabaseHas(UserFavoriteArtist::class, [
            UserFavoriteArtist::ARTIST_ID => $this->artist->id,
            UserFavoriteArtist::LAST_CHECKED_ALBUM_ID => null,
        ]);
    }

    public function test_artist_delete(): void
    {
        $result = $this->service->deleteArtist($this->artist->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted($this->album);
        $this->assertSoftDeleted($this->artist);
        $this->assertDatabaseMissing(UserFavoriteArtist::class, [
            UserFavoriteArtist::ARTIST_ID => $this->artist->id,
        ]);
    }
}
