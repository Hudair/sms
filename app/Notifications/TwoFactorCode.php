<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCode extends Notification
{
    use Queueable;


    protected $action_url;

    /**
     * Create a new notification instance.
     *
     * @param $action_url
     */
    public function __construct($action_url = null)
    {

        if ($action_url) {
            $this->action_url = $action_url;
        } else {
            $this->action_url = route('verify.index');
        }

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                ->line('Your two factor code is '.$notifiable->two_factor_code)
                ->action('Verify Here', $this->action_url)
                ->line('The code will expire in 10 minutes')
                ->line('If you have not tried to login, ignore this message.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
