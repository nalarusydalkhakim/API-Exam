<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Entities\QuestionOption;
use Modules\LearningManagement\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class CreateQuestionOptionRequest extends BaseRequest
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
            'key' => [
                'nullable',
                'distinct',
                'alpha:ascii',
                'max:1'
            ],
            'option' => 'required|string|max:50000',
            'is_correct' => [
                'required',
                'boolean',
                'distinct',
                Rule::unique('question_options', 'is_correct')->where(function (Builder $query) {
                    return $query->where('question_id', $this->route('question'));
                })
            ],
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'options.*.key' => 'Pilihan',
            'options.*.is_correct' => 'Pilihan benar'
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
