<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendCampaignCopy extends Notification
{
    use Queueable;

    protected $message;
    protected $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message, $url)
    {
        $this->message = $message;
        $this->url     = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
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
     *
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->subject('Campaign Message Copy From '.config('app.name'))
                ->line('Here is your campaign Message: '.$this->message)
                ->action('View Campaign', $this->url)
                ->line('Thank you for using our application!');
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
