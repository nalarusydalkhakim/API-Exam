<?php

namespace Modules\Notification\Services;

use Illuminate\Http\Request;
use Modules\Notification\Repositories\NotificationTokenRepository;
use Illuminate\Support\Str;
use Kreait\Firebase\Contract\Messaging;
use Modules\Pds\Services\EmployeeService;
use Modules\Pds\Services\StudentService;

class NotificationTokenService
{
    protected $notificationTokenRepo, $messaging;

    public function __construct(NotificationTokenRepository $notificationTokenRepo, Messaging $messaging)
    {
        $this->notificationTokenRepo = $notificationTokenRepo;
        $this->messaging = $messaging;
    }

    public function create(string $userId, Request $request)
    {
        try {
            $input = collect($request->validated());
            $input->put('id', Str::uuid()->toString());
            $this->subscribeUserToTopic($request);
            return $this->notificationTokenRepo->create($userId, $input);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function subscribeUserToTopic($request)
    {
        try {
            $user = $request->user();
            $schoolId = null;
            if ($user->level == 'student') {
                $schoolId = (new StudentService)->findById($user->id)?->school_id;
            } else if ($user->level == 'school') {
                $schoolId = (new EmployeeService)->findById($user->id)?->school_id;
            }
            $this->messaging->subscribeToTopic('pasinaon-announcement', $request->token);
            if ($schoolId) {
                $this->messaging->subscribeToTopic('school-' . $schoolId, $request->token);
            }
        } catch (\Exception $e) {
            throw new \Exception("Error subscribing user to topic: " . $e->getMessage());
        }
    }
}
