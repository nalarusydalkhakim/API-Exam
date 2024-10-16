<?php

namespace Modules\LearningManagement\Services\Student;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\LearningManagement\Repositories\Base\EventContentRepository;
use Modules\LearningManagement\Repositories\Student\ClassSubjectRepository;
use Modules\LearningManagement\Repositories\Student\EventTaskRepository;
use Modules\LearningManagement\Repositories\Student\ScheduleClassSubjectRepository;
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
        $this->eventTaskRepository = new EventTaskRepository();
    }

    public function getStudentDashboardById($studentId)
    {
        $request = collect([
            'user_id' => $studentId
        ]);
        return [
            'task_count' => $this->eventTaskRepository->count($request),
            'latest_tasks' => $this->eventTaskRepository->getLatest($request, 10),
            'student_task' => $this->eventTaskRepository->countTaskByResultStatus($request),
        ];
    }
}
