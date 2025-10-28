<?php

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Artist;
use App\Models\User;
use App\Models\UserFavoriteArtist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFavoritesTest extends TestCase
{
    private User $user;
    private Artist $artist;
    private Artist $anotherArtist;
    private Album $album;
    private Album $anotherAlbum;

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory(1)->create()->first();
        $this->artist = Artist::factory(1)->create()->first();
        $this->album = Album::factory(1)->for($this->artist)->create()->first();
        $this->anotherArtist = Artist::factory(1)->create()->first();
        $this->anotherAlbum = Album::factory(1)->for($this->anotherArtist)->create()->first();
    }

    private function setDefaultEntry(): void
    {
        UserFavoriteArtist::updateOrCreate([
            UserFavoriteArtist::USER_ID => $this->user->id,
            UserFavoriteArtist::ARTIST_ID => $this->artist->id,
        ], [
            UserFavoriteArtist::LISTENING_NOW => false,
            UserFavoriteArtist::LAST_CHECKED_ALBUM_ID => null,
        ]);
    }

    private function removeEntry(): void
    {
        UserFavoriteArtist::query()
            ->where(UserFavoriteArtist::USER_ID, $this->user->id)
            ->where(UserFavoriteArtist::ARTIST_ID, $this->artist->id)
            ->delete();
    }

    public function test_guest_access(): void
    {
        $response = $this->actingAsGuest()->postJson("/api/v1/artist/{$this->artist->id}/favorite");
        $response->assertUnauthorized();

        $response = $this->actingAsGuest()->postJson("/api/v1/artist/{$this->artist->id}/listening-now");
        $response->assertUnauthorized();

        $response = $this->actingAsGuest()->postJson("/api/v1/artist/{$this->artist->id}/check-album/{$this->album->id}");
        $response->assertUnauthorized();
    }

    public function test_nonexistent_artist(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/artist/99999999/favorite');
        $response->assertNotFound();
    }

    public function test_toggle_favorite(): void
    {
        $response = $this->actingAs($this->user)->postJson("/api/v1/artist/{$this->artist->id}/favorite");
        $response->assertOk();
        $this->assertDatabaseHas(UserFavoriteArtist::class, [
            UserFavoriteArtist::USER_ID => $this->user->id,
            UserFavoriteArtist::ARTIST_ID => $this->artist->id,
        ]);

        $response = $this->actingAs($this->user)->postJson("/api/v1/artist/{$this->artist->id}/favorite");
        $response->assertOk();
        $this->assertDatabaseMissing(UserFavoriteArtist::class, [
            UserFavoriteArtist::USER_ID => $this->user->id,
            UserFavoriteArtist::ARTIST_ID => $this->artist->id,
        ]);
    }

    public function test_toggle_listening_now(): void
    {
        $this->removeEntry();

        $response = $this->actingAs($this->user)->postJson("/api/v1/artist/{$this->artist->id}/listening-now");
        $response->assertForbidden();

        $this->setDefaultEntry();

        $response = $this->actingAs($this->user)->postJson("/api/v1/artist/{$this->artist->id}/listening-now");
        $response->assertOk();
        $this->assertDatabaseHas(UserFavoriteArtist::class, [
            UserFavoriteArtist::USER_ID => $this->user->id,
            UserFavoriteArtist::ARTIST_ID => $this->artist->id,
            UserFavoriteArtist::LISTENING_NOW => true,
        ]);

        $response = $this->actingAs($this->user)->postJson("/api/v1/artist/{$this->artist->id}/listening-now");
        $response->assertOk();
        $this->assertDatabaseHas(UserFavoriteArtist::class, [
            UserFavoriteArtist::USER_ID => $this->user->id,
            UserFavoriteArtist::ARTIST_ID => $this->artist->id,
            UserFavoriteArtist::LISTENING_NOW => false,
        ]);
    }

    public function test_set_last_checked_album(): void
    {
        $this->removeEntry();

        $response = $this->actingAs($this->user)->postJson("/api/v1/artist/{$this->artist->id}/check-album/{$this->album->id}");
        $response->assertForbidden();

        $this->setDefaultEntry();

        $response = $this->actingAs($this->user)->postJson("/api/v1/artist/{$this->artist->id}/check-album/{$this->album->id}");
        $response->assertOk();
        $this->assertDatabaseHas(UserFavoriteArtist::class, [
            UserFavoriteArtist::USER_ID => $this->user->id,
            UserFavoriteArtist::ARTIST_ID => $this->artist->id,
            UserFavoriteArtist::LAST_CHECKED_ALBUM_ID => $this->album->id,
        ]);

        $response = $this->actingAs($this->user)->postJson("/api/v1/artist/{$this->artist->id}/check-album/{$this->anotherAlbum->id}");
        $response->assertNotFound();
    }
}
