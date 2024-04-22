<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProfileService
 *
 * @package App\Services
 */
class ProfileService
{
    /**
     * update
     *
     * @param User $user
     * @param array $input
     * @return User|bool
     */
    public function update(User $user, array $input)
    {
        try {
            $username = array_get_string($input, 'username');
            $avatar = array_get_value($input, 'avatar');
            if ($avatar instanceof UploadedFile) {
                $pathFile = "users/$username";
                if (Storage::directoryExists($pathFile)) {
                    Storage::deleteDirectory($pathFile);
                }
                $path = Storage::disk('public')->put($pathFile, $avatar);
                $avatar = Storage::disk('public')->url($path);
            }

            $data = [
                'name' => array_get_string($input, 'name'),
                'gender' => array_get_int($input, 'gender', 1),
                'birthday' => array_get_string($input, 'birthday'),
                'phone_number' => array_get_string($input, 'contact'),
                'avatar' => $avatar,
            ];
            $user->fill($data)->saveOrFail();
            return $user;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }

    /**
     * change password
     *
     * @param User $user
     * @param array $input
     * @return User|bool
     */
    public function changePassword(User $user, array $input)
    {
        try {
            $newPassword = array_get_string($input, 'new_password');
            $user->fill(['password' => Hash::make($newPassword)])->saveOrFail();
            return $user;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }
}
