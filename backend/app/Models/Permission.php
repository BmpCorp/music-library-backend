<?php

namespace App\Models;

use App\Enums\PermissionCode;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Permission extends \Backpack\PermissionManager\app\Models\Permission
{
    protected $appends = [
        'admin_name',
    ];

    protected function adminName(): Attribute
    {
        $names = PermissionCode::getNames();
        return Attribute::make(
            get: fn($value, array $attributes) => $names[$this->name] ?? $this->name,
        );
    }
}
