<?php

namespace App\Models;

use App\Attributes\MediaLibraryCollectionAttribute;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\Base\Artist as BaseArtist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperArtist
 */
class Artist extends BaseArtist implements HasMedia
{
    use CrudTrait, InteractsWithMedia;

    public const LOGO = 'logo';

    /**
     * @var string[]
     */
    protected $hidden = [
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT,
        'media',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        self::TITLE,
        self::DESCRIPTION,
        self::GENRES,
        self::COUNTRY_ID,
        self::LOGO,
    ];

    protected $appends = [
        self::LOGO,
    ];

    protected $with = [
        'media',
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::LOGO)->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion(self::LOGO . '_resized')
            ->performOnCollections(self::LOGO)
            ->format('webp')
            ->fit(Fit::Contain, 512, 512);
    }

    protected function logo(): Attribute
    {
        return new MediaLibraryCollectionAttribute(
            model: $this,
            collection: self::LOGO,
            conversion: self::LOGO . '_resized',
            single: true,
        );
    }
}
