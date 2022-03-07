<?php

namespace Database\Seeders;

use App\Models\Keywords;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeywordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('keywords')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sender_ids = [
                [
                        'user_id'          => 1,
                        'currency_id'      => 1,
                        'title'            => '50% OFF',
                        'keyword_name'     => '50OFF',
                        'sender_id'        => 'Codeglen',
                        'reply_text'       => 'you will get 50% from our next promotion.',
                        'reply_voice'      => 'you will get 50% from our next promotion.',
                        'price'            => 10,
                        'billing_cycle'    => 'yearly',
                        'frequency_amount' => '1',
                        'frequency_unit'   => 'year',
                        'status'           => 'available',
                ],
                [
                        'user_id'          => 3,
                        'currency_id'      => 2,
                        'title'            => 'CR7',
                        'keyword_name'     => 'CR7',
                        'sender_id'        => 'Codeglen',
                        'reply_text'       => 'Thank you for voting Cristiano Ronaldo.',
                        'reply_voice'      => 'Thank you for voting Cristiano Ronaldo.',
                        'price'            => 10,
                        'billing_cycle'    => 'yearly',
                        'frequency_amount' => '1',
                        'frequency_unit'   => 'year',
                        'validity_date'    => Carbon::now()->add(1, 'year'),
                        'status'           => 'assigned',
                ],
                [
                        'user_id'          => 3,
                        'currency_id'      => 3,
                        'title'            => 'MESSI10',
                        'keyword_name'     => 'MESSI10',
                        'sender_id'        => 'Codeglen',
                        'reply_text'       => 'Thank you for voting Leonel Messi.',
                        'reply_voice'      => 'Thank you for voting Leonel Messi.',
                        'price'            => 10,
                        'billing_cycle'    => 'yearly',
                        'frequency_amount' => '1',
                        'frequency_unit'   => 'year',
                        'validity_date'    => Carbon::yesterday(),
                        'status'           => 'expired',
                ],
                [
                        'user_id'          => 1,
                        'currency_id'      => 1,
                        'title'            => '999',
                        'keyword_name'     => '999',
                        'sender_id'        => 'Codeglen',
                        'reply_text'       => 'You will receive all govt facilities from now.',
                        'reply_voice'      => 'You will receive all govt facilities from now.',
                        'price'            => 10,
                        'billing_cycle'    => 'yearly',
                        'frequency_amount' => '1',
                        'frequency_unit'   => 'year',
                        'status'           => 'available',
                ],
        ];

        foreach ($sender_ids as $senderId) {
            Keywords::create($senderId);
        }

    }
}
