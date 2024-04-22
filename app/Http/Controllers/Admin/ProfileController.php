<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateRequest;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;

/**
 * Class ProfileController
 *
 * @package App\Http\Controllers\Admin
 */
class ProfileController extends Controller
{
    /**
     * update profile
     *
     * @param UpdateRequest $request
     * @param ProfileService $profileService
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function update(UpdateRequest $request, ProfileService $profileService)
    {
        $author = $this->author();
        $input = $request->all();
        $user = $author->getUser();
        $rs = $profileService->update($user, $input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'Update failed');
        }
        return $this->responseStatusSuccess($rs, 'Update success');
    }

    /**
     * change password
     *
     * @param ChangePasswordRequest $request
     * @param ProfileService $profileService
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function updatePassword(ChangePasswordRequest $request, ProfileService $profileService)
    {
        $author = $this->author();
        $input = $request->all();
        $user = $author->getUser();
        $rs = $profileService->changePassword($user, $input);
        if (!$rs) {
            return $this->responseStatusFailed(500, 'Change password failed');
        }
        return $this->responseStatusSuccess([], 'Change password successful');
    }
}
