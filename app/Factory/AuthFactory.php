<?php

namespace App\Factory;

use App\Supports\Authorized\Administrator;
use App\Supports\Authorized\AuthorInterface;
use App\Models\User as EloquentUser;
use App\Models\Role as EloquentRole;
use App\Supports\Authorized\Manager;

/**
 * Class AuthFactory
 *
 * @package App\Factory
 */
final class AuthFactory
{
    /**
     * @param EloquentUser $user
     * @param EloquentRole $role
     * @return AuthorInterface
     */
    public static function create(EloquentUser $user, EloquentRole $role): AuthorInterface
    {
        switch ($user->role_id) {
            case 1:
                return new Administrator($user, $role);
                break;
            default:
                return new Manager($user, $role);
        }
    }
}
