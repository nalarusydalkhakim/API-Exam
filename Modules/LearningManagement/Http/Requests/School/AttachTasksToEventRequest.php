<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class AttachTasksToEventRequest extends BaseRequest
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
        $this->request->add(['event_id' => $this->route('event')]);
        return [
            'event_id' => [
                'required',
                'exists:events,id'
            ],
            'tasks' => 'required|array',
            'tasks.*.task_id' => [
                'required',
                'exists:tasks,id',
                Rule::unique('event_tasks', 'task_id')->where(function (Builder $query) {
                    return $query->where('event_id', $this->route('event'));
                })
            ],
            'tasks.*.start_at' => 'required|date_format:Y-m-d H:i',
            'tasks.*.end_at' => 'required|date_format:Y-m-d H:i|after:start_at',
            'tasks.*.point_correct' => 'required|numeric',
            'tasks.*.point_incorrect' => 'required|numeric',
            'tasks.*.point_empty' => 'required|numeric'
        ];
    }

    public function attributes()
    {
        return [
            'tasks.*.task_id' => 'Ujian :index',
            'tasks.*.start_at' => 'Tanggal Mulai Ujian :index',
            'tasks.*.end_at' => 'Tanggal Selesai Ujian :index',
            'tasks.*.point_correct' => 'Jawaban Benar Ujian :index',
            'tasks.*.point_incorrect' => 'Jawaban Salah Ujian :index',
            'tasks.*.point_empty' => 'Jawaban Kosong Ujian :index',
        ];
    }
}
