<?php

namespace App\Console\Commands;

use App\Models\Keywords;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keywords:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Keywords expire date';

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
        $keywords = Keywords::where('status', 'assigned')->where('validity_date', "<", Carbon::now()->endOfDay())->cursor();

        foreach ($keywords as $keyword) {
            $keyword->update([
                    'status' => 'expired',
            ]);
        }


        return 0;
    }
}
