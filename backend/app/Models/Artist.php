<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\Base\Artist as BaseArtist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperArtist
 */
class Artist extends BaseArtist
{
    use CrudTrait;
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
        self::COUNTRY_ID,
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function favoriteOfUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, UserFavoriteArtist::class);
    }

    public function scopeWhereFamilyFriendly(Builder $builder): void
    {
        $builder->whereDoesntHave('albums', function (Builder $query) {
            $query->where(Album::HAS_EXPLICIT_LYRICS, true);
        });
    }
}
