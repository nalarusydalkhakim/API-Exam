<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Http\Requests\BaseRequest;

class UpdateQuestionAnswerBulkRequest extends BaseRequest
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
            'question_answers.*.id' => 'required|exists:question_answers,id',
            'question_answers.*.score' => 'nullable|numeric',
            'question_answers.*.is_correct' => 'required|boolean',
            'question_answers.*.feedback' => 'nullable|string|max:50000',
        ];
    }
}
