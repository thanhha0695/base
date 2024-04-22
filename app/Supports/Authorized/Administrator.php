<?php

namespace App\Supports\Authorized;


/**
 * Class Administrator
 *
 * @package App\Supports\Authorized
 */
class Administrator extends AbstractAuthor
{
    /**
     * is privilege
     *
     * @return bool
     */
    public function isPrivilege(): bool
    {
        return true;
    }
}
