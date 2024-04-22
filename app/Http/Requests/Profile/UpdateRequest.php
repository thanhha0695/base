<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\Request;
use App\Rules\FileOrStringRule;

/**
 * Class UpdateRequest
 *
 * @package App\Http\Requests\Profile
 */
class UpdateRequest extends Request
{
    public function rules()
    {
        return [
            'username' => [
                'required',
                'string',
                'alpha_num:ascii'
            ],
            'avatar' => [
                'nullable',
                new FileOrStringRule()
            ],
            'name' => [
                'required',
                'string'
            ],
            'email' => [
                'required',
                'email'
            ],
            'gender' => [
                'required',
                'numeric'
            ],
            'contact' => [
                'required',
                'string'
            ]
        ];
    }
}
