<?php

namespace Modules\LearningManagement\Services\Admin;

use Modules\LearningManagement\Repositories\Admin\ClassSubjectRepository;
use Modules\LearningManagement\Repositories\Admin\CourseRepository;
use Modules\LearningManagement\Services\Base\ClassSubjectService as BaseClassSubjectService;

class ClassSubjectService extends BaseClassSubjectService
{
    protected $courseRepository, $classSubjectRepository;

    public function __construct()
    {
        $this->courseRepository = new CourseRepository;
        $this->classSubjectRepository = new ClassSubjectRepository;
    }
}
