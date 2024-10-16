<?php

namespace Modules\LearningManagement\Services\School;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\LearningManagement\Repositories\School\EventTaskRepository;
use Modules\LearningManagement\Repositories\School\QuestionRepository;
use Modules\LearningManagement\Repositories\School\TaskRepository;
use Modules\LearningManagement\Services\Base\DashboardService as BaseDashboardService;

class DashboardService extends BaseDashboardService
{
    protected $eventTaskRepository;

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

    public function getDashboardByUserId($userId)
    {
        $request = collect(['owner_id' => $userId]);
        return [
            'task_count' => $this->taskRepository->count(collect($request->all())),
            'question_count' => $this->questionRepository->count(collect($request->all())),
            'latest_tasks' => $this->eventTaskRepository->getLatest(collect($request->all()), 10),
            'student_task' => $this->eventTaskRepository->countTaskByResultStatus(collect($request->all())),
            'task_by_type' => $this->taskRepository->countTaskByType(collect($request->all())),
            'question_by_level' => $this->questionRepository->countQuestionByLevel(collect($request->all()))
        ];
    }

    public function getTaskDashboardByUserId($userId)
    {
        $request = collect(['committee_id' => $userId]);
        return [
            'task_bank_count' => $this->taskRepository->count(collect([]), $userId),
            'latest_tasks' => $this->eventTaskRepository->getLatest(collect($request->all()), 10),
            'student_task' => $this->eventTaskRepository->countTaskByResultStatus(collect($request->all())),
        ];
    }

    public function getQuestionDashboardByUserId($userId)
    {
        return [
            'question_bank_count' => $this->questionRepository->count(collect([]), $userId),
        ];
    }
}
