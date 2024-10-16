<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Events\RequestEmailVerification;
use App\Listeners\SendEmailVerificationNotification;

use App\Events\EmailVerified;
use App\Listeners\SendEmailVerifiedNotification;
use App\Listeners\SetEmailVerified;
use App\Listeners\RemoveVerificationToken;

use App\Events\RequestPasswordReset;
use App\Listeners\SendEmailRequestPasswordResetNotification;

use App\Events\PasswordReset;
use App\Listeners\SendEmailPasswordResetNotification;

use App\Events\PasswordChanged;
use App\Listeners\SendEmailPasswordChangedNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        RequestEmailVerification::class => [
            SendEmailVerificationNotification::class
        ],
        EmailVerified::class => [
            SendEmailVerifiedNotification::class,
            SetEmailVerified::class,
            RemoveVerificationToken::class
        ],
        RequestPasswordReset::class => [
            SendEmailRequestPasswordResetNotification::class
        ],
        PasswordReset::class => [
            SendEmailPasswordResetNotification::class
        ],
        PasswordChanged::class => [
            SendEmailPasswordChangedNotification::class
        ]
    ];
}