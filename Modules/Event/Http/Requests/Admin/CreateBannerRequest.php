<?php

namespace Modules\Event\Http\Requests\Admin;

use Modules\Event\Http\Requests\BaseRequest;

class CreateBannerRequest extends BaseRequest
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
            'photo' => 'required|image|max:5000',
        ];
    }
}
