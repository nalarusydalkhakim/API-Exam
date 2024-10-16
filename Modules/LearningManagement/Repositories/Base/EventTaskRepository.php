<?php

namespace Modules\LearningManagement\Repositories\Base;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\LearningManagement\Entities\EventTask;

class EventTaskRepository
{
    protected $eventTaskQuery;
    public function __construct()
    {
        $this->eventTaskQuery = new EventTask();
    }

    public function getLatest(Collection $request, Int $limit = 10)
    {
        return $this->eventTaskQuery
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->join('events', 'events.id', 'event_tasks.event_id')
            ->join('subjects', 'subjects.id', 'tasks.subject_id')
            ->select(
                'tasks.id',
                'event_tasks.id as event_task_id',
                'event_tasks.event_id',
                'tasks.name',
                'tasks.class',
                'subjects.name as subject',
                'event_tasks.start_at',
                'event_tasks.end_at'
            )
            ->when($userId = $request->get('committee_id'), function ($q) use ($userId) {
                $q->join('event_admins', function ($join) use ($userId) {
                    $join->on('event_admins.event_id', 'events.id');
                    $join->on('event_admins.user_id', DB::raw("'$userId'"));
                });
            })
            ->when($studentId = $request->get('student_id'), function ($q) use ($studentId) {
                $q->join('event_participants', function ($join) use ($studentId) {
                    $join->on('event_participants.event_id', 'events.id');
                    $join->on('event_participants.user_id', DB::raw("'$studentId'"));
                });
            })
            ->orderBy('event_tasks.start_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function countTaskByResultStatus(Collection $request = null)
    {
        $possibleStatuses = ['Belum Dikerjakan', 'Sedang Dikerjakan', 'Belum Dikoreksi', 'Selesai'];

        $data = DB::table('event_participants')
            ->join('events', 'events.id', 'event_participants.event_id')
            ->join('event_tasks', 'event_tasks.event_id', 'events.id')
            ->leftJoin('task_results', function ($join) {
                $join->on('task_results.event_task_id', 'event_tasks.id');
                $join->on('task_results.user_id', 'event_participants.user_id');
            })
            ->when($userId = $request->get('committee_id'), function ($q) use ($userId) {
                $q->join('event_admins', function ($join) use ($userId) {
                    $join->on('event_admins.event_id', 'events.id');
                    $join->on('event_admins.user_id', DB::raw("'$userId'"));
                });
            })
            ->groupBy('task_results.status')
            ->select(
                DB::raw("COALESCE(task_results.status, 'Belum Dikerjakan') as status"),
                DB::raw('count(*) as count')
            )
            ->get();

        $statusCounts = [];
        foreach ($possibleStatuses as $status) {
            $statusCounts[$status] = 0;
        }
        foreach ($data as $result) {
            $statusCounts[$result->status] = $result->count;
        }

        $statusCounts = array_map(function ($count, $status) {
            return ['status' => $status, 'count' => $count];
        }, $statusCounts, array_keys($statusCounts));

        return $statusCounts;
    }

    public function getAll(array $request)
    {
        $this->filter($request)->simpleSelect();
        return $this->eventTaskQuery
            ->get();
    }

    public function getAllByEventId(string $eventId)
    {
        $this->simpleSelect();
        return $this->eventTaskQuery
            ->where('event_id', $eventId)
            ->get();
    }

    public function getPaginate(array $request): Paginator
    {
        $this->filter($request)->simpleSelect();
        return $this->eventTaskQuery
            ->simplePaginate($request['per_page'] ?? 10)
            ->appends($request);
    }

    public function findById($id)
    {
        $this->detailSelect();
        return $this->eventTaskQuery
            ->where('event_tasks.id', $id)
            ->first();
    }

    public function findByTaskIdAndEventId($taskId, String $eventId)
    {
        $this->detailSelect();
        return $this->eventTaskQuery
            ->where('event_tasks.event_id', $eventId)
            ->where('event_tasks.task_id', $taskId)
            ->first();
    }

    public function create(Collection $input)
    {
        return $this->eventTaskQuery
            ->insert($input->all());
    }

    public function createBulk(Collection $input)
    {
        return $this->eventTaskQuery
            ->insert($input->all());
    }

    public function update($eventTaskId, Collection $input, String $eventId = null, String $teacherId = null)
    {
        $input->put('updated_at', now());
        return $this->eventTaskQuery
            ->where('event_tasks.id', $eventTaskId)
            ->when($eventId, function ($q) use ($eventId) {
                $q->where('event_tasks.event_id', $eventId);
            })
            ->update($input->all());
    }

    public function delete($id)
    {
        $data = $this->eventTaskQuery
            ->where('event_tasks.id', $id)
            ->delete();

        return $data;
    }

    public function filter(array $request)
    {
        $filter = collect($request);
        $this->eventTaskQuery = $this->eventTaskQuery
            ->when($filter->get('search'), function ($q) use ($filter) {
                $q->where(function ($q) use ($filter) {
                    $q->Where('event_tasks.name', 'ilike', '%' . $filter->get('search') . '%');
                });
            })->when($filter->get('name'), function ($q) use ($filter) {
                $q->Where('event_tasks.name', 'ilike', '%' . $filter->get('name') . '%');
            })->when($filter->get('event_id'), function ($q) use ($filter) {
                $q->Where('event_tasks.event_id', $filter->get('event_id'));
            });
        return $this;
    }

    public function simpleSelect()
    {
        $this->eventTaskQuery = $this->eventTaskQuery
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->select([
                'event_tasks.*',
                'tasks.name',
                'tasks.description',
                'tasks.visibility'
            ]);
        return $this;
    }

    public function detailSelect()
    {
        $this->eventTaskQuery = $this->eventTaskQuery
            ->with('taskSections')
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->select([
                'event_tasks.*',
                'tasks.name',
                'tasks.description',
                'tasks.visibility'
            ]);
        return $this;
    }
}
