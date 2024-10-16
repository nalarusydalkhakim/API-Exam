<?php

namespace Modules\LearningManagement\Services\Base;

use Carbon\Carbon;
use Exception;
use Modules\LearningManagement\Repositories\Base\TaskRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use PDOException;

class TaskService
{
    protected $taskRepository;

    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->taskRepository = new TaskRepository;
    }

    public function getPaginate(Request $request, $userId = null)
    {
        $request->query->add([
            'user_id' => $userId
        ]);
        $data = $this->taskRepository->getPaginate($request->all(), $userId);
        return $data;
    }

    public function findById(String $id)
    {
        $data = $this->taskRepository->findById($id);
        return $data;
    }

    public function create(Request $request, $userId = null)
    {
        try {
            DB::beginTransaction();
            $input = collect($request->validated());
            $task = collect($request->validated())->except(['sections', 'courses']);
            $task->put('owner_id', $userId);
            $task->put('id', Str::uuid()->toString());
            $this->taskRepository->create($task);
            $this->initRepository();
            DB::commit();
            return $this->findById($task->get('id'));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function update($id, Request $request, String $schoolId = null, String $teacherId = null)
    {
        try {
            if ($this->taskRepository->update(
                $id,
                collect($request->validated()),
                $schoolId,
                $teacherId
            )) {
                $this->initRepository();
                return $this->findById($id);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete(String $id, String $schoolId = null, String $teacherId = null)
    {
        try {
            return $this->taskRepository->delete(
                $id,
                $schoolId,
                $teacherId
            );
        } catch (PDOException $e) {
            if ($e->getCode() == '23503') {
                throw new Exception('Gagal menghapus, Ujian ini sudah dikerjakan atau berhubungan dengan data lain.');
            } else {
                throw $e;
            }
        }
    }
}
