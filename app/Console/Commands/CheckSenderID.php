<?php

namespace App\Console\Commands;

use App\Models\Senderid;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckSenderID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'senderid:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Sender id expire date';

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
        $senderids = Senderid::where('status', 'active')->where('validity_date', "<", Carbon::now()->endOfDay())->cursor();

        foreach ($senderids as $senderid) {
            $senderid->update([
                    'status' => 'expired',
            ]);
        }

        return 0;
    }
}
