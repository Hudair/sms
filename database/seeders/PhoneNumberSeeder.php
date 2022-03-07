<?php

namespace Database\Seeders;

use App\Models\PhoneNumbers;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhoneNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('phone_numbers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $phone_numbers = [
                [
                        'user_id'          => 1,
                        'number'           => '8801721970168',
                        'status'           => 'available',
                        'capabilities'     => json_encode(['sms', 'voice', 'mms', 'whatsapp']),
                        'price'            => 5,
                        'billing_cycle'    => 'monthly',
                        'frequency_amount' => 1,
                        'frequency_unit'   => 'month',
                        'currency_id'      => 1,
                ],
                [
                        'user_id'          => 3,
                        'number'           => '8801921970168',
                        'status'           => 'assigned',
                        'capabilities'     => json_encode(['voice', 'mms', 'whatsapp']),
                        'price'            => 5,
                        'billing_cycle'    => 'monthly',
                        'frequency_amount' => 1,
                        'frequency_unit'   => 'month',
                        'currency_id'      => 2,
                        'validity_date'    => Carbon::now()->addMonth(),
                ],
                [
                        'user_id'          => 3,
                        'number'           => '8801621970168',
                        'status'           => 'expired',
                        'price'            => 5,
                        'capabilities'     => json_encode(['sms', 'mms', 'whatsapp']),
                        'billing_cycle'    => 'custom',
                        'frequency_amount' => 6,
                        'frequency_unit'   => 'month',
                        'currency_id'      => 3,
                ],
                [
                        'user_id'          => 1,
                        'number'           => '8801521970168',
                        'status'           => 'available',
                        'price'            => 5,
                        'capabilities'     => json_encode(['sms', 'voice', 'whatsapp']),
                        'billing_cycle'    => 'yearly',
                        'frequency_amount' => 1,
                        'frequency_unit'   => 'year',
                        'currency_id'      => 1,
                ],
                [
                        'user_id'          => 3,
                        'number'           => '8801821970168',
                        'status'           => 'assigned',
                        'price'            => 5,
                        'capabilities'     => json_encode(['sms', 'voice', 'mms']),
                        'billing_cycle'    => 'monthly',
                        'frequency_amount' => 6,
                        'frequency_unit'   => 'month',
                        'currency_id'      => 3,
                        'validity_date'    => Carbon::now()->add('month', 6),
                ],
        ];

        foreach ($phone_numbers as $number) {
            PhoneNumbers::create($number);
        }

    }
}
