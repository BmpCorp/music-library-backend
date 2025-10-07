<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Base\ApiController;
use App\Models\Artist;
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
    public function show(string $id)
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
}
