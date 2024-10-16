<?php

namespace Modules\LearningManagement\Services\Base;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\LearningManagement\Repositories\School\TaskResultRepository;

class TaskResultService
{
    protected $taskResultRepository;

    public function __construct()
    {
        $this->taskResultRepository = new TaskResultRepository;
    }

    public function getPaginate(Request $request)
    {
        $data = $this->taskResultRepository->getPaginate($request->all());
        $data->getCollection()->transform(function ($item) {
            $item->student_photo = $item->student_photo ? Storage::url($item->student_photo) : null;
            return $item;
        });
        return $data;
    }

    public function findById(String $id)
    {
        $data = $this->taskResultRepository->findById($id);
        if ($data) {
            $data->student_photo = $data->student_photo ? Storage::url($data->student_photo) : null;
        }
        return $data;
    }

    public function create(Request $request)
    {
        try {
            $content = $this->taskResultRepository->create($request->validated());
            return $content;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update($id, Request $request)
    {
        try {
            $content = $this->taskResultRepository->update($id, $request->validated());
            return $content;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($taskResultId)
    {
        try {
            return $this->taskResultRepository->delete(
                $taskResultId
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
