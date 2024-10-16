<?php

namespace Modules\LearningManagement\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\LearningManagement\Events\CourseContentWasCreated;
use Modules\LearningManagement\Listeners\NotifyUsersOfANewCourseContent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CourseContentWasCreated::class => [
            NotifyUsersOfANewCourseContent::class
        ]
    ];
}
