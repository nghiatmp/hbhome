<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ProjectRole extends Enum
{
    const DEVELOPER = 1;
    const TESTER = 2;
    const BA = 3;
    const COMTOR = 4;
    const ACCOUNT = 5;
    const QA = 6;
    const OTHERS = 7;
    const ADMIN = 15;
}
