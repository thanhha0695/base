<?php

namespace App\Services;

use App\Models\User;
use App\Supports\Authorized\AuthorInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserService
 *
 * @package App\Services
 */
class UserService
{
    /**
     * @var RoleService
     */
    public RoleService $roleService;

    /**
     * UserService constructor.
     *
     * @param RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * fetch
     *
     * @param AuthorInterface $author
     * @param array $roleIds
     * @return array|Collection|User[]
     */
    public function fetch(AuthorInterface $author, array $roleIds)
    {
        $isPrivilege = $author->isPrivilege();
        $isManage = $author->isManage();
        if (!$isPrivilege && !$isManage) {
            return [];
        }
        $selects = [
            'users.id',
            'users.name',
            'roles.id as roleId',
            'roles.name as roleName',
            'users.status',
            'users.email',
            'users.phone_number',
            'users.username',
        ];
        if ($isPrivilege) {
            return User::query()
                ->join('roles', 'users.role_id', 'roles.id')
                ->select($selects)
                ->orderBy('users.id', 'desc')
                ->get();
        }
        return User::query()
            ->join('roles', 'users.role_id', 'roles.id')
            ->whereIn('role_id', $roleIds)
            ->select($selects)
            ->orderBy('users.id', 'desc')
            ->get();
    }

    /**
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function findUser(string $email)
    {
        return User::query()
            ->where('email', $email)
            ->where('status', 1)
            ->firstOrFail();
    }

    /**
     * find user by id
     *
     * @param int $userId
     * @return User|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function findUserById(int $userId)
    {
        return User::query()
            ->findOrFail($userId);
    }

    /**
     * create user
     *
     * @param array $input
     * @return User|bool
     */
    public function create(array $input)
    {
        try {
            $data = $this->makeDataInput($input);
            $model = new User();
            $model->fill($data)->saveOrFail();
            return $model;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * store
     *
     * @param array $input
     * @return array|bool
     */
    public function store(array $input)
    {
        try {
            $data = $this->makeDataInput($input);
            $model = new User();
            $model->fill($data)->saveOrFail();
            return $this->processUser($model);
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * update user
     *
     * @param User $user
     * @param array $input
     * @return array|bool
     */
    public function update(User $user, array $input)
    {
        try {
            $data = $this->makeDataInput($input, true);
            $user->fill($data)->saveOrFail();
            return $this->processUser($user);
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * process data input before update or create
     *
     * @param array $input
     * @param bool $isUpdate
     * @return array
     */
    private function makeDataInput(array $input, $isUpdate = false)
    {
        $password = array_get_value($input, 'password', );
        $email = array_get_value($input, 'email');
        $status = array_get_value($input, 'status', 1);
        $avatar = array_get_value($input, 'avatar', '');
        $note = array_get_value($input, 'note');
        $roleId = array_get_value($input, 'role_id', 2);
        $data = [
            'email' => $email,
            'name' => array_get_value($input, 'name'),
            'birthday' => array_get_value($input, 'birthday'),
            'gender' => array_get_value($input, 'gender', 1),
            'status' => $status,
            'avatar' => $avatar,
            'note' => $note,
            'role_id' => $roleId,
            'username' => array_get_string($input, 'username')
        ];
        if (!$isUpdate) {
            $password = $password ?? 'abc@123';
            $data['username'] = array_get_value($input, 'username');
            $data['password'] = Hash::make($password);
        }

        if ($isUpdate && !empty($password)) {
            $data['password'] = Hash::make($password);
        }
        return $data;
    }

    public function findOrCreate(string $email, array $data)
    {
        $data['status'] = 1;
        return User::query()->firstOrCreate(['email' => $email], $data);
    }

    /**
     * destroy
     *
     * @param User $user
     * @return bool
     */
    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();
            $user->tools()->detach();
            $user->delete();
            DB::commit();
            return true;
        } catch (\Throwable $e) {
            logger()->error($e);
            DB::rollBack();
            return false;
        }
    }

    /**
     * fetch user not exist in manager id of role table
     *
     * @param AuthorInterface $author
     * @param array $roleIds
     * @param array $manageIds
     * @return array
     */
    public function fetchUserManagerRole(AuthorInterface $author, array $roleIds, array $manageIds = [])
    {
        $isPrivilege = $author->isPrivilege();
        $isManage = $author->isManage();
        if (!$isPrivilege && !$isManage) {
            return [];
        }
        $query = User::query()
//            ->whereNotIn('id', $manageIds)
            ->select(['id', 'name']);
        if ($isPrivilege) {
            return $query
                ->get()
                ->toArray();
        }
        return $query
            ->whereIn('role_id', $roleIds)
            ->get()
            ->toArray();
    }

    /**
     * update permission
     *
     * @param User $user
     * @param array $input
     * @return bool
     */
    public function updatePermission(User $user, array $input)
    {
        try {
            $data = [];
            $defaultAction = [
                'view' => false,
                'create' => false,
                'update' => false,
                'delete' => false
            ];
            foreach ($input as $item) {
                $toolId = array_get_int($item, 'tool_id');
                $action = array_get_array($item, 'action', $defaultAction);
                $data[] = [
                    'tool_id' => $toolId,
                    'action' => json_encode($action)
                ];
            }
            $user->tools()->detach();
            $user->tools()->attach($data);
//            $user->tools()->sync($data);
            return true;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * process user data
     *
     * @param User $user
     * @return array
     */
    private function processUser(User $user)
    {
        $user->load(['role' => function ($query) {
            $query->select(['roles.id', 'roles.name']);
        }]);
        $role = $user->role;
        $roleName = $role->name;
        return [
          'id' => $user->id,
          'role_id' => $user->role_id,
          'username' => $user->username,
          'email' => $user->email,
          'phone_number' => $user->phone_number,
          'status' => $user->status,
          'role_name' => $roleName,
          'name' => $user->name
        ];
    }
}
