<?php

namespace Modules\Notification\Interfaces;

interface CourseTaskInterface
{
    public function getClassSubjectId(): string;
    public function getCourseId(): string;
    public function getCourseTaskId(): string;
    public function getUserId(): string;
    public function getTitle(): string;
}
