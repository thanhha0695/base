<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tool\CreateRequest;
use App\Http\Requests\Tool\MoveRequest;
use App\Http\Requests\Tool\UpdateRequest;
use App\Services\OauthClientService;
use App\Services\ToolService;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;

/**
 * Class ToolController
 * @package App\Http\Controllers\Admin
 */
class ToolController extends Controller
{
    /**
     * fetch
     *
     * @param ToolService $toolService
     * @param OauthClientService $oauthClientService
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function fetch(ToolService $toolService, OauthClientService $oauthClientService)
    {
        $author = $this->author();
        $clientId = request()->input('client_id') ?? 1;
        $tools = $toolService->fetchTool($author, (int) $clientId);
        $clients = $oauthClientService->fetchClient($author);
        return $this->responseStatusSuccess([
            'tools' => $tools,
            'clients' => $clients
        ], '');
    }

//    /**
//     * create and update tool
//     *
//     * @param CreateRequest $request
//     * @param ToolService $toolService
//     * @return JsonResponse
//     * @throws AuthenticationException
//     */
//    public function create(CreateRequest $request, ToolService $toolService)
//    {
//        $author = $this->author();
//        $input = $request->all();
//        $clientId = $request->input('client_id');
//        $rs = $toolService->create($author, $input, $clientId);
//        if (!$rs) {
//            return $this->responseStatusFailed(500,'Create failed');
//        }
//        return $this->responseStatusSuccess($rs, 'Create successful');
//    }

    /**
     * create
     *
     * @param CreateRequest $request
     * @param ToolService $toolService
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function create(CreateRequest $request, ToolService $toolService)
    {
        $author = $this->author();
        $input = $request->all();
        $rs = $toolService->create($author, $input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'Create failed');
        }
        return $this->responseStatusSuccess($rs, 'Create successful');
    }

    /**
     * update
     *
     * @param UpdateRequest $request
     * @param ToolService $toolService
     * @param string $toolId
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, ToolService $toolService, string $toolId)
    {
        $input = $request->all();
        $clientId = array_get_int($input, 'client_id');
        $tool = $toolService->findOrFail($clientId, (int) $toolId);
        $rs = $toolService->update($tool, $input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'Update failed');
        }
        return $this->responseStatusSuccess($rs, 'Update successful');
    }

    /**
     * delete
     *
     * @param ToolService $toolService
     * @param string $toolId
     * @return JsonResponse
     */
    public function delete(ToolService $toolService, string $toolId)
    {
        $clientId = request()->input('client_id');
        $tool = $toolService->findOrFail((int) $clientId, (int) $toolId);
        $rs = $toolService->destroy($tool);
        if (!$rs) {
            return $this->responseStatusFailed(500,'Delete failed');
        }
        return $this->responseStatusSuccess($rs, 'Delete successful');
    }

    /**
     * move position tool
     *
     * @param MoveRequest $request
     * @param ToolService $toolService
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function updateMove(MoveRequest $request, ToolService $toolService)
    {
        $author = $this->author();
        $input = $request->all();
        $rs = $toolService->move($author, $input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'Move failed');
        }
        return $this->responseStatusSuccess($rs, 'Move successful');
    }
}
