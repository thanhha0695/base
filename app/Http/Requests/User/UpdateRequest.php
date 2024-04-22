<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\User
 */
class UpdateRequest extends Request
{
    public function rules()
    {
        $userId = (int) $this->route('user_id');
        return [
            'password' => [
                'nullable',
                'string',
                'min:6',
                'max:100',
                'confirmed'
            ],
            'password_confirmation' => [
                'nullable',
                'string',
                'min:6',
                'max:100'
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
                ->where(function ($query) use($userId) {
                    $query->where('id', '<>', $userId);
                })
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
                'nullable',
                'numeric'
            ],
            'avatar' => [
                'nullable',
                'string'
            ],
            'status' => [
                'required',
                'numeric'
            ],
            'role_id' => [
                'required',
                'numeric',
                Rule::exists('roles', 'id')
            ]
        ];
    }
}
