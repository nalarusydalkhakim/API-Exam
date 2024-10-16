<?php

namespace Modules\LearningManagement\Repositories\Admin;

use Modules\LearningManagement\Entities\Task;
use Modules\LearningManagement\Repositories\Base\TaskRepository as BaseTaskRepository;

class TaskRepository extends BaseTaskRepository
{
    public function __construct()
    {
        $this->taskQuery = new Task();
    }
}
