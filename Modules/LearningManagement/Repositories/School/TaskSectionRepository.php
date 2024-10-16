<?php

namespace Modules\LearningManagement\Repositories\School;

use Modules\LearningManagement\Repositories\Base\TaskSectionRepository as BaseTaskSectionRepository;

class TaskSectionRepository extends BaseTaskSectionRepository
{
    protected $taskSectionQuery;
    
    public function simpleSelect()
    {
        $this->taskSectionQuery = $this->taskSectionQuery
            ->select([
                'task_sections.*'
            ]);
        return $this;
    }

    public function detailSelect()
    {
        $this->taskSectionQuery = $this->taskSectionQuery
            ->select([
                'task_sections.*'
            ]);
        return $this;
    }
}
