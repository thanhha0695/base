<?php

namespace App\Http\Requests\Tool;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $id = $this->route('tool_id');
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('tools', 'name')
                ->where(function ($query) use ($id) {
                    $query->where('id', '<>', $id);
                })
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
