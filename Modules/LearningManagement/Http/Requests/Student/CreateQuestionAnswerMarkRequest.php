<?php

namespace Modules\LearningManagement\Http\Requests\Student;

use Modules\LearningManagement\Http\Requests\BaseRequest;
use Modules\LearningManagement\Repositories\Base\TaskQuestionRepository;

class CreateQuestionAnswerMarkRequest extends BaseRequest
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
            'mark' => 'required|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'mark' => 'Tanda',
        ];
    }
}
