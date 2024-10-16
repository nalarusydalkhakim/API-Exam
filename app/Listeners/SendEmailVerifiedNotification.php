<?php

namespace App\Listeners;

use App\Events\EmailVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\Activated;

class SendEmailVerifiedNotification
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
        $event->user->notify(new Activated($event->user));
    }
}
