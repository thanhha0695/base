<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tool;
use App\Models\User;
use App\Supports\Authorized\AuthorInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ToolService
 * @package App\Services
 */
class ToolService
{
    /**
     * find or fail
     *
     * @param int $clientId
     * @param int $toolId
     * @return Tool|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function findOrFail(int $clientId, int $toolId)
    {
        return Tool::query()
            ->where('client_id', $clientId)
            ->findOrFail($toolId);
    }

    /**
     * update
     *
     * @param Tool $tool
     * @param array $input
     * @return Tool|bool
     */
    public function update(Tool $tool, array $input)
    {
        $clientId = array_get_int($input, 'client_id', 1);
        $data = [
            'name' => array_get_string($input, 'name'),
            'uri' => array_get_string($input, 'uri'),
            'icon' => array_get_string($input, 'icon'),
            'client_id' => $clientId,
        ];
        try {
            $tool->fill($data)->saveOrFail();
            return $tool;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * move position tool
     *
     * @param AuthorInterface $author
     * @param array $input
     * @return array|bool|Collection
     */
    public function move(AuthorInterface $author, array $input)
    {
        $clientId = array_get_int($input, 'client_id');
        $end = array_get_array($input, 'end');
        $start = array_get_array($input, 'start');
        $idStart = array_get_int($start, 'id');
        $idEnd = array_get_int($end, 'id');
        $positionStart = array_get_int($start, 'position');
        $positionEnd = array_get_int($end, 'position');
        $inc = 1;
        $conditionStart = '<';
        $conditionEnd = '>=';
        if ($positionStart < $positionEnd) {
            $inc = -1;
            $conditionStart = '>';
            $conditionEnd = '<=';
        }
        try {
            DB::beginTransaction();
            $move = Tool::query()
                ->where('client_id', $clientId)
                ->where('position', $conditionStart, $positionStart)
                ->where('position', $conditionEnd, $positionEnd)
                ->increment('position', $inc);
            $updatePositionStart = Tool::query()
                ->where('client_id', $clientId)
                ->where('id', $idStart)
                ->update(['position' => $positionEnd]);
            DB::commit();
            return $this->fetchTool($author, $clientId);
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * @param int $clientId
     * @return array
     */
    public function fetchToolByClient($clientId = 1)
    {
        return Tool::query()
            ->where('client_id', $clientId)
            ->whereNull('deleted_at')
            ->pluck('uri', 'id')
            ->toArray();
    }

    /**
     * fetch tool
     *
     * @param AuthorInterface $author
     * @param int $clientId
     * @return array|Collection
     */
    public function fetchTool(AuthorInterface $author, int $clientId)
    {
        $isPrivilege = $author->isPrivilege();
        if ($isPrivilege) {
            return Tool::query()
                ->where('client_id', $clientId)
                ->get();
        }
        $user = $author->getUser();
//        $permissions = $this->fetchPermission($user);
        $permissions = $this->fetchPermissionsSplitByUser($user);
        return $this->getToolAndActionByPermission($permissions, $clientId);
//        $listToolId = array_keys($permissions);
//        $tools = Tool::query()
//            ->where('client_id', $clientId)
//            ->whereIn('id', $listToolId)
//            ->orderBy('position', 'DESC')
//            ->get();
//        $data = [];
//        foreach ($tools as $tool) {
//            /**@var Tool $tool*/
//            $toolId = $tool->id;
//            $action = $permissions[$toolId];
//            $data[$toolId] = $tool->toArray();
//            $data[$toolId]['action'] = $action;
//        }
//        return $data;
    }

    /**
     * create tool
     *
     * @param AuthorInterface $author
     * @param array $input
     * @param int $clientId
     * @return mixed
     */
    public function createAndUpdate(AuthorInterface $author, array $input, int $clientId)
    {
        $clientId = array_get_int($input, 'client_id', $clientId);
        $tools = array_get_array($input, 'tools');
        $max = $this->findToolIdMax();
        $position = $this->findMaxPositionByClientId($clientId) ?? 0;
        $newId = $max + 1;
        $resultUpdate = [];
        $data = [];
        $creatorId = $author->getUserId();
        try {
            DB::beginTransaction();
            foreach ($tools as $item) {
                $parentId = array_get_int($item, 'parent_id');
                $toolId = array_get_int($item, 'tool_id');
                $item['client_id'] = $clientId;
                if ($toolId) {
                    $item['id'] = $toolId;
                    // nếu có id thì update và đẩy vào mảng để trả ra ngoài view
                    $tool = $this->findToolByIdAndClientId($toolId, $clientId);
                    if ($tool) {
                        $tool->fill($item)->saveOrFail();
                        $resultUpdate[] = $tool->toArray();
                        continue;
                    }
                }
                $max++;
                if (!$parentId) {
                    $position++;
                    $parentId = $max;
                    $item['position'] = $position;
                }
                $item['parent_id'] = $parentId;
                $item['id'] = $max;
                $item['creator_id'] = $creatorId;
                $data[] = $item;
            }
            if (!empty($data)) {
                Tool::query()->insert($data);
            }
            DB::commit();
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }

        $resultCreate = $this->fetchToolCreate($newId);
        return array_merge($resultCreate, $resultUpdate);
    }

    /**
     * create
     *
     * @param AuthorInterface $author
     * @param array $input
     * @return Tool|bool
     */
    public function create(AuthorInterface $author, array $input)
    {
        $creatorId = $author->getUserId();
        $clientId = array_get_int($input, 'client_id', 1);
        $idNew = $this->findNextIdAutoIncrement();
        $positionMax = $this->findMax($clientId, 'position');
//        $idNew = $maxID + 1;
        $position = $positionMax + 1;
        $parentIdInput = array_get_int($input, 'parent_id');
        $data = [
            'name' => array_get_string($input, 'name'),
            'uri' => array_get_string($input, 'uri'),
            'icon' => array_get_string($input, 'icon'),
            'parent_id' => $parentIdInput ?? $idNew,
            'position' => $position,
            'client_id' => $clientId,
            'creator_id' => $creatorId
        ];
        if (!empty($parentIdInput)) {
            unset($data['position']);
        }
        try {
            $model = new Tool();
            $model->fill($data)->saveOrFail();
            return $model;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     *
     *
     * @return mixed
     */
    public function findNextIdAutoIncrement()
    {
        $statement  = DB::select("SHOW TABLE STATUS LIKE 'tools'");
        $nextUserId = $statement[0]->Auto_increment;

        return $nextUserId;
    }

    /**
     * find tool latest
     *
     * @param int $clientId
     * @return \Illuminate\Database\Eloquent\Builder|Tool
     */
    private function findToolLatest(int $clientId)
    {
        return Tool::query()
            ->where('client_id', $clientId)
            ->latest(['id', 'position']);
    }

    /**
     * delete tool
     *
     * @param Tool $tool
     * @return bool
     */
    public function destroy(Tool $tool)
    {
        try {
            DB::beginTransaction();
            $toolId = $tool->id;
            $query = Tool::query()->where('parent_id', $toolId)->select(['id']);
            $listTool = $query->get();
            foreach ($listTool as $item) {
                /**@var Tool $item*/
                $item->roles()->detach();
            }
            $query->delete();
            DB::commit();
            return true;
        } catch (\Throwable $e) {
            logger()->error($e);
            DB::rollBack();
            return false;
        }
    }

    /**
     * @param int $toolId
     * @param int $clientId
     * @return Tool|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function findToolByIdAndClientId(int $toolId, int $clientId)
    {
        return Tool::query()
            ->where('client_id', $clientId)
            ->where('id', $toolId)
            ->first();
    }

    /**
     * fetch tool create
     *
     * @param int $toolId
     * @return array
     */
    public function fetchToolCreate(int $toolId)
    {
        return Tool::query()
            ->where('id', '>=', $toolId)
            ->get()
            ->toArray();
    }

    /**
     * @return mixed
     */
    public function findToolIdMax()
    {
        return Tool::query()
            ->max('id');
    }

    public function findMax(int $clientId, string $column)
    {
        return Tool::query()
            ->where('client_id', $clientId)
            ->max($column);
    }


    /**
     *
     *
     * @param int $clientId
     * @return mixed
     */
    public function findMaxPositionByClientId(int $clientId)
    {
        return Tool::query()
            ->where('client_id', $clientId)
            ->max('position');
    }

    /**
     * fetch permission
     *
     * @param User $user
     * @return array
     */
    public function fetchPermission(User $user)
    {
        $userId = $user->id;
        $roleId = $user->role_id;
        $permissions = Permission::query()
            ->where('role_id', $roleId)
            ->orWhere('user_id', $userId)
            ->get();
        return $this->processDataPermission($permissions);
    }

    /**
     * fetch permission split by user
     *
     * @param User $user
     * @return array
     */
    public function fetchPermissionsSplitByUser(User $user)
    {
        $userId = $user->id;
        $roleId = $user->role_id;
        $userPermissions = Permission::query()
            ->where('user_id', $userId)
            ->pluck('action', 'tool_id')
            ->toArray();
        $rolePermissions = Permission::query()
            ->where('role_id', $roleId)
            ->pluck('action', 'tool_id')
            ->toArray();
        foreach ($rolePermissions as $toolId => $action) {
            if (isset($userPermissions[$toolId])) {
                unset($rolePermissions[$toolId]);
                continue;
            }
            $userPermissions[$toolId] = $action;
        }
        foreach ($userPermissions as $toolId => $action) {
            $userPermissions[$toolId] = json_decode($action, true);
        }
        return $userPermissions;
    }

    /**
     * fetch tool and permission by user
     *
     * @param AuthorInterface $author
     * @param User $user
     * @param int $clientId
     * @return array
     */
    public function fetchToolPermissionByUser(AuthorInterface $author, User $user, int $clientId)
    {
        $isPrivilege = $author->isPrivilege();
        $isManage = $author->isManage();
        $userAuthenticate = $author->getUser();
        if (!$isPrivilege && !$isManage) {
            return [
                'userPermissions' => [],
                'parentPermissions' => []
            ];
        }
        $token = $author->getUser()->token();
        $clientId = $clientId ?: $token->client_id ?? 1;
        if ($isPrivilege) {
            $permissions = Tool::query()
                ->where('client_id', $clientId)
                ->get()
                ->toArray();
            return [
                'userPermissions' => $permissions,
                'parentPermissions' => []
            ];
        }
//        $permissionUser = $this->fetchPermission($user);
//        $permissionAuthenticate = $this->fetchPermission($userAuthenticate);
        $userPermissions = $this->fetchPermissionsSplitByUser($user);
        $userAuthPermissions = $this->fetchPermissionsSplitByUser($userAuthenticate);
        $action = [
            'view' => false,
            'create' => false,
            'update' => false,
            'edit' => false,
        ];
        foreach ($userAuthPermissions as $id => $item) {
            if (!isset($userPermissions[$id])) {
                $userPermissions[$id] = $action;
            }
        }
        return [
            'userPermissions' => $this->getToolAndActionByPermission($userPermissions, $clientId),
            'parentPermissions' => $userAuthPermissions
        ];
    }

    /**
     * fetch permission by role
     *
     * @param AuthorInterface $author
     * @param Role $role
     * @param int $clientId
     * @return array|array[]
     */
    public function fetchPermissionByRole(AuthorInterface $author, Role $role, int $clientId)
    {
        $isPrivilege = $author->isPrivilege();
        $isManage = $author->isManage();
        if (!$isPrivilege && !$isManage) {
            return [
                'userPermissions' => [],
                'parentPermissions' => []
            ];
        }
        $token = $author->getUser()->token();
        $clientId = $clientId ?: $token->client_id ?? 1;
        if ($isPrivilege) {
            $permissions = Tool::query()
                ->where('client_id', $clientId)
                ->get()
                ->toArray();
            return [
                'rolePermissions' => $permissions,
                'parentPermissions' => []
            ];
        }
        $user = $author->getUser();
        $permissions = Permission::query()
            ->where('role_id', $role->id)
            ->get();
        $rolePermissions = $this->processDataPermission($permissions);
        $parentPermissions = $this->fetchPermissionsSplitByUser($user);
        if (empty($rolePermissions)) {
            foreach ($parentPermissions as $toolId => $permission) {
                $rolePermissions[$toolId] = $permission;
                $rolePermissions[$toolId] = [
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                ];
            }
        }
        return [
            'rolePermissions' => $rolePermissions = $this->getToolAndActionByPermission($rolePermissions, $clientId),
            'parentPermissions' => $parentPermissions
        ];
    }

    /**
     * process permission - group permission roles and user
     *
     * @param Collection $permissions
     * @return array
     */
    private function processDataPermission(Collection $permissions)
    {
        $data = [];
        foreach ($permissions as $permission) {
            /**@var Permission $permission*/
            $toolId = $permission->tool_id;
            $action = json_decode($permission->action);
            $view = optional($action)->view ?? false;
            $update = optional($action)->update ?? false;
            $create = optional($action)->create ?? false;
            $delete = optional($action)->delete ?? false;
            // check duplicate user and role permission
            if (isset($data[$toolId])) {
                if ($view === true) {
                    $data[$toolId]['view'] = $view;
                }
                if ($update === true) {
                    $data[$toolId]['update'] = $update;
                }
                if ($create === true) {
                    $data[$toolId]['create'] = $create;
                }
                if ($delete === true) {
                    $data[$toolId]['delete'] = $delete;
                }
                continue;
            }
            $data[$toolId] = [
                'view' => $view,
                'update' => $update,
                'create' => $create,
                'delete' => $delete,
            ];
        }
        return $data;
    }

    /**
     * get tool and action by permission
     *
     * @param array $permissions
     * @param int $clientId
     * @return array
     */
    private function getToolAndActionByPermission(array $permissions, int $clientId)
    {
        $data = [];
        $listToolId = array_keys($permissions);
        $tools = Tool::query()
            ->where('client_id', $clientId)
            ->whereIn('id', $listToolId)
            ->orderBy('position', 'DESC')
            ->get();
        foreach ($tools as $tool) {
            /**@var Tool $tool*/
            $toolId = $tool->id;
            $action = $permissions[$toolId];
            $parentId = $tool->parent_id;
            if ($toolId !== $parentId && !isset($data[$parentId])) {
                $toolParent = $this->findParentTool($parentId);
                if (!empty($toolParent)) {
                    $data[$parentId] = $toolParent->toArray();
                    $data[$parentId]['action'] = $action;
                }
            }
            $data[$toolId] = $tool->toArray();
            $data[$toolId]['action'] = $action;
        }
        return $data;
    }

    /**
     * @param int $toolId
     * @return Tool|\Illuminate\Database\Eloquent\Model|object|null
     */
    private function findParentTool(int $toolId)
    {
        return Tool::query()
            ->where('id', $toolId)
            ->first();
    }
}
