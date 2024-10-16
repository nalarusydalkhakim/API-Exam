<?php

namespace Modules\LearningManagement\Events;

use Illuminate\Queue\SerializesModels;

class CourseContentWasCreated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        protected String $classSubjectId,
        protected String $courseId,
        protected String $courseContentId,
        protected String $userIds,
        protected String $contentName,
        protected String $title,
        protected String $desc
    ) {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
