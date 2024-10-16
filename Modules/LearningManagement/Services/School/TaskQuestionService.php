<?php

namespace Modules\LearningManagement\Services\School;

use Modules\LearningManagement\Repositories\School\TaskQuestionRepository;
use Modules\LearningManagement\Repositories\School\TaskRepository;
use Modules\LearningManagement\Services\Base\TaskQuestionService as BaseTaskQuestionService;

class TaskQuestionService extends BaseTaskQuestionService
{

    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->taskQuestionRepository = new TaskQuestionRepository;
        $this->taskRepository = new TaskRepository;
    }
}
