<?php

namespace Modules\LearningManagement\Repositories\Base;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\LearningManagement\Entities\TaskSection;

class TaskSectionRepository
{
    protected $taskSectionQuery;
    public function __construct()
    {
        $this->taskSectionQuery = new TaskSection();
    }

    public function getAll($taskId, array $request, String $schoolId = null)
    {
        $this->filter($request, $schoolId)->simpleSelect();
        return $this->taskSectionQuery
            ->where('task_id', $taskId)
            ->get();
    }

    public function getPaginate(array $request, String $schoolId = null): Paginator
    {
        $this->filter($request, $schoolId)->simpleSelect();
        return $this->taskSectionQuery
            ->simplePaginate($request['per_page'] ?? 10)
            ->appends($request);
    }

    public function findById($id)
    {
        $this->detailSelect();
        return $this->taskSectionQuery
            ->where('task_sections.id', $id)
            ->first();
    }

    public function create(Collection $input)
    {
        $input->put('id', $input->get('id') ?? Str::uuid()->toString());
        $input->put('created_at', now());
        $input->put('updated_at', $input->get('created_at'));
        $this->taskSectionQuery
            ->create($input->all());
        return $input->get('id');
    }

    public function createBulk(Collection $input)
    {
        return $this->taskSectionQuery
            ->insert($input->all());
    }

    public function update($id, Collection $input, $taskId, $userId = null)
    {
        $input->put('updated_at', now());
        return $this->taskSectionQuery
            ->where('task_sections.id', $id)
            ->where('task_sections.task_id', $taskId)
            ->update($input->all());
    }

    public function delete($id, $taskId, $userId = null)
    {
        return $this->taskSectionQuery
            ->where('task_sections.id', $id)
            ->where('task_sections.task_id', $taskId)
            ->delete();
    }

    public function filter(array $request, $userId = null)
    {
        $filter = collect($request);
        $this->taskSectionQuery = $this->taskSectionQuery
            ->when($filter->get('search'), function ($q) use ($filter) {
                $q->where(function ($q) use ($filter) {
                    $q->Where('task_sections.taskSection', 'ilike', '%' . $filter->get('search') . '%');
                });
            });
        return $this;
    }

    public function simpleSelect()
    {
        return $this;
    }

    public function detailSelect()
    {
        return $this;
    }
}
