<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url('http://localhost:5173/#/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->getEmailForPasswordReset()));

        return (new MailMessage)
                    ->subject('Password Reset Request')
                    ->view('emails.reset-password', [
                        'url' => $url,
                        'name' => $notifiable->name,
                    ]);
    }


    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
