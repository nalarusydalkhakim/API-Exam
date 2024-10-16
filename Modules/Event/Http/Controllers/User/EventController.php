<?php

namespace Modules\Event\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Event\Entities\Event;

class EventController extends Controller
{
    public function index(Request $request)
    {
        return $this->ok(
            Event::latest()
                ->leftJoin('event_participants', function ($q) use ($request) {
                    $q->on('event_participants.event_id', 'events.id')
                        ->where('event_participants.user_id', $request->user()->id);
                })
                ->select([
                    'events.*',
                    DB::raw('CASE WHEN COUNT(event_participants.id) > 0 THEN 1 ELSE 0 END as is_participant')
                ])
                ->withCount([
                    'participants'
                ])
                ->when($request->search, function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                })
                ->when($request->type == 'joined', function ($q) use ($request) {
                    $q->whereNotNull('event_participants.id');
                })
                ->when($request->status, function ($q) use ($request) {
                    $q->where('events.status', $request->status);
                })
                ->where('is_visible', true)
                ->groupBy('events.id')
                ->paginate($request->get('per_page', 20)),
            'Data event'
        );
    }

    public function show(Event $event, Request $request)
    {
        $data =  $event
            ->load([
                'payment' => function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id)->latest();
                },
                'sponsors'
            ])->loadCount([
                'participants as is_participant' => function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
                },
                'participants'
            ]);
        return $this->ok(
            $data,
            'detail data event'
        );
    }
}
