<?php

namespace App\Http\Middleware;

use App\Factory\AuthFactory;
use App\Models\Role;
use App\Models\User;
use App\Services\ToolService;
use App\Supports\Enums\RoleDev;
use App\Supports\Traits\ResponseStatus;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use function Nette\Utils\in;

class AuthorFactoryIfAuthenticated
{
    use ResponseStatus;

    private array $dontCheckPermission =[
        '/auth/logout',
        '/profile/update',
        '/profile/change-password',
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws \ErrorException
     */
    public function handle(Request $request, Closure $next)
    {
        /**@var User $user*/
        $user = auth()->user();
        if (null === $user) {
            throw new \ErrorException('Authenticated account is null');
        }
        $roleId = $user->role_id;
        if (!in_array($roleId, RoleDev::getListAdmin())) {
            $permission = $this->checkPermission($request, $user);
            if (true !== $permission) {
                return $permission;
            }
        }
        $role = $this->findRoleById($roleId);
        /**@var Role $role*/
        $author = AuthFactory::create($user, $role);
        $request->attributes->add(['author' => $author]);
        return $next($request);
    }

    /**
     * check permission when role is not admin
     *
     * @param Request $request
     * @param User $user
     * @return bool|JsonResponse
     */
    private function checkPermission(Request $request, User $user)
    {
        $prefixApi = '/api';
        $currentUri = $request->getRequestUri();
        if (str_contains($currentUri, $prefixApi)) {
            $currentUri = str_replace($prefixApi, '', $currentUri);
        }
        if (in_array($currentUri, $this->dontCheckPermission)) {
            return true;
        }

        $toolService = app(ToolService::class);
        /**@var ToolService $toolService*/
        $clientId = optional($user->token())->client_id ?? 1;
        $tools = $toolService->fetchToolByClient($clientId);
        $toolIdRequest = false;
        foreach ($tools as $toolId => $uri_tool) {
            if (empty($uri_tool)) {
                continue;
            }
            if (str_contains($currentUri, $uri_tool)) {
                $toolIdRequest = $toolId;
                break;
            }
        }
        $isCheckPermission = array_search($currentUri, array_column($tools, 'uri')) || $toolIdRequest;
        return $this->havePermission($request, $user, $isCheckPermission, $toolIdRequest);
    }

    /**
     * kiểm tra quyền truy câp theo các action được cấp
     *
     * @param Request $request
     * @param User $user
     * @param boolean $isCheckPermission
     * @param int $toolIdRequest
     * @return bool|JsonResponse
     */
    private function havePermission(Request $request, User $user, bool $isCheckPermission, int $toolIdRequest)
    {
        if ($isCheckPermission) {
            $toolService = app(ToolService::class);
            /**@var ToolService $toolService*/
//            $permissions = $toolService->fetchPermission($user);
            $permissions = $toolService->fetchPermissionsSplitByUser($user);
            $action = $request->route()->getActionMethod();
            // mặc định lấy index từ uri thực hiện request
            $actionTool = $permissions[$toolIdRequest] ?? false;
            if (empty($actionTool)) {
                return $this->responseStatusFailed(403, 'permission denied');
            }
            // lấy các quyền đã config
            $result = $this->actionAccept($actionTool, $action);
            if ($result !== true) {
                return $result;
            }
            return true;
        }
        return $this->responseStatusFailed(403, 'Permission denied');
    }

    /**
     * find role by id
     *
     * @param int $roleId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    private function findRoleById(int $roleId)
    {
        return Role::query()
            ->where('id', $roleId)
            ->first();
    }

    /**
     * check quyền truy cập theo action
     *
     * @param array $action_tool
     * @param string $action_route
     * @return bool|JsonResponse
     */
    private function actionAccept(array $action_tool, string $action_route)
    {
        $view = array_get_value($action_tool, 'view');
        $create = array_get_value($action_tool, 'create');
        $update = array_get_value($action_tool, 'update');
        $delete = array_get_value($action_tool, 'delete');
        // check quyền từng action
        if (!$view) {
            return $this->responseStatusFailed(403, 'Permission denied');
        }
        if (str_contains($action_route, 'update') && !$update) {
            return $this->responseStatusFailed(403, 'Permission denied');
        }
        if (str_contains($action_route, 'delete') && !$delete) {
            return $this->responseStatusFailed(403, 'Permission denied');
        }
        if (str_contains($action_route, 'create') && !$create) {
            return $this->responseStatusFailed(403, 'Permission denied');
        }
        return true;
    }
}
