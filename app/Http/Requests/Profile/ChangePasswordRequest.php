<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\Request;

/**
 * Class ChangePasswordRequest
 *
 * @package App\Http\Requests\Profile
 */
class ChangePasswordRequest extends Request
{
    /**
     * @return array|\string[][]
     */
    public function rules()
    {
        return [
            'password' => [
                'required',
                'string',
                'current_password:api'
            ],
            'new_password' => [
                'required',
                'string',
                'min:6',
                'max:100',
                'confirmed'
            ],
            'new_password_confirmation' => [
                'required',
                'string',
                'min:6',
                'max:100'
            ],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributes()
    {
        return [
            'password' => 'Current Password',
            'new_password' => 'New Password',
            'new_password_confirmation' => 'Retype New Password',
        ];
    }

    /**
     * @return array|string[]
     */
    public function messages()
    {
        return [
            'new_password.confirmed' => 'The Retype New Password does not match.'
        ];
    }
}
