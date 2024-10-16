<?php

namespace Modules\LearningManagement\Repositories\Admin;

use Modules\LearningManagement\Entities\EventTask;
use Modules\LearningManagement\Repositories\Base\EventTaskRepository as BaseEventTaskRepository;

class EventTaskRepository extends BaseEventTaskRepository
{
    public function __construct()
    {
        $this->eventTaskQuery = new EventTask();
    }
}
