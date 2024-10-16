<?php

namespace App\Listeners;

use App\Events\PasswordChanged as PasswordChangedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\PasswordChanged;

class SendEmailPasswordChangedNotification
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
    public function handle(PasswordChangedEvent $event): void
    {
        $event->user->notify(new PasswordChanged($event->user));
    }
}
