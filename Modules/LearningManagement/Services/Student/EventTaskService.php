<?php

namespace Modules\LearningManagement\Services\Student;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\LearningManagement\Repositories\Student\EventTaskRepository;
use Modules\LearningManagement\Repositories\Student\TaskResultRepository;
use Modules\LearningManagement\Services\Base\EventTaskService as BaseEventTaskService;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EventTaskService
{
    protected $taskResultRepository, $eventTaskRepository;
    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->eventTaskRepository = new EventTaskRepository;
    }

    public function getAllByStudent(Request $request, $eventId, $studentId)
    {
        $data = $this->eventTaskRepository->getAll(
            collect($request->all()),
            $eventId,
            $studentId
        );
        $data = $data->map(function ($task) {
            $task->duration = Carbon::parse($task->start_at)->diffInMinutes($task->end_at);
            if ($task->status == 'Belum Dikerjakan') {
                $task->remaining_duration = $task->duration;
            } else if ($task->status == 'Sedang Dikerjakan') {
                $task->remaining_duration = Carbon::now()->diffInMinutes($task->end_at);
            } else {
                $task->remaining_duration = 0;
            }
            return $task;
        });
        return $data;
    }

    public function getAll(Request $request, $eventId = null, $studentId = null)
    {
        $data = $this->eventTaskRepository->getAll(collect($request->all()), $eventId, $studentId);
        return $data->map(function ($task) {
            $task->duration = Carbon::parse($task->start_at)->diffInMinutes($task->end_at);
            if ($task->status == 'Belum Dikerjakan') {
                $task->remaining_duration = $task->duration;
            } else if ($task->status == 'Sedang Dikerjakan') {
                $task->remaining_duration = Carbon::now()->diffInMinutes($task->end_at);
            } else {
                $task->remaining_duration = 0;
            }
            return $task;
        });
    }

    public function findById(String $id = null, $studentId = null)
    {
        try {
            $eventTask = $this->eventTaskRepository->findById($id, $studentId);
            if ($eventTask) {
                $eventTask->duration = Carbon::parse($eventTask->start_at)->diffInMinutes($eventTask->end_at);
                if ($eventTask->status == 'Belum Dikerjakan') {
                    $eventTask->remaining_duration = $eventTask->duration;
                } else if ($eventTask->status == 'Sedang Dikerjakan') {
                    $eventTask->remaining_duration = Carbon::now()->diffInMinutes($eventTask->end_at);
                } else {
                    $eventTask->remaining_duration = 0;
                }
                if ($eventTask->event_photo) {
                    $eventTask->event_photo = $eventTask->event_photo ? Storage::url($eventTask->event_photo) : null;
                }
                return $eventTask;
            }
            return null;
        } catch (\Throwable $th) {
            throw $th;
        }
        return null;
    }

    public function getTaskQuestionGroupBySection(String $id, $studentId, $resultStatus = 'Belum Dikerjakan')
    {
        $data = $this->eventTaskRepository->getTaskQuestionGroupBySection($id, $studentId);
        $taskQuestionCount = 0;
        foreach ($data as $section) {
            foreach ($section->taskQuestions as $taskQuestion) {
                $taskQuestionCount++;
                if ($taskQuestion) {
                    $taskQuestion->file = $taskQuestion->file ? Storage::url($taskQuestion->file) : null;
                }
                if ($resultStatus !== 'Selesai') {
                    foreach ($taskQuestion->questionOptions as $questionOption) {
                        $questionOption->is_correct = false;
                    }
                    if ($taskQuestion->answer) {
                        $taskQuestion->answer->score = null;
                        $taskQuestion->answer->is_correct = false;
                    }
                }
            }
        }
        if (!$taskQuestionCount) {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Ujian ini tidak memiliki soal');
        }
        return $data;
    }

    public function start(String $id, $studentId)
    {
        try {
            DB::beginTransaction();
            $eventTaskRepository = new EventTaskRepository;
            $task = $eventTaskRepository->findById($id, $studentId);

            if (!$task) {
                throw new AccessDeniedHttpException('Anda tidak dapat mengerjakan tugas ini');
            } else {
                $now = now();
                if (!$task->is_owned) {
                    throw new AccessDeniedHttpException('Anda tidak diizinkan mengikuti ujian ini, silahkan mengikuti event dulu');
                } else if ($task->start_at >= $now) {
                    throw new AccessDeniedHttpException('Waktu mengerjakan tugas belum dimulai');
                }
            }

            $this->taskResultRepository = new TaskResultRepository;
            $taskResult = $this->taskResultRepository->findById($task->event_task_result_id);

            if (!$taskResult) {
                $taskResult = $this->taskResultRepository->start($id, $studentId);
                $taskQuestions =  $this->getTaskQuestionGroupBySection($id, $studentId, $taskResult->status);
                $this->createEmptyAnswer($taskQuestions, $studentId, $task->point_empty);
            } else {
                $taskQuestions =  $this->getTaskQuestionGroupBySection($id, $studentId, $taskResult->status);
            }

            DB::commit();
            return $taskQuestions;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function isCanAnswer(String $id, $studentId)
    {
        $this->taskResultRepository = new TaskResultRepository;
        $taskStatus = $this->taskResultRepository->getTaskStatus($id, $studentId);
        $now = Carbon::now();
        if (!$taskStatus) {
            throw new AccessDeniedHttpException('Silahkan tekan mulai dahulu sebelum menjawab soal.');
        } else if ($taskStatus->status !== 'Sedang Dikerjakan' && $taskStatus->status !== 'Belum Dikerjakan') {
            throw new AccessDeniedHttpException('Ujian atau ujian yang sudah dikumpulkan tidak dapat diubah kembali.');
        } else if ($taskStatus->start_at >= $now) {
            throw new AccessDeniedHttpException('Ujian atau ujian belum waktunya dikerjakan.');
        } else if ($taskStatus->end_at <= $now) {
            throw new AccessDeniedHttpException('Waktu mengerjakan ujian atau ujian sudah berakhir.');
        }
    }

    public function answer(Request $request, String $taskQuestionId, String $eventTaskId, $studentId)
    {
        try {
            $this->isCanAnswer($eventTaskId, $studentId);
            $input = collect($request->only(['text', 'file', 'question_option_id']));

            if ($file = $request->file('file')) {
                $input->put('file_name', $file->getClientOriginalName());
                $input->put('file', $file->storeAs(
                    '/lms/event-tasks/' . $eventTaskId . '/task-questions/' . $taskQuestionId . '/file-answers',
                    Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-') . '-' . time() . '.' . $file->getClientOriginalExtension()
                ));
            }

            $input->put('user_id', $studentId);
            $input->put('task_question_id', $taskQuestionId);
            return $this->eventTaskRepository->createAnswer($input);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setMark(Request $request, String $taskQuestionId, String $eventTaskId, $studentId)
    {
        try {
            $input = collect($request->only(['mark']));

            $input->put('user_id', $studentId);
            $input->put('task_question_id', $taskQuestionId);
            return $this->eventTaskRepository->setMark($input);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function finish(String $id, $studentId)
    {
        try {
            $this->taskResultRepository = new TaskResultRepository;
            $eventTask = $this->eventTaskRepository->findById($id, $studentId);
            $point = null;
            if ($eventTask->auto_correction) {
                $point = $this->autoCorrection($eventTask->task_id, $studentId, $eventTask->point_correct, $eventTask->point_incorrect, $eventTask->point_empty);
            }
            return $this->taskResultRepository->finish($id, $studentId, $point);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function createEmptyAnswer(Collection $sections, $studentId, $point)
    {
        try {
            $inputs = [];
            $now = Carbon::now();
            foreach ($sections as $section) {
                foreach ($section->taskQuestions as $question) {
                    $inputs[] = [
                        'id' => Str::uuid()->toString(),
                        'task_question_id' => $question->id,
                        'user_id' => $studentId,
                        'score' => $point,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }
            $this->eventTaskRepository->createEmptyAnswer($inputs);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autoCorrection(String $taskId, $studentId, $correct, $incorrect, $empty)
    {
        try {
            return $this->eventTaskRepository->autoCorrection($taskId, $studentId, $correct, $incorrect, $empty);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
