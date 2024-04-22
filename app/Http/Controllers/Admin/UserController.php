<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdatePermissionRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use App\Services\OauthClientService;
use App\Services\RoleService;
use App\Services\ToolService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\User
 */
class UserController extends Controller
{
    /**
     * check user authenticate
     *
     * @param ToolService $toolService
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function userAuthenticate(ToolService $toolService)
    {
        $author = $this->author();
        $user = $author->getUser();
        if ($user->status !== 1) {
            return $this->responseStatusFailed(401, 'Unauthorized');
        }
        $roleName = $author->getRoleName();
        $token = $user->token();
        $clientId = $token->client_id ?? 1;
        $tools = $toolService->fetchTool($author, $clientId);
        $user->role_name = $roleName;
        $user->is_manage = $author->isManage();
        $user->isPrivilege = $author->isPrivilege();
        $user->client_id = $clientId;
        return $this->responseStatusSuccess([
            'user' => $user,
            'tools' => $tools,
        ], '');
    }

    /**
     * register
     *
     * @param RegisterRequest $request
     * @param UserService $userService
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, UserService $userService)
    {
        $input = $request->all();
        $rs = $userService->store($input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'register failed');
        }
        return $this->responseStatusSuccess($rs);
    }

    /**
     * find user by id
     *
     * @param UserService $userService
     * @param string $userId
     * @return JsonResponse
     */
    public function findUser(UserService $userService, string $userId)
    {
        $rs = $userService->findUserById((int) $userId);
        return $this->responseStatusSuccess($rs);
    }

    /**
     * fetch all user by field search
     *
     * @param UserService $userService
     * @param RoleService $roleService
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function fetch(UserService $userService, RoleService $roleService)
    {
        $author = $this->author();
        $roles = $roleService->fetch($author);
        $roleIds = array_column($roles, 'id') ?? [];
        $users = $userService->fetch($author, $roleIds);
        return $this->responseStatusSuccess([
            'users' => $users,
            'roles' => $roles
        ], '');
    }

    /**
     * update user
     *
     * @param UpdateRequest $request
     * @param UserService $userService
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function updateProfile(UpdateRequest $request, UserService $userService)
    {
        $input = $request->all();
        $user = $this->author()->getUser();
        $rs = $userService->update($user, $input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'update failed');
        }
        return $this->responseStatusSuccess($rs, 'Update successful');
    }

    /**
     * update user
     *
     * @param UpdateRequest $request
     * @param UserService $userService
     * @param string $userId
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, UserService $userService, string $userId)
    {
        $input = $request->all();
        $user = $userService->findUserById((int) $userId);
        /**@var User $user*/
        $rs = $userService->update($user, $input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'update failed');
        }
        return $this->responseStatusSuccess($rs, 'Update successful');
    }

    /**
     * create admin
     *
     * @param StoreRequest $request
     * @param UserService $userService
     * @return JsonResponse
     */
    public function createAdmin(StoreRequest $request, UserService $userService)
    {
        $input = $request->all();
        $rs = $userService->store($input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'create failed');
        }
        return $this->responseStatusSuccess($rs, 'Add successful');
    }

    /**
     * delete user
     *
     * @param UserService $userService
     * @param string $userId
     * @return JsonResponse
     */
    public function delete(UserService $userService, string $userId)
    {
        $user = $userService->findUserById((int) $userId);
        /**@var User $user*/
        $rs = $userService->destroy($user);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'Delete failed');
        }
        return $this->responseStatusSuccess([], 'Delete successful');
    }

    /**
     * fetch permission
     *
     * @param UserService $userService
     * @param ToolService $toolService
     * @param OauthClientService $oauthClientService
     * @param string $userId
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function fetchPermission(UserService $userService, ToolService $toolService, OauthClientService $oauthClientService, string $userId)
    {
        $user = $userService->findUserById((int) $userId);
        $author = $this->author();
        $clientId = request()->input('client_id');
        $permissions = $toolService->fetchToolPermissionByUser($author, $user, (int) $clientId);
        $permissions['clients'] = $oauthClientService->fetchClient($author);
        return $this->responseStatusSuccess($permissions, '');
    }

    /**
     * update permission
     *
     * @param UpdatePermissionRequest $request
     * @param UserService $userService
     * @param string $userId
     * @return JsonResponse
     */
    public function updatePermission(UpdatePermissionRequest $request, UserService $userService, string $userId)
    {
        $input = $request->input('permissions');
        $user = $userService->findUserById((int) $userId);
        $rs = $userService->updatePermission($user, $input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'Update permission failed');
        }
        return $this->responseStatusSuccess([], 'Update permission successful');
    }
}
