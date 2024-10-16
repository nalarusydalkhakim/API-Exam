<?php

namespace Modules\Event\Http\Requests\Admin;

use Modules\Event\Http\Requests\BaseRequest;

class UpdateEventSponsorRequest extends BaseRequest
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
            'name' => 'sometimes|string|max:200',
            'photo' => 'sometimes|image|max:5000',
            'link' => 'sometimes|url|max:200',
        ];
    }
}
