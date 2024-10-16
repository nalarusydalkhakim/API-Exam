<?php

namespace Modules\Event\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventSponsor;
use Modules\Event\Http\Requests\Admin\CreateEventSponsorRequest;
use Modules\Event\Http\Requests\Admin\UpdateEventSponsorRequest;

class EventSponsorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|Lihat Sponsor Event'])->only('index', 'show');
        $this->middleware(['role_or_permission:Super Admin|Tambah Sponsor Event'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Sponsor Event'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Sponsor Event'])->only('destroy');
    }

    public function index(Event $event)
    {
        return $this->ok($event->sponsors, 'Data sponsor event');
    }

    public function store(Event $event, CreateEventSponsorRequest $request)
    {
        $input = $request->validated();
        if ($request->has('photo')) {
            $input['photo'] = $request->file('photo')->store('event-sponsors');
        }
        $event = $event->sponsors()->create($input);

        return $this->ok($event, 'berhasil menyimpan data');
    }

    public function show(EventSponsor $eventSponsor)
    {
        return $this->ok(($eventSponsor), 'detail data sponsor event');
    }

    public function update(UpdateEventSponsorRequest $request, EventSponsor $eventSponsor)
    {
        $input = $request->validated();
        if ($request->has('photo')) {
            $input['photo'] = $request->file('photo')->store('event-photos');
        }

        return $this->ok($eventSponsor->update($input), 'berhasil menyimpan data');
    }

    public function destroy(EventSponsor $eventSponsor)
    {
        return $this->ok($eventSponsor->delete(), 'Data dihapus');
    }
}
