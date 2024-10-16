<?php

namespace Modules\Event\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventParticipant;
use Modules\Event\Entities\EventSponsor;
use Modules\Event\Http\Requests\Admin\CreateEventParticipantRequest;
use Modules\Event\Http\Requests\Admin\InviteEventParticipantRequest;
use Modules\Event\Http\Requests\Admin\UpdateEventSponsorRequest;

class EventParticipantController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|Tambah Peserta Event'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Peserta Event'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Peserta Event'])->only('destroy');
    }

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

    public function store(Event $event, CreateEventParticipantRequest $request)
    {
        try {
            $participant = $event->participants()->firstOrCreate([
                'user_id' => $request->user_id
            ]);

            return $this->ok($participant, 'berhasil menyimpan data');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function invite(Event $event, InviteEventParticipantRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'status' => 'active'
            ]);

            $user->syncRoles('Peserta Event');

            $user->profile()->create([]);

            $participant = $event->participants()->create([
                'user_id' => $user->id
            ]);
            DB::commit();

            return $this->ok($participant, 'berhasil menyimpan data');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function show(EventParticipant $eventParticipant)
    {
        return $this->ok(($eventParticipant->load('user.profile')), 'detail data');
    }

    public function update(UpdateEventSponsorRequest $request, EventSponsor $eventSponsor)
    {
        $input = $request->validated();
        if ($request->has('photo')) {
            $input['photo'] = $request->file('photo')->store('event-photos');
        }

        return $this->ok($eventSponsor->update($input), 'berhasil menyimpan data');
    }

    public function destroy(EventParticipant $eventParticipant)
    {
        return $this->ok($eventParticipant->delete(), 'Data dihapus');
    }
}
