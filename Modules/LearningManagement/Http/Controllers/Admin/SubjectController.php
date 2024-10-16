<?php

namespace Modules\LearningManagement\Http\Controllers\Admin;

use Modules\LearningManagement\Http\Controllers\Base\SubjectController as BaseSubjectController;
use Modules\LearningManagement\Http\Requests\Admin\CreateSubjectRequest;
use Modules\LearningManagement\Http\Requests\Admin\UpdateSubjectRequest;
use Modules\LearningManagement\Services\Base\SubjectService;

class SubjectController extends BaseSubjectController
{
    public function __construct()
    {
        $this->service = new SubjectService;
        $this->middleware(['role_or_permission:Super Admin|Lihat Mata Pelajaran'])->only(['index', 'show']);
        $this->middleware(['role_or_permission:Super Admin|Edit Mata Pelajaran'])->only(['update']);
    }

    public function store(CreateSubjectRequest $request)
    {
        return $this->ok($this->service->create(
            $request,
        ), 'Berhasil menambah data');
    }

    public function update(UpdateSubjectRequest $request)
    {
        if ($data = $this->service->update(
            $request->route('subject'),
            $request,
        ))
            return $this->ok($data, 'Berhasil mengubah data');
        return $this->error('Data tidak ditemukan', 404);
    }
}
