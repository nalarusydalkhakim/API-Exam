<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Entities\Question;
use Modules\LearningManagement\Http\Requests\BaseRequest;
use Modules\LearningManagement\Entities\QuestionOption;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class CreateQuestionBulkRequest extends BaseRequest
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
            'questions' => 'required|array',
            'questions.*.subject_id' => 'sometimes|exists:subjects,id',
            'questions.*.class' => 'sometimes|numeric',
            'questions.*.answer_type' => 'required|in:' . implode(',', Question::answer_types),
            'questions.*.question' => 'required|string|max:50000',
            'questions.*.file' => 'nullable|file|max:100000|mimes:jpg,jpeg,png,pdf,xls,xlsx,doc,docx,ppt,pptx,mp4,mov,ogg,qt,zip,rar',
            'questions.*.explanation' => 'nullable|string|max:50000',
            'questions.*.level' => 'nullable|in:' . implode(',', Question::levels),
            'questions.*.options.*.key' => [
                'nullable',
                'distinct',
                'alpha:ascii',
                'max:1'
            ],
            'questions.*.options.*.option' => 'required|string|max:50000',
            'questions.*.options.*.is_correct' => [
                'required',
                'boolean'
            ],
        ];
        return $rules;
    }

    public function attributes()
    {
        return [
            'questions.*.options.*.key' => 'Pilihan',
            'questions.*.options.*.option' => 'Isi Pilihan',
            'questions.*.options.*.is_correct' => 'Pilihan benar'
        ];
    }

    public function messages()
    {
        return [
            'options.*.key.unique' => 'Data :attribute :input sudah ada sebelumnya',
            'options.*.key.distinct' => 'Data :attribute :input tidak boleh sama',
            'options.*.is_correct.unique' => 'Data :attribute hanya boleh satu'
        ];
    }
}
