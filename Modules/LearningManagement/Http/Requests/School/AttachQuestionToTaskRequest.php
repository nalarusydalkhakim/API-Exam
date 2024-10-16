<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class AttachQuestionToTaskRequest extends BaseRequest
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
            'task_section_id' => [
                'bail',
                'required',
                Rule::exists('task_sections', 'id')->where(fn (Builder $query) => $query->where('task_id', $this->route('task')))
            ],
            'question_id' => [
                'bail',
                'required',
                'exists:questions,id',
                Rule::unique('task_questions')->where(fn (Builder $query) => $query->where('task_section_id', $this->task_section_id))
            ],
            'number' => [
                'bail',
                'nullable',
                'numeric',
                Rule::unique('task_questions')->where(fn (Builder $query) => $query->where('task_section_id', $this->task_section_id))
            ],
        ];
    }
}
