<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Entities\Question;
use Modules\LearningManagement\Http\Requests\BaseRequest;

class CreateQuestionRequest extends BaseRequest
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
            'answer_type' => [
                'bail',
                'required',
                'in:' . implode(',', Question::answer_types)
            ],
            'question' => 'required|string|max:50000',
            'file' => 'nullable|file|max:100000|mimes:jpg,jpeg,png,pdf,xls,xlsx,doc,docx,ppt,pptx,mp4,mov,ogg,qt,zip,rar',
            'explanation' => 'nullable|string|max:50000',
            'level' => 'nullable|in:' . implode(',', Question::levels),
            'visibility' => 'sometimes|in:' . implode(',', Question::visibilities),
            'subject_id' => 'nullable|string|exists:subjects,id',
            'class' => 'nullable|numeric|between:1,12',
        ];

        if ($this->answer_type === 'Pilihan Ganda') {
            $rules = array_merge($rules, [
                'options' => 'required|array',
                'options.*.key' => [
                    'nullable',
                    'distinct',
                    'alpha:ascii',
                    'max:1'
                ],
                'options.*.option' => 'required|string|max:50000',
                'options.*.is_correct' => [
                    'required',
                    'boolean'
                ],
            ]);
        }
        return $rules;
    }

    public function attributes()
    {
        $customAttributes = [];
        if ($this->answer_type === 'Pilihan Ganda') {
            // Assuming you have access to the 'options' array in your model or request
            foreach ($this->options ?? [] as $index => $option) {
                $customAttributes["options.{$index}.option"] = "Pilihan {$option['key']}";
            }
        }

        return $customAttributes;
    }
}
