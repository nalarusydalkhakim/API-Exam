<?php

namespace Modules\LearningManagement\Services\School;

use Modules\LearningManagement\Repositories\School\TaskSectionRepository;
use Modules\LearningManagement\Services\Base\TaskSectionService as BaseTaskSectionService;

class TaskSectionService extends BaseTaskSectionService
{
    public function __construct()
    {
        $this->taskSectionRepository = new TaskSectionRepository;
    }
}
