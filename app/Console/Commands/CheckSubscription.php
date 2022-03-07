<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionLog;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subscription scheduling';

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

        $subscriptions = Subscription::whereNull('end_at')->where('current_period_ends_at', "<", Carbon::now()->endOfDay())->cursor();

        foreach ($subscriptions as $subscription) {

            $subscription->cancelNow();

            $subscription->addLog(SubscriptionLog::TYPE_EXPIRED, [
                    'plan'  => $subscription->plan->getBillableName(),
                    'price' => $subscription->plan->getBillableFormattedPrice(),
            ]);
        }

        return 0;
    }
}
