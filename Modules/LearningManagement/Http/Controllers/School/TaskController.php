<?php

namespace Modules\LearningManagement\Http\Controllers\School;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Base\TaskController as BaseTaskController;
use Modules\LearningManagement\Services\School\TaskService;
use Modules\LearningManagement\Http\Requests\School\CreateTaskRequest;
use Modules\LearningManagement\Http\Requests\School\UpdateTaskRequest;

class TaskController extends BaseTaskController
{
    public function __construct()
    {
        $this->service = new TaskService;
        $this->middleware(['role_or_permission:Super Admin|Lihat Bank Ujian'])->only(['index', 'show']);
        $this->middleware(['role_or_permission:Super Admin|Tambah Bank Ujian'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Bank Ujian'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Bank Ujian'])->only('destroy');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTaskRequest $request)
    {
        return $this->ok($this->service->create($request, $request->user()->id), 'Berhasil menyimpan data');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request)
    {
        if ($data = $this->service->update(
            $request->route('task'),
            $request,
            $request->user()->id
        ))
            return $this->ok($data, 'Berhasil mengubah data');
        return $this->error('Anda tidak diizinkan mengubah data ini', 403);
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
            $request->route('task'),
            $request->user()->id
        ))
            return $this->ok([], 'Berhasil menghapus data');
        return $this->error('Anda tidak diizinkan mengubah data ini', 403);
    }
}
