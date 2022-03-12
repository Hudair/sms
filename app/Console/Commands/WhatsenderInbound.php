<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\DLRController;
use App\Models\SendingServer;
use Exception;
use Illuminate\Console\Command;

class WhatsenderInbound extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsender:inbound';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store Whatsender Inbound messages';

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
     * @throws Exception
     */
    public function handle(): bool
    {
        $sending_servers = SendingServer::where('settings', 'Whatsender')->where('status', 1)->cursor();
        foreach ($sending_servers as $server){

            $parameters = [
                    'flow' => 'in',
                    'ack'  => [
                            'delivered',
                    ],
                    'type' => [
                            'text',
                    ],
                    'kind' => ['message'],
            ];


            $curl = curl_init();

            curl_setopt_array($curl, [
                    CURLOPT_URL            => "https://api.whatsender.io/v1/io/$server->device_id/messages?".http_build_query($parameters),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING       => "",
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 30,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => "GET",
                    CURLOPT_HTTPHEADER     => [
                            "Token: $server->api_token",
                    ],
            ]);

            $result = curl_exec($curl);
            $err    = curl_error($curl);

            curl_close($curl);

            if ($err || $result === false) {
                return false;
            }

            $json_result = json_decode($result, true);

            if (is_array($json_result) && count($json_result) > 0) {
                foreach ($json_result as $result) {
                    if (array_key_exists('body', $result)){

                        $to = str_replace(['(', ')', '+', '-', ' '], '', trim($result['fromNumber']));
                        $from     = str_replace(['(', ')', '+', '-', ' '], '', trim($result['toNumber']));
                        $message   = $result['body'];

                        if ($to == '' && $from == '' && $message == '') {
                            continue;
                        }

                        $dlr = new DLRController();

                        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
                        $cost          = ceil($message_count);

                        $dlr::inboundDLR($to, $message, 'Whatsender', $cost, $from);
                    }
                }
                return true;
            }
            return false;
        }
        return false;
    }
}
