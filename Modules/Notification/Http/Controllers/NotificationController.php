<?php

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\Request;
use Modules\LearningManagement\Entities\CourseTask;
use Modules\Notification\Http\Controllers\Controller;
use Modules\Notification\Services\NotificationService;

class NotificationController extends Controller
{
    public function index(NotificationService $service, Request $request)
    {
        return $this->ok($service->getNotificationsByUserId($request->user()->id, $request), 'Data notifikasi');
    }

    public function read(NotificationService $service, Request $request)
    {
        return $this->ok($service->markReadNotificationById($request->route('notification')), 'Berhasil melihat notifikasi');
    }

    public function readAll(NotificationService $service, Request $request)
    {
        return $this->ok($service->markReadNotificationsByUserId($request->user()->id), 'Berhasil melihat semua notifikasi');
    }

    public function addCourseTask(NotificationService $service, Request $request)
    {
        return $this->ok($service->sendCourseTaskNotificationToUserIds(
            $request->get('classSubjectId'),
            $request->get('courseId'),
            $request->get('courseTaskId'),
            [$request->user()->id]
        ), 'Tugas Baru');
    }

    public function addCourseContent(NotificationService $service, Request $request)
    {
        return $this->ok($service->sendCourseContentNotificationToUserIds(
            $request->get('classSubjectId'),
            $request->get('courseId'),
            $request->get('courseContentId'),
            [$request->user()->id]
        ), 'Materi Baru');
    }

    public function addAnnouncement(NotificationService $service, Request $request)
    {
        return $this->ok($service->sendAnnouncementNotificationToUserIds([$request->user()->id], $request->title, $request->desc), 'Pengumuman Baru');
    }
}
