<?php

namespace App\Models;

use App\Models\Base\Country as BaseCountry;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperCountry
 */
class Country extends BaseCountry
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
		self::CODE,
		self::NAME,
	];

    public function artists(): HasMany
    {
        return $this->hasMany(Artist::class);
    }
}
