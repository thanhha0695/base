<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class UpdatePermissionRequest
 *
 * @package App\Http\Requests\Role
 */
class UpdatePermissionRequest extends Request
{
    public function rules()
    {
        return [
            'permissions' => [
                'required',
                'array'
            ],
            'permissions.*.tool_id' => [
                'required',
                'numeric',
                Rule::exists('tools', 'id')
            ],
            'permissions.*.client_id' => [
                'required',
                'numeric',
                Rule::exists('oauth_clients', 'id')
            ],
            'permissions.*.action' => [
                'required',
                'array'
            ],
            'permissions.*.action.view' => [
                'required',
                'boolean'
            ],
            'permissions.*.action.update' => [
                'required',
                'boolean'
            ],
            'permissions.*.action.create' => [
                'required',
                'boolean'
            ],
            'permissions.*.action.delete' => [
                'required',
                'boolean'
            ],
        ];
    }
}
