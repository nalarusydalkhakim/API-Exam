<?php

namespace Modules\LearningManagement\Http\Requests\Student;

use Modules\LearningManagement\Http\Requests\BaseRequest;
use Modules\LearningManagement\Repositories\Base\TaskQuestionRepository;

class CreateQuestionAnswerRequest extends BaseRequest
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
        $taskQuestionRepo = new TaskQuestionRepository;
        $taskQuestion = $taskQuestionRepo->getQuestionTypeById($this->route('task_question'));
        $this->request->add(['answer_type' => $taskQuestion->answer_type ?? null]);
        return [
            'text' => 'required_if:answer_type,Esai|nullable|string|max:50000',
            'question_option_id' => 'required_if:answer_type,Pilihan Ganda|nullable|exists:question_options,id',
            'file' => 'required_if:answer_type,Unggah Berkas|nullable|mimes:pdf,doc,docx,xls,xlsx,zip,rar,png,jpg,jpeg,mp4'
        ];
    }

    public function attributes()
    {
        return [
            'text' => 'Jawaban teks',
            'question_option_id' => 'Jawaban pilihan ganda',
            'file' => 'Jawaban berkas',
        ];
    }
}
