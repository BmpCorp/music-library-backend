<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Base\ApiController;
use App\Models\Album;
use App\Models\Artist;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;

class AlbumController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $query = $this->request->get('query', '');
        $page = $this->request->get('page', 1);

        $service = new SearchService();
        $builder = $service
            ->search(Album::class, $query)
            ->with('artist:' . implode(',', [Artist::ID, Artist::TITLE]));

        $result = $service->paginate($builder, $page);

        return $this->response->pagination($result);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $album = Album::with('artist')->find($id);

        if (!$album) {
            return $this->response->notFound();
        }

        return $this->response->item($album);
    }
}
