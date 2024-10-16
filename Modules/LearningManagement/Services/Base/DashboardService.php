<?php

namespace Modules\LearningManagement\Services\Base;

use Modules\LearningManagement\Repositories\Base\QuestionRepository;
use Modules\LearningManagement\Repositories\Base\TaskRepository;

class DashboardService
{
    protected
        $taskRepository,
        $questionRepository;

    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->taskRepository = new TaskRepository();
        $this->questionRepository = new  QuestionRepository();
    }
}
