<?php

namespace Modules\LearningManagement\Repositories\School;

use Modules\LearningManagement\Entities\TaskSection;
use Modules\LearningManagement\Repositories\Base\TaskQuestionRepository as BaseTaskQuestionRepository;

class TaskQuestionRepository extends BaseTaskQuestionRepository
{

    public function getGroupBySection(String $taskId)
    {
        return TaskSection::with([
            'taskQuestions',
            'taskQuestions.questionOptions'
        ])
            ->where('task_id', $taskId)
            ->oldest()
            ->get();
    }

    public function simpleSelect()
    {
        $this->taskQuestionQuery = $this->taskQuestionQuery
            ->select([
                'task_questions.*'
            ]);
        return $this;
    }

    public function detailSelect()
    {
        $this->taskQuestionQuery = $this->taskQuestionQuery
            ->with('questionOptions')
            ->join('questions', 'questions.id', '=', 'task_questions.question_id')
            ->select([
                'task_questions.*',
                'questions.answer_type',
                'questions.question',
                'questions.file',
                'questions.file_name',
                'questions.explanation',
                'questions.level',
                'questions.visibility',
                'questions.owner_id as question_owner_id'
            ]);
        return $this;
    }
}
