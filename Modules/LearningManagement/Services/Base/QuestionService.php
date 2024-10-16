<?php

namespace Modules\LearningManagement\Services\Base;

use Carbon\Carbon;
use Exception;
use Modules\LearningManagement\Repositories\Base\QuestionRepository;
use Illuminate\Http\Request;
use Modules\LearningManagement\Repositories\Base\QuestionOptionRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDOException;

class QuestionService
{
    protected $questionRepository;

    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->questionRepository = new QuestionRepository;
    }

    public function getPaginate(Request $request, $userId = null)
    {
        $request->query->add([
            'user_id' => $userId
        ]);
        return $this->questionRepository->getPaginate($request->all(), $userId);
    }

    public function findById(String $id)
    {
        return $this->questionRepository->findById($id);
    }

    public function create(Request $request, $teacherId = null)
    {
        try {
            DB::beginTransaction();
            $now = Carbon::now();
            $question = collect($request->validated());
            $question->put('id', Str::uuid()->toString());
            $question->put('owner_id', $teacherId);
            if ($file = $question->get('file')) {
                $question->put('file_name', $file->getClientOriginalName());
                $question->put('file', $file->storeAs(
                    'lms/user/' . $teacherId . '/questions/' . $question->get('id'),
                    Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-') . '-' . time() . '.' . $file->getClientOriginalExtension()
                ));
            }
            $questionId = $this->questionRepository->create($question->except('options'));
            if ($question->get('answer_type') == 'Pilihan Ganda') {
                $questionOptionRepository = new QuestionOptionRepository;
                $inputQuestionOptions = [];
                foreach ($question->get('options') as $option) {
                    $inputQuestionOptions[] = array_merge(
                        $option,
                        [
                            'id' => Str::uuid()->toString(),
                            'question_id' => $questionId,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]
                    );
                }
                $questionOptionRepository->create(collect($inputQuestionOptions));
            }
            DB::commit();
            $this->initRepository();
            return $this->findById($questionId);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function createBulk(Request $request, $teacherId = null)
    {
        try {
            DB::beginTransaction();
            $questions = $request->questions;
            $now = Carbon::now();
            foreach ($questions as $inputQuestion) {
                $question = collect($inputQuestion);
                $question->put('id', Str::uuid()->toString());
                $question->put('teacher_id', $teacherId);
                if ($file = $question->get('file')) {
                    $question->put('file_name', $file->getClientOriginalName());
                    $question->put('file', $file->storeAs(
                        'schools/lms/' . $teacherId . '/questions/' . $question->get('id'),
                        Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-') . '-' . time() . '.' . $file->getClientOriginalExtension()
                    ));
                }
                $questionId = $this->questionRepository->create($question->except('options'));
                if ($question->get('answer_type') == 'Pilihan Ganda') {
                    $questionOptionRepository = new QuestionOptionRepository;
                    $inputQuestionOptions = [];
                    foreach ($question->get('options') as $option) {
                        $inputQuestionOptions[] = array_merge(
                            $option,
                            [
                                'id' => Str::uuid()->toString(),
                                'question_id' => $questionId,
                                'created_at' => $now,
                                'updated_at' => $now
                            ]
                        );
                    }
                    $questionOptionRepository->create(collect($inputQuestionOptions));
                }
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function update($id, Request $request, String $teacherId = null)
    {
        try {
            DB::beginTransaction();
            $now = Carbon::now();
            $question = collect($request->validated());
            if ($file = $question->get('file')) {
                $question->put('file_name', $file->getClientOriginalName());
                $question->put('file', $file->storeAs(
                    'schools/lms/' . $teacherId . '/questions/' . $question->get('id'),
                    Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-') . '-' . time() . '.' . $file->getClientOriginalExtension()
                ));
            }
            $isUpdated = $this->questionRepository->update(
                $id,
                $question->except('options'),
                $teacherId
            );
            if ($isUpdated) {
                $questionOptionIds = [];
                if ($question->get('answer_type') == 'Pilihan Ganda') {
                    $questionOptionRepository = new QuestionOptionRepository;
                    $inputQuestionOptions = [];
                    foreach ($question->get('options', []) as $option) {
                        $option = collect($option);
                        if ($option->get('id')) {
                            $questionOptionRepository->update(
                                $option->get('id'),
                                $option
                            );
                            $questionOptionIds[] = $option->get('id');
                        } else {
                            $inputQuestionOptions[] = array_merge(
                                $option->all(),
                                [
                                    'id' => Str::uuid()->toString(),
                                    'question_id' => $id,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ]
                            );
                        }
                    }
                    $questionOptionRepository->deleteWhereNotIn($id, $questionOptionIds);
                    $questionOptionRepository->create(collect($inputQuestionOptions));
                }
            } else {
                return false;
            }
            DB::commit();
            $this->initRepository();
            return $this->findById($id);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function delete(String $id, String $teacherId = null)
    {
        try {
            return $this->questionRepository->delete(
                $id,
                $teacherId
            );
        } catch (PDOException $e) {
            if ($e->getCode() == '23503') {
                throw new Exception('Gagal menghapus, Soal ini sudah dikerjakan siswa atau berhubungan dengan data lain.');
            } else {
                throw $e;
            }
        }
    }
}
