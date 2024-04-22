<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class StoreRequest
 *
 * @package App\Http\Requests\User
 */
class StoreRequest extends Request
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
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
            ],
            'name' => [
                'required',
                'string'
            ],
            'role_id' => [
                'required',
                'numeric',
                Rule::exists('roles', 'id')
            ],
        ];
    }
}
