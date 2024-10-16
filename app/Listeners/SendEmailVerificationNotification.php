<?php

namespace App\Listeners;

use App\Events\RequestEmailVerification;
use App\Notifications\Activation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SendEmailVerificationNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RequestEmailVerification $event): void
    {
        $token = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 6);

        DB::table('verification_tokens')
            ->where('user_id', $event->user->id)
            ->delete();

        DB::table('verification_tokens')
            ->insert([
                "user_id" => $event->user->id,
                "token" => $token,
                "purpose" => 'Verifikasi email',
                "expired_at" =>  Carbon::now()->addHour()
            ]);
        $event->user->notify(new Activation($event->user, $token));
    }
}
