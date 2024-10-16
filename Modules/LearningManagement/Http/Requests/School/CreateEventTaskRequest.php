<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CreateEventTaskRequest extends BaseRequest
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
            'event_id' => [
                'required',
                Rule::exists('events', 'id')
            ],
            'start_at' => 'required|date_format:Y-m-d H:i',
            'end_at' => 'required|date_format:Y-m-d H:i|after:start_at',
            'point_correct' => 'required|numeric',
            'point_incorrect' => 'required|numeric',
            'point_empty' => 'required|numeric',
        ];
    }
}
