<?php

namespace Modules\UserManagement\Http\Requests\Admin;

use Modules\Event\Http\Requests\BaseRequest;

class CreateUserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|email|max:200|unique:users',
            'name' => 'required|string|max:200',
            'password' => 'required|string|max:20',
            'photo' => 'nullable|image|max:5000',
        ];
    }
}