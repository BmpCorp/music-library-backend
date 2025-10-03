<?php

namespace App\Utilities;

use App\Enums\PermissionCode;

final class AdminField
{
    public const WRAPPER_QUARTER = [
        'class' => 'form-group col-sm-3 mb-3',
    ];
    public const WRAPPER_THIRD = [
        'class' => 'form-group col-sm-4 mb-3',
    ];
    public const WRAPPER_HALF = [
        'class' => 'form-group col-sm-6 mb-3',
    ];
    public const WRAPPER_TWO_THIRDS = [
        'class' => 'form-group col-sm-8 mb-3',
    ];
    public const WRAPPER_THREE_QUARTERS = [
        'class' => 'form-group col-sm-9 mb-3',
    ];

    public const TEXTAREA_ROWS_3 = [
        'rows' => 3,
    ];
    public const INPUT_MAX_LENGTH_255 = [
        'maxlength' => 255,
    ];
    public const INPUT_READONLY = [
        'readonly' => true,
    ];
    public const INPUT_DISABLED = [
        'disabled' => true,
    ];

    public static function makeUserWrapper(string $userIdField = 'user_id'): array
    {
        return backpack_user()->canAny([PermissionCode::FULL_ACCESS, PermissionCode::USERS]) ? [
            'href' => fn ($crud, $column, $entry) => ($entry->{$userIdField} ? backpack_url('user/'.$entry->{$userIdField}.'/edit') : '#'),
            'target' => '_blank',
        ] : [];
    }
}
