<?php

namespace Modules\Event\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Event\Entities\Event;
use Modules\Event\Entities\EventParticipant;
use Modules\Event\Entities\EventPayment;
use Modules\Event\Http\Requests\User\CreateEventPaymentRequest;

class EventPaymentController extends Controller
{
    public function index(Request $request)
    {
        return $this->ok(
            EventPayment::latest()
                ->with('event')
                ->where('user_id', $request->user()->id)
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
                ->where('user_id', $request->user()->id)
                ->find($eventPayment),
            'Data pembayaran event'
        );
    }

    public function store(CreateEventPaymentRequest $request)
    {
        $event = Event::withCount('participants')
            ->where('id', $request->event_id)
            ->firstOrFail();
        if ($event->participants()->where('user_id', $request->user()->id)->exists()) {
            throw ValidationException::withMessages([
                'user_id' => ['Anda sudah tergabung pada event ini'],
            ]);
        } else if (EventPayment::where('event_id', $request->event_id)->where('user_id', $request->user()->id)->whereIn('status', ['Belum Dibayar', 'Menunggu Pembayaran'])->exists()) {
            throw ValidationException::withMessages([
                'user_id' => ['Anda sudah melakukan order pada event ini, silahkan lakukan pembayaran dahulu.'],
            ]);
        } else if ($event->quota && $event->quota < $event->participants_count) {
            throw ValidationException::withMessages([
                'user_id' => ['Kuota pendaftaran telah habis.'],
            ]);
        } else if ($event->status !== 'Pendaftaran') {
            if ($event->status === 'Belum Mulai') {
                throw ValidationException::withMessages([
                    'user_id' => ['Pendaftaran belum dibuka.'],
                ]);
            } else {
                throw ValidationException::withMessages([
                    'user_id' => ['Pendaftaran sudah ditutup.'],
                ]);
            }
        }
        if ($event->price) {
            $eventPayment = EventPayment::create([
                'event_id' => $request->event_id,
                'user_id' => $request->user()->id,
                'code' => 'GNS-' . time() . '-' . rand(100, 999),
                'price' => $event->price - ($event->price * ($event->discount / 100)),
                'status' => 'Belum Dibayar'
            ]);
            return $this->ok($eventPayment, 'Berhasil, Silahkan lakukan pembayaran');
        } else {
            $event->participants()->create([
                'user_id' => $request->user()->id
            ]);
        }
        return $this->created('Berhasil bergabung');
    }

    public function pay(EventPayment $eventPayment)
    {
        if (!$eventPayment->token) {
            // Set your Merchant Server Key
            \Midtrans\Config::$serverKey = 'SB-Mid-server-6A1vfGnXQIf7DNwqmJLPAL4m';
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            \Midtrans\Config::$isProduction = false;
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = true;
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = true;

            $params = array(
                'transaction_details' => array(
                    'order_id' => $eventPayment->code,
                    'gross_amount' => $eventPayment->price,
                )
            );

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $eventPayment->token = $snapToken;
            $eventPayment->save();
        }

        return $this->ok([
            'link' => url('/midtrans-snap/' . $eventPayment->token)
        ], 'data pembayaran');
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

    public function snap()
    {
        return View('event::midtrans-snap');
    }

    public function notifications()
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = 'SB-Mid-server-6A1vfGnXQIf7DNwqmJLPAL4m';
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $notif = new \Midtrans\Notification();

        $transaction = $notif->transaction_status;
        $fraud = $notif->fraud_status;
        $type = $notif->payment_type;

        $eventPayment = EventPayment::where('code', $notif->order_id)->firstOrFail();

        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status in merchant's database to 'Challenge by FDS'
                    // TODO merchant should decide whether this transaction is authorized or not in MAP
                } else {
                    // TODO set payment status in merchant's database to 'Success'
                }
            }
        } else if ($transaction == 'settlement') {
            $eventPayment->status = 'Sukses';
            $eventPayment->createEventParticipant();
        } else if ($transaction == 'pending') {
            $eventPayment->status = 'Menunggu Pembayaran';
        } else if ($transaction == 'deny') {
            $eventPayment->status = 'Gagal';
        } else if ($transaction == 'expire') {
            $eventPayment->status = 'Gagal';
        } else if ($transaction == 'cancel') {
            $eventPayment->status = 'Gagal';
        }
        $eventPayment->save();
    }
}
