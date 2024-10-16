<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Entities\QuestionOption;
use Modules\LearningManagement\Http\Requests\BaseRequest;

class UpdateQuestionOptionRequest extends BaseRequest
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
            'key' => 'nullable|alpha:ascii|max:1',
            'option' => 'required|string|max:50000',
            'is_correct' => 'required|boolean',
        ];
    }
}
