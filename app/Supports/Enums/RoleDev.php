<?php

namespace App\Supports\Enums;

/**
 * Class RoleDev
 * @package App\Supports\Enums
 */
class RoleDev
{
    public const ADMINISTRATOR = 1;

    public static function getListAdmin()
    {
        return [
            self::ADMINISTRATOR
        ];
    }
}
