<?php


namespace App\Repositories\Eloquent;

use App\Jobs\ImportCampaign;
use App\Jobs\ScheduleBatchJob;
use App\Jobs\StoreCampaignJob;
use App\Library\Tool;
use App\Models\Campaigns;
use App\Models\CampaignsList;
use App\Models\CampaignsRecipients;
use App\Models\CampaignsSenderid;
use App\Models\CampaignsSendingServer;
use App\Models\ContactGroups;
use App\Models\CsvData;
use App\Models\ImportJobHistory;
use App\Models\PhoneNumbers;
use App\Models\PlansSendingServer;
use App\Models\Senderid;
use App\Models\SendingServer;
use App\Models\Templates;
use App\Models\User;
use App\Notifications\SendCampaignCopy;
use App\Repositories\Contracts\CampaignRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Throwable;

class EloquentCampaignRepository extends EloquentBaseRepository implements CampaignRepository
{

    public static $serverPools = [];

    /**
     * EloquentCampaignRepository constructor.
     *
     * @param  Campaigns  $campaigns
     */
    public function __construct(Campaigns $campaigns)
    {
        parent::__construct($campaigns);
    }


    /**
     * send quick message
     *
     * @param  Campaigns  $campaign
     * @param  array  $input
     *
     * @return JsonResponse|mixed
     * @throws Exception
     */
    public function quickSend(Campaigns $campaign, array $input): JsonResponse
    {

        if (isset($input['user_id'])) {
            $user = User::find($input['user_id']);
        } else {
            $user = Auth::user();
        }
        $sms_type = $input['sms_type'];

        if ($user->sms_unit == 0) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.campaigns.sending_limit_exceed'),
            ]);
        }

        if ($user->customer->activeSubscription()) {
            $sending_server = null;

            if ($sms_type == 'unicode') {
                $db_sms_type = 'plain';
            } else {
                $db_sms_type = $sms_type;
            }

            $plan_id = $user->customer->activeSubscription()->plan_id;
            // Check the customer has permissions using sending servers and has his own sending servers
            if ($user->customer->getOption('create_sending_server') == 'yes') {

                if (PlansSendingServer::where('plan_id', $plan_id)->count()) {
                    $sending_server = SendingServer::where('user_id', $user->id)->where($db_sms_type, 1)->where('status', true)->first();
                    if ( ! $sending_server) {
                        $sending_server = PlansSendingServer::where('plan_id', $plan_id)->where('is_primary', 1)->first()->sendingServer;
                    }
                }

            } else {
                // If customer dont have permission creating sending servers
                $sending_server = PlansSendingServer::where('plan_id', $plan_id)->where('is_primary', 1)->first()->sendingServer;
            }

            if ($sending_server == null) {
                return response()->json([
                        'status'  => 'error',
                        'message' => __('locale.campaigns.sending_server_not_available'),
                ]);
            }

            if ($sending_server->{$db_sms_type} != 1) {
                return response()->json([
                        'status'  => 'error',
                        'message' => __('locale.campaigns.sending_server_not_available'),
                ]);
            }

            $sender_id = null;
            if ($user->customer->getOption('sender_id_verification') == 'yes') {
                if (isset($input['originator'])) {
                    if ($input['originator'] == 'sender_id' && isset($input['sender_id'])) {
                        $sender_id = $input['sender_id'];
                    } elseif ($input['originator'] == 'phone_number' && isset($input['phone_number'])) {
                        $sender_id = $input['phone_number'];
                    }
                } elseif (isset($input['sender_id'])) {
                    $sender_id = $input['sender_id'];
                }

                $check_sender_id = Senderid::where('user_id', $user->id)->where('sender_id', $sender_id)->where('status', 'active')->first();
                if ( ! $check_sender_id) {
                    $number = PhoneNumbers::where('user_id', $user->id)->where('number', $sender_id)->where('status', 'assigned')->first();

                    if ( ! $number) {
                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_invalid', ['sender_id' => $sender_id]),
                        ]);
                    }

                    $capabilities = str_contains($number->capabilities, 'sms');

                    if ( ! $capabilities) {
                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_sms_capabilities', ['sender_id' => $sender_id]),
                        ]);
                    }

                }
            } elseif (isset($input['sender_id'])) {
                $sender_id = $input['sender_id'];
            }

            $message = null;
            if (isset($input['message'])) {
                $message = $input['message'];
            }

            $price = 0;

            if ($sms_type == 'plain') {
                if (strlen($message) != strlen(utf8_decode($message))) {
                    $sms_type = 'unicode';
                }

                if ($sms_type == 'unicode') {
                    $length_count = mb_strlen(preg_replace('/\s+/', ' ', trim($message)), 'UTF-8');

                    if ($length_count <= 70) {
                        $sms_count = 1;
                    } else {
                        $sms_count = $length_count / 67;
                    }
                } else {
                    $length_count = strlen(preg_replace('/\s+/', ' ', trim($message)));
                    if ($length_count <= 160) {
                        $sms_count = 1;
                    } else {
                        $sms_count = $length_count / 157;
                    }
                }
            } elseif ($sms_type == 'unicode') {
                $length_count = mb_strlen(preg_replace('/\s+/', ' ', trim($message)), 'UTF-8');

                if ($length_count <= 70) {
                    $sms_count = 1;
                } else {
                    $sms_count = $length_count / 67;
                }
            } else {
                $length_count = strlen(preg_replace('/\s+/', ' ', trim($message)));
                if ($length_count <= 160) {
                    $sms_count = 1;
                } else {
                    $sms_count = $length_count / 157;
                }
            }

            $sms_count = ceil($sms_count);


            if ($sms_type == 'plain' || $sms_type == 'unicode') {
                $unit_price = $user->customer->getOption('plain_sms');
                $price      = $sms_count * $unit_price;
            }

            if ($sms_type == 'voice') {
                $unit_price = $user->customer->getOption('voice_sms');
                $price      = $sms_count * $unit_price;
            }

            if ($sms_type == 'mms') {
                $unit_price = $user->customer->getOption('mms_sms');
                $price      = $sms_count * $unit_price;
            }

            if ($sms_type == 'whatsapp') {
                $unit_price = $user->customer->getOption('whatsapp_sms');
                $price      = $sms_count * $unit_price;
            }

            $price = (int) $price;

            if ($user->sms_unit != '-1') {

                if ($price > $user->sms_unit) {
                    return response()->json([
                            'status'  => 'error',
                            'message' => __('locale.campaigns.not_enough_balance', [
                                    'current_balance' => $user->sms_unit,
                                    'campaign_price'  => $price,
                            ]),
                    ]);
                }
            }

            //prepared message data

            $preparedData = [
                    'user_id'        => $user->id,
                    'phone'          => $input['recipient'],
                    'sender_id'      => $sender_id,
                    'message'        => $message,
                    'cost'           => $price,
                    'sending_server' => $sending_server,
                    'status'         => null,
                    'sms_type'       => $sms_type,
            ];

            if (isset($input['api_key'])) {
                $preparedData['api_key'] = $input['api_key'];
            }

            $data = null;

            if ($sms_type == 'plain' || $sms_type == 'unicode') {
                $data = $campaign->sendPlainSMS($preparedData);
            }

            if ($sms_type == 'voice') {
                $preparedData['language'] = $input['language'];
                $preparedData['gender']   = $input['gender'];

                $data = $campaign->sendVoiceSMS($preparedData);
            }

            if ($sms_type == 'mms') {
                $preparedData['media_url'] = Tool::uploadImage($input['mms_file']);
                $data                      = $campaign->sendMMS($preparedData);
            }

            if ($sms_type == 'whatsapp') {
                $data = $campaign->sendWhatsApp($preparedData);
            }

            if (is_object($data)) {
                if ( ! empty($data->status)) {
                    if (substr_count($data->status, 'Delivered') == 1) {

                        $remaining_balance = $user->sms_unit - $price;

                        $user->update(['sms_unit' => $remaining_balance]);

                        return response()->json([
                                'status'  => 'success',
                                'data'    => $data,
                                'message' => __('locale.campaigns.message_successfully_delivered'),
                        ]);
                    } else {
                        return response()->json([
                                'status'  => 'info',
                                'message' => $data->status,
                                'data'    => $data,
                        ]);
                    }
                }
            }

            return response()->json([
                    'status'  => 'info',
                    'message' => __('locale.exceptions.something_went_wrong'),
                    'data'    => $data,
            ]);


        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.subscription.no_active_subscription'),
        ]);

    }


    /**
     * @param  Campaigns  $campaign
     * @param  array  $input
     *
     * @return JsonResponse
     */
    public function campaignBuilder(Campaigns $campaign, array $input): JsonResponse
    {

        if (Auth::user()->sms_unit != '-1' && Auth::user()->sms_unit == 0) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.campaigns.sending_limit_exceed'),
            ]);
        }

        $sms_type = $input['sms_type'];

        //create campaign
        $new_campaign = Campaigns::create([
                'user_id'       => Auth::user()->id,
                'campaign_name' => $input['name'],
                'message'       => $input['message'],
                'sms_type'      => $sms_type,
                'status'        => Campaigns::STATUS_NEW,
        ]);

        if ( ! $new_campaign) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        if ($sms_type == 'unicode') {
            $db_sms_type = 'plain';
        } else {
            $db_sms_type = $sms_type;
        }


        $sending_servers = $new_campaign->getSendingServers($db_sms_type);

        if (empty($sending_servers)) {

            $new_campaign->delete();

            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.campaigns.sending_server_not_available'),
            ]);
        }


        $sender_id = null;
        if (Auth::user()->customer->getOption('sender_id_verification') == 'yes') {
            if (isset($input['originator'])) {
                if ($input['originator'] == 'sender_id') {

                    if ( ! isset($input['sender_id'])) {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }

                    $sender_id = $input['sender_id'];

                    if (isset($sender_id) && is_array($sender_id) && count($sender_id) > 0) {
                        $invalid   = [];
                        $senderids = Senderid::where('user_id', Auth::user()->id)
                                ->where('status', 'active')
                                ->select('sender_id')
                                ->cursor()
                                ->pluck('sender_id')
                                ->all();

                        foreach ($sender_id as $sender) {
                            if ( ! in_array($sender, $senderids)) {
                                $invalid[] = $sender;
                            }
                        }

                        if (count($invalid)) {

                            $new_campaign->delete();

                            return response()->json([
                                    'status'  => 'error',
                                    'message' => __('locale.sender_id.sender_id_invalid', ['sender_id' => $invalid[0]]),
                            ]);
                        }
                    } else {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }
                } else {

                    if ( ! isset($input['phone_number'])) {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.phone_numbers_required'),
                        ]);
                    }

                    $sender_id = $input['phone_number'];

                    if (isset($sender_id) && is_array($sender_id) && count($sender_id) > 0) {
                        $type_supported = [];
                        PhoneNumbers::where('user_id', Auth::user()->id)
                                ->where('status', 'assigned')
                                ->cursor()
                                ->reject(function ($number) use ($sender_id, &$type_supported, &$invalid) {
                                    if (in_array($number->number, $sender_id) && ! str_contains($number->capabilities, 'sms')) {
                                        return $type_supported[] = $number->number;
                                    }

                                    return $sender_id;
                                })->all();

                        if (count($type_supported)) {

                            $new_campaign->delete();

                            return response()->json([
                                    'status'  => 'error',
                                    'message' => __('locale.sender_id.sender_id_sms_capabilities', ['sender_id' => $type_supported[0]]),
                            ]);
                        }
                    } else {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }
                }
            } else {

                $new_campaign->delete();

                return response()->json([
                        'status'  => 'error',
                        'message' => __('locale.sender_id.sender_id_required'),
                ]);
            }
        } else {
            if (isset($input['originator'])) {
                if ($input['originator'] == 'sender_id') {
                    if ( ! isset($input['sender_id'])) {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }

                    $sender_id = $input['sender_id'];
                } else {

                    if ( ! isset($input['phone_number'])) {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.phone_numbers_required'),
                        ]);
                    }

                    $sender_id = $input['phone_number'];
                }

                if ( ! isset($sender_id) || ! is_array($sender_id) || count($sender_id) <= 0) {

                    $new_campaign->delete();

                    return response()->json([
                            'status'  => 'error',
                            'message' => __('locale.sender_id.sender_id_required'),
                    ]);
                }
            }
            if (isset($input['sender_id'])) {
                $sender_id = $input['sender_id'];
            }
        }


        $total = 0;

        foreach ($sender_id as $id) {

            $data = [
                    'campaign_id' => $new_campaign->id,
                    'sender_id'   => $id,
            ];

            if (isset($input['originator'])) {
                $data['originator'] = $input['originator'];
            }

            CampaignsSenderid::create($data);
        }


        // update contact groups details
        if (isset($input['contact_groups']) && is_array($input['contact_groups']) && count($input['contact_groups']) > 0) {
            $contact_groups = ContactGroups::whereIn('id', $input['contact_groups'])->where('status', true)->where('customer_id', Auth::user()->id)->cursor();
            foreach ($contact_groups as $group) {
                $total += $group->subscribersCount($group->cache);
                CampaignsList::create([
                        'campaign_id'     => $new_campaign->id,
                        'contact_list_id' => $group->id,
                ]);
            }
        }

        // update manual input numbers
        if (isset($input['recipients'])) {
            switch ($input['delimiter']) {
                case ',':
                    $recipients = explode(',', $input['recipients']);
                    break;

                case ';':
                    $recipients = explode(';', $input['recipients']);
                    break;

                case '|':
                    $recipients = explode('|', $input['recipients']);
                    break;

                case 'tab':
                    $recipients = explode(' ', $input['recipients']);
                    break;

                case 'new_line':
                    $recipients = explode("\n", $input['recipients']);
                    break;

                default:
                    $recipients = [];
                    break;
            }

            $recipients = collect($recipients)->unique();

            $total += $recipients->count();

            $numbers = [];

            foreach ($recipients->chunk(500) as $chunk) {
                foreach ($chunk as $number) {
                    $numbers[] = [
                            'campaign_id' => $new_campaign->id,
                            'recipient'   => preg_replace("/\r/", "", $number),
                            'created_at'  => Carbon::now(),
                            'updated_at'  => Carbon::now(),
                    ];
                }
            }
            CampaignsRecipients::insert($numbers);
        }

        if ($total == 0) {

            $new_campaign->delete();

            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.campaigns.contact_not_found'),
            ]);
        }

        if (Auth::user()->sms_unit != '-1') {
            $sms_count = 1;
            $price     = 0;

            if (isset($input['message'])) {
                $length_count = strlen(preg_replace('/\s+/', ' ', trim($input['message'])));
                if ($length_count <= 160) {
                    $sms_count = 1;
                } else {
                    $sms_count = $length_count / 157;
                }
            }


            if ($sms_type == 'plain' || $sms_type == 'unicode') {
                $unit_price = Auth::user()->customer->getOption('plain_sms');
                $price      = $total * $unit_price;
            }

            if ($sms_type == 'voice') {
                $unit_price = Auth::user()->customer->getOption('voice_sms');
                $price      = $total * $unit_price;
            }

            if ($sms_type == 'mms') {
                $unit_price = Auth::user()->customer->getOption('mms_sms');
                $price      = $total * $unit_price;
            }

            if ($sms_type == 'whatsapp') {
                $unit_price = Auth::user()->customer->getOption('whatsapp_sms');
                $price      = $total * $unit_price;
            }

            $sms_count = ceil($sms_count);
            $price     *= $sms_count;

            $balance = Auth::user()->sms_unit;

            if ($price > $balance) {

                $new_campaign->delete();

                return response()->json([
                        'status'  => 'error',
                        'message' => __('locale.campaigns.not_enough_balance', [
                                'current_balance' => $balance,
                                'campaign_price'  => $price,
                        ]),
                ]);
            }
        }

        foreach ($sending_servers as $server_id => $fitness) {
            CampaignsSendingServer::create([
                    'campaign_id'       => $new_campaign->id,
                    'sending_server_id' => $server_id,
                    'fitness'           => $fitness,
            ]);
        }

        if (isset($input['advanced']) && $input['advanced'] == "true") {
            if (isset($input['send_copy']) && $input['send_copy'] == "true") {
                Auth::user()->notify(new SendCampaignCopy($input['message'], route('customer.reports.campaign.edit', $new_campaign->uid)));
            }
            // if advanced set true then work with send copy to email and create template
            if (isset($input['create_template']) && $input['create_template'] == "true") {
                // create sms template
                Templates::create([
                        'user_id' => Auth::user()->id,
                        'name'    => $input['name'],
                        'message' => $input['message'],
                        'status'  => true,
                ]);
            }
        }

        // if schedule is available then check date, time and timezone
        if (isset($input['schedule']) && $input['schedule'] == "true") {

            $schedule_date = $input['schedule_date'].' '.$input['schedule_time'];
            $schedule_time = Tool::systemTimeFromString($schedule_date, $input['timezone']);

            $new_campaign->timezone      = $input['timezone'];
            $new_campaign->status        = Campaigns::STATUS_SCHEDULED;
            $new_campaign->schedule_time = $schedule_time;


            if ($input['frequency_cycle'] == 'onetime') {
                // working with onetime schedule
                $new_campaign->schedule_type = Campaigns::TYPE_ONETIME;
            } else {
                // working with recurring schedule
                //if schedule time frequency is not one time then check frequency details
                $recurring_date = $input['recurring_date'].' '.$input['recurring_time'];
                $recurring_end  = Tool::systemTimeFromString($recurring_date, $input['timezone']);

                $new_campaign->schedule_type = Campaigns::TYPE_RECURRING;
                $new_campaign->recurring_end = $recurring_end;

                if (isset($input['frequency_cycle'])) {
                    if ($input['frequency_cycle'] != 'custom') {
                        $schedule_cycle                 = $campaign::scheduleCycleValues();
                        $limits                         = $schedule_cycle[$input['frequency_cycle']];
                        $new_campaign->frequency_cycle  = $input['frequency_cycle'];
                        $new_campaign->frequency_amount = $limits['frequency_amount'];
                        $new_campaign->frequency_unit   = $limits['frequency_unit'];
                    } else {
                        $new_campaign->frequency_cycle  = $input['frequency_cycle'];
                        $new_campaign->frequency_amount = $input['frequency_amount'];
                        $new_campaign->frequency_unit   = $input['frequency_unit'];
                    }
                }
            }
        } else {
            $new_campaign->status = Campaigns::STATUS_QUEUED;
        }

        //update cache
        $new_campaign->cache = json_encode([
                'ContactCount'         => $total,
                'DeliveredCount'       => 0,
                'FailedDeliveredCount' => 0,
                'NotDeliveredCount'    => 0,
        ]);

        if ($sms_type == 'voice') {
            $new_campaign->language = $input['language'];
            $new_campaign->gender   = $input['gender'];
        }

        if ($sms_type == 'mms') {
            $new_campaign->media_url = Tool::uploadImage($input['mms_file']);
        }

        //finally store data and return response
        $camp = $new_campaign->save();

        if ($camp) {

            try {
                if (isset($schedule_time)) {
                    $delay_minutes = Carbon::now()->diffInMinutes($schedule_time);
                    dispatch(new StoreCampaignJob($new_campaign->id))->delay(now()->addMinutes($delay_minutes));
                } else {
                    dispatch(new StoreCampaignJob($new_campaign->id));
                }

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.campaigns.campaign_send_successfully'),
                ]);
            } catch (Throwable $exception) {
                $new_campaign->delete();

                return response()->json([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        $new_campaign->delete();

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }

    /**
     * send message using file
     *
     * @param  Campaigns  $campaign
     * @param  array  $input
     *
     * @return JsonResponse|mixed
     */
    public function sendUsingFile(Campaigns $campaign, array $input): JsonResponse
    {

        if (Auth::user()->sms_unit != '-1' && Auth::user()->sms_unit == 0) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.campaigns.sending_limit_exceed'),
            ]);
        }

        $data     = CsvData::find($input['csv_data_file_id']);
        $csv_data = json_decode($data->csv_data, true);

        $db_fields = $input['fields'];

        $form_data = json_decode($input['form_data'], true);

        if (is_array($db_fields) && ! in_array('phone', $db_fields)) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.filezone.phone_number_column_require'),
            ]);
        }

        $collection = collect($csv_data)->skip($data->csv_header);

        $total = $collection->count();

        if ($total == 0) {

            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.campaigns.contact_not_found'),
            ]);
        }

        //create campaign
        $new_campaign = Campaigns::create([
                'user_id'       => Auth::user()->id,
                'campaign_name' => $form_data['name'],
                'sms_type'      => $form_data['sms_type'],
                'upload_type'   => 'file',
                'status'        => Campaigns::STATUS_NEW,
        ]);

        if ( ! $new_campaign) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        if ($form_data['sms_type'] == 'unicode') {
            $db_sms_type = 'plain';
        } else {
            $db_sms_type = $form_data['sms_type'];
        }

        $sending_servers = $new_campaign->getSendingServers($db_sms_type);

        if (empty($sending_servers)) {

            $new_campaign->delete();

            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.campaigns.sending_server_not_available'),
            ]);
        }


        $sender_id = null;
        if (Auth::user()->customer->getOption('sender_id_verification') == 'yes') {
            if (isset($form_data['originator'])) {
                if ($form_data['originator'] == 'sender_id') {

                    if ( ! isset($form_data['sender_id'])) {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }

                    $sender_id = $form_data['sender_id'];

                    if (isset($sender_id) && is_array($sender_id) && count($sender_id) > 0) {
                        $invalid   = [];
                        $senderids = Senderid::where('user_id', Auth::user()->id)
                                ->where('status', 'active')
                                ->select('sender_id')
                                ->cursor()
                                ->pluck('sender_id')
                                ->all();

                        foreach ($sender_id as $sender) {
                            if ( ! in_array($sender, $senderids)) {
                                $invalid[] = $sender;
                            }
                        }

                        if (count($invalid)) {

                            $new_campaign->delete();

                            return response()->json([
                                    'status'  => 'error',
                                    'message' => __('locale.sender_id.sender_id_invalid', ['sender_id' => $invalid[0]]),
                            ]);
                        }
                    } else {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }
                } else {

                    if ( ! isset($form_data['phone_number'])) {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.phone_numbers_required'),
                        ]);
                    }

                    $sender_id = $form_data['phone_number'];

                    if (isset($sender_id) && is_array($sender_id) && count($sender_id) > 0) {
                        $type_supported = [];
                        PhoneNumbers::where('user_id', Auth::user()->id)
                                ->where('status', 'assigned')
                                ->cursor()
                                ->reject(function ($number) use ($sender_id, &$type_supported, &$invalid) {
                                    if (in_array($number->number, $sender_id) && ! str_contains($number->capabilities, 'sms')) {
                                        return $type_supported[] = $number->number;
                                    }

                                    return $sender_id;
                                })->all();

                        if (count($type_supported)) {

                            $new_campaign->delete();

                            return response()->json([
                                    'status'  => 'error',
                                    'message' => __('locale.sender_id.sender_id_sms_capabilities', ['sender_id' => $type_supported[0]]),
                            ]);
                        }
                    } else {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }
                }
            } else {

                $new_campaign->delete();

                return response()->json([
                        'status'  => 'error',
                        'message' => __('locale.sender_id.sender_id_required'),
                ]);
            }
        } else {
            if (isset($form_data['originator'])) {
                if ($form_data['originator'] == 'sender_id') {
                    if ( ! isset($form_data['sender_id'])) {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.sender_id_required'),
                        ]);
                    }

                    $sender_id = $form_data['sender_id'];
                } else {

                    if ( ! isset($form_data['phone_number'])) {

                        $new_campaign->delete();

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.sender_id.phone_numbers_required'),
                        ]);
                    }

                    $sender_id = $form_data['phone_number'];
                }

                if ( ! isset($sender_id) || ! is_array($sender_id) || count($sender_id) <= 0) {

                    $new_campaign->delete();

                    return response()->json([
                            'status'  => 'error',
                            'message' => __('locale.sender_id.sender_id_required'),
                    ]);
                }
            }
            if (isset($form_data['sender_id'])) {
                $sender_id = $form_data['sender_id'];
            }
        }

        foreach ($sender_id as $id) {
            CampaignsSenderid::create([
                    'campaign_id' => $new_campaign->id,
                    'sender_id'   => $id,
            ]);
        }


        if (Auth::user()->sms_unit != '-1') {

            $price = 0;

            if ($form_data['sms_type'] == 'plain' || $form_data['sms_type'] == 'unicode') {
                $unit_price = Auth::user()->customer->getOption('plain_sms');
                $price      = $total * $unit_price;
            }

            if ($form_data['sms_type'] == 'voice') {
                $unit_price = Auth::user()->customer->getOption('voice_sms');
                $price      = $total * $unit_price;
            }

            if ($form_data['sms_type'] == 'mms') {
                $unit_price = Auth::user()->customer->getOption('mms_sms');
                $price      = $total * $unit_price;
            }

            if ($form_data['sms_type'] == 'whatsapp') {
                $unit_price = Auth::user()->customer->getOption('whatsapp_sms');
                $price      = $total * $unit_price;
            }

            $balance = Auth::user()->sms_unit;

            if ($price > $balance) {

                $new_campaign->delete();

                return response()->json([
                        'status'  => 'error',
                        'message' => __('locale.campaigns.not_enough_balance', [
                                'current_balance' => $balance,
                                'campaign_price'  => $price,
                        ]),
                ]);
            }
        }

        foreach ($sending_servers as $server_id => $fitness) {
            CampaignsSendingServer::create([
                    'campaign_id'       => $new_campaign->id,
                    'sending_server_id' => $server_id,
                    'fitness'           => $fitness,
            ]);
        }

        // if schedule is available then check date, time and timezone
        if (isset($form_data['schedule']) && $form_data['schedule'] == "true") {

            $schedule_date = $form_data['schedule_date'].' '.$form_data['schedule_time'];
            $schedule_time = Tool::systemTimeFromString($schedule_date, $form_data['timezone']);

            $new_campaign->timezone      = $form_data['timezone'];
            $new_campaign->status        = Campaigns::STATUS_SCHEDULED;
            $new_campaign->schedule_type = Campaigns::TYPE_ONETIME;
            $new_campaign->schedule_time = $schedule_time;
        } else {
            $new_campaign->status = Campaigns::STATUS_QUEUED;
        }


        //update cache
        $new_campaign->cache = json_encode([
                'ContactCount'         => $total,
                'DeliveredCount'       => 0,
                'FailedDeliveredCount' => 0,
                'NotDeliveredCount'    => 0,
        ]);

        if ($form_data['sms_type'] == 'voice') {
            $new_campaign->language = $form_data['language'];
            $new_campaign->gender   = $form_data['gender'];
        }

        if ($form_data['sms_type'] == 'mms') {
            $new_campaign->media_url = $form_data['media_url'];
        }

        //finally store data and return response
        $camp = $new_campaign->save();

        if ($camp) {

            try {
                if (isset($schedule_time)) {
                    $delay_minutes = Carbon::now()->diffInMinutes($schedule_time);
                    dispatch(new ScheduleBatchJob(Auth::user()->id, $new_campaign->id, $collection, $db_fields))->delay(now()->addMinutes($delay_minutes));

                    return response()->json([
                            'status'  => 'success',
                            'message' => __('locale.campaigns.campaign_successfully_imported_in_background'),
                    ]);

                }

                $batch_list = [];
                Tool::resetMaxExecutionTime();
                $collection->chunk(5000)
                        ->each(function ($lines) use ($new_campaign, &$batch_list, $db_fields) {
                            $batch_list[] = new ImportCampaign(Auth::user()->id, $new_campaign->id, $lines, $db_fields);
                        });

                $import_name = 'ImportContacts_'.date('Ymdhms');

                $import_job = ImportJobHistory::create([
                        'name'      => $import_name,
                        'import_id' => $new_campaign->uid,
                        'type'      => 'import_campaign',
                        'status'    => 'processing',
                        'options'   => json_encode(['status' => 'processing', 'message' => 'Import campaign are running']),
                        'batch_id'  => null,
                ]);

                $batch = Bus::batch($batch_list)
                        ->then(function (Batch $batch) use ($new_campaign, $import_name, $import_job) {
                            $new_campaign->processing();
                            $new_campaign->update(['batch_id' => $batch->id]);
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
                        ->finally(function (Batch $batch) use ($new_campaign, $data) {
                            $import_history = ImportJobHistory::where('batch_id', $batch->id)->first();
                            if ($import_history) {
                                $import_history->status  = 'finished';
                                $import_history->options = json_encode(['status' => 'finished', 'message' => 'Import campaign was successfully imported.']);
                                $import_history->save();
                                $new_campaign->delivered();
                            }

                            $data->delete();
                            //send event notification remaining
                        })
                        ->name($import_name)
                        ->dispatch();

                $new_campaign->update(['batch_id' => $batch->id]);

                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.campaigns.campaign_successfully_imported_in_background'),
                ]);


            } catch (Throwable $exception) {
                $new_campaign->delete();

                return response()->json([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        $new_campaign->delete();

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function cancel(Campaigns $campaign)
    {
        // TODO: Implement cancel() method.
    }

    /**
     * @inheritDoc
     */
    public function pause(Campaigns $campaign)
    {
        // TODO: Implement pause() method.
    }

    /**
     * @inheritDoc
     */
    public function destroy(Campaigns $campaign)
    {
        // TODO: Implement destroy() method.
    }

    /**
     * @inheritDoc
     */
    public function update(Campaigns $campaign, array $input)
    {
        // TODO: Implement update() method.
    }

    /**
     * @inheritDoc
     */
    public function resend(Campaigns $campaign)
    {
        // TODO: Implement resend() method.
    }
}
