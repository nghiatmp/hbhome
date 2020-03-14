<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ActivityLogTypes extends Enum
{
    const PROJECT = 1;
    const PHASE = 2;
    const RESOURCE = 3;
    const MEMBER = 4;
}
