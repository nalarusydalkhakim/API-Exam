<?php

namespace Modules\Notification\Entities;

use Modules\Notification\Interfaces\CourseTaskInterface;

class CourseTask implements CourseTaskInterface
{
    private $classSubjectId;
    private $courseId;
    private $courseTaskId;
    private $userId;
    private $title;

    public function __construct(
        string $classSubjectId,
        string $courseId,
        string $courseTaskId,
        string $userId,
        string $title = 'Terdapat tugas baru'
    ) {
        $this->classSubjectId = $classSubjectId;
        $this->courseId = $courseId;
        $this->courseTaskId = $courseTaskId;
        $this->userId = $userId;
        $this->title = $title;
    }

    public function getClassSubjectId(): string
    {
        return $this->classSubjectId;
    }

    public function getCourseId(): string
    {
        return $this->courseId;
    }

    public function getCourseTaskId(): string
    {
        return $this->courseTaskId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
