<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Entities\Task;
use Modules\LearningManagement\Http\Requests\BaseRequest;

class UpdateTaskRequest extends BaseRequest
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
            'subject_id' => 'nullable|string|exists:subjects,id',
            'class' => 'nullable|numeric|between:1,12',
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:5000',
            'visibility' => 'sometimes|in:' . implode(',', Task::visibilities),
            'auto_correction' => 'nullable|boolean'
        ];
    }
}