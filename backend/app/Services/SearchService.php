<?php

namespace App\Services;

use App\Models\SearchableModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    private const int MAX_SEARCH_RESULTS = 100;

    public const int ON_PAGE = 10;

    /**
     * Get eloquent collection of searchable model, avoiding explicit search if not needed
     * @param string $modelClass SearchableModel subclass string
     * @return Builder<SearchableModel>
     * @throws \InvalidArgumentException
     */
    public function search(string $modelClass, string $query = ''): Builder
    {
        if (!class_exists($modelClass) || !is_subclass_of($modelClass, SearchableModel::class)) {
            throw new \InvalidArgumentException("Class {$modelClass} does not exist or is not a searchable model.");
        }

        // Just return default sort if no query is provided
        if ($query === '') {
            return $modelClass::query();
        }

        // Otherwise, perform search using Scout and get ids from indexed data.
        // Passing an empty list into whereIn clause will cause an SQL error, so we should check for this case.
        $foundIds = $modelClass::search($query)->take(self::MAX_SEARCH_RESULTS)->keys();

        if ($foundIds->isEmpty()) {
            return $modelClass::voidResults();
        }

        return $modelClass::query()
            ->whereIn('id', $foundIds)
            ->orderByRaw('FIELD(id, ' . $foundIds->join(',') . ')');
    }

    public function paginate(Builder $query, ?int $page = 1): LengthAwarePaginator
    {
        return $query->paginate(perPage: self::ON_PAGE, page: $page);
    }
}
