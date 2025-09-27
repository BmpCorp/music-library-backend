<?php

namespace App\Models;

use App\Models\Base\Country as BaseCountry;

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
}
