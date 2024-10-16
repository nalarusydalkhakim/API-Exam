<?php

namespace Modules\Event\Http\Requests\User;

use Modules\Event\Http\Requests\BaseRequest;

class CreateEventPaymentRequest extends BaseRequest
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
            'event_id' => 'required|exists:events,id',
        ];
    }
}
