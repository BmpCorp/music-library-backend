<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Base\ApiController;
use App\Models\Album;
use App\Models\Artist;
use App\Models\UserFavoriteArtist;
use App\Services\SearchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Artists",
 *     description="Operations related to artists"
 * )
 */
class ArtistController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/artist",
     *     summary="Get a paginated list of artists",
     *     tags={"Artists", "List"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search query for artist names or music genres",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", format="int64", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="no_explicit",
     *         in="query",
     *         description="Filter out artists with explicit content (1 for true, 0 for false)",
     *         required=false,
     *         @OA\Schema(type="integer", enum={0, 1})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of artists retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="items", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Artist Name"),
     *                         @OA\Property(property="description", type="string", example="This Band Rocks!"),
     *                         @OA\Property(property="genres", type="string", example="Pop, Rock"),
     *                         @OA\Property(property="country_id", type="integer", example=15),
     *                         @OA\Property(property="logo", type="string", example="https://example.com/logo.png")
     *                     )
     *                 ),
     *                 @OA\Property(property="pagination", type="object",
     *                     @OA\Property(property="total", type="integer", example=100),
     *                     @OA\Property(property="page", type="integer", example=1),
     *                     @OA\Property(property="pages", type="integer", example=10),
     *                 )
     *             )
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/v1/artist/{id}",
     *     summary="Get a single artist by ID",
     *     tags={"Artists"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the artist to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Artist data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="item", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Artist Name"),
     *                     @OA\Property(property="description", type="string", example="This Band Rocks!"),
     *                     @OA\Property(property="genres", type="string", example="Pop, Rock"),
     *                     @OA\Property(property="logo", type="string", example="https://example.com/logo.png"),
     *                     @OA\Property(property="favorite_of_users_count", type="integer", example=12),
     *                     @OA\Property(property="albums", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=101),
     *                             @OA\Property(property="title", type="string", example="Album Title"),
     *                             @OA\Property(property="description", type="string", example="First album after reunition"),
     *                             @OA\Property(property="genres", type="string", example="Pop, Rock"),
     *                             @OA\Property(property="year", type="integer", example=2023),
     *                             @OA\Property(property="song_count", type="integer", example=9),
     *                             @OA\Property(property="has_explicit_lyrics", type="boolean", example=true),
     *                             @OA\Property(property="cover", type="string", example="https://example.com/album_cover.png")
     *                         )
     *                     ),
     *                     @OA\Property(property="country", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="code", type="string", example="RU"),
     *                         @OA\Property(property="title", type="string", example="Russia")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
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

    /**
     * @OA\Post(
     *     path="/api/v1/artist/{id}/favorite",
     *     summary="Toggle an artist as favorite for the authenticated user",
     *     tags={"Artists"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the artist to toggle as favorite",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Artist favorite status toggled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="description", type="string", example="Artist added to favorites")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Artist not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="description", type="string", example="Not found")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/artist/{id}/listening-now",
     *     summary="Toggle listening now status for a favorite artist",
     *     tags={"Artists"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the artist to toggle listening now status",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listening now status toggled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="description", type="string", example="You're now listening to this artist.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Artist not found in favorites",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="description", type="string", example="You don't have this artist in your favorites.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/artist/{artistId}/check-album/{albumId}",
     *     summary="Set the last checked album for a favorite artist",
     *     tags={"Artists"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="artistId",
     *         in="path",
     *         description="ID of the artist",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Parameter(
     *         name="albumId",
     *         in="path",
     *         description="ID of the album to set as last checked",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Last checked album updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="description", type="string", example="Last checked album updated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Artist not found in favorites or album not found for this artist",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="description", type="string", example="You don't have this artist in your favorites.")
     *         )
     *     )
     * )
     */
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
