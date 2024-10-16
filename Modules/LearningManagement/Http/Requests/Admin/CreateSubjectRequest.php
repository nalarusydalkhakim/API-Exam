<?php

namespace Modules\LearningManagement\Http\Requests\Admin;

use Modules\LearningManagement\Http\Requests\BaseRequest;

class CreateSubjectRequest extends BaseRequest
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
            'name' => 'required|string|max:200',
        ];
    }
}
