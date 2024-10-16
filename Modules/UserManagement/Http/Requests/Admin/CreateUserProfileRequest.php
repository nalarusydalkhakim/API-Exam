<?php

namespace Modules\UserManagement\Http\Requests\Admin;

use Modules\Event\Http\Requests\BaseRequest;

class CreateUserProfileRequest extends BaseRequest
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
            'organization' => 'nullable|string|max:200',
            'phone' => 'nullable|numeric|digits_between:5,20',
            'gender' => 'nullable|string|in:Laki-laki,Perempuan',
            'village_id' => 'nullable|exists:id_villages,id',
            'address' => 'nullable|string|max:200',
            'class' => 'nullable|string|max:200',
        ];
    }
}
