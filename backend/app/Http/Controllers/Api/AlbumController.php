<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Base\ApiController;
use App\Models\Album;
use App\Models\Artist;
use App\Services\SearchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Albums",
 *     description="Operations related to albums"
 * )
 */
class AlbumController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/album",
     *     summary="Get a paginated list of albums",
     *     tags={"Albums", "List"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search query for album/artist names or music genres",
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
     *         description="Filter out albums with explicit content (1 for true, 0 for false)",
     *         required=false,
     *         @OA\Schema(type="integer", enum={0, 1})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of albums retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="items", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Album Title"),
     *                         @OA\Property(property="description", type="string", example="First album after reunition"),
     *                         @OA\Property(property="genres", type="string", example="Pop, Rock"),
     *                         @OA\Property(property="year", type="integer", example=2023),
     *                         @OA\Property(property="song_count", type="integer", example=9),
     *                         @OA\Property(property="has_explicit_lyrics", type="boolean", example=true),
     *                         @OA\Property(property="cover", type="string", example="https://example.com/album_cover.png"),
     *                         @OA\Property(property="artist", type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="title", type="string", example="Artist Title"),
     *                             @OA\Property(property="logo", type="string", example="https://example.com/logo.png"),
     *                         )
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
            ->search(Album::class, $input['query'] ?? '')
            ->when($input['no_explicit'] ?? false, function (Builder $query) {
                $query->where(Album::HAS_EXPLICIT_LYRICS, false);
            })
            ->with('artist:' . implode(',', [Artist::ID, Artist::TITLE]));

        $result = $service->paginate($builder, $input['page'] ?? 1);

        return $this->response->pagination($result);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/album/{id}",
     *     summary="Get a single album by ID",
     *     tags={"Albums"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the album to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Album data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="item", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Album Title"),
     *                     @OA\Property(property="description", type="string", example="First album after reunition"),
     *                     @OA\Property(property="genres", type="string", example="Pop, Rock"),
     *                     @OA\Property(property="year", type="integer", example=2023),
     *                     @OA\Property(property="song_count", type="integer", example=9),
     *                     @OA\Property(property="has_explicit_lyrics", type="boolean", example=true),
     *                     @OA\Property(property="cover", type="string", example="https://example.com/album_cover.png"),
     *                     @OA\Property(property="artist", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Artist Title"),
     *                         @OA\Property(property="description", type="string", example="This Band Rocks!"),
     *                         @OA\Property(property="genres", type="string", example="Pop, Rock"),
     *                         @OA\Property(property="country_id", type="integer", example=15),
     *                         @OA\Property(property="logo", type="string", example="https://example.com/logo.png"),
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $album = Album::with('artist')->find($id);

        if (!$album) {
            return $this->response->notFound();
        }

        return $this->response->item($album);
    }
}
