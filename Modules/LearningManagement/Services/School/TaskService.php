<?php

namespace Modules\LearningManagement\Services\School;

use Modules\LearningManagement\Repositories\School\TaskRepository;
use Modules\LearningManagement\Services\Base\TaskService as BaseTaskService;

class TaskService extends BaseTaskService
{
    public function __construct()
    {
        $this->taskRepository = new TaskRepository;
    }
}
