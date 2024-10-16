<?php

namespace Modules\LearningManagement\Repositories\Admin;

use Modules\LearningManagement\Entities\Question;
use Modules\LearningManagement\Repositories\Base\QuestionRepository as BaseQuestionRepository;

class QuestionRepository extends BaseQuestionRepository
{
    public function __construct()
    {
        $this->questionQuery = new Question();
    }
}
