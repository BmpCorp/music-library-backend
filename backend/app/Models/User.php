<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\Base\User as BaseUser;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends BaseUser
{
    /**
     * @use HasFactory<UserFactory>
     */
    use CrudTrait, HasFactory, Notifiable, HasApiTokens, HasRoles, HasPermissions;

    public const PLAIN_PASSWORD = 'plain_password';

    /**
     * @var string[]
     */
	protected $hidden = [
		self::PASSWORD,
		self::REMEMBER_TOKEN,
		self::CREATED_AT,
		self::UPDATED_AT,
        self::DELETED_AT,
	];

    /**
     * @var string[]
     */
	protected $fillable = [
		self::NAME,
		self::EMAIL,
		self::EMAIL_VERIFIED_AT,
		self::PASSWORD,
        self::PLAIN_PASSWORD,
		self::REMEMBER_TOKEN,
	];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            self::PASSWORD => 'hashed',
        ];
    }

    public function favoriteArtists(): HasManyThrough
    {
        return $this->hasManyThrough(Artist::class, UserFavoriteArtist::class);
    }

    public function plainPassword(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? [
                self::PASSWORD => \Hash::make($value),
            ] : [],
        );
    }
}
