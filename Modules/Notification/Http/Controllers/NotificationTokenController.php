<?php

namespace Modules\Notification\Http\Controllers;

use Modules\Notification\Http\Controllers\Controller;
use Modules\Notification\Http\Requests\CreateNotificationTokenRequest;
use Modules\Notification\Services\NotificationTokenService;

class NotificationTokenController extends Controller
{
    public function store(NotificationTokenService $service, CreateNotificationTokenRequest $request)
    {
        $service->create($request->user()->id, $request);
        return $this->created('Berhasil menyimpan token');
    }
}
