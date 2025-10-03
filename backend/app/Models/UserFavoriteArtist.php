<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\Base\UserFavoriteArtist as BaseUserArtistFavorite;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperUserFavoriteArtist
 */
class UserFavoriteArtist extends BaseUserArtistFavorite
{
    use CrudTrait;
    /**
     * @var string[]
     */
	protected $hidden = [
		self::CREATED_AT,
		self::UPDATED_AT,
	];

    /**
     * @var string[]
     */
	protected $fillable = [
		self::LAST_CHECKED_ALBUM_ID,
		self::LISTENING_NOW,
	];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}
