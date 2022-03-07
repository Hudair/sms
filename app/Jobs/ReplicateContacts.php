<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReplicateContacts implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $group_id;
    protected $contact;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @param $group_id
     * @param $contact
     */
    public function __construct($group_id, $contact)
    {
        $this->group_id = $group_id;
        $this->contact  = $contact;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        foreach ($this->contact as $contact) {
            $new_contact             = $contact->replicate();
            $new_contact->uid        = uniqid();
            $new_contact->group_id   = $this->group_id;
            $new_contact->created_at = now()->toDateTimeString();
            $new_contact->updated_at = now()->toDateTimeString();

            $new_contact->save();
        }


    }
}
