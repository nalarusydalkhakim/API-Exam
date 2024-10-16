<?php

namespace Modules\LearningManagement\Services\School;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\LearningManagement\Repositories\School\EventTaskRepository;
use Modules\LearningManagement\Repositories\School\TaskResultRepository;
use Modules\LearningManagement\Services\Base\TaskResultService as BaseTaskResultService;

class TaskResultService extends BaseTaskResultService
{
    protected $eventTaskRepository;
    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->taskResultRepository = new TaskResultRepository;
        $this->eventTaskRepository = new EventTaskRepository;
    }

    public function getAllEventTaskResult(Request $request)
    {
        return $this->eventTaskRepository->getPaginateWithSummary(collect($request));
    }
    public function getTaskResultWithAnswers($taskResultId, $schoolId)
    {
        try {
            $data = $this->taskResultRepository->getTaskResultWithAnswers(
                $taskResultId,
                $schoolId
            );

            $data->user_photo = $data->user_photo ? Storage::url($data->user_photo) : '';

            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function answerCorrections(Request $request)
    {
        try {
            $request = collect($request->validated());
            foreach ($request->get('question_answers', []) as $key => $questionAnswer) {
                $this->taskResultRepository->answerCorrection(
                    collect($questionAnswer)->only('score', 'is_correct', 'feedback'),
                    collect($questionAnswer)->only('id')->get('id')
                );
            }
            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function resultCorrection(Request $requestResult, Request $requestAnswer, $taskResultId)
    {
        try {
            $input = collect($requestResult->validated());
            //$input['is_passed'] = $this->checkIsPassed($taskResultId, $requestResult->get('score'));
            $input['status'] = 'Selesai';
            $this->taskResultRepository->resultCorrection(
                $input,
                $taskResultId
            );
            $this->answerCorrections($requestAnswer);
            return $this->findById($taskResultId);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function makeChance($taskResultId, $schoolId)
    {
        try {
            $input = array();
            $input['created_at'] = now();
            $input['status'] = 'Belum Dikerjakan';
            $this->taskResultRepository->update(
                $taskResultId,
                collect($input),
                $schoolId
            );
            return $this->findById($taskResultId);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function checkIsPassed($taskResultId, $score)
    {
        $this->initRepository();
        $classSubject = $this->taskResultRepository->getKkmByTaskResultId($taskResultId);
        if ($classSubject) {
            return $classSubject->score_minimum < $score;
        }
        return false;
    }
}
