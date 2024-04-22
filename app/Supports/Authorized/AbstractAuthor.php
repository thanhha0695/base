<?php

namespace App\Supports\Authorized;

use App\Models\User as EloquentUser;
use App\Models\Role as EloquentRole;

/**
 * Class AbstractAuthor
 *
 * @package App\Supports\Authorized
 */
abstract class AbstractAuthor implements AuthorInterface
{
    /**
     * @var EloquentUser
     */
    protected EloquentUser $user;

    /**
     * @var EloquentRole|null
     */
    protected ?EloquentRole $role;

    /**
     * AbstractAuthor constructor.
     *
     * @param EloquentUser $user
     * @param EloquentRole $role
     */
    public function __construct(EloquentUser $user, EloquentRole $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    /**
     * get user id
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user->id;
    }

    /**
     * get email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->user->email;
    }

    /**
     * get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->user->name;
    }

    /**
     * get username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->user->username;
    }

    /**
     * is privilege
     *
     * @return bool
     */
    public function isPrivilege(): bool
    {
        return false;
    }

    /**
     * is manage
     *
     * @return bool
     */
    public function isManage(): bool
    {
        return $this->user->id === $this->role->manage_id;
    }

    /**
     * get role id
     *
     * @return int
     */
    public function getRoleId(): int
    {
        return optional($this->role)->id;
    }

    /**
     * get role name
     *
     * @return mixed|null
     */
    public function getRoleName()
    {
        return optional($this->role)->name;
    }

    /**
     * @return EloquentUser
     */
    public function getUser(): EloquentUser
    {
        return $this->user;
    }

    /**
     * @return EloquentRole|null
     */
    public function getRole(): ?EloquentRole
    {
        return $this->role;
    }
}
