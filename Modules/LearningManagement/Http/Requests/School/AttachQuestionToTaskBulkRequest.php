<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Http\Requests\BaseRequest;
use Modules\LearningManagement\Http\Requests\Rules\CheckQuestionHasSameTypeWithSection;

class AttachQuestionToTaskBulkRequest extends BaseRequest
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
        $this->request->add(['task_id' => $this->route('task')]);
        return [
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:task_sections,id',
            'sections.*.questions.*.question_id' => [
                'bail',
                'required',
                'distinct',
                'exists:questions,id'
            ],
            'sections.*.questions.*.number' => 'nullable|numeric',
        ];
    }
}
