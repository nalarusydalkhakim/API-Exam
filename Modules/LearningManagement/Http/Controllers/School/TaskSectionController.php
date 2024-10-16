<?php

namespace Modules\LearningManagement\Http\Controllers\School;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Base\TaskSectionController as BaseTaskSectionController;
use Modules\LearningManagement\Http\Requests\School\CreateTaskSectionRequest;
use Modules\LearningManagement\Http\Requests\School\UpdateTaskSectionRequest;
use Modules\LearningManagement\Services\School\TaskSectionService;

class TaskSectionController extends BaseTaskSectionController
{
    public function __construct()
    {
        $this->service = new TaskSectionService;
        $this->middleware(['role_or_permission:Super Admin|Lihat Bank Ujian'])->only(['index', 'show']);
        $this->middleware(['role_or_permission:Super Admin|Tambah Bank Ujian'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Bank Ujian'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Bank Ujian'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->ok(
            $this->service->getAll(
                $request->route('task'),
                $request,
                $request->route('schoolId')
            ),
            'Data Bagian Soal'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTaskSectionRequest $request)
    {
        return $this->ok(
            $this->service->create(
                $request->route('task'),
                $request,
                $request->user()->id
            ),
            'Berhasil menyimpan data'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($content)
    {
        $data = $this->service->findById($content);
        if ($data)
            return $this->ok($data, 'Data detail Bagian Soal');
        return $this->error('Data tidak ditemukan', 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskSectionRequest $request)
    {
        if ($data = $this->service->update(
            $request->route('task_section'),
            $request,
            $request->route('task'),
            $request->user()->id
        ))
            return $this->ok($data, 'Berhasil mengubah data');
        return $this->error('Data tidak ditemukan', 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($this->service->delete(
            $request->route('task_section'),
            $request->route('task'),
            $request->route('schoolId'),
            $request->user()->id
        ))
            return $this->ok([], 'Berhasil menghapus data');
        return $this->error('Data tidak ditemukan', 404);
    }
}
