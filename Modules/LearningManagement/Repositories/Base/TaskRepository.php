<?php

namespace Modules\LearningManagement\Repositories\Base;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\LearningManagement\Entities\Task;

class TaskRepository
{
    protected $taskQuery;
    public function __construct()
    {
        $this->taskQuery = new Task();
    }

    public function count(Collection $request, String $userId = null): Int
    {
        $this->taskQuery = new Task();
        $this->filter($request->all(), $userId)->simpleSelect();
        return $this->taskQuery
            ->count();
    }

    public function countTaskByType(Collection $request, $userId = null)
    {
        $this->taskQuery = new Task();
        $this->filter($request->all(), $userId)->simpleSelect();
        return 1;
    }

    public function getLatest(Collection $request, String $userId = null)
    {
        $this->taskQuery = new Task();
        $this->filter($request->all(), $userId)->simpleSelect();
        return $this->taskQuery
            ->get();
    }

    public function getAll(array $request, String $userId = null)
    {
        $this->filter($request, $userId)->simpleSelect()->order($request);
        return $this->taskQuery
            ->get();
    }

    public function getPaginate(array $request, String $userId = null): LengthAwarePaginator
    {
        $this->filter($request, $userId)->simpleSelect()->order($request);
        return $this->taskQuery
            ->paginate($request['per_page'] ?? 10)
            ->appends($request);
    }

    public function findById($id)
    {
        $this->detailSelect();
        return $this->taskQuery
            ->where('tasks.id', $id)
            ->first();
    }

    public function create(Collection $input)
    {
        $input->put('id', $input->get('id') ?? Str::uuid()->toString());
        $input->put('created_at', now());
        $input->put('updated_at', $input->get('created_at'));
        $this->taskQuery
            ->insert($input->all());
        return $input->get('id');
    }

    public function update($id, Collection $input, String $userId = null)
    {
        $input->put('updated_at', now());
        return $this->taskQuery
            ->where('tasks.id', $id)
            ->when($userId, function ($q) use ($userId) {
                $q->where('tasks.owner_id', $userId);
            })
            ->update($input->all());
    }

    public function delete($id, String $userId = null)
    {
        return $this->taskQuery
            ->where('tasks.id', $id)
            ->when($userId, function ($q) use ($userId) {
                $q->where('tasks.user_id', $userId);
            })
            ->delete();
    }

    public function filter(array $request, String $userId = null)
    {
        $filter = collect($request);
        $this->taskQuery = $this->taskQuery
            ->when($filter->get('search'), function ($q) use ($filter) {
                $q->where(function ($q) use ($filter) {
                    $q->Where('tasks.name', 'like', '%' . $filter->get('search') . '%');
                });
            })->when($filter->get('name'), function ($q) use ($filter) {
                $q->where('tasks.name', 'like', '%' . $filter->get('name') . '%');
            })->when($filter->get('subject_id'), function ($q) use ($filter) {
                $q->where('tasks.subject_id', $filter->get('subject_id'));
            })->when($filter->get('class'), function ($q) use ($filter) {
                $q->where('tasks.class', $filter->get('class'));
            })->when($userId && !$filter->get('visibility'), function ($q) use ($userId) {
                $q->where('tasks.owner_id', $userId);
            })->when($filter->get('visibility'), function ($q) use ($filter, $userId) {
                $q->when($filter->get('visibility') == 'mine', function ($q) use ($userId) {
                    $q->Where('tasks.owner_id', $userId);
                });
                $q->when($filter->get('visibility') == 'public', function ($q) {
                    $q->Where('tasks.visibility', 'Publik');
                });
            });
        return $this;
    }


    private function order(array $request)
    {
        $this->taskQuery = $this->taskQuery->when(isset($request['order_field']) && isset($request['order_direction']), function ($q) use ($request) {
            $q->orderBy($request['order_field'], $request['order_direction']);
        })
            ->when(!isset($request['order_field']) || !isset($request['order_direction']), function ($q) use ($request) {
                $q->orderBy('tasks.created_at', 'asc');
            });
    }

    public function simpleSelect()
    {
        $this->taskQuery = $this->taskQuery
            ->select([
                'tasks.*'
            ]);
        return $this;
    }

    public function detailSelect()
    {
        $this->taskQuery = $this->taskQuery
            ->with('taskSections')
            ->select([
                'tasks.*'
            ]);
        return $this;
    }
}
