<?php

namespace Modules\LearningManagement\Services\School;

use Modules\LearningManagement\Repositories\School\QuestionOptionRepository;
use Modules\LearningManagement\Services\Base\QuestionOptionService as BaseQuestionOptionService;

class QuestionOptionService extends BaseQuestionOptionService
{
    public function __construct()
    {
        $this->questionOptionRepository = new QuestionOptionRepository;
    }
}
