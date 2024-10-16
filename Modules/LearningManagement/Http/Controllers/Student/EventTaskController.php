<?php

namespace Modules\LearningManagement\Http\Controllers\Student;

use Illuminate\Http\Request;
use Modules\LearningManagement\Http\Controllers\Base\TaskController as BaseTaskController;
use Modules\LearningManagement\Http\Requests\Student\CreateQuestionAnswerMarkRequest;
use Modules\LearningManagement\Http\Requests\Student\CreateQuestionAnswerRequest;
use Modules\LearningManagement\Services\Student\EventTaskService;

class EventTaskController extends BaseTaskController
{
    public function __construct()
    {
        $this->service = new EventTaskService;
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
                $request,
                $request->event_id,
                $request->user()->id
            ),
            'Data Ujian'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $data = $this->service->findById(
            $request->route('event_task'),
            $request->user()->id
        );
        if ($data)
            return $this->ok($data, 'Data detail ujian');
        return $this->error('Data tidak ditemukan', 404);
    }

    public function start(Request $request)
    {
        $data = $this->service->start(
            $request->route('event_task'),
            $request->user()->id
        );
        if ($data)
            return $this->ok($data, 'Data soal');
        return $this->error('Data tidak ditemukan', 404);
    }

    public function answer(CreateQuestionAnswerRequest $request)
    {
        $data = $this->service->answer(
            $request,
            $request->route('task_question'),
            $request->route('event_task'),
            $request->user()->id
        );
        if ($data)
            return $this->ok($data, 'Berhasil menyimpan jawaban');
        return $this->error('Data tidak ditemukan', 404);
    }

    public function setMark(CreateQuestionAnswerMarkRequest $request)
    {
        $data = $this->service->setMark(
            $request,
            $request->route('task_question'),
            $request->route('event_task'),
            $request->user()->id
        );
        if ($data)
            return $this->ok($data, 'Berhasil menandai jawaban');
        return $this->error('Data tidak ditemukan', 404);
    }

    public function finish(Request $request)
    {
        $data = $this->service->finish(
            $request->route('event_task'),
            $request->user()->id
        );
        if ($data)
            return $this->created('Berhasil mengumpulkan ujian / ujian');
        return $this->error('Data tidak ditemukan', 404);
    }
}
