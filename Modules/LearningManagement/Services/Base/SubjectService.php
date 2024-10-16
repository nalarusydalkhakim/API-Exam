<?php

namespace Modules\LearningManagement\Services\Base;

use Modules\LearningManagement\Repositories\Base\SubjectRepository;
use Illuminate\Http\Request;

class SubjectService
{
    protected $subjectRepository;

    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->subjectRepository = new SubjectRepository;
    }

    public function getPaginate(Request $request)
    {
        $data = $this->subjectRepository->getPaginate($request->all());
        return $data;
    }

    public function getAll(Request $request)
    {
        $data = $this->subjectRepository->getAll($request->all());
        return $data;
    }

    public function findById(String $id)
    {
        $data = $this->subjectRepository->findById($id);
        return $data;
    }

    public function create(Request $request)
    {
        try {
            $input = collect($request->validated());
            if ($subjectId = $this->subjectRepository->create($input)) {
                $this->initRepository();
                return $this->findById($subjectId);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update($id, Request $request)
    {
        try {
            if ($this->subjectRepository->update(
                $id,
                collect($request->validated())
            )) {
                $this->initRepository();
                return $this->findById($id);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete(String $id)
    {
        return $this->subjectRepository->delete($id);
    }
}
