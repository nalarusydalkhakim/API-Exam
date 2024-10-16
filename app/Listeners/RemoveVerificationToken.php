<?php

namespace App\Listeners;

use App\Events\EmailVerified;
use Illuminate\Support\Facades\DB;

class RemoveVerificationToken
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
    public function handle(EmailVerified $event): void
    {
        DB::table('verification_tokens')
            ->where('user_id', $event->user->id)
            ->delete();
    }
}
