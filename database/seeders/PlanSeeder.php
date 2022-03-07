<?php

namespace Database\Seeders;


use App\Models\Plan;
use App\Models\PlansSendingServer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('plans')->truncate();
        DB::table('plans_sending_servers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $plans = [
                [
                        'user_id'              => 1,
                        'currency_id'          => 1,
                        'name'                 => 'Free',
                        'description'          => 'Free Plan for new user\'s',
                        'price'                => '0.00',
                        'billing_cycle'        => 'monthly',
                        'frequency_amount'     => 1,
                        'frequency_unit'       => 'month',
                        'options'              => '{"sms_max":"100","whatsapp_max":"100","list_max":"5","subscriber_max":"500","subscriber_per_list_max":"100","segment_per_list_max":"3","billing_cycle":"monthly","sending_limit":"50000_per_hour","sending_quota":"100","sending_quota_time":"1","sending_quota_time_unit":"hour","max_process":"1","unsubscribe_url_required":"yes","create_sending_server":"no","sending_servers_max":"5","list_import":"yes","list_export":"yes","api_access":"no","create_sub_account":"no","delete_sms_history":"no","add_previous_balance":"no","sender_id_verification":"yes","send_spam_message":"no","plain_sms":"1","receive_plain_sms":"0","voice_sms":"2","receive_voice_sms":"0","mms_sms":"3","receive_mms_sms":"0","whatsapp_sms":"1","receive_whatsapp_sms":"0","cutting_system":"no","cutting_value":"5","cutting_unit":"percentage","cutting_logic":"random"}',
                        'status'               => true,
                        'tax_billing_required' => false,
                ],
                [
                        'user_id'              => 1,
                        'currency_id'          => 3,
                        'name'                 => 'Standard',
                        'description'          => 'Most Popular Plan',
                        'price'                => '500.00',
                        'billing_cycle'        => 'custom',
                        'frequency_amount'     => 6,
                        'frequency_unit'       => 'month',
                        'is_popular'           => true,
                        'options'              => '{"sms_max":"10000","whatsapp_max":"10000","list_max":"-1","subscriber_max":"-1","subscriber_per_list_max":"-1","segment_per_list_max":"-1","billing_cycle":"monthly","sending_limit":"10000_per_hour","sending_quota":"1000","sending_quota_time":"1","sending_quota_time_unit":"hour","max_process":"2","unsubscribe_url_required":"yes","create_sending_server":"yes","sending_servers_max":"5","list_import":"yes","list_export":"yes","api_access":"yes","create_sub_account":"yes","delete_sms_history":"yes","add_previous_balance":"yes","sender_id_verification":"yes","send_spam_message":"yes","plain_sms":"1","receive_plain_sms":"0","voice_sms":"2","receive_voice_sms":"0","mms_sms":"3","receive_mms_sms":"0","whatsapp_sms":"1","receive_whatsapp_sms":"0","quota_value":10000,"quota_base":1,"quota_unit":"hour","cutting_system":"yes","cutting_value":"5","cutting_unit":"percentage","cutting_logic":"random"}',
                        'status'               => true,
                        'tax_billing_required' => true,
                ],
                [
                        'user_id'              => 1,
                        'currency_id'          => 1,
                        'name'                 => 'Premium',
                        'description'          => 'Premium package. Specially for corporate service.',
                        'price'                => '5000.00',
                        'billing_cycle'        => 'yearly',
                        'frequency_amount'     => 1,
                        'frequency_unit'       => 'year',
                        'options'              => '{"sms_max":"-1","whatsapp_max":"-1","list_max":"-1","subscriber_max":"-1","subscriber_per_list_max":"-1","segment_per_list_max":"-1","billing_cycle":"monthly","sending_limit":"50000_per_hour","sending_quota":"1000","sending_quota_time":"1","sending_quota_time_unit":"hour","max_process":"3","unsubscribe_url_required":"yes","create_sending_server":"yes","sending_servers_max":"5","list_import":"yes","list_export":"yes","api_access":"yes","create_sub_account":"yes","delete_sms_history":"yes","add_previous_balance":"yes","sender_id_verification":"yes","send_spam_message":"yes","plain_sms":"1","receive_plain_sms":"0","voice_sms":"1","receive_voice_sms":"0","mms_sms":"2","receive_mms_sms":"0","whatsapp_sms":"1","receive_whatsapp_sms":"0","quota_value":50000,"quota_base":1,"quota_unit":"hour","cutting_system":"yes","cutting_value":"5","cutting_unit":"percentage","cutting_logic":"random"}',
                        'status'               => true,
                        'tax_billing_required' => true,
                ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }

        $plan_sending_server = [
                [
                        'sending_server_id' => 1,
                        'plan_id'           => 1,
                        'fitness'           => 100,
                        'is_primary'        => 1,

                ],
                [
                        'sending_server_id' => 1,
                        'plan_id'           => 2,
                        'fitness'           => 30,
                        'is_primary'        => 0,

                ],
                [
                        'sending_server_id' => 2,
                        'plan_id'           => 2,
                        'fitness'           => 30,
                        'is_primary'        => 0,

                ],
                [
                        'sending_server_id' => 3,
                        'plan_id'           => 2,
                        'fitness'           => 40,
                        'is_primary'        => 1,

                ],
                [
                        'sending_server_id' => 1,
                        'plan_id'           => 3,
                        'fitness'           => 20,
                        'is_primary'        => 0,

                ],
                [
                        'sending_server_id' => 2,
                        'plan_id'           => 3,
                        'fitness'           => 20,
                        'is_primary'        => 0,

                ],
                [
                        'sending_server_id' => 3,
                        'plan_id'           => 3,
                        'fitness'           => 30,
                        'is_primary'        => 1,

                ],
                [
                        'sending_server_id' => 4,
                        'plan_id'           => 3,
                        'fitness'           => 15,
                        'is_primary'        => 0,

                ],
                [
                        'sending_server_id' => 5,
                        'plan_id'           => 3,
                        'fitness'           => 15,
                        'is_primary'        => 0,

                ],
        ];

        foreach ($plan_sending_server as $server) {
            PlansSendingServer::create($server);
        }

    }

}
