<?php

namespace Modules\LearningManagement\Services\School;

use Modules\LearningManagement\Repositories\School\EventTaskRepository;
use Modules\LearningManagement\Repositories\School\TaskRepository;
use Modules\LearningManagement\Services\Base\EventTaskService as BaseEventTaskService;

class EventTaskService extends BaseEventTaskService
{
    public function __construct()
    {
        $this->taskRepository = new TaskRepository;
        $this->eventTaskRepository = new EventTaskRepository;
    }
}
