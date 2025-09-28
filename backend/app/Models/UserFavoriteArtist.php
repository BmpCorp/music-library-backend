<?php

namespace App\Models;

use App\Models\Base\UserFavoriteArtist as BaseUserArtistFavorite;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperUserFavoriteArtist
 */
class UserFavoriteArtist extends BaseUserArtistFavorite
{
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
		self::LAST_CHECKED_ALBUM,
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
