<?php

namespace Modules\LearningManagement\Services\Base;

use Modules\LearningManagement\Repositories\Base\QuestionOptionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionOptionService
{
    protected $questionOptionRepository;

    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->questionOptionRepository = new QuestionOptionRepository;
    }

    public function findById(String $id)
    {
        $data = $this->questionOptionRepository->findById($id);
        return $data;
    }

    public function create($questionId, Request $requestQuestionOption, $schoolId = null, $teacherId = null)
    {
        try {
            $inputQuestionOptions = [];
            $now = now();
            foreach ($requestQuestionOption->options as $value) {
                $inputQuestionOptions[] = array_merge(
                    $value,
                    [
                        'id' => Str::uuid()->toString(),
                        'question_id' => $questionId,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]
                    );
            }

            return $this->questionOptionRepository->create(collect($inputQuestionOptions));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update($id, Request $request, $questionId, String $schoolId = null, String $teacherId = null)
    {
        try {
            if ($this->questionOptionRepository->update(
                $id,
                collect($request->validated()),
                $schoolId,
                $teacherId
            )) {
                $this->initRepository();
                if ($request->is_correct) {
                    $this->questionOptionRepository->setCorrectAnswer($questionId, $id);
                }
                return $this->findById($id);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete(String $id, String $questionId = null, String $schoolId = null, String $teacherId = null)
    {
        return $this->questionOptionRepository->delete(
            $id,
            $questionId,
            $schoolId,
            $teacherId
        );
    }
}
