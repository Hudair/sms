<?php

namespace App\Console\Commands;

use App\Jobs\StoreCampaignJob;
use App\Models\Campaigns;
use App\Models\CampaignsList;
use App\Models\CampaignsRecipients;
use App\Models\CampaignsSenderid;
use App\Models\CampaignsSendingServer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RunCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run campaign';

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
    public function handle(): int
    {

        $fromDate = Carbon::now()->subDays(3)->toDateTimeString();
        $toDate   = Carbon::now()->toDateTimeString();
        //collect recurring campaign and check status
        $recurring = Campaigns::where('schedule_type', 'recurring')->where('status', 'scheduled')->where('upload_type', 'normal')->whereBetween('schedule_time', [$fromDate, $toDate])->cursor();

        foreach ($recurring as $sms) {
            if ($sms->recurring_end->gt(Carbon::now())) {
                // recurring running
                dispatch(new StoreCampaignJob($sms->id));

                if ($sms->frequency_cycle != 'custom') {
                    $schedule_cycle   = $sms::scheduleCycleValues();
                    $limits           = $schedule_cycle[$sms->frequency_cycle];
                    $frequency_amount = $limits['frequency_amount'];
                    $frequency_unit   = $limits['frequency_unit'];
                } else {
                    $frequency_amount = $sms->frequency_amount;
                    $frequency_unit   = $sms->frequency_unit;
                }

                $schedule_date = $sms->nextScheduleDate($sms->schedule_time, $frequency_unit, $frequency_amount);

                $new_camp = $sms->replicate()->fill([
                        'status'        => 'scheduled',
                        'schedule_time' => $schedule_date,
                ]);

                $data = $new_camp->save();

                if ($data) {

                    //insert campaign contact list
                    foreach (CampaignsList::where('campaign_id', $sms->id)->cursor() as $list) {
                        CampaignsList::create([
                                'campaign_id'     => $new_camp->id,
                                'contact_list_id' => $list->contact_list_id,
                        ]);
                    }

                    //insert campaign recipients
                    foreach (CampaignsRecipients::where('campaign_id', $sms->id)->cursor() as $recipients) {
                        CampaignsRecipients::create([
                                'campaign_id' => $new_camp->id,
                                'recipient'   => $recipients->recipient,
                        ]);
                    }


                    //insert campaign sender ids
                    foreach (CampaignsSenderid::where('campaign_id', $sms->id)->cursor() as $sender_ids) {
                        CampaignsSenderid::create([
                                'campaign_id' => $new_camp->id,
                                'sender_id'   => $sender_ids->sender_id,
                                'originator'  => $sender_ids->originator,
                        ]);
                    }


                    //insert campaign sending servers
                    foreach (CampaignsSendingServer::where('campaign_id', $sms->id)->cursor() as $servers) {
                        CampaignsSendingServer::create([
                                'campaign_id'       => $new_camp->id,
                                'sending_server_id' => $servers->sending_server_id,
                                'fitness'           => $servers->fitness,
                        ]);
                    }
                }

            } else {
                //recurring date end
                $sms->delivered();
            }
        }

        return 0;
    }
}
