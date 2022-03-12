<?php

namespace Database\Seeders;

use App\Models\ChatBox;
use App\Models\ChatBoxMessage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatBoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('chat_boxes')->truncate();
        DB::table('chat_box_messages')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $chatbox = [
                [
                        'user_id'      => 3,
                        'from'         => '8801921970168',
                        'to'           => '8801721970168',
                        'notification' => '3',
                ],
                [
                        'user_id'      => 3,
                        'from'         => '8801821970168',
                        'to'           => '8801621970168',
                        'notification' => '0',
                ],
        ];

        foreach ($chatbox as $box) {
            ChatBox::create($box);
        }

        $chatbox_messages = [
                [
                        'box_id'            => 1,
                        'message'           => 'test message',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'from',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 2,
                        'message'           => 'test message',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'from',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 2,
                        'message'           => 'test message reply',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'to',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 1,
                        'message'           => 'test message',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'from',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 1,
                        'message'           => 'Est ut quas aut. Saepe dolor quo numquam rem ratio...',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'to',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 2,
                        'message'           => 'test message',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'to',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 2,
                        'message'           => 'test message',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'from',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 1,
                        'message'           => 'test message',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'from',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 1,
                        'message'           => 'test message',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'to',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 1,
                        'message'           => 'test message',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'from',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 1,
                        'message'           => 'test message',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'to',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 1,
                        'message'           => 'test message',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'from',
                        'sending_server_id' => 1,
                ],
                [
                        'box_id'            => 2,
                        'message'           => 'test message',
                        'media_url'         => null,
                        'sms_type'          => 'sms',
                        'send_by'           => 'to',
                        'sending_server_id' => 1,
                ],
        ];

        foreach ($chatbox_messages as $message){
            ChatBoxMessage::create($message);
        }

    }
}
