<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Entities\TaskResult;
use Modules\LearningManagement\Http\Requests\BaseRequest;

class CreateTaskResultRequest extends BaseRequest
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
            'task_id' => 'required|exists:tasks,id',
            'student_id' => 'required|exists:students,id',
            'score' => 'nullable|numeric',
            'status' => 'required|in:'.implode(',', TaskResult::statuses),
            'is_passed' => 'nullable|boolean',
            'finish_at' => 'nullable|date_format:Y-m-d H:i'
        ];
    }
}
