<?php

namespace App\Http\Requests\Tool;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class MoveRequest
 * @package App\Http\Requests\Tool
 */
class MoveRequest extends Request
{
    public function rules()
    {
        return [
            'client_id' => [
                'required',
                Rule::exists('oauth_clients', 'id')
            ],
            'start' => [
                'required',
                'array'
            ],
            'start.id' => [
                'required',
                Rule::exists('tools', 'id')
            ],
            'start.position' => [
                'required',
                'numeric'
            ],
            'end' => [
                'required',
                'array'
            ],
            'end.id' => [
                'required',
                Rule::exists('tools', 'id')
            ],
            'end.position' => [
                'required',
                'numeric'
            ]
        ];
    }
}
