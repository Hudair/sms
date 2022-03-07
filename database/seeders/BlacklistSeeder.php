<?php

namespace Database\Seeders;

use App\Models\Blacklists;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlacklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('blacklists')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $blacklists = [
                [
                        'user_id' => 1,
                        'number'  => '8801721970156',
                        'reason'  => null,
                ],
                [
                        'user_id' => 4,
                        'number'  => '8801921970156',
                        'reason'  => strtoupper('stop promotion'),
                ],
                [
                        'user_id' => 5,
                        'number'  => '8801621970156',
                        'reason'  => 'SPAMMING',
                ],
                [
                        'user_id' => 4,
                        'number'  => '8801721970156',
                        'reason'  => null,
                ],
                [
                        'user_id' => 1,
                        'number'  => '8801821970156',
                        'reason'  => strtoupper('stop promotion'),
                ],
        ];

        foreach ($blacklists as $blacklist) {
            Blacklists::create($blacklist);
        }

    }
}
