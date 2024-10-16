<?php

namespace Modules\Notification\Entities;

use App\Models\User as BaseUser;
use Illuminate\Notifications\Notifiable;

class User extends BaseUser
{
    use Notifiable;

    public function routeNotificationForFcm($notification)
    {
        return $this->notificationTokens()->pluck('token')->toArray();
    }

    public function notificationTokens()
    {
        return $this->hasMany(NotificationToken::class);
    }
}
