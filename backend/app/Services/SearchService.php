<?php

namespace App\Services;

use App\Models\Artist;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    private const int MAX_SEARCH_RESULTS = 100;

    public const int ON_PAGE = 10;

    /**
     * Get paginated artist collection, performing search if necessary.
     */
    public function searchArtists(?string $query = null, int $page = 1): LengthAwarePaginator
    {
        if (empty($query)) {
            return Artist::paginate(perPage: self::ON_PAGE, page: $page);
        }

        $foundIds = Artist::search($query)->take(self::MAX_SEARCH_RESULTS)->keys();
        if ($foundIds->isEmpty()) {
            return new LengthAwarePaginator(null, 0, self::ON_PAGE, $page);
        }

        return Artist::whereIn(Artist::ID, $foundIds)
            ->orderByRaw('FIELD(id, ' . $foundIds->join(',') . ')')
            ->paginate(perPage: self::ON_PAGE, page: $page);
    }
}
