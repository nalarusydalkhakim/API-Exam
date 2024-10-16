<?php

namespace Modules\UserManagement\Http\Requests\Admin;

use Modules\Event\Http\Requests\BaseRequest;

class UpdateUserRoleRequest extends BaseRequest
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
        $rules = [
            'role' => 'required|exists:roles,name',
        ];

        if (!$this->user()->hasRole('Super Admin')) {
            $rules['role'] = 'required|exists:roles,name|in:Admin Event,Peserta Event';
        }

        return $rules;
    }
}
