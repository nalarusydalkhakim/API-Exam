<?php

namespace Modules\LearningManagement\Repositories\School;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Event\Entities\EventParticipant;
use Modules\LearningManagement\Entities\QuestionAnswer;
use Modules\LearningManagement\Entities\TaskResult;
use Modules\LearningManagement\Entities\TaskSection;
use Modules\LearningManagement\Repositories\Base\TaskResultRepository as BaseTaskResultRepository;

class TaskResultRepository extends BaseTaskResultRepository
{
    private $eventParticipant;

    public function __construct()
    {
        $this->taskResultQuery = new TaskResult();
        $this->eventParticipant = new EventParticipant();
    }

    public function getPaginate(array $request): LengthAwarePaginator
    {
        $eventTaskId = collect($request)->get('event_task_id');
        return $this->eventParticipant
            ->join('users', 'users.id', 'event_participants.user_id')
            ->leftJoin('task_results', function ($join) use ($eventTaskId) {
                $join->on('task_results.user_id', 'users.id');
                $join->on('task_results.event_task_id', DB::raw("'$eventTaskId'"));
            })
            ->select([
                'task_results.*',
                'users.id as user_id',
                'users.name as user_name',
                'users.photo as user_photo',
                'users.email as user_email',
                DB::raw("COALESCE(task_results.status, 'Belum Dikerjakan') as status"),
            ])
            ->when(isset($request['event_id']), function ($q) use ($request) {
                $q->where('event_participants.event_id', $request['event_id']);
            })
            ->when(isset($request['order_field']) && isset($request['order_direction']), function ($q) use ($request) {
                $q->orderBy($request['order_field'], $request['order_direction']);
            })
            ->when(!isset($request['order_field']) || !isset($request['order_direction']), function ($q) use ($request) {
                $q->orderBy('user_name', 'asc');
            })
            ->paginate($request['per_page'] ?? 10)
            ->appends($request);
    }

    public function simpleSelect()
    {
        $this->taskResultQuery = $this->taskResultQuery
            ->join('users', 'users.id', 'task_results.user_id')
            ->select([
                'task_results.*',
            ]);
        return $this;
    }

    public function detailSelect()
    {
        $this->taskResultQuery = $this->taskResultQuery
            ->join('users', 'users.id', 'task_results.user_id')
            ->select([
                'task_results.*',
            ]);
        return $this;
    }

    public function filter(array $request, String $schoolId = null)
    {
        $filter = collect($request);
        $this->taskResultQuery = $this->taskResultQuery
            ->when($filter->get('search'), function ($q) use ($filter) {
                $q->where(function ($q) use ($filter) {
                    $q->Where('users.name', 'ilike', '%' . $filter->get('search') . '%');
                });
            })
            ->when($filter->get('event_task_id'), function ($q) use ($filter) {
                $q->where('task_results.event_task_id', $filter->get('event_task_id'));
            });
        return $this;
    }

    public function answerCorrection(Collection $input, String $questionAnswerId)
    {
        return QuestionAnswer::where('question_answers.id', $questionAnswerId)
            ->update($input->all());
    }

    public function resultCorrection(Collection $input, String $taskResultId)
    {
        return TaskResult::where('task_results.id', $taskResultId)
            ->update($input->all());
    }
}
