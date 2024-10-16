<?php

namespace Modules\LearningManagement\Repositories\Base;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\LearningManagement\Entities\TaskResult;
use Modules\LearningManagement\Entities\TaskSection;

class TaskResultRepository
{
    protected $taskResultQuery;
    public function __construct()
    {
        $this->taskResultQuery = new TaskResult();
    }

    public function getAll(array $request, String $schoolId = null)
    {
        $this->filter($request, $schoolId)->simpleSelect();
        return $this->taskResultQuery
            ->all();
    }

    public function getPaginate(array $request): LengthAwarePaginator
    {
        $this->filter($request)->simpleSelect();
        return $this->taskResultQuery
            ->paginate($request['per_page'] ?? 10)
            ->appends($request);
    }

    public function getTaskResultWithAnswers(String $taskResultId)
    {
        $taskResult =  TaskResult::where('task_results.id', $taskResultId)
            ->join('event_tasks', 'event_tasks.id', 'task_results.event_task_id')
            ->join('tasks', 'tasks.id', 'event_tasks.task_id')
            ->join('users', 'users.id', 'task_results.user_id')
            ->select(
                'task_results.*',
                'event_tasks.task_id',
                'event_tasks.point_correct as task_point_correct',
                'event_tasks.point_incorrect as task_point_incorrect',
                'event_tasks.point_empty as task_point_empty',
                'users.name as user_name',
                'users.photo as user_photo',
                'users.email as user_email',
            )
            ->firstOrFail();
        $taskResult->task_sections = TaskSection::with([
            'taskQuestions.questionOptions',
            'taskQuestions.answer' => function ($q) use ($taskResult) {
                $q->where('question_answers.user_id', $taskResult->user_id);
            }
        ])
            ->where('task_id', $taskResult->task_id)
            ->oldest()
            ->get();
        return $taskResult;
    }

    public function findById($id)
    {
        $this->detailSelect();
        return $this->taskResultQuery
            ->where('task_results.id', $id)
            ->first();
    }

    public function getKkmByTaskResultId($taskResultId)
    {
        return $this->taskResultQuery
            ->where('task_results.id', $taskResultId)
            ->join('course_tasks', 'course_tasks.id', 'task_results.course_task_id')
            ->join('courses', 'courses.id', 'course_tasks.course_id')
            ->join('class_subjects', 'class_subjects.id', 'courses.class_subject_id')
            ->select('class_subjects.score_minimum')
            ->first();
    }

    public function create(Collection $input)
    {
        $input->put('id', $input->get('id') ?? Str::uuid()->toString());
        $input->put('created_at', now());
        $input->put('updated_at', $input->get('created_at'));
        $this->taskResultQuery
            ->create($input->all());
        return $input->get('id');
    }

    public function update($id, Collection $input)
    {
        $input->put('updated_at', now());
        return $this->taskResultQuery
            ->where('task_results.id', $id)
            ->update($input->all());
    }

    public function delete($id)
    {
        return $this->taskResultQuery
            ->where('task_results.id', $id)
            ->delete();
    }

    public function filter(array $request)
    {
        $filter = collect($request);
        $this->taskResultQuery = $this->taskResultQuery
            ->when($filter->get('search'), function ($q) use ($filter) {
                $q->where(function ($q) use ($filter) {
                    $q->Where('students.name', 'ilike', '%' . $filter->get('search') . '%');
                });
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
