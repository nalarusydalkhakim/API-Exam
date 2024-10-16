<?php

namespace Modules\LearningManagement\Repositories\Student;

use Illuminate\Support\Collection;
use Modules\LearningManagement\Entities\TaskResult;
use Modules\LearningManagement\Repositories\Base\TaskResultRepository as BaseTaskResultRepository;

class TaskResultRepository extends BaseTaskResultRepository
{
    protected $taskResultQuery;

    public function __construct()
    {
        $this->taskResultQuery = new TaskResult();
    }

    public function simpleSelect()
    {
        $this->taskResultQuery = $this->taskResultQuery
            ->select([
                'task_results.*'
            ]);
        return $this;
    }

    public function detailSelect()
    {
        $this->taskResultQuery = $this->taskResultQuery
            ->select([
                'task_results.*'
            ]);
        return $this;
    }

    public function getTaskStatus(String $taskId, String $studentId)
    {
        return $this->taskResultQuery
            ->join('event_tasks', 'event_tasks.id', 'task_results.event_task_id')
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->where([
                ['event_task_id', '=', $taskId],
                ['user_id', '=', $studentId]
            ])->select(
                'task_results.id',
                'task_results.status',
                'task_results.created_at',
                'event_tasks.start_at',
                'event_tasks.end_at'
            )
            ->first();
    }

    public function start(String $taskId, String $studentId)
    {
        return $this->taskResultQuery->create([
            'event_task_id' => $taskId,
            'user_id' => $studentId,
            'score' => null,
            'status' => 'Sedang Dikerjakan',
            'is_passed' => null,
            'finish_at' => null
        ]);
    }

    public function finish(String $taskId, String $studentId, $point = null)
    {
        return $this->taskResultQuery
            ->where('task_results.user_id', $studentId)
            ->where('task_results.event_task_id', $taskId)
            ->whereIn('task_results.status', ['Sedang Dikerjakan', 'Belum Dikerjakan'])
            ->update([
                'task_results.score' => $point,
                'task_results.status' => $point ? 'Selesai' : 'Belum Dikoreksi',
                'task_results.is_passed' => null,
                'task_results.finish_at' => now()
            ]);
    }
}
