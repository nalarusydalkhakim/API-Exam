<?php

namespace Modules\Event\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventAdmin;
use Modules\Event\Entities\EventSponsor;
use Modules\Event\Http\Requests\Admin\CreateEventAdminRequest;
use Modules\Event\Http\Requests\Admin\InviteEventAdminRequest;
use Modules\Event\Http\Requests\Admin\UpdateEventSponsorRequest;

class EventAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|Lihat Panitia Event'])->only('index', 'show');
        $this->middleware(['role_or_permission:Super Admin|Tambah Panitia Event'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Panitia Event'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Panitia Event'])->only('destroy');
    }

    public function index(Event $event, Request $request)
    {
        $data = $event->admins()
            ->with('user.profile')
            ->when($request->has('search'), function ($q) use ($request) {
                $q->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->paginate($request->get('per_page', 10));
        return $this->ok($data, 'Data admin event');
    }

    public function store(Event $event, CreateEventAdminRequest $request)
    {
        try {
            $admin = $event->admins()->firstOrCreate([
                'user_id' => $request->user_id
            ]);

            return $this->ok($admin, 'berhasil menyimpan data');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function invite(Event $event, InviteEventAdminRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'status' => 'active'
            ]);

            $user->profile()->create([]);

            $admin = $event->admins()->create([
                'user_id' => $user->id
            ]);
            DB::commit();

            return $this->ok($admin, 'berhasil menyimpan data');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function show(EventAdmin $eventAdmin)
    {
        return $this->ok(($eventAdmin->load('user.profile')), 'detail data');
    }

    public function update(UpdateEventSponsorRequest $request, EventSponsor $eventSponsor)
    {
        $input = $request->validated();
        if ($request->has('photo')) {
            $input['photo'] = $request->file('photo')->store('event-photos');
        }

        return $this->ok($eventSponsor->update($input), 'berhasil menyimpan data');
    }

    public function destroy(EventAdmin $eventAdmin)
    {
        return $this->ok($eventAdmin->delete(), 'Data dihapus');
    }
}
