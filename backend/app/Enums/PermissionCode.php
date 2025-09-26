<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

final class PermissionCode extends Enum implements LocalizedEnum
{
    const FULL_ACCESS = 'full_access';

    const USERS = 'users';
    const ROLES = 'roles';

    const DICTIONARIES = 'dictionaries';

    const CONTENT = 'content';

    const FEEDBACK = 'feedback';

    const MAINTENANCE = 'maintenance';
}
