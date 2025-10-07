<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\SearchableModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Album
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $title
 * @property string|null $slug
 * @property string|null $description
 * @property string|null $genres
 * @property int $artist_id
 * @property int|null $year
 * @property int|null $song_count
 * @property bool $has_explicit_lyrics
 * @package App\Models\Base
 * @mixin IdeHelperAlbum
 */
class Album extends SearchableModel
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
    const ARTIST_ID = 'artist_id';
    const YEAR = 'year';
    const SONG_COUNT = 'song_count';
    const HAS_EXPLICIT_LYRICS = 'has_explicit_lyrics';
    protected $table = 'albums';

    protected $casts = [
        self::ID => 'int',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::ARTIST_ID => 'int',
        self::YEAR => 'int',
        self::SONG_COUNT => 'int',
        self::HAS_EXPLICIT_LYRICS => 'bool'
    ];
}
