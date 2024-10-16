<?php

namespace Modules\Event\Http\Requests\Admin;

use Modules\Event\Http\Requests\BaseRequest;

class CreateEventRequest extends BaseRequest
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
            'description' => 'required|string|max:50000',
            'photo' => 'required|image|max:5000',
            'start_at' => 'required|date_format:Y-m-d H:i',
            'end_at' => 'required|date_format:Y-m-d H:i|after:start_at',
            'quota' => 'required|integer',
            'is_visible' => 'required|boolean',
            'price' => 'required|numeric',
            'discount' => 'required|numeric|between:0,100'
        ];
    }
}
