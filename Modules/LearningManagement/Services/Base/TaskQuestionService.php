<?php

namespace Modules\LearningManagement\Services\Base;

use Carbon\Carbon;
use Modules\LearningManagement\Repositories\Base\TaskQuestionRepository;
use Illuminate\Http\Request;
use Modules\LearningManagement\Repositories\Base\TaskRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\LearningManagement\Repositories\School\QuestionOptionRepository;
use Modules\LearningManagement\Repositories\School\QuestionRepository;
use Modules\LearningManagement\Services\School\QuestionService;
use Modules\LearningManagement\Services\School\TaskService;

class TaskQuestionService
{
    protected $taskQuestionRepository, $taskRepository;

    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->taskQuestionRepository = new TaskQuestionRepository;
        $this->taskRepository = new TaskRepository;
    }

    public function getAll(Request $request)
    {
        $data = $this->taskQuestionRepository->getAll($request->all());
        return $data;
    }

    public function getPaginate(Request $request, $userId = null, $eventId)
    {
        $request->query->add([
            'user_id' => $userId,
            'event_id' => $eventId
        ]);
        $data = $this->taskQuestionRepository->getPaginate($request->all(), $userId, $eventId);
        return $data;
    }

    public function findById(String $id)
    {
        $data = $this->taskQuestionRepository->findById($id);
        return $data;
    }

    public function getWhereInIds(array $ids)
    {
        $data = $this->taskQuestionRepository->getWhereInIds($ids);
        return $data;
    }

    public function getGroupBySection(String $taskId)
    {
        $data = $this->taskQuestionRepository->getGroupBySection($taskId);
        foreach ($data as $taskQuestion) {
            foreach ($taskQuestion->taskQuestions ?? [] as $question) {
                if ($question) {
                    $question->file = $question->file ? Storage::url($question->file) : null;
                }
            }
        }
        return $data;
    }

    public function create(String $taskId, String $taskSectionId, Request $requestTaskQuestion, Request $requestQuestion, String $userId = null)
    {
        try {
            DB::beginTransaction();
            $inputQuestion = collect($requestQuestion->validated())->except('options');
            $inputQuestion->put('id', Str::uuid()->toString());
            $inputQuestion->put('task_section_id', $taskSectionId);
            $inputQuestion->put('owner_id', $userId);
            if ($file = $requestQuestion->get('file')) {
                $inputQuestion->put('file_name', $file->getClientOriginalName());
                $inputQuestion->put('file', $file->storeAs(
                    '/lms/questions/' . $inputQuestion->get('id'),
                    Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-') . '-' . time() . '.' . $file->getClientOriginalExtension()
                ));
            }
            $taskService = new TaskService();
            $task = $taskService->findById($taskId);
            if ($task) {
                $inputQuestion->put('class', $task->class);
                $inputQuestion->put('subject_id', $task->subject_id);
            }
            $questionRepository = new QuestionRepository;
            $questionId = $questionRepository->create($inputQuestion);
            if ($requestQuestion->get('answer_type') == 'Pilihan Ganda') {
                $inputQuestionOptions = [];
                foreach (collect($requestQuestion->validated())->only('options')->get('options') as $option) {
                    $inputQuestionOptions[] = array_merge($option, [
                        'question_id' => $questionId,
                        'id' => Str::uuid()->toString()
                    ]);
                }
                $questionOptionRepository = new QuestionOptionRepository;
                $questionOptionRepository->create(collect($inputQuestionOptions));
            }
            $inputTaskQuestion = collect($requestTaskQuestion->validated());
            $inputTaskQuestion->put('task_section_id', $taskSectionId);
            $inputTaskQuestion->put('question_id', $questionId);
            $taskQuestionId = $this->taskQuestionRepository->create($inputTaskQuestion);
            DB::commit();
            if ($taskQuestionId) {
                $this->initRepository();
                return $this->findById($taskQuestionId);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function attach(Request $request)
    {
        try {
            $inputTaskQuestion = collect($request->validated());
            $inputTaskQuestion->put('id', Str::uuid()->toString());
            $inputTaskQuestion->put('created_at', now());
            $inputTaskQuestion->put('updated_at', now());
            $this->taskQuestionRepository->attach($inputTaskQuestion);
            $this->initRepository();
            return $this->findById($inputTaskQuestion->get('id'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update($taskQuestionId, Request $requestTaskQuestion, Request $requestQuestion, String $taskId = null, String $userId = null)
    {
        try {
            $taskQuestion = $this->findById($taskQuestionId);
            $this->initRepository();
            $this->taskQuestionRepository->update(
                $taskQuestionId,
                collect($requestTaskQuestion->validated()),
                $taskId,
                $userId
            );
            $questionService = new QuestionService();
            $questionService->update($taskQuestion->question_id, $requestQuestion, $userId);
            $this->initRepository();
            return $this->findById($taskQuestionId);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete(String $id)
    {
        return $this->taskQuestionRepository->delete($id);
    }
}
