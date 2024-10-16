<?php

namespace Modules\LearningManagement\Http\Controllers\School;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Base\TaskQuestionController as BaseTaskQuestionController;
use Modules\LearningManagement\Http\Requests\School\AttachQuestionToTaskBulkRequest;
use Modules\LearningManagement\Http\Requests\School\AttachQuestionToTaskRequest;
use Modules\LearningManagement\Http\Requests\School\CreateQuestionRequest;
use Modules\LearningManagement\Services\School\TaskQuestionService;
use Modules\LearningManagement\Http\Requests\School\CreateTaskQuestionBulkRequest;
use Modules\LearningManagement\Http\Requests\School\CreateTaskQuestionRequest;
use Modules\LearningManagement\Http\Requests\School\UpdateQuestionRequest;
use Modules\LearningManagement\Http\Requests\School\UpdateTaskQuestionRequest;

class TaskQuestionController extends BaseTaskQuestionController
{
    public function __construct()
    {
        $this->service = new TaskQuestionService;
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
    public function store(Request $request, CreateTaskQuestionRequest $requestTaskQuestion, CreateQuestionRequest $requestQuestion)
    {
        return $this->ok($this->service->create(
            $request->route('task'),
            $request->route('task_section'),
            $requestTaskQuestion,
            $requestQuestion,
            $request->user()->id
        ), 'Berhasil menyimpan data');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBulk(Request $request, CreateTaskQuestionBulkRequest $requestTaskQuestion)
    {
        return $this->ok($this->service->createBulk(
            $request->route('task'),
            $requestTaskQuestion,
            $request->user()->id
        ), 'Berhasil menyimpan data');
    }

    public function attach(AttachQuestionToTaskRequest $request)
    {
        return $this->ok($this->service->attach($request), 'Berhasil menyimpan data');
    }

    public function bulkAttach(AttachQuestionToTaskBulkRequest $request)
    {
        $this->service->bulkAttach($request);
        return $this->created('Berhasil menyimpan data');
    }

    public function detach(Request $request)
    {
        if ($this->service->delete($request->route('task_question')))
            return $this->ok([], 'Berhasil menghapus data');
        return $this->error('Data tidak ditemukan', 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UpdateTaskQuestionRequest $requestTaskQuestion, UpdateQuestionRequest $requestQuestion)
    {
        if ($data = $this->service->update(
            $request->route('task_question'),
            $requestTaskQuestion,
            $requestQuestion,
            $request->route('task'),
            $request->user()->id
        ))
            return $this->ok($data, 'Berhasil mengubah data');
        return $this->error('Data tidak ditemukan', 404);
    }
}
