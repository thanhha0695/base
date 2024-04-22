<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\Role
 */
class UpdateRequest extends Request
{
    public function rules()
    {
        $roleId = (int) $this->route('role_id');
        return [
            'manage_id' => [
                'required',
                'numeric',
                Rule::exists('users', 'id')
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('roles', 'name')
                ->where(function ($query) use ($roleId) {
                    $query->where('id', '<>', $roleId);
                })
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
}
