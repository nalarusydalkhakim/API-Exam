<?php

namespace Modules\LearningManagement\Listeners;

use Modules\LearningManagement\Events\CourseContentWasCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Notification\Services\NotificationService;

class NotifyUsersOfANewCourseContent implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param CourseContentWasCreated $event
     * @return void
     */
    public function handle(CourseContentWasCreated $event)
    {
        $notification = new NotificationService;
        $notification->sendCourseContentNotificationToClassSubjectId(
            $event->courseContentId,
            $event->courseId,
            $event->classSubjectId,
            'Terdapat materi baru',
            'Materi baru yang berjudul ' . $event->contentName
        );
    }
}
