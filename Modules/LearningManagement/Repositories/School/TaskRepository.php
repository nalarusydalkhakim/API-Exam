<?php

namespace Modules\LearningManagement\Repositories\School;

use Illuminate\Support\Collection;
use Modules\LearningManagement\Repositories\Base\TaskRepository as BaseTaskRepository;

class TaskRepository extends BaseTaskRepository
{
    protected $taskQuery;

    public function simpleSelect()
    {
        $this->taskQuery = $this->taskQuery
            ->with('owner:id,name,email,photo', 'subject')
            ->select([
                'tasks.*'
            ])->withCount('taskQuestions');
        return $this;
    }

    public function detailSelect()
    {
        $this->taskQuery = $this->taskQuery
            ->with('owner:id,name,email,photo', 'subject', 'taskSections')
            ->select([
                'tasks.*'
            ]);
        return $this;
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
                $q->where('tasks.owner_id', $userId);
            })
            ->delete();
    }
}
