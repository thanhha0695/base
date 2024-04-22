<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class RegisterRequest
 *
 * @package App\Http\Requests\Auth
 */
class RegisterRequest extends Request
{
    public function rules()
    {
        return [
            'username' => [
                'required',
                'string',
                'alpha-num',
                'max:100',
                Rule::unique('users', 'username')
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:100',
                'confirmed'
            ],
            'password_confirmation' => [
                'required',
                'string',
                'min:6',
                'max:100'
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
            ],
            'name' => [
                'required',
                'string'
            ],
            'birthday' => [
                'nullable',
                'date'
            ],
            'gender' => [
                'required',
                'numeric'
            ]
        ];
    }
}
