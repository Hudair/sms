<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Library\Tool;
use App\Models\Campaigns;
use App\Models\Notifications;
use App\Models\SendingServer;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SMSUnitRunningLow;
use App\Notifications\SubscriptionExpire;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CheckUserPreferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:preferences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user preferences settings';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $users = User::where('is_customer', 1)->where('status', 1)->cursor();

        foreach ($users as $user) {
            if ($user->customer->activeSubscription() !== null) {
                $subscription = $user->customer->activeSubscription();
                if ($subscription->getOption('subscription_warning') == 1) {
                    $check = Subscription::where('user_id', $user->id)
                            ->whereRaw('date(current_period_ends_at) =?', Carbon::now()->addDays($subscription->end_period_last_days)->toDateString())
                            ->first();

                    if ($check) {

                        Notifications::create([
                                'user_id'           => $user->id,
                                'notification_for'  => 'customer',
                                'notification_type' => 'subscription',
                                'message'           => 'Your subscription will end at '.Tool::customerDateTime($subscription->current_period_ends_at),
                        ]);

                        if ($subscription->getOption('subscription_notify') == 'both') {
                            $user->notify(new SubscriptionExpire($subscription));

                            $sending_server = SendingServer::where('status', true)->where('uid', Helper::app_config('notification_sms_gateway'))->first();
                            if ($sending_server && isset($user->customer->phone)) {

                                $input = [
                                        'sender_id'      => Helper::app_config('notification_sender_id'),
                                        'phone'          => $user->customer->phone,
                                        'sending_server' => $sending_server,
                                        'user_id'        => 1,
                                        'sms_type'       => 'plain',
                                        'status'         => null,
                                        'cost'           => 1,
                                        'message'        => 'Your subscription will end at '.Tool::customerDateTime($check->current_period_ends_at),
                                ];


                                $campaign = new Campaigns();
                                $campaign->sendPlainSMS($input);
                            }


                        }

                        if ($subscription->getOption('subscription_notify') == 'email') {
                            $user->notify(new SubscriptionExpire($subscription));
                        }

                        if ($subscription->getOption('subscription_notify') == 'sms') {
                            $sending_server = SendingServer::where('status', true)->where('uid', Helper::app_config('notification_sms_gateway'))->first();
                            if ($sending_server && isset($user->customer->phone)) {

                                $input = [
                                        'sender_id'      => Helper::app_config('notification_sender_id'),
                                        'phone'          => $user->customer->phone,
                                        'sending_server' => $sending_server,
                                        'sms_type'       => 'plain',
                                        'status'         => null,
                                        'cost'           => 1,
                                        'user_id'        => 1,
                                        'message'        => 'Your subscription will end at '.Tool::customerDateTime($check->current_period_ends_at),
                                ];


                                $campaign = new Campaigns();
                                $campaign->sendPlainSMS($input);
                            }

                        }


                    }

                }

                if ($subscription->getOption('credit_warning') == 1 && $user->customer->getSendingQuota() != '-1') {
                    $remaining = $user->customer->getSendingQuota() - $user->customer->getSendingQuotaUsage();

                    if ($remaining == $subscription->getOption('credit')) {

                        Notifications::create([
                                'user_id'           => $user->id,
                                'notification_for'  => 'customer',
                                'notification_type' => 'subscription',
                                'message'           => 'Your sms unit running low!!',
                        ]);

                        if ($subscription->getOption('credit_notify') == 'both') {
                            $user->notify(new SMSUnitRunningLow($remaining));

                            $sending_server = SendingServer::where('status', true)->where('uid', Helper::app_config('notification_sms_gateway'))->first();
                            if ($sending_server && isset($user->customer->phone)) {

                                $input = [
                                        'sender_id'      => Helper::app_config('notification_sender_id'),
                                        'phone'          => $user->customer->phone,
                                        'sending_server' => $sending_server,
                                        'user_id'        => 1,
                                        'sms_type'       => 'plain',
                                        'status'         => null,
                                        'cost'           => 1,
                                        'message'        => 'Your sms unit is running low. Your current sms unit '.$remaining,
                                ];


                                $campaign = new Campaigns();
                                $campaign->sendPlainSMS($input);
                            }


                        }

                        if ($subscription->getOption('credit_notify') == 'email') {
                            $user->notify(new SMSUnitRunningLow($remaining));
                        }

                        if ($subscription->getOption('credit_notify') == 'sms') {
                            $sending_server = SendingServer::where('status', true)->where('uid', Helper::app_config('notification_sms_gateway'))->first();
                            if ($sending_server && isset($user->customer->phone)) {

                                $input = [
                                        'sender_id'      => Helper::app_config('notification_sender_id'),
                                        'phone'          => $user->customer->phone,
                                        'sending_server' => $sending_server,
                                        'user_id'        => 1,
                                        'sms_type'       => 'plain',
                                        'status'         => null,
                                        'cost'           => 1,
                                        'message'        => 'Your sms unit is running low. Your current sms unit '.$remaining,
                                ];

                                $campaign = new Campaigns();
                                $campaign->sendPlainSMS($input);
                            }
                        }
                    }
                }
            }
        }

        return 0;
    }
}
