<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Http\Requests\BaseRequest;

class UpdateQuestionAnswerRequest extends BaseRequest
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
            'score' => 'required|numeric',
            'is_correct' => 'required|boolean',
            'feedback' => 'nullable|string|max:50000',
        ];
    }
}
