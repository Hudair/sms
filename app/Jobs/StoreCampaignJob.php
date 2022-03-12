<?php

namespace App\Jobs;

use App\Models\Campaigns;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class StoreCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    protected $campaign_id;

    /**
     * Create a new job instance.
     *
     * @param $campaign_id
     */
    public function __construct($campaign_id)
    {
        $this->campaign_id = $campaign_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $campaign = Campaigns::find($this->campaign_id);
        if ($campaign) {
            $campaign->singleProcess();
        }
    }

    public function failed(Throwable $exception)
    {
        $campaign = Campaigns::find($this->campaign_id);
        $campaign->failed($exception->getMessage());
    }
}
