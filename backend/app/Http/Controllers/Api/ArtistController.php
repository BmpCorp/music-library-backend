<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Base\ApiController;
use App\Models\Artist;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;

class ArtistController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $query = $this->request->get('query');
        $page = $this->request->get('page', 1);

        $paginatedResult = (new SearchService)->searchArtists($query, $page);

        return $this->response->pagination($paginatedResult);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $artist = Artist::with('albums')->find($id);

        if (!$artist) {
            return $this->response->notFound();
        }

        return $this->response->item($artist);
    }
}
