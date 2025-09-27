<?php

namespace App\Models;

use App\Models\Base\Album as BaseAlbum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperAlbum
 */
class Album extends BaseAlbum
{
    /**
     * @var string[]
     */
	protected $hidden = [
		self::CREATED_AT,
		self::UPDATED_AT,
		self::DELETED_AT,
	];

    /**
     * @var string[]
     */
	protected $fillable = [
		self::TITLE,
		self::DESCRIPTION,
		self::GENRES,
		self::ARTIST_ID,
		self::YEAR,
		self::SONG_COUNT,
		self::HAS_EXPLICIT_LYRICS,
	];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }
}
