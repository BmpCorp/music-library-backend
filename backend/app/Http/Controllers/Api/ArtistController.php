<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Base\ApiController;
use App\Models\Album;
use App\Models\Artist;
use App\Models\UserFavoriteArtist;
use App\Services\SearchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ArtistController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $input = $this->request->validate([
            'query' => 'sometimes|string|max:255',
            'page' => 'sometimes|integer|min:1',
            'no_explicit' => 'sometimes|in:0,1',
        ]);

        $service = new SearchService();
        $builder = $service
            ->search(Artist::class, $input['query'] ?? '')
            ->when($input['no_explicit'] ?? false, function (Builder $query) {
                $query->whereFamilyFriendly();
            });

        $result = $service->paginate($builder, $input['page'] ?? 1);

        return $this->response->pagination($result);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $artist = Artist::query()
            ->with(['albums', 'country'])
            ->withCount('favoriteOfUsers')
            ->find($id);

        if (!$artist) {
            return $this->response->notFound();
        }

        return $this->response->item($artist);
    }

    public function toggleFavorite(string $id): JsonResponse
    {
        if (Artist::whereId($id)->doesntExist()) {
            return $this->response->notFound();
        }

        $user = $this->user();
        $entry = UserFavoriteArtist::query()
            ->where(UserFavoriteArtist::USER_ID, $user->id)
            ->where(UserFavoriteArtist::ARTIST_ID, $id)
            ->first();

        if ($entry) {
            $entry->delete();
            return $this->response->success([], 'Artist removed from favorites');
        }

        UserFavoriteArtist::create([
            UserFavoriteArtist::USER_ID => $user->id,
            UserFavoriteArtist::ARTIST_ID => $id,
        ]);
        return $this->response->success([], 'Artist added to favorites');
    }

    public function toggleListeningNow(string $id): JsonResponse
    {
        $entry = UserFavoriteArtist::query()
            ->where(UserFavoriteArtist::ARTIST_ID, $id)
            ->where(UserFavoriteArtist::USER_ID, $this->user()->id)
            ->first();

        if (!$entry) {
            return $this->response->notFound('You don\'t have this artist in your favorites.');
        }

        $entry->update([
            UserFavoriteArtist::LISTENING_NOW => !$entry->listening_now,
        ]);

        return $this->response->success(
            [],
            'You\'re now' . ($entry->listening_now ? '' : ' not') . ' listening to this artist.',
        );
    }

    public function setLastCheckedAlbum(string $artistId, string $albumId): JsonResponse
    {
        $entry = UserFavoriteArtist::query()
            ->where(UserFavoriteArtist::ARTIST_ID, $artistId)
            ->where(UserFavoriteArtist::USER_ID, $this->user()->id)
            ->first();

        if (!$entry) {
            return $this->response->notFound('You don\'t have this artist in your favorites.');
        }

        if (Album::whereId($albumId)->where(Album::ARTIST_ID, $artistId)->doesntExist()) {
            return $this->response->notFound('Album not found for this artist.');
        }

        $entry->update([
            UserFavoriteArtist::LAST_CHECKED_ALBUM_ID => $albumId,
        ]);

        return $this->response->success([], 'Last checked album updated.');
    }
}
