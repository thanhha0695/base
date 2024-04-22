<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class CreateRequest
 * @package App\Http\Requests\Role
 */
class CreateRequest extends Request
{
    public function rules()
    {
        return [
            'manage_id' => [
                'required',
                'numeric',
                Rule::exists('users', 'id'),
                Rule::unique('roles', 'manage_id')
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('roles', 'name')
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'parent_id' => [
                'nullable',
                'numeric',
                Rule::exists('roles', 'id')
            ]
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributes()
    {
        return [
            'manage_id' => 'manage',
            'parent_id' => 'parent role'
        ];
    }
}
