<?php

namespace Modules\LearningManagement\Http\Requests\School;

use Modules\LearningManagement\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rules\Unique;

class AttachTaskToEventsRequest extends BaseRequest
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
        $this->request->add(['task_id' => $this->route('task')]);
        return [
            'task_id' => [
                'required',
                Rule::exists('tasks', 'id')
            ],
            'events' => 'required|array',
            'events.*.event_id' => [
                'required',
                'exists:events,id',
                Rule::unique('event_tasks', 'event_id')->where(fn (Builder $query) => $query->where('task_id', $this->task_id))
            ],
            'events.*.start_at' => 'required|date_format:Y-m-d H:i',
            'events.*.end_at' => 'required|date_format:Y-m-d H:i|after:start_at',
            'events.*.point_correct' => 'required|numeric',
            'events.*.point_incorrect' => 'required|numeric',
            'events.*.point_empty' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'events.*.event_id' => 'Event :index',
            'events.*.start_at' => 'Tanggal Mulai Event :index',
            'events.*.end_at' => 'Tanggal Selesai Event :index',
            'events.*.point_correct' => 'Jawaban Benar Event :index',
            'events.*.point_incorrect' => 'Jawaban Salah Event :index',
            'events.*.point_empty' => 'Jawaban Kosong Event :index',
        ];
    }
}
