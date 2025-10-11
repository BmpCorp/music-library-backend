<?php

namespace App\Models;

use App\Attributes\MediaLibraryCollectionAttribute;
use App\Models\Base\Album as BaseAlbum;
use App\Utilities\SearchableString;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Database\Factories\AlbumFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperAlbum
 */
class Album extends BaseAlbum implements HasMedia
{
    /**
     * @use HasFactory<AlbumFactory>
     */
    use CrudTrait, HasFactory, InteractsWithMedia, Sluggable;

    public const COVER = 'cover';

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
        self::SLUG,
        self::DESCRIPTION,
        self::GENRES,
        self::ARTIST_ID,
        self::YEAR,
        self::SONG_COUNT,
        self::HAS_EXPLICIT_LYRICS,
        self::COVER,
    ];

    protected $appends = [
        self::COVER,
    ];

    protected $with = [
        'media',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::COVER)->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion(self::COVER . '_resized')
            ->performOnCollections(self::COVER)
            ->format('webp')
            ->fit(Fit::Contain, 512, 512);
    }

    protected function cover(): Attribute
    {
        return new MediaLibraryCollectionAttribute(
            model: $this,
            collection: self::COVER,
            conversion: self::COVER . '_resized',
            single: true,
        );
    }

    public function sluggable(): array
    {
        return [
            self::SLUG => [
                'source' => self::TITLE,
            ],
        ];
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            ...SearchableString::make('title', $this->title),
            ...SearchableString::make('genres', $this->genres),
            ...SearchableString::make('artist', $this->artist->title),
        ];
    }
}
