<?php

namespace Modules\Notification\Services;

use Illuminate\Http\Request;
use Modules\Notification\Entities\Notification;
use Modules\Notification\Entities\User;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Modules\AcademicAndCurriculum\Entities\ClassSubject;
use Modules\AcademicAndCurriculum\Entities\StudentSubject;
use Modules\Notification\Notifications\Announcement;
use Modules\Notification\Notifications\CourseContent;
use Modules\Notification\Notifications\CourseTask;
use Modules\Notification\Transformers\NotificationResource;

class NotificationService
{
    public function getNotificationsByUserId(String $userId, $request)
    {
        return NotificationResource::collection(
            User::find($userId)
                ->notifications()
                ->when($type = $request->type, function ($q) use ($type) {
                    $q->where('type', $this->getNotificationClass($type));
                })
                ->latest()
                ->take(30)
                ->get()
        );
    }

    public function markReadNotificationsByUserId(String $userId)
    {
        return User::find($userId)->notifications()->update(['read_at' => now()]);
    }

    public function markReadNotificationById(String $id)
    {
        return Notification::find($id)->update(['read_at' => now()]);
    }

    public function deleteNotificationById(String $id)
    {
        return Notification::find($id)->update(['read_at' => now()]);
    }

    public function sendCourseTaskNotificationToUserIds(String $classSubjectId, String $courseId, String $courseTaskId,  array $userIds, $title = 'notifikasi tugas', String $desc = 'terdapat notifikasi tugas')
    {
        $users = User::whereIn('id', $userIds)->select('id', 'email')->get();
        $data = [
            'id' => $courseTaskId,
            'title' => $title,
            'desc' => $desc,
            'meta' => [
                'classSubjectId' => $classSubjectId,
                'courseId' => $courseId,
                'courseTaskId' => $courseTaskId,
                'webLink' => ''
            ]
        ];
        return NotificationFacade::send($users, new CourseTask($data));
    }

    public function sendCourseContentNotificationToUserIds(String $classSubjectId, String $courseId, String $courseContentId,  array $userIds, $title = 'Notifikasi tugas', String $desc = 'Terdapat notifikasi materi')
    {
        $users = User::whereIn('id', $userIds)->select('id', 'email')->get();
        $data = [
            'id' => $courseContentId,
            'title' => $title,
            'desc' => $desc,
            'meta' => [
                'classSubjectId' => $classSubjectId,
                'courseId' => $courseId,
                'courseContentId' => $courseContentId,
                'webLink' => ''
            ]
        ];
        return NotificationFacade::send($users, new CourseContent($data));
    }

    public function sendAnnouncementNotificationToUserIds(array $userIds, $title = 'Pemberitahuan', String $desc = 'Terdapat pemberitahuan')
    {
        $users = User::whereIn('id', $userIds)->select('id', 'email')->get();
        $data = [
            'id' => '-',
            'title' => $title,
            'desc' => $desc,
        ];
        return NotificationFacade::send($users, new Announcement($data));
    }

    public function sendCourseTaskNotificationToClassSubjectId(String $courseTaskId, String $classSubjectId, $title = 'Terdapat tugas baru')
    {
        $classSubject = ClassSubject::find($classSubjectId);
        $data = [
            'id' => $courseTaskId,
            'title' => $title,
            'desc' => 'tugas baru pada'
        ];
        return NotificationFacade::send($classSubject, new CourseTask($data));
    }

    public function sendCourseContentNotificationToClassSubjectId(String $courseContentId, String $courseId, String $classSubjectId, $title = 'Materi baru', String $desc = 'Terdapat tugas baru')
    {
        $userIds = StudentSubject::where('class_subject_id', $classSubjectId)->pluck('student_id')->toArray();
        return $this->sendCourseContentNotificationToUserIds(
            $classSubjectId,
            $courseId,
            $courseContentId,
            $userIds,
            $title,
            $desc
        );
    }


    public function sendTaskNotificationToUsers(array $userIds, $data)
    {
        $users = User::where('id', $userIds)->get();
        return NotificationFacade::send($users, new CourseTask($data));
    }

    public function getNotificationClass($type)
    {
        $types = [
            'announcement' => 'Modules\Notification\Notifications\Announcement',
            'task' => 'Modules\Notification\Notifications\Task',
            'content' => 'Modules\Notification\Notifications\Content',
            'course-task' => 'Modules\Notification\Notifications\CourseTask',
            'course-content' => 'Modules\Notification\Notifications\CourseContent'
        ];

        return $types[$type] ?? '-';
    }
}
