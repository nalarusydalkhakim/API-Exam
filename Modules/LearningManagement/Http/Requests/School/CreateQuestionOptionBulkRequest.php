<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Entities\QuestionOption;
use Modules\LearningManagement\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class CreateQuestionOptionBulkRequest extends BaseRequest
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
            'options.*.key' => [
                'nullable',
                'distinct',
                'alpha:ascii',
                'max:1',
                Rule::unique('question_options', 'key')->where(function (Builder $query) {
                    return $query->where('question_id', $this->route('question'));
                })
            ],
            'options.*.option' => 'required|string|max:50000',
            'options.*.is_correct' => [
                'nullable',
                'boolean',
                Rule::unique('question_options', 'is_correct')->where(function (Builder $query) {
                    return $query->where('question_id', $this->route('question'))
                        ->where('is_correct', true);
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
            'options.*.is_correct.unique' => 'Data :attribute hanya boleh satu'
        ];
    }
}
