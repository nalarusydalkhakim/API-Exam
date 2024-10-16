<?php

namespace Modules\Event\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Event\Entities\EventPayment;

class EventPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:Super Admin|Lihat Data Pembayaran'])->only('index', 'show');
        $this->middleware(['role_or_permission:Super Admin|Tambah Data Pembayaran'])->only('store');
        $this->middleware(['role_or_permission:Super Admin|Edit Data Pembayaran'])->only('update');
        $this->middleware(['role_or_permission:Super Admin|Hapus Data Pembayaran'])->only('destroy');
    }

    public function index(Request $request)
    {
        return $this->ok(
            EventPayment::latest()
                ->with('event', 'user')
                ->when($request->search, function ($q) use ($request) {
                    $q->where(function ($q) use ($request) {
                        $q->where('code', $request->search)
                            ->orWhereHas('user', function ($q) use ($request) {
                                $q->where('name', 'like', '%' . $request->search . '%');
                            })
                            ->orWhereHas('event', function ($q) use ($request) {
                                $q->where('name', 'like', '%' . $request->search . '%');
                            });
                    });
                })
                ->paginate(),
            'Data pembayaran event'
        );
    }

    public function show($eventPayment, Request $request)
    {
        return $this->ok(
            EventPayment::with('event', 'user.profile')
                ->find($eventPayment),
            'Data pembayaran event'
        );
    }

    public function approve(EventPayment $eventPayment)
    {
        if ($eventPayment->status == 'Sukses') {
            throw ValidationException::withMessages([
                'user_id' => ['Transaksi ini telah dibayar'],
            ]);
        }
        try {
            DB::beginTransaction();
            $eventPayment->status = 'Sukses';
            $eventPayment->save();
            $eventPayment->createEventParticipant();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $this->ok($eventPayment, 'berhasil menerima pembayaran');
    }

    public function cancel(EventPayment $eventPayment)
    {
        if ($eventPayment->status == 'Sukses') {
            throw ValidationException::withMessages([
                'user_id' => ['Transaksi ini telah dibayar'],
            ]);
        }

        $eventPayment->status = 'Gagal';
        $eventPayment->save();

        return $this->ok($eventPayment, 'berhasil membatalkan pembayaran');
    }
}
