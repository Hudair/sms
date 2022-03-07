<?php

namespace App\Notifications;

use App\Helpers\Helper;
use App\Library\Tool;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpire extends Notification
{
    use Queueable;


    protected $subscription;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
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
                ->from(Helper::app_config('notification_email'), Helper::app_config('notification_from_name'))
                ->subject('Subscription Expire notice from '.config('app.name'))
                ->line('Your subscription will end at '.Tool::customerDateTime($this->subscription->current_period_ends_at))
                ->action('Renew', route('customer.subscriptions.index'))
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
