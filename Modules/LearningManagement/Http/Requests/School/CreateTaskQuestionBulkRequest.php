<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Entities\Question;
use Modules\LearningManagement\Entities\QuestionOption;
use Modules\LearningManagement\Http\Requests\BaseRequest;
use Modules\LearningManagement\Http\Requests\Rules\CheckQuestionHasSameTypeWithSection;

class CreateTaskQuestionBulkRequest extends BaseRequest
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
        $rules = [
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:task_sections,id',
            'sections.*.questions' => 'required|array',
            'sections.*.questions.*.number' => 'nullable|numeric|distinct',
            'sections.*.questions.*.subject_id' => 'sometimes|exists:subjects,id',
            'sections.*.questions.*.class' => 'sometimes|numeric',
            'sections.*.questions.*.answer_type' => [
                'bail',
                'required',
                'in:' . implode(',', Question::answer_types),
                new CheckQuestionHasSameTypeWithSection
            ],
            'sections.*.questions.*.question' => 'required|string|max:50000',
            'sections.*.questions.*.file' => 'nullable|file|max:100000|mimes:jpg,jpeg,png,pdf,xls,xlsx,doc,docx,ppt,pptx,mp4,mov,ogg,qt,zip,rar',
            'sections.*.questions.*.explanation' => 'nullable|string|max:50000',
            'sections.*.questions.*.level' => 'nullable|in:' . implode(',', Question::levels),
            'sections.*.questions.*.options' => 'nullable|array',
            'sections.*.questions.*.options.*.key' => [
                'nullable',
                'distinct',
                'alpha:ascii',
                'max:1'
            ],
            'sections.*.questions.*.options.*.option' => 'nullable|string|max:50000',
            'sections.*.questions.*.options.*.is_correct' => [
                'required',
                'boolean'
            ],
        ];
        return $rules;
    }

    public function attributes()
    {
        return [
            'sections.*.questions.*.options.*.key' => 'Pilihan',
            'sections.*.questions.*.options.*.option' => 'Isi Pilihan',
            'sections.*.questions.*.options.*.is_correct' => 'Pilihan benar'
        ];
    }
}
