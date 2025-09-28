<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserFavoriteArtist
 *
 * @property int $user_id
 * @property int $artist_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $last_checked_album
 * @property bool $listening_now
 * @package App\Models\Base
 * @mixin IdeHelperUserFavoriteArtist
 */
class UserFavoriteArtist extends Model
{
    const USER_ID = 'user_id';
    const ARTIST_ID = 'artist_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const LAST_CHECKED_ALBUM = 'last_checked_album';
    const LISTENING_NOW = 'listening_now';
    protected $table = 'user_favorite_artists';
    public $incrementing = false;

    protected $casts = [
        self::USER_ID => 'int',
        self::ARTIST_ID => 'int',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::LAST_CHECKED_ALBUM => 'int',
        self::LISTENING_NOW => 'bool'
    ];
}
