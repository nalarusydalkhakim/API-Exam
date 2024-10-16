<?php

namespace Modules\Notification\Http\Requests;

use Modules\Pds\Http\Requests\BaseRequest;

class CreateNotificationTokenRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'device' => 'required|in:android,iphone,web',
            'token' => 'required|string',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
