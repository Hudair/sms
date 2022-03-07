<?php

namespace App\Jobs;

use App\Library\Tool;
use App\Models\Campaigns;
use App\Models\ImportJobHistory;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Throwable;

class ScheduleBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $collection;
    protected $campaign_id;
    protected $user_id;
    protected $db_fields;

    /**
     * Create a new job instance.
     *
     * @param $user_id
     * @param $campaign_id
     * @param $collection
     * @param $db_fields
     */
    public function __construct($user_id, $campaign_id, $collection, $db_fields)
    {
        $this->user_id     = $user_id;
        $this->campaign_id = $campaign_id;
        $this->collection  = $collection;
        $this->db_fields   = $db_fields;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Throwable
     */
    public function handle()
    {

        $campaign  = Campaigns::find($this->campaign_id);
        $db_fields = $this->db_fields;
        $user_id   = $this->user_id;

        $batch_list = [];
        Tool::resetMaxExecutionTime();
        $this->collection->chunk(5000)
                ->each(function ($lines) use ($campaign, &$batch_list, $db_fields, $user_id) {
                    $batch_list[] = new ImportCampaign($user_id, $campaign->id, $lines, $db_fields);
                });

        $import_name = 'ImportContacts_'.date('Ymdhms');

        $import_job = ImportJobHistory::create([
                'name'      => $import_name,
                'import_id' => $campaign->uid,
                'type'      => 'import_campaign',
                'status'    => 'processing',
                'options'   => json_encode(['status' => 'processing', 'message' => 'Import campaign are running']),
                'batch_id'  => null,
        ]);

        $batch = Bus::batch($batch_list)
                ->then(function (Batch $batch) use ($campaign, $import_name, $import_job) {

                    $campaign->processing();

                    $campaign->update([
                            'batch_id' => $batch->id
                    ]);
                    $import_job->update(['batch_id' => $batch->id]);
                })
                ->catch(function (Batch $batch, Throwable $e) {
                    $import_history = ImportJobHistory::where('batch_id', $batch->id)->first();
                    if ($import_history) {
                        $import_history->status  = 'failed';
                        $import_history->options = json_encode(['status' => 'failed', 'message' => $e->getMessage()]);
                        $import_history->save();
                    }

                })
                ->finally(function (Batch $batch) use ($campaign) {
                    $import_history = ImportJobHistory::where('batch_id', $batch->id)->first();
                    if ($import_history) {
                        $import_history->status  = 'finished';
                        $import_history->options = json_encode(['status' => 'finished', 'message' => 'Import campaign was successfully imported.']);
                        $import_history->save();
                        $campaign->delivered();
                    }

                    //send event notification remaining
                })
                ->name($import_name)
                ->dispatch();

        $campaign->update(['batch_id' => $batch->id]);


    }
}
