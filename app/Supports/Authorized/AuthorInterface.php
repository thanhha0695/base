<?php

namespace App\Supports\Authorized;

use App\Models\User;
use App\Models\User as EloquentUser;
use App\Models\Role as EloquentRole;

/**
 * Interface AuthorInterface
 *
 * @package App\Supports\Authorized
 */
interface AuthorInterface
{
    /**
     * AuthorInterface constructor.
     *
     * @param EloquentUser $user
     * @param EloquentRole|null $role
     */
    public function __construct(EloquentUser $user, EloquentRole $role);

    /**
     * get User id
     *
     * @return int
     */
    public function getUserId(): int;

    /**
     * get name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * get email
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * get username
     *
     * @return string
     */
    public function getUsername(): string;

    /**
     * check is privilege
     *
     * @return bool
     */
    public function isPrivilege(): bool;

    /**
     * check is manage
     *
     * @return bool
     */
    public function isManage(): bool;

    /**
     * get role id
     *
     * @return int
     */
    public function getRoleId(): int;

    /**
     * get role name
     *
     * @return mixed
     */
    public function getRoleName();

    /**
     * get user
     *
     * @return EloquentUser
     */
    public function getUser(): EloquentUser;

    /**
     * get role
     *
     * @return EloquentRole|null
     */
    public function getRole(): ?EloquentRole;
}
