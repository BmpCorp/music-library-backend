<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * @mixin \Eloquent
 */
abstract class SearchableModel extends Model
{
    use Searchable;

    /**
     * Explicitly make query return empty result.
     * Useful in situations when some conditions should be met.
     */
    public function scopeVoidResults(Builder $builder): void
    {
        $builder->where('0 = 1');
    }
}
