<?php

namespace Modules\Event\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventParticipant;

class EventParticipantController extends Controller
{
    public function index(Event $event, Request $request)
    {
        $data = $event->participants()
            ->with('user.profile')
            ->when($request->has('search'), function ($q) use ($request) {
                $q->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->paginate($request->get('per_page', 10));
        return $this->ok($data, 'Data peserta event');
    }

    public function show(EventParticipant $eventParticipant)
    {
        return $this->ok(($eventParticipant->load('user.profile')), 'detail data');
    }
}
