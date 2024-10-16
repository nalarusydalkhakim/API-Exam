<?php

namespace Modules\LearningManagement\Http\Controllers\School;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Base\EventTaskController as BaseEventTaskController;
use Modules\LearningManagement\Services\School\EventTaskService;
use Modules\LearningManagement\Http\Requests\School\AttachTasksToEventRequest;
use Modules\LearningManagement\Http\Requests\School\AttachTaskToEventsRequest;
use Modules\LearningManagement\Http\Requests\School\CreateEventTaskRequest;
use Modules\LearningManagement\Http\Requests\School\UpdateEventTaskRequest;
use Modules\LearningManagement\Http\Requests\School\CreateTaskRequest;

class EventTaskController extends BaseEventTaskController
{
    public function __construct()
    {
        $this->service = new EventTaskService;
        $this->middleware(['role_or_permission:Super Admin|Lihat Ujian Event'])->only(['index', 'show']);
        $this->middleware(['role_or_permission:Super Admin|Tambah Ujian Event'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Ujian Event'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Ujian Event'])->only('destroy');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CreateTaskRequest $requestTask, CreateEventTaskRequest $requestEventTask)
    {
        return $this->ok($this->service->create(
            $requestTask,
            $requestEventTask,
            $request->user()->id
        ), 'Berhasil menyimpan data');
    }

    public function attachTasksToEvent(AttachTasksToEventRequest $request)
    {
        if ($this->service->attachToEvent($request))
            return $this->ok([], 'Berhasil menyimpan data');
        return $this->error('Gagal menyimpan data', 500);
    }

    public function attachTaskToEvents(AttachTaskToEventsRequest $request)
    {
        if ($this->service->attachToEvents($request))
            return $this->ok([], 'Berhasil menyimpan data');
        return $this->error('Gagal menyimpan data', 500);
    }

    public function detachTaskFromEvent(Request $request)
    {
        if ($this->service->delete($request->route('event_task')))
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
    public function update(UpdateEventTaskRequest $request)
    {
        if ($data = $this->service->update(
            $request->route('event_task'),
            $request,
            $request->user()->id
        ))
            return $this->ok($data, 'Berhasil mengubah data');
        return $this->error('Data tidak ditemukan', 404);
    }
}
