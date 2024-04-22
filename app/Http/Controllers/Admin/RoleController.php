<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRequest;
use App\Http\Requests\Role\UpdatePermissionRequest;
use App\Http\Requests\Role\UpdateRequest;
use App\Models\Role;
use App\Services\OauthClientService;
use App\Services\RoleService;
use App\Services\ToolService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;

/**
 * Class RoleController
 *
 * @package App\Http\Controllers\Roles
 */
class RoleController extends Controller
{
    /**
     * fetch role
     *
     * @param RoleService $roleService
     * @param UserService $userService
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function fetch(RoleService $roleService, UserService $userService)
    {
        $author = $this->author();
        $roles = $roleService->fetch($author);
        $roleIds = array_column($roles, 'id') ?? [];
        $managerIds = array_column($roles, 'manage_id') ?? [];
        $users = $userService->fetchUserManagerRole($author, $roleIds, $managerIds);
        return $this->responseStatusSuccess([
            'roles' => $roles,
            'users' => $users
        ], '');
    }

    /**
     * create role
     *
     * @param CreateRequest $request
     * @param RoleService $roleService
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function create(CreateRequest $request, RoleService $roleService)
    {
        $author = $this->author();
        $input = $request->all();
        $rs = $roleService->store($author, $input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'insert failed');
        }
        return $this->responseStatusSuccess($rs, 'Insert successful');
    }

    /**
     * update role
     *
     * @param UpdateRequest $request
     * @param RoleService $roleService
     * @param string $roleId
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, RoleService $roleService, string $roleId)
    {
        $input = $request->all();
        $role = $roleService->findById((int) $roleId);
        /**@var Role $role*/
        $rs = $roleService->update($role, $input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'Update failed');
        }
        return $this->responseStatusSuccess($rs, 'Update successful');
    }

    /**
     * delete role
     *
     * @param RoleService $roleService
     * @param string $roleId
     * @return JsonResponse
     */
    public function delete(RoleService $roleService, string $roleId)
    {
        $role = $roleService->findById((int) $roleId);
        /**@var Role $role*/
        $rs = $roleService->delete($role);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'Delete failed');
        }
        return $this->responseStatusSuccess($rs, 'Delete successful');
    }

    /**
     * fetch permission by role
     *
     * @param RoleService $roleService
     * @param ToolService $toolService
     * @param OauthClientService $oauthClientService
     * @param string $roleId
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function fetchPermission(RoleService $roleService, ToolService $toolService, OauthClientService $oauthClientService, string $roleId)
    {
        $author = $this->author();
        $role = $roleService->findById((int) $roleId);
        $clientId = request()->input('client_id');
        $permissions = $toolService->fetchPermissionByRole($author, $role, (int) $clientId);
        $permissions['clients'] = $oauthClientService->fetchClient($author);
        return $this->responseStatusSuccess($permissions, '');
    }

    /**
     * update permission
     *
     * @param UpdatePermissionRequest $request
     * @param RoleService $roleService
     * @param string $roleId
     * @return JsonResponse
     */
    public function updatePermission(UpdatePermissionRequest $request, RoleService $roleService, string $roleId)
    {
        $input = $request->input('permissions');
        $role = $roleService->findById((int) $roleId);
        $rs = $roleService->updatePermission($role, $input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'Update permission failed');
        }
        return $this->responseStatusSuccess([], 'Update permission successful');
    }
}
