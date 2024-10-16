<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class PasswordReset extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    protected $user;
    protected $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $token = $this->token;
        $resetUrl = config('app.fe_url') . '/reset-password?email='.$this->user->email . '&token=' . $this->token;

        return (new MailMessage)
                    ->subject('Reset Password')
                    ->line('Kami telah menerima permintaan perubahan password.')
                    ->line('Masukkan kode dibawah ini untuk mereset password.')
                    ->line(new HtmlString('<strong>'.$token.'</strong>'))
                    ->line('Atau gunakan link berikut untuk mereset password:')
                    ->action('Reset Password', $resetUrl)
                    ->line('Jika Anda tidak mengirim permintaan perubahan password, Silahkan abaikan email ini.')
                    ->line('Terimakasih !');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
