<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class RoleNames extends Enum implements LocalizedEnum
{
    const ADMIN = 'Администратор';
}
