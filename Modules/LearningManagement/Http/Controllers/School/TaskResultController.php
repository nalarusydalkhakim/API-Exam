<?php

namespace Modules\LearningManagement\Http\Controllers\School;

use Illuminate\Http\Request;
use Modules\LearningManagement\Services\School\TaskResultService;
use Modules\LearningManagement\Http\Controllers\Controller;
use Modules\LearningManagement\Http\Requests\School\UpdateQuestionAnswerBulkRequest;
use Modules\LearningManagement\Http\Requests\School\UpdateQuestionAnswerRequest;
use Modules\LearningManagement\Http\Requests\School\UpdateTaskResultRequest;

class TaskResultController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|Lihat Bank Ujian'])->only(['index', 'show']);
        $this->middleware(['role_or_permission:Super Admin|Tambah Bank Ujian'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Bank Ujian'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Bank Ujian'])->only('destroy');
    }

    public function getAllEventTaskResult(Request $request, TaskResultService $service)
    {
        $request->query->add([
            'event_id' => $request->route('event'),
            'teacher_id' => $request->user()->id
        ]);

        return $this->ok($service->getAllEventTaskResult($request), 'Data hasil tugas');
    }

    public function index(Request $request, TaskResultService $service)
    {
        $request->query->add([
            'event_id' => $request->route('event'),
            'event_task_id' => $request->route('event_task'),
            'teacher_id' => $request->user()->id
        ]);
        return $this->ok($service->getPaginate($request), 'Data hasil');
    }

    public function show(Request $request, TaskResultService $service)
    {
        $data = $service->findById($request->route('task_result'));
        if ($data)
            return $this->ok($data, 'Data detail hasil');
        return $this->error('Data tidak ditemukan', 404);
    }

    public function getAnswer(Request $request, TaskResultService $service)
    {
        $data = $service->getTaskResultWithAnswers(
            $request->route('task_result'),
            $request->route('schoolId')
        );
        if ($data)
            return $this->ok($data, 'Data jawaban siswa');
        return $this->error('Data tidak ditemukan', 404);
    }

    public function answerCorrection(UpdateQuestionAnswerBulkRequest $request, TaskResultService $service)
    {
        $data = $service->answerCorrections(
            $request,
            $request->route('task_result'),
            $request->route('schoolId')
        );
        if ($data)
            return $this->created('berhasil mengoreksi jawaban siswa');
        return $this->error('Data tidak ditemukan', 404);
    }

    public function resultCorrection(UpdateTaskResultRequest $requestResult, UpdateQuestionAnswerBulkRequest $requestAnswer, TaskResultService $service)
    {
        $data = $service->resultCorrection(
            $requestResult,
            $requestAnswer,
            $requestResult->route('task_result'),
            $requestResult->route('schoolId')
        );
        if ($data)
            return $this->ok($data, 'berhasil mengoreksi jawaban siswa');
        return $this->error('Data tidak ditemukan', 404);
    }

    public function makeChance(Request $request, TaskResultService $service)
    {
        $data = $service->makeChance(
            $request->route('task_result'),
            $request->route('schoolId')
        );
        if ($data)
            return $this->ok($data, 'berhasil memberi kesempatan');
        return $this->error('Data tidak ditemukan', 404);
    }

    public function destroy(Request $request, TaskResultService $service)
    {
        $data = $service->delete($request->route('task_result'), $request->route('schoolId'));
        if ($data)
            return $this->ok([], 'Data hasil berhasil dihapus');
        return $this->error('Data tidak ditemukan', 404);
    }
}
