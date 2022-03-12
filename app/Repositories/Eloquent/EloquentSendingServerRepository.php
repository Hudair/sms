<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Models\CustomSendingServer;
use App\Models\SendingServer;
use App\Repositories\Contracts\SendingServerRepository;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class EloquentSendingServerRepository extends EloquentBaseRepository implements SendingServerRepository
{

    /**
     * EloquentSendingServerRepository constructor.
     *
     * @param  SendingServer  $sendingServer
     *
     */
    public function __construct(SendingServer $sendingServer)
    {
        parent::__construct($sendingServer);
    }


    /**
     * Store Sending server
     *
     * @param  array  $input
     *
     * @return SendingServer|mixed
     *
     * @throws GeneralException
     */
    public function store(array $input): SendingServer
    {

        $insert_value = array_keys($this->allSendingServer()[$input['settings']]);

        /** @var SendingServer $sendingServer */
        $sendingServer = $this->make(Arr::only($input, $insert_value));

        $sendingServer->status  = true;
        $sendingServer->user_id = auth()->user()->id;

        if ( ! $this->save($sendingServer)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $sendingServer;

    }

    /**
     * @param  SendingServer  $sendingServer
     *
     * @return bool
     * @throws GeneralException
     */
    private function save(SendingServer $sendingServer): bool
    {
        if ( ! $sendingServer->save()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return true;
    }

    /**
     * @param  CustomSendingServer  $customSendingServer
     *
     * @return bool
     * @throws GeneralException
     */
    private function saveCustom(CustomSendingServer $customSendingServer): bool
    {
        if ( ! $customSendingServer->save()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return true;
    }

    /**
     * @param  SendingServer  $sendingServer
     * @param  array  $input
     *
     * @return SendingServer
     * @throws Exception|Throwable
     *
     * @throws Exception
     */
    public function update(SendingServer $sendingServer, array $input): SendingServer
    {
        if ( ! $sendingServer->update($input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $sendingServer;
    }

    /**
     * @param  SendingServer  $sendingServer
     * @param  int|null  $user_id
     *
     * @return bool|null
     * @throws GeneralException
     * @throws Exception
     */
    public function destroy(SendingServer $sendingServer, int $user_id = null): bool
    {

        if ($user_id) {

            //Delete sending server
            if ( ! SendingServer::where('uid', $sendingServer->uid)->where('user_id', $user_id)->delete()) {
                //throw exception if not deleted
                throw new GeneralException(__('locale.exceptions.something_went_wrong'));
            }
        } else {
            $plans = SendingServer::with('plans')->get();

            //Delete sending server
            if ( ! $sendingServer->delete()) {
                //throw exception if not deleted
                throw new GeneralException(__('locale.exceptions.something_went_wrong'));
            }

            foreach ($plans as $plan) {
                foreach ($plan->plans as $data) {
                    $data->checkStatus();
                }
            }
        }


        return true;
    }

    /**
     * @param  array  $ids
     * @param  int|null  $user_id
     *
     * @return mixed
     * @throws Throwable
     */
    public function batchDestroy(array $ids, int $user_id = null): bool
    {

        if ($user_id) {
            $sendingSevers = $this->query()->whereIn('uid', $ids)->where('user_id', $user_id)->cursor();
            foreach ($sendingSevers as $sever) {
                $sever->delete();
            }
        } else {
            DB::transaction(function () use ($ids) {
                $sendingSevers = $this->query()->whereIn('uid', $ids)->cursor();
                foreach ($sendingSevers as $sever) {
                    if ($sever->delete()) {
                        $plans = $sever::with('plans')->get();
                        foreach ($plans as $plan) {
                            foreach ($plan->plans as $data) {
                                $data->checkStatus();
                            }
                        }
                    }
                }
            });
        }

        return true;
    }

    /**
     * @param  array  $ids
     *
     * @return mixed
     * @throws Exception|Throwable
     *
     */
    public function batchActive(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['status' => true])
            ) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

    /**
     * @param  array  $ids
     *
     * @return mixed
     * @throws Exception|Throwable
     *
     */
    public function batchDisable(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['status' => false])
            ) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

    public function sendTestSMS(SendingServer $sendingServer, array $input)
    {
        // TODO: Implement sendTestSMS() method.
    }

    /**
     * All Sending Servers
     *
     * @return array[]
     */
    public function allSendingServer(): array
    {
        return [
                "Twilio" => [
                        'name'            => 'Twilio',
                        'settings'        => 'Twilio',
                        'account_sid'     => 'account_sid',
                        'auth_token'      => 'auth_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => true,
                        'whatsapp'        => true,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "TwilioCopilot" => [
                        'name'            => 'Twilio Copilot',
                        'settings'        => 'TwilioCopilot',
                        'account_sid'     => 'account_sid',
                        'auth_token'      => 'auth_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 100,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "EasySendSMS"     => [
                        'name'            => 'EasySendSMS',
                        'settings'        => 'EasySendSMS',
                        'api_link'        => 'https://www.easysendsms.com/sms/bulksms-api/bulksms-api',
                        'username'        => 'username',
                        'password'        => 'password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "CheapGlobalSMS"  => [
                        'name'            => 'CheapGlobalSMS',
                        'settings'        => 'CheapGlobalSMS',
                        'api_link'        => 'https://cheapglobalsms.com/api_v1',
                        'username'        => 'sub_account',
                        'password'        => 'sub_account_pass',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "ClickatellTouch" => [
                        'name'            => 'Clickatell Touch',
                        'settings'        => 'ClickatellTouch',
                        'api_link'        => 'https://platform.clickatell.com/messages/http/send',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "ClickatellCentral" => [
                        'name'            => 'Clickatell Central',
                        'settings'        => 'ClickatellCentral',
                        'api_link'        => 'https://api.clickatell.com/http/sendmsg',
                        'username'        => 'user_name',
                        'password'        => 'password',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "RouteMobile" => [
                        'name'            => 'Route Mobile',
                        'settings'        => 'RouteMobile',
                        'api_link'        => 'https://api.rmlconnect.net/bulksms/bulksms',
                        'username'        => 'user_name',
                        'password'        => 'password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "TextLocal" => [
                        'name'            => 'Text Local',
                        'settings'        => 'TextLocal',
                        'api_link'        => 'https://api.txtlocal.com/send/',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1000,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Plivo" => [
                        'name'            => 'Plivo',
                        'settings'        => 'Plivo',
                        'auth_id'         => 'auth_id',
                        'auth_token'      => 'auth_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => true,
                        'whatsapp'        => false,
                        'sms_per_request' => 4,
                        'quota_value'     => 200,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "PlivoPowerpack" => [
                        'name'            => 'Plivo Powerpack',
                        'settings'        => 'PlivoPowerpack',
                        'auth_id'         => 'auth_id',
                        'auth_token'      => 'auth_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 4,
                        'quota_value'     => 200,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "SMSGlobal" => [
                        'name'            => 'SMS Global',
                        'settings'        => 'SMSGlobal',
                        'api_link'        => 'https://api.smsglobal.com/http-api.php',
                        'username'        => 'user_name',
                        'password'        => 'password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "BulkSMS" => [
                        'name'            => 'Bulk SMS',
                        'settings'        => 'BulkSMS',
                        'api_link'        => 'https://api.bulksms.com/v1/messages',
                        'username'        => 'user_name',
                        'password'        => 'password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Vonage" => [
                        'name'            => 'Nexmo is now Vonage',
                        'settings'        => 'Vonage',
                        'api_link'        => 'https://rest.nexmo.com/sms/json',
                        'api_key'         => 'api_key',
                        'api_secret'      => 'api_secret',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Infobip" => [
                        'name'            => 'Infobip',
                        'settings'        => 'Infobip',
                        'api_link'        => 'https://89g329.api.infobip.com/sms/2/text/advanced',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => true,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "1s2u" => [
                        'name'            => '1s2u',
                        'settings'        => '1s2u',
                        'api_link'        => 'https://api.1s2u.io/bulksms',
                        'username'        => 'user_name',
                        'password'        => 'password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "MessageBird" => [
                        'name'            => 'Message Bird',
                        'settings'        => 'MessageBird',
                        'api_link'        => 'https://rest.messagebird.com/messages',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => true,
                        'whatsapp'        => true,
                        'sms_per_request' => 5,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "AmazonSNS" => [
                        'name'            => 'Amazon SNS',
                        'settings'        => 'AmazonSNS',
                        'access_key'      => 'access_key',
                        'secret_access'   => 'secret_access',
                        'region'          => 'region',
                        'sms_type'        => 'Transational',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Tyntec" => [
                        'name'            => 'Tyntec',
                        'settings'        => 'Tyntec',
                        'api_link'        => 'https://api.tyntec.com/messaging/v1/sms',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "WhatsAppChatApi" => [
                        'name'            => 'WhatsApp By Chat API',
                        'settings'        => 'WhatsAppChatApi',
                        'api_link'        => 'https://eu8.chat-api.com/instance105654',
                        'api_token'       => 'api_token',
                        'schedule'        => true,
                        'type'            => 'whatsapp',
                        'two_way'         => true,
                        'plain'           => false,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => true,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "KarixIO" => [
                        'name'            => 'KarixIO',
                        'settings'        => 'KarixIO',
                        'api_link'        => 'https://api.karix.io/message/',
                        'auth_id'         => 'auth_id',
                        'auth_token'      => 'auth_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "SignalWire" => [
                        'name'            => 'SignalWire',
                        'settings'        => 'SignalWire',
                        'api_link'        => 'https://example.signalwire.com',
                        'api_token'       => 'api_token',
                        'project_id'      => 'project_id',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => true,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Telnyx" => [
                        'name'            => 'Telnyx',
                        'settings'        => 'Telnyx',
                        'api_link'        => 'https://api.telnyx.com/v2/messages',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Bandwidth" => [
                        'name'            => 'Bandwidth',
                        'settings'        => 'Bandwidth',
                        'api_link'        => 'https://messaging.bandwidth.com/api/v2/users/account_id/messages',
                        'api_token'       => 'api_token',
                        'api_secret'      => 'api_secret',
                        'application_id'  => 'application_id',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "SMPP" => [
                        'name'            => 'SMPP',
                        'settings'        => 'SMPP',
                        'api_link'        => 'IP/DOMAIN',
                        'port'            => 'PORT',
                        'username'        => 'SYSTEM ID/Username',
                        'password'        => 'Password',
                        'source_addr_ton' => '5',
                        'source_addr_npi' => '0',
                        'dest_addr_ton'   => '1',
                        'dest_addr_npi'   => '0',
                        'schedule'        => true,
                        'type'            => 'smpp',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
            /*
            |--------------------------------------------------------------------------
            | Added in version 3.0.1
            |--------------------------------------------------------------------------
            |
            |
            |
            */

                "RouteeNet"         => [
                        'name'            => 'RouteeNet',
                        'settings'        => 'RouteeNet',
                        'api_link'        => 'https://connect.routee.net/sms',
                        'application_id'  => 'application_id',
                        'api_secret'      => 'application_secret',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "HutchLk"           => [
                        'name'            => 'HutchLk',
                        'settings'        => 'HutchLk',
                        'api_link'        => 'https://bulksms.hutch.lk/sendsmsmultimask.php',
                        'username'        => 'username',
                        'password'        => 'password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "Teletopiasms"      => [
                        'name'            => 'Teletopiasms',
                        'settings'        => 'Teletopiasms',
                        'api_link'        => 'https://api1.teletopiasms.no/gateway/v3/plain',
                        'username'        => 'username',
                        'password'        => 'password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "BroadcasterMobile" => [
                        'name'            => 'BroadcasterMobile',
                        'settings'        => 'BroadcasterMobile',
                        'api_link'        => 'https://api.broadcastermobile.com/brdcstr-endpoint-web/services/messaging',
                        'api_token'       => 'api_token',
                        'api_key'         => 'api_key',
                        'c1'              => 'country_code',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "Solutions4mobiles" => [
                        'name'            => 'Solutions4mobiles',
                        'settings'        => 'Solutions4mobiles',
                        'api_link'        => 'https://sms.solutions4mobiles.com/',
                        'username'        => 'username',
                        'password'        => 'password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "BeemAfrica"        => [
                        'name'            => 'BeemAfrica',
                        'settings'        => 'BeemAfrica',
                        'api_link'        => 'https://apisms.beem.africa/v1/send',
                        'api_key'         => 'api_key',
                        'api_secret'      => 'api_secret',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "BulkSMSOnline"     => [
                        'name'            => 'BulkSMSOnline',
                        'settings'        => 'BulkSMSOnline',
                        'api_link'        => 'https://api.bulksmsonline.com:9090/smsapi',
                        'username'        => 'username',
                        'password'        => 'password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "FlowRoute"         => [
                        'name'            => 'FlowRoute',
                        'settings'        => 'FlowRoute',
                        'api_link'        => 'https://api.flowroute.com/v2.1/messages',
                        'access_key'      => 'access_key',
                        'api_secret'      => 'secret_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "WaApi"             => [
                        'name'            => 'WA Api',
                        'settings'        => 'WaApi',
                        'api_link'        => 'http://apiv3.waapi.co/api/send.php',
                        'c1'              => 'client_id',
                        'c2'              => 'instance_id',
                        'schedule'        => true,
                        'type'            => 'whatsapp',
                        'two_way'         => false,
                        'plain'           => false,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => true,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "ElitBuzzBD"        => [
                        'name'            => 'ElitBuzzBD',
                        'settings'        => 'ElitBuzzBD',
                        'api_link'        => 'https://msg.elitbuzz-bd.com/smsapi',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "GreenWebBD"        => [
                        'name'            => 'GreenWebBD',
                        'settings'        => 'GreenWebBD',
                        'api_link'        => 'https://api.greenweb.com.bd/api.php?json',
                        'api_token'       => 'api_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "HablameV2"         => [
                        'name'            => 'HablameV2',
                        'settings'        => 'HablameV2',
                        'api_link'        => 'https://api101.hablame.co/api/sms/v2.1/send/',
                        'api_token'       => 'api_token',
                        'api_key'         => 'api_key',
                        'c1'              => 'account',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "ZamtelCoZm"        => [
                        'name'            => 'ZamtelCoZm',
                        'settings'        => 'ZamtelCoZm',
                        'api_link'        => 'https://bulksms.zamtel.co.zm/api/sms/send/batch',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "CellCast"          => [
                        'name'            => 'CellCast',
                        'settings'        => 'CellCast',
                        'api_link'        => 'https://cellcast.com.au/api/v3/send-sms',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "AfricasTalking"    => [
                        'name'            => 'AfricasTalking',
                        'settings'        => 'AfricasTalking',
                        'api_link'        => 'https://api.africastalking.com/version1/messaging',
                        'api_key'         => 'api_key',
                        'username'        => 'username',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "CaihCom"           => [
                        'name'            => 'CaihCom',
                        'settings'        => 'CaihCom',
                        'api_link'        => 'http://sms.caihcom.com/sms/send',
                        'api_token'       => 'api_token',
                        'c1'              => 'channel_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "KeccelSMS"         => [
                        'name'            => 'KeccelSMS',
                        'settings'        => 'KeccelSMS',
                        'api_link'        => 'http://161.97.92.251:22099/message',
                        'application_id'  => 'id',
                        'password'        => 'pass',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "JohnsonConnect"    => [
                        'name'            => 'JohnsonConnect',
                        'settings'        => 'JohnsonConnect',
                        'api_link'        => 'http://161.117.182.177:8080/api/sms/mtsend',
                        'api_key'         => 'app_key',
                        'api_secret'      => 'secret_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "SpeedaMobile"      => [
                        'name'            => 'SpeedaMobile',
                        'settings'        => 'SpeedaMobile',
                        'api_link'        => 'http://api.smsala.com/api/SendSMS',
                        'auth_id'         => 'api_id',
                        'password'        => 'api_password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "SMSala"            => [
                        'name'            => 'SMSala',
                        'settings'        => 'SMSala',
                        'api_link'        => 'http://api.smsala.com/api/SendSMS',
                        'auth_id'         => 'api_id',
                        'password'        => 'api_password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "Text2World"        => [
                        'name'            => 'Text2World',
                        'settings'        => 'Text2World',
                        'api_link'        => 'https://text2world.com/sendsms/sendsms.php',
                        'username'        => 'username',
                        'password'        => 'password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "EnableX"           => [
                        'name'            => 'EnableX',
                        'settings'        => 'EnableX',
                        'api_link'        => 'https://api.enablex.io/sms/v1/messages/',
                        'application_id'  => 'APP ID',
                        'api_key'         => 'APP Key',
                        'c1'              => 'Campaign ID',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "SpoofSend"         => [
                        'name'            => 'SpoofSend',
                        'settings'        => 'SpoofSend',
                        'api_link'        => 'https://user.spoofsend.com/smsAPI',
                        'api_key'         => 'api_key',
                        'api_token'       => 'api_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "AlhajSms"          => [
                        'name'            => 'AlhajSms',
                        'settings'        => 'AlhajSms',
                        'api_link'        => 'https://login.haj-sms.com/smsAPI',
                        'api_key'         => 'api_key',
                        'api_token'       => 'api_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "SendroidUltimate"  => [
                        'name'            => 'SendroidUltimate',
                        'settings'        => 'SendroidUltimate',
                        'api_link'        => 'https://yourapplicationurl.com/smsAPI',
                        'api_key'         => 'api_key',
                        'api_token'       => 'api_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "RealSMS"           => [
                        'name'            => 'RealSMS',
                        'settings'        => 'RealSMS',
                        'api_link'        => 'https://yourapplicationurl.com/smsAPI',
                        'api_key'         => 'api_key',
                        'api_token'       => 'api_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "Callr"             => [
                        'name'            => 'Callr',
                        'settings'        => 'Callr',
                        'api_link'        => 'https://api.callr.com/rest/v1.1/sms',
                        'username'        => 'api_login',
                        'password'        => 'api_password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "Skyetel"           => [
                        'name'            => 'Skyetel',
                        'settings'        => 'Skyetel',
                        'api_link'        => 'https://sms.skyetel.com/v1/out',
                        'account_sid'     => 'account_sid',
                        'api_secret'      => 'api_secret',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "LTR"          => [
                        'name'            => 'LTR',
                        'settings'        => 'LTR',
                        'api_link'        => 'https://sms.lrt.com.pk/api/sms-single-or-bulk-api.php',
                        'username'        => 'username',
                        'password'        => 'password',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "Bulksmsplans" => [
                        'name'            => 'Bulksmsplans',
                        'settings'        => 'Bulksmsplans',
                        'api_link'        => 'http://bulksmsplans.com/api/send_sms',
                        'auth_id'         => 'API ID',
                        'password'        => 'API Password',
                        'route'           => 'Transactional',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Sinch"      => [
                        'name'            => 'Sinch',
                        'settings'        => 'Sinch',
                        'api_link'        => 'https://api.clxcommunications.com/xms/v1/awfvq1',
                        'api_token'       => 'api_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "D7Networks" => [
                        'name'            => 'D7Networks',
                        'settings'        => 'D7Networks',
                        'api_link'        => 'https://rest-api.d7networks.com/secure/send',
                        'username'        => 'username',
                        'password'        => 'password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "CMCOM"      => [
                        'name'            => 'CMCOM',
                        'settings'        => 'CMCOM',
                        'api_link'        => 'https://gw.cmtelecom.com/v1.0/message',
                        'api_token'       => 'product_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "PitchWink" => [
                        'name'            => 'PitchWink',
                        'settings'        => 'PitchWink',
                        'api_link'        => 'https://pw-api.com/sms/v_4_00/postsms.aspx',
                        'c1'              => 'Credential',
                        'api_token'       => 'api_token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Wavy" => [
                        'name'            => 'Wavy',
                        'settings'        => 'Wavy',
                        'api_link'        => 'https://api-messaging.wavy.global/v1/send-sms',
                        'auth_token'      => 'authenticationtoken',
                        'username'        => 'username',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Solucoesdigitais" => [
                        'name'            => 'Solucoesdigitais',
                        'settings'        => 'Solucoesdigitais',
                        'api_link'        => 'https://sms.solucoesdigitais.cc/integracao/v2/envio_transacional',
                        'username'        => 'usuario',
                        'password'        => 'senha',
                        'c1'              => 'centro_custo_interno',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "SmartVision"    => [
                        'name'            => 'SmartVision',
                        'settings'        => 'SmartVision',
                        'api_link'        => 'http://customers.smsmarketing.ae/app/smsapi/index.php',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "ZipComIo"       => [
                        'name'            => 'ZipComIo',
                        'settings'        => 'ZipComIo',
                        'api_link'        => 'https://api.zipcom.io/2019-01-23/messages',
                        'api_key'         => 'api_key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "GlobalSMSCN"    => [
                        'name'            => 'GlobalSMSCN',
                        'settings'        => 'GlobalSMSCN',
                        'api_link'        => 'http://sms.skylinelabs.cc:20004/v3/sendSms',
                        'api_key'         => 'API KEY',
                        'api_secret'      => 'API Secret',
                        'application_id'  => 'Application ID',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "Web2SMS237"     => [
                        'name'            => 'Web2SMS237',
                        'settings'        => 'Web2SMS237',
                        'api_link'        => 'https://api.web2sms237.com/sms/send',
                        'api_key'         => 'User ID',
                        'api_secret'      => 'User Secret',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "BongaTech"      => [
                        'name'            => 'BongaTech',
                        'settings'        => 'BongaTech',
                        'api_link'        => 'https://bulk.bongatech.co.ke/api/v1/send-basic-sms',
                        'username'        => 'Username',
                        'password'        => 'Password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "FloatSMS"       => [
                        'name'            => 'FloatSMS',
                        'settings'        => 'FloatSMS',
                        'api_link'        => 'https://app.floatsms.com/api/send',
                        'api_key'         => 'API Key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 100,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "YooAPI"         => [
                        'name'            => 'YooAPI',
                        'settings'        => 'YooAPI',
                        'api_link'        => 'https://my.yooapi.com/api/send.php',
                        'c1'              => 'client_id',
                        'c2'              => 'instance_id',
                        'schedule'        => true,
                        'type'            => 'whatsapp',
                        'two_way'         => false,
                        'plain'           => false,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => true,
                        'sms_per_request' => 100,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "MaisSMS"        => [
                        'name'            => 'MaisSMS',
                        'settings'        => 'MaisSMS',
                        'api_link'        => 'http://http.maissms.com.br/mt',
                        'api_token'       => 'Token',
                        'c1'              => 'parceiro_id',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 100,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "EasySmsXyz"     => [
                        'name'            => 'EasySmsXyz',
                        'settings'        => 'EasySmsXyz',
                        'api_link'        => 'https://app.easysms.xyz/services/send.php',
                        'api_key'         => 'Api Key',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 100,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "Sozuri"         => [
                        'name'            => 'Sozuri',
                        'settings'        => 'Sozuri',
                        'api_link'        => 'https://sozuri.net/api/v1/messaging',
                        'api_key'         => 'Api Key',
                        'project_id'      => 'Project',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 100,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "ExpertTexting"  => [
                        'name'            => 'ExpertTexting',
                        'settings'        => 'ExpertTexting',
                        'api_link'        => 'https://www.experttexting.com/ExptRestApi/sms/json/Message/Send',
                        'username'        => 'Username',
                        'api_key'         => 'api_key',
                        'api_secret'      => 'api_secret',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "Ejoin"          => [
                        'name'            => 'Ejoin',
                        'settings'        => 'Ejoin',
                        'api_link'        => 'http://103.114.97.146/goip_post_sms.html',
                        'username'        => 'Username',
                        'password'        => 'Password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "BulkSMSNigeria" => [
                        'name'            => 'BulkSMSNigeria',
                        'settings'        => 'BulkSMSNigeria',
                        'api_link'        => 'https://www.bulksmsnigeria.com/api/v1/sms/create',
                        'api_token'       => 'API Token',
                        'c1'              => 'DND',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "SendSMSGate" => [
                        'name'            => 'SendSMSGate',
                        'settings'        => 'SendSMSGate',
                        'api_link'        => 'https://cloud.sendsmsgate.com/sendsms.php',
                        'username'        => 'Username',
                        'password'        => 'Password',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Gateway360" => [
                        'name'            => 'Gateway360',
                        'settings'        => 'Gateway360',
                        'api_link'        => 'https://api.gateway360.com/api/3.0/sms/send',
                        'api_key'         => 'API KEY',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "AjuraTech"  => [
                        'name'            => 'AjuraTech',
                        'settings'        => 'AjuraTech',
                        'api_link'        => 'https://smpp.ajuratech.com:7790/sendtext',
                        'api_key'         => 'API KEY',
                        'api_secret'      => 'Secret KEY',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "SMSCloudCI" => [
                        'name'            => 'SMSCloudCI',
                        'settings'        => 'SMSCloudCI',
                        'api_link'        => 'https://api.smscloud.ci/v1/campaigns',
                        'api_token'       => 'API TOKEN',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "LifetimeSMS"  => [
                        'name'            => 'LifetimeSMS',
                        'settings'        => 'LifetimeSMS',
                        'api_link'        => 'https://lifetimesms.com/plain',
                        'api_token'       => 'API TOKEN',
                        'api_secret'      => 'API SECRET',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "PARATUS"      => [
                        'name'            => 'PARATUS',
                        'settings'        => 'PARATUS',
                        'api_link'        => 'https://[playSMS_domain_or_url]/index.php',
                        'username'        => 'Username',
                        'api_token'       => 'Webservices Token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "MOOVCI"       => [
                        'name'            => 'MOOVCI',
                        'settings'        => 'MOOVCI',
                        'api_link'        => 'http://41.191.68.213:80/apiSms/http/sendsms/',
                        'api_key'         => 'api key',
                        'c1'              => 'Login',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "LeTexto"      => [
                        'name'            => 'LeTexto',
                        'settings'        => 'LeTexto',
                        'api_link'        => 'https://api.letexto.com/v1/campaigns',
                        'api_token'       => 'api token',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "SMSCarrierEU" => [
                        'name'            => 'SMSCarrierEU',
                        'settings'        => 'SMSCarrierEU',
                        'api_link'        => 'https://smsc.i-digital-m.com/smsgw/sendsms.php',
                        'username'        => 'username',
                        'password'        => 'password',
                        'type'            => 'http',
                        'schedule'        => true,
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "MSMPusher"    => [
                        'name'            => 'MSMPusher',
                        'settings'        => 'MSMPusher',
                        'api_link'        => 'http://api.msmpusher.com/api',
                        'c1'              => 'Private Key',
                        'c2'              => 'Public Key',
                        'type'            => 'http',
                        'schedule'        => true,
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],
                "TxTria"       => [
                        'name'            => 'TxTria',
                        'settings'        => 'TxTria',
                        'api_link'        => 'https://txtria.com/api/sendsms',
                        'c1'              => 'System ID',
                        'auth_token'      => 'Auth Token',
                        'type'            => 'http',
                        'schedule'        => true,
                        'two_way'         => false,
                        'plain'           => true,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Whatsender" => [
                        'name'            => 'Whatsender',
                        'settings'        => 'Whatsender',
                        'api_link'        => 'https://api.whatsender.io/v1/messages',
                        'api_token'       => 'api_token',
                        'device_id'       => 'device_id',
                        'schedule'        => true,
                        'type'            => 'whatsapp',
                        'two_way'         => true,
                        'plain'           => false,
                        'mms'             => true,
                        'voice'           => false,
                        'whatsapp'        => true,
                        'sms_per_request' => 1,
                        'quota_value'     => 1,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

                "Gatewayapi"  => [
                        'name'            => 'Gatewayapi',
                        'settings'        => 'Gatewayapi',
                        'api_link'        => 'https://gatewayapi.com/rest/mtsms',
                        'api_token'       => 'API TOKEN',
                        'schedule'        => true,
                        'type'            => 'http',
                        'two_way'         => true,
                        'plain'           => true,
                        'mms'             => false,
                        'voice'           => false,
                        'whatsapp'        => false,
                        'sms_per_request' => 1,
                        'quota_value'     => 60,
                        'quota_base'      => 1,
                        'quota_unit'      => 'minute',
                ],

//
//                "SmsGatewayMe" => [
//                        'name'            => 'SmsGatewayMe',
//                        'settings'        => 'SmsGatewayMe',
//                        'api_link'        => 'https://smsgateway.me/api/v4',
//                        'api_token'         => 'api_token',
//                        'device_id'         => 'device_id',
//                        'schedule'        => true,
//                        'type'            => 'http',
//                        'two_way'         => false,
//                        'plain'           => true,
//                        'mms'             => false,
//                        'voice'           => false,
//                        'whatsapp'        => false,
//                        'sms_per_request' => 1,
//                        'quota_value'     => 100,
//                        'quota_base'      => 1,
//                        'quota_unit'      => 'minute',
//                ],

        ];
    }


    /**
     * Store custom sending server
     *
     * @param  array  $input
     *
     * @return SendingServer|mixed
     * @throws GeneralException
     * @throws Exception
     */
    public function storeCustom(array $input): SendingServer
    {
        $sendingServerInput = ['name',
                'api_link',
                'success_keyword',
                'plain',
                'schedule',
                'quota_value',
                'quota_base',
                'quota_unit',
                'sms_per_request',
                'cutting_value',
                'cutting_unit',
                'cutting_logic'];

        /** @var SendingServer $sendingServer */
        $sendingServer = $this->make(Arr::only($input, $sendingServerInput));

        $settings = ucfirst(preg_replace('/\s+/', '', $input['name']));

        $sendingServer->settings = $settings;
        $sendingServer->status   = true;
        $sendingServer->custom   = true;
        $sendingServer->user_id  = auth()->user()->id;

        if ($this->save($sendingServer)) {

            $customServer        = new CustomSendingServer();
            $customSendingServer = $customServer->make(Arr::only($input, ['http_request_method',
                    'json_encoded_post',
                    'content_type',
                    'content_type_accept',
                    'character_encoding',
                    'ssl_certificate_verification',
                    'authorization',
                    'multi_sms_delimiter',
                    'username_param',
                    'username_value',
                    'password_param',
                    'password_value',
                    'password_status',
                    'action_param',
                    'action_value',
                    'action_status',
                    'source_param',
                    'source_value',
                    'source_status',
                    'destination_param',
                    'message_param',
                    'unicode_param',
                    'unicode_value',
                    'unicode_status',
                    'route_param',
                    'route_value',
                    'route_status',
                    'language_param',
                    'language_value',
                    'language_status',
                    'custom_one_param',
                    'custom_one_value',
                    'custom_one_status',
                    'custom_two_param',
                    'custom_two_value',
                    'custom_two_status',
                    'custom_three_param',
                    'custom_three_value',
                    'custom_three_status']));

            $customSendingServer->server_id = $sendingServer->id;

            if ( ! $this->saveCustom($customSendingServer)) {
                $sendingServer->delete();
                throw new GeneralException(__('locale.exceptions.something_went_wrong'));
            }

            return $sendingServer;
        }
        throw new GeneralException(__('locale.exceptions.something_went_wrong'));
    }


    /**
     * @param  SendingServer  $sendingServer
     * @param  array  $input
     *
     * @return SendingServer
     * @throws Exception|Throwable
     *
     * @throws Exception
     */
    public function updateCustom(SendingServer $sendingServer, array $input): SendingServer
    {

        if ($sendingServer->update($input)) {
            $customServer = CustomSendingServer::where('server_id', $sendingServer->id)->first();

            if ( ! $customServer->update($input)) {
                throw new GeneralException(__('locale.exceptions.something_went_wrong'));
            }

            return $sendingServer;
        }

        throw new GeneralException(__('locale.exceptions.something_went_wrong'));
    }

}
