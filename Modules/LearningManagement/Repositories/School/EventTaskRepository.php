<?php

namespace Modules\LearningManagement\Repositories\School;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\LearningManagement\Repositories\Base\EventTaskRepository as BaseEventTaskRepository;

class EventTaskRepository extends BaseEventTaskRepository
{
    protected $eventTaskQuery;


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
            ->with('taskEvents')
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->select([
                'event_tasks.*',
                'tasks.name',
                'tasks.description',
                'tasks.visibility'
            ]);
        return $this;
    }

    public function getPaginateWithSummary(Collection $request)
    {
        $this->filter($request->all());
        return $this->eventTaskQuery
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->leftJoin('events', 'events.id', 'event_tasks.event_id')
            ->leftJoin('event_participants', 'event_participants.event_id', 'event_tasks.event_id')
            ->select([
                'event_tasks.*',
                'tasks.name',
                DB::raw("count(event_participants.id) as student_count")
            ])
            ->withCount([
                'taskResults as sedang_dikerjakan_count' => function ($q) {
                    $q->where('task_results.status', 'Sedang Dikerjakan');
                },
                'taskResults as mengumpulkan_count' => function ($q) {
                    $q->whereIn('task_results.status', ['Belum Dikoreksi', 'Sedang Dikoreksi', 'Selesai']);
                },
                'taskResults as belum_dikoreksi_count' => function ($q) {
                    $q->where('task_results.status', 'Belum Dikoreksi');
                },
                'taskResults as selesai_count' => function ($q) {
                    $q->where('task_results.status', 'Selesai');
                },
                'taskResults as passed_count' => function ($q) {
                    $q->where('task_results.is_passed', true);
                },
            ])
            ->groupBy('event_tasks.id', 'tasks.subject_id', 'tasks.name')
            ->paginate($request->get('per_page', 10))
            ->appends($request->all());
    }

    public function filter(array $request)
    {
        $filter = collect($request);
        $this->eventTaskQuery = $this->eventTaskQuery
            ->when($filter->get('search'), function ($q) use ($filter) {
                $q->where(function ($q) use ($filter) {
                    $q->Where('tasks.name', 'like', '%' . $filter->get('search') . '%');
                });
            })->when($filter->get('name'), function ($q) use ($filter) {
                $q->Where('tasks.name', 'ilike', '%' . $filter->get('name') . '%');
            })->when($filter->get('event_id'), function ($q) use ($filter) {
                $q->Where('event_tasks.event_id', $filter->get('event_id'));
            })->when($filter->get('class_subject_id'), function ($q) use ($filter) {
                $q->Where('events.class_subject_id', $filter->get('class_subject_id'));
            })->when(isset($request['order_field']) && isset($request['order_direction']), function ($q) use ($request) {
                $q->orderBy($request['order_field'], $request['order_direction']);
            })->when(!isset($request['order_field']) || !isset($request['order_direction']), function ($q) use ($request) {
                $q->orderBy('created_at', 'asc');
            });
        return $this;
    }
}
