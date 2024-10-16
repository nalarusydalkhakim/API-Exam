<?php

namespace Modules\LearningManagement\Repositories\Base;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\LearningManagement\Entities\TaskQuestion;

class TaskQuestionRepository
{
    protected $taskQuestionQuery;
    public function __construct()
    {
        $this->taskQuestionQuery = new TaskQuestion();
    }

    public function getAll(array $request)
    {
        $this->filter($request)->simpleSelect();
        return $this->taskQuestionQuery
            ->get();
    }

    public function getPaginate(array $request): Paginator
    {
        $this->filter($request)->simpleSelect();
        return $this->taskQuestionQuery
            ->simplePaginate($request['per_page'] ?? 10)
            ->appends($request);
    }

    public function findById($id)
    {
        $this->detailSelect();
        return $this->taskQuestionQuery
            ->where('task_questions.id', $id)
            ->first();
    }

    public function getWhereInIds(array $ids)
    {
        return TaskQuestion::with('question.options')
            ->whereIn('task_questions.id', $ids)
            ->get();
    }

    public function getQuestionTypeById($id)
    {
        return $this->taskQuestionQuery
            ->join('questions', 'questions.id', 'task_questions.question_id')
            ->where('task_questions.id', $id)
            ->select('questions.answer_type')
            ->first();
    }

    public function findByTaskIdAndCourseId($taskId, String $courseId)
    {
        $this->detailSelect();
        return $this->taskQuestionQuery
            ->where('task_questions.question_id', $courseId)
            ->where('task_questions.task_id', $taskId)
            ->first();
    }

    public function create(Collection $input)
    {
        $input->put('id', $input->get('id') ?? Str::uuid()->toString());
        $input->put('created_at', now());
        $input->put('updated_at', $input->get('created_at'));
        $this->taskQuestionQuery
            ->insert($input->all());
        return $input->get('id');
    }

    public function createBulk(Collection $input)
    {
        return $this->taskQuestionQuery
            ->insert($input->all());
    }

    public function attach(Collection $input)
    {
        return $this->taskQuestionQuery
            ->insert($input->all());
    }

    public function update($taskQuestionId, Collection $input, String $userId = null)
    {
        $input->put('updated_at', now());
        return $this->taskQuestionQuery
            ->where('task_questions.id', $taskQuestionId)
            ->when($userId, function ($q) use ($userId) {
                $q->join('questions', 'questions.id', 'task_questions.question_id');
                $q->where('questions.owner_id', $userId);
            })
            ->update($input->all());
    }

    private function order(array $request)
    {
        $this->taskQuestionQuery = $this->taskQuestionQuery->when(isset($request['order_field']) && isset($request['order_direction']), function ($q) use ($request) {
            $q->orderBy($request['order_field'], $request['order_direction']);
        })
            ->when(!isset($request['order_field']) || !isset($request['order_direction']), function ($q) use ($request) {
                $q->orderBy('questions.created_at', 'asc');
            });
    }

    public function delete($id)
    {
        $data = $this->taskQuestionQuery
            ->where('task_questions.id', $id)
            ->delete();

        return $data;
    }

    public function filter(array $request)
    {
        $filter = collect($request);
        $this->taskQuestionQuery = $this->taskQuestionQuery
            ->when($filter->get('search'), function ($q) use ($filter) {
                $q->where(function ($q) use ($filter) {
                    $q->Where('task_questions.name', 'ilike', '%' . $filter->get('search') . '%');
                });
            })->when($filter->get('name'), function ($q) use ($filter) {
                $q->Where('task_questions.name', 'ilike', '%' . $filter->get('name') . '%');
            })->when($filter->get('class_subject_id'), function ($q) use ($filter) {
                $q->Where('task_questions.class_subject_id', $filter->get('class_subject_id'));
            })->when($filter->get('is_can_access'), function ($q) use ($filter) {
                $q->Where('task_questions.is_can_access', $filter->get('is_can_access'));
            });
        return $this;
    }

    public function simpleSelect()
    {
        return $this;
    }

    public function detailSelect()
    {
        return $this;
    }
}
