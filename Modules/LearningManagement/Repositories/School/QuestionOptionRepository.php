<?php

namespace Modules\LearningManagement\Repositories\School;

use Modules\LearningManagement\Repositories\Base\QuestionOptionRepository as BaseQuestionOptionRepository;

class QuestionOptionRepository extends BaseQuestionOptionRepository
{
    protected $questionOptionQuery;
    
    public function simpleSelect()
    {
        $this->questionOptionQuery = $this->questionOptionQuery
            ->select([
                'question_options.*'
            ]);
        return $this;
    }

    public function detailSelect()
    {
        $this->questionOptionQuery = $this->questionOptionQuery
            ->select([
                'question_options.*'
            ]);
        return $this;
    }
}
