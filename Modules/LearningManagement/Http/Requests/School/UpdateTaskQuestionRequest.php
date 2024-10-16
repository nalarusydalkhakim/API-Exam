<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\LearningManagement\Http\Requests\BaseRequest;

class UpdateTaskQuestionRequest extends BaseRequest
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
        Validator::extend('unique_question_number', function ($attribute, $value) {
            $taskSectionId = DB::table('task_questions')
                ->where('task_questions.id', $this->route('task_question'))
                ->pluck('task_section_id')->first();
            $query = DB::table('task_questions')
                ->join('task_sections', 'task_sections.id', 'task_questions.task_section_id')
                ->where('task_sections.id', $taskSectionId)
                ->where('task_sections.task_id', $this->route('task'))
                ->where('task_questions.id', '!=', $this->route('task_question'))
                ->where('task_questions.number', $value);
            return !$query->count();
        });
        return [
            'number' => [
                'nullable',
                'unique_question_number'
            ]
        ];
    }

    public function messages()
    {
        return [
            'number.unique_question_number' => 'Nomor soal sudah ada (duplikat)'
        ];
    }
}
