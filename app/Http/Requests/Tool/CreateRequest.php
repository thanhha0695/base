<?php

namespace App\Http\Requests\Tool;


use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class CreateRequest
 *
 * @package App\Http\Requests\Tool
 */
class CreateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('tools', 'name')
            ],
            'uri' => [
                'nullable',
                'string'
            ],
            'icon' => [
                'nullable',
                'string'
            ],
            'client_id' => [
                'required',
                'numeric',
                Rule::exists('oauth_clients', 'id')
            ],
            'parent_id' => [
                'nullable',
                'numeric'
            ]
        ];
    }
}
