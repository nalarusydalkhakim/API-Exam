<?php

namespace Modules\LearningManagement\Services\Admin;

use Modules\LearningManagement\Repositories\Admin\EventTaskRepository;
use Modules\LearningManagement\Repositories\Admin\QuestionRepository;
use Modules\LearningManagement\Repositories\Admin\TaskRepository;

class DashboardService
{
    protected
        $taskRepository,
        $questionRepository,
        $eventTaskRepository;

    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->taskRepository = new TaskRepository();
        $this->questionRepository = new  QuestionRepository();
        $this->eventTaskRepository = new EventTaskRepository();
    }

    public function getTaskDashboard()
    {
        $request = collect([]);
        return [
            'task_bank_count' => $this->taskRepository->count(collect([])),
            'latest_tasks' => $this->eventTaskRepository->getLatest(collect($request->all()), 10),
            'student_task' => $this->eventTaskRepository->countTaskByResultStatus(collect($request->all())),
        ];
    }

    public function getQuestionDashboard()
    {
        return [
            'question_bank_count' => $this->questionRepository->count(collect([])),
        ];
    }
}
