<?php

namespace Modules\LearningManagement\Http\Controllers\General;

use Modules\LearningManagement\Http\Controllers\Base\SubjectController as BaseSubjectController;
use Modules\LearningManagement\Services\Base\SubjectService;

class SubjectController extends BaseSubjectController
{
    public function __construct()
    {
        $this->service = new SubjectService;
    }
}
