<?php

namespace App\Services;

use App\Models\Role;
use App\Supports\Authorized\AuthorInterface;
use Illuminate\Support\Facades\DB;

/**
 * Class RoleService
 *
 * @package App\Services
 */
class RoleService
{
    /**
     * @param int $roleId
     * @return Role|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function findById(int $roleId)
    {
        return Role::query()
            ->findOrFail($roleId);
    }

    /**
     * @param AuthorInterface $author
     * @return array
     */
    public function fetch(AuthorInterface $author)
    {
        $isPrivilege = $author->isPrivilege();
        $isManage = $author->isManage();
        if (!$isPrivilege && !$isManage) {
            return [];
        }
        $selectColumns = [
            'roles.id',
            'roles.name',
            'roles.description',
            'roles.manage_id as manage_id',
            'users.name as manage',
            'roles.parent_id'
        ];
        $query = Role::query()
            ->join('users', 'users.id', 'roles.manage_id');
        if ($isPrivilege) {
            return $query
                ->select($selectColumns)
                ->orderBy('id', 'desc')
                ->get()
                ->toArray();
        }
        $roleId = $author->getRoleId();
        $role = $query
            ->where('roles.id', $roleId)
            ->select($selectColumns)
            ->first();
        $data[] = $role;
        /**@var Role $role*/
        $this->getRoleByParentId($role, $selectColumns, $data);
        return $data;
    }

    /**
     * create role
     *
     * @param AuthorInterface $author
     * @param array $input
     * @return array|bool
     */
    public function store(AuthorInterface $author, array $input)
    {
        $creatorId = $author->getUserId();
        $max = $this->findMaxParentId();
        try {
            $data = [
                'manage_id' => array_get_int($input, 'manage_id'),
                'name' => array_get_value($input, 'name'),
                'description' => array_get_value($input, 'description'),
                'parent_id' => array_get_value($input, 'parent_id', ($max + 1)),
                'creator_id' => $creatorId
            ];
            $model = new Role();
            $model->fill($data)->saveOrFail();
            return $this->processRole($model);
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * update role
     *
     * @param Role $role
     * @param array $input
     * @return array|bool
     */
    public function update(Role $role, array $input)
    {
        try {
            $data = [
                'manage_id' => array_get_value($input, 'manage_id'),
                'name' => array_get_value($input, 'name'),
                'description' => array_get_value($input, 'description'),
                'parent_id' => array_get_value($input, 'parent_id'),
            ];
            $role->fill($data)->saveOrFail();
            return $this->processRole($role);
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * delete role
     *
     * @param Role $role
     * @return bool
     */
    public function delete(Role $role)
    {
        try {
            DB::beginTransaction();
            $role->tools()->detach();
            $role->delete();
            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error($e);
            return false;
        }
    }

    public function fetchPermission(AuthorInterface $author, Role $role)
    {
        $isPrivilege = $author->isPrivilege();
        $isManage = $author->isManage();
        if (!$isPrivilege && !$isManage) {
            return [];
        }
    }

    /**
     * @param Role $role
     * @param array $input
     * @return bool
     */
    public function updatePermission(Role $role, array $input)
    {
        try {
            $data = [];
            foreach ($input as $item) {
                $toolId = array_get_int($item, 'tool_id');
                $action = array_get_array($item, 'action');
                $data[] = [
                    'tool_id' => $toolId,
                    'action' => json_encode($action)
                ];
            }
            $role->tools()->detach();
            $role->tools()->attach($data);
            return true;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * find max parent id
     *
     * @return mixed
     */
    private function findMaxParentId()
    {
        return Role::query()
            ->whereNull('deleted_at')
            ->max('id');
    }

    /**
     * get roles child by parent id
     *
     * @param Role $role
     * @param array $selectColumns
     * @param array $data
     */
    private function getRoleByParentId(Role $role, array $selectColumns, &$data = [])
    {
        $parentId = $role->id;
        $roleChildren = Role::query()
            ->join('users', 'users.id', 'roles.manage_id')
            ->where('roles.id', '<>', $parentId)
            ->where('roles.parent_id', $parentId)
            ->select($selectColumns)
            ->orderBy('id', 'desc')
            ->get();
        if (!empty($roleChildren)) {
            foreach ($roleChildren as $roleChild) {
                /**@var Role $roleChild*/
                $this->getRoleByParentId($roleChild, $selectColumns, $data);
                $data[] = $roleChild;
            }
        }
    }

    /**
     * process role data
     *
     * @param Role $role
     * @return array
     */
    private function processRole(Role $role)
    {
        $role->load('manage');
        $manage = $role->manage;
        $data = $role->toArray();
        $data['manage'] = $manage->name;
        return $data;
    }
}
