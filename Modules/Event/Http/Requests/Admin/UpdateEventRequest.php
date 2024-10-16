<?php

namespace Modules\Event\Http\Requests\Admin;

use Modules\Event\Http\Requests\BaseRequest;

class UpdateEventRequest extends BaseRequest
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
            'description' => 'sometimes|string|max:50000',
            'announcement' => 'nullable|string|max:50000',
            'photo' => 'sometimes|image|max:5000',
            'start_at' => 'sometimes|date_format:Y-m-d H:i',
            'end_at' => 'sometimes|date_format:Y-m-d H:i|after:start_at',
            'quota' => 'sometimes|integer',
            'status' => 'sometimes|in:Belum Mulai,Pendaftaran,Sedang Berlangsung,Selesai',
            'is_visible' => 'sometimes|boolean',
            'price' => 'sometimes|numeric',
            'discount' => 'sometimes|numeric|between:0,100'
        ];
    }
}
