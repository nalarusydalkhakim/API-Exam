<?php

namespace Modules\LearningManagement\Services\School;

use Modules\LearningManagement\Repositories\School\QuestionRepository;
use Modules\LearningManagement\Services\Base\QuestionService as BaseQuestionService;

class QuestionService extends BaseQuestionService
{
    public function __construct()
    {
        $this->initRepository();
    }

    protected function initRepository()
    {
        $this->questionRepository = new QuestionRepository;
    }
}
