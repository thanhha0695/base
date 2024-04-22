<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Laravel\Passport\PersonalAccessTokenResult;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;

class AuthController extends Controller
{
    /**
     * attempt
     *
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function attempt(AuthRequest $request)
    {
        $input = $request->all();
        $remember = array_get_value($input, 'remember');
        $attempt = [
            'username' => array_get_string($input, 'username'),
            'password' => array_get_string($input, 'password')
        ];
        if (!Auth::attempt($attempt)) {
            return $this->responseStatusFailed(401, 'The username or password invalid');
        }
        $user = auth()->user();
        if ($user && $user->status !== 1) {
            return $this->responseStatusFailed(401, 'User un-active');
        }
        $accessToken = $user->createToken(Str::random(8))->accessToken;
        if (empty($accessToken)) {
            return $this->responseStatusFailed(500, 'An error');
        }
        $expire = $remember ? 30 : 1;
        Passport::tokensExpireIn(now()->addDays($expire));
        return $this->responseStatusSuccess([
            'user' => $user,
            'accessToken' => $accessToken
        ], '');
    }

    /**
     * google login
     *
     * @return JsonResponse
     */
    public function login()
    {
        return $this->responseStatusSuccess(Socialite::driver('google')
            ->stateless()
            ->with(["prompt" => "select_account"])
            ->scopes(['openid'])
            ->redirect()
            ->getTargetUrl());
    }

    /**
     * google callback
     *
     * @param UserService $userService
     * @return mixed
     */
    public function googleCallback(UserService $userService)
    {
        try {
            $userGoogle = Socialite::driver('google')->stateless()->user();
            $email = $userGoogle->getEmail();
            $data = [
                'email' => $email,
                'name' => $userGoogle->getName(),
                'avatar' => $userGoogle->getAvatar()
            ];
            $user = $userService->findOrCreate($email, $data);
            if (empty($user) || optional($user)->status !== 1) {
                return $this->responseStatusFailed(401, 'Unauthorised');
            }
            auth()->login($user);
            $personalAccess = $user->createToken(Str::random(8));
            /**@var PersonalAccessTokenResult $personalAccess*/
            $accessToken = $personalAccess->accessToken;
            $token = $personalAccess->token;
            $token->expires_at = now()->addDays(1);
            $token->save();
            if (empty($accessToken)) {
                return $this->responseStatusFailed(500, 'An error');
            }
//            Passport::tokensExpireIn(now()->addDays(1));
            setcookie('_ac', $accessToken, 86400);
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            logger()->error($e);
        }
    }

    /**
     * logout
     *
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function logout()
    {
        $author = $this->author();
        $token = $author->getUser()->token();
        $token->revoke();
        return $this->responseStatusSuccess([], 'Logout success');
    }
}
