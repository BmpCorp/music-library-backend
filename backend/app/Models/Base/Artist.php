<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\SearchableModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Artist
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $title
 * @property string|null $slug
 * @property string|null $description
 * @property string|null $genres
 * @property int|null $country_id
 * @package App\Models\Base
 * @mixin IdeHelperArtist
 */
class Artist extends SearchableModel
{
    use SoftDeletes;
    const ID = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
    const TITLE = 'title';
    const SLUG = 'slug';
    const DESCRIPTION = 'description';
    const GENRES = 'genres';
    const COUNTRY_ID = 'country_id';
    protected $table = 'artists';

    protected $casts = [
        self::ID => 'int',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::COUNTRY_ID => 'int'
    ];
}
