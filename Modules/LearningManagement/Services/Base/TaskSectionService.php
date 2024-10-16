<?php

namespace Modules\LearningManagement\Services\Base;

use Modules\LearningManagement\Repositories\Base\TaskSectionRepository;
use Illuminate\Http\Request;

class TaskSectionService
{
    protected $taskSectionRepository;

    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->taskSectionRepository = new TaskSectionRepository;
    }

    public function getAll($taskId, Request $request, $schoolId = null)
    {
        $data = $this->taskSectionRepository->getAll(
            $taskId,
            $request->all(),
            $schoolId
        );
        return $data;
    }

    public function findById(String $id)
    {
        $data = $this->taskSectionRepository->findById($id);
        return $data;
    }

    public function create($taskId, Request $request)
    {
        try {
            $input = collect($request->validated());
            $input->put('task_id', $taskId);
            $taskSectionId = $this->taskSectionRepository->create($input);
            if ($taskSectionId) {
                $this->initRepository();
                return $this->findById($taskSectionId);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update($id, Request $request, $taskId, $userId = null)
    {
        try {
            $taskSection = $this->taskSectionRepository->update(
                $id,
                collect($request->validated()),
                $taskId,
                $userId
            );
            if ($taskSection) {
                $this->initRepository();
                return $this->findById($id);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($id, $taskId, $schoolId, $teacherId = null)
    {
        return $this->taskSectionRepository->delete($id, $taskId, $schoolId, $teacherId);
    }
}
