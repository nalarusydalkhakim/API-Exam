<?php

namespace Modules\Event\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Event\Entities\Event;
use Modules\Event\Http\Requests\Admin\CreateEventRequest;
use Modules\Event\Http\Requests\Admin\UpdateEventRequest;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|Lihat Data Event'])->only('index', 'show');
        $this->middleware(['role_or_permission:Super Admin|Tambah Data Event'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Data Event'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Data Event'])->only('destroy');
    }

    public function index(Request $request)
    {
        return $this->ok(
            Event::latest()
                ->leftJoin('event_admins', function ($q) use ($request) {
                    $q->on('event_admins.event_id', 'events.id')
                        ->where('event_admins.user_id', $request->user()->id);
                })
                ->select([
                    'events.*',
                    DB::raw('CASE WHEN COUNT(event_admins.id) > 0 THEN 1 ELSE 0 END as is_participant')
                ])
                ->withCount([
                    'participants'
                ])
                ->when($request->search, function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                })
                ->when($request->type == 'joined' && $request->user()->level === 'committee', function ($q) use ($request) {
                    $q->whereNotNull('event_admins.id');
                })
                ->when($request->status, function ($q) use ($request) {
                    $q->where('events.status', $request->status);
                })
                ->when($request->has('is_visible'), function ($q) use ($request) {
                    $q->where('events.is_visible', $request->is_visible);
                })
                ->groupBy('events.id')
                ->paginate($request->get('per_page', 20)),
            'Data event'
        );
    }

    public function store(CreateEventRequest $requestEvent)
    {
        $inputEvent = $requestEvent->validated();
        if ($requestEvent->has('photo')) {
            $inputEvent['photo'] = $requestEvent->file('photo')->store('event-photos');
        }
        $inputEvent['status'] = 'Belum Mulai';
        $event = Event::create($inputEvent);

        return $this->ok($event, 'berhasil menyimpan data');
    }

    public function show(Event $event, Request $request)
    {
        $data =  $event
            ->load([
                'payment' => function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id);
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

    public function update(UpdateEventRequest $request, Event $event)
    {
        $input = $request->validated();
        if ($request->has('photo')) {
            $input['photo'] = $request->file('photo')->store('event-photos');
        }

        return $this->ok($event->update($input), 'berhasil menyimpan data');
    }

    public function destroy(Event $event)
    {
        return $this->ok($event->delete(), 'Data dihapus');
    }
}
