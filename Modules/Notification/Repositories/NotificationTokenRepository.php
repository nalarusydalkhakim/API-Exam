<?php

namespace Modules\Notification\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NotificationTokenRepository
{
    protected $notificationToken;
    public function __construct()
    {
        $this->notificationToken = DB::table('notification_tokens');
    }

    public function getByUserId($userId)
    {
        return $this->notificationToken
            ->where('notification_tokens.user_id', $userId)
            ->first();
    }

    public function create(String $userId, Collection $input)
    {
        $this->notificationToken
            ->updateOrInsert(['user_id' => $userId, 'device' => $input->get('device'), 'token' => $input->get('token')], $input->all());
        return $input->get('id');
    }

    public function delete($id, String $schoolId = null)
    {
        $data = $this->notificationToken
            ->where('notification_tokens.id', $id)
            ->delete();

        return $data;
    }
}
