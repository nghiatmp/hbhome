<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SystemRole extends Enum
{
    const INACTIVE = 0;
    const MEMBER = 3;
    const LEADER = 7;
    const ADMIN = 15;
}
