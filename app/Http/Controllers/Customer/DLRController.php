<?php

namespace App\Http\Controllers\Customer;

use App\Events\MessageReceived;
use App\Http\Controllers\Controller;
use App\Library\SMSCounter;
use App\Models\Blacklists;
use App\Models\Campaigns;
use App\Models\ChatBox;
use App\Models\ChatBoxMessage;
use App\Models\ContactGroups;
use App\Models\ContactGroupsOptinKeywords;
use App\Models\ContactGroupsOptoutKeywords;
use App\Models\Contacts;
use App\Models\Country;
use App\Models\Keywords;
use App\Models\Notifications;
use App\Models\PhoneNumbers;
use App\Models\Reports;
use App\Models\SendingServer;
use App\Models\User;
use App\Repositories\Eloquent\EloquentCampaignRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use libphonenumber\PhoneNumberUtil;
use Twilio\TwiML\Messaging\Message;
use Twilio\TwiML\MessagingResponse;

class DLRController extends Controller
{

    /**
     * update dlr
     *
     * @param      $message_id
     * @param      $status
     * @param  null  $sender_id
     * @param  null  $phone
     *
     * @return mixed
     */
    public static function updateDLR($message_id, $status, $phone = null, $sender_id = null)
    {

        $get_data = Reports::query()->when($message_id, function ($query) use ($message_id) {
            $query->whereLike(['status'], $message_id);
        })->when($sender_id, function ($query) use ($sender_id) {
            $query->whereLike(['from'], $sender_id);
        })->when($phone, function ($query) use ($phone) {
            $query->whereLike(['to'], $phone);
        })->first();


        if ($get_data) {

            if ($get_data->campaign_id) {
                Campaigns::find($get_data->campaign_id)->updateCache();
            }

            $get_data->status = $status.'|'.$message_id;
            $get_data->save();
        }

        return $status;
    }


    /**
     *twilio dlr
     *
     * @param  Request  $request
     */
    public function dlrTwilio(Request $request)
    {
        $message_id = $request->MessageSid;
        $status     = $request->MessageStatus;

        if ($status == 'delivered' || $status == 'sent') {
            $status = 'Delivered';
        }

        $this::updateDLR($message_id, $status);

    }

    /**
     * Route mobile DLR
     *
     * @param  Request  $request
     */
    public function dlrRouteMobile(Request $request)
    {
        $message_id = $request->sMessageId;
        $status     = $request->sStatus;
        $sender_id  = $request->sSender;
        $phone      = $request->sMobileNo;

        if ($status == 'DELIVRD' || $status == 'ACCEPTED') {
            $status = 'Delivered';
        }

        $this::updateDLR($message_id, $status, $sender_id, $phone);
    }


    /**
     * text local DLR
     *
     * @param  Request  $request
     */
    public function dlrTextLocal(Request $request)
    {
        $message_id = $request->customID;
        $status     = $request->status;
        $phone      = $request->number;

        if ($status == 'D') {
            $status = 'Delivered';
        }

        $this::updateDLR($message_id, $status, null, $phone);
    }


    /**
     * Plivo DLR
     *
     * @param  Request  $request
     */
    public function dlrPlivo(Request $request)
    {
        $message_id = $request->MessageUUID;
        $status     = $request->Status;
        $phone      = $request->To;
        $sender_id  = $request->From;

        if ($status == 'delivered' || $status == 'sent') {
            $status = 'Delivered';
        }

        $this::updateDLR($message_id, $status, $phone, $sender_id);
    }

    /**
     * SMS Global DLR
     *
     * @param  Request  $request
     */
    public function dlrSMSGlobal(Request $request)
    {
        $message_id = $request->msgid;
        $status     = $request->dlrstatus;

        if ($status == 'DELIVRD') {
            $status = 'Delivered';
        }

        $this::updateDLR($message_id, $status);
    }


    /**
     * nexmo now Vonage DLR
     *
     * @param  Request  $request
     */
    public function dlrVonage(Request $request)
    {
        $message_id = $request->messageId;
        $status     = $request->status;
        $phone      = $request->msisdn;
        $sender_id  = $request->to;

        if ($status == 'delivered' || $status == 'accepted') {
            $status = 'Delivered';
        }

        $this::updateDLR($message_id, $status, $phone, $sender_id);
    }

    /**
     * infobip DLR
     *
     * @param  Request  $request
     */
    public function dlrInfobip(Request $request)
    {
        $get_data = $request->getContent();

        $get_data = json_decode($get_data, true);
        if (isset($get_data) && is_array($get_data) && array_key_exists('results', $get_data)) {
            $message_id = $get_data['results']['0']['messageId'];

            foreach ($get_data['results'] as $msg) {

                if (isset($msg['status']['groupName'])) {

                    $status = $msg['status']['groupName'];

                    if ($status == 'DELIVERED') {
                        $status = 'Delivered';
                    }

                    $this::updateDLR($message_id, $status);
                }

            }
        }
    }

    public function dlrEasySendSMS(Request $request)
    {
        $message_id = $request->messageid;
        $status     = $request->status;
        $phone      = $request->mobile;
        $sender_id  = $request->sender;

        if ($status == 'delivered') {
            $status = 'Delivered';
        }

        $this::updateDLR($message_id, $status, $phone, $sender_id);
    }


    /**
     * AfricasTalking delivery reports
     *
     * @param  Request  $request
     */
    public function dlrAfricasTalking(Request $request)
    {
        $message_id = $request->id;
        $status     = $request->status;
        $phone      = str_replace(['(', ')', '+', '-', ' '], '', $request->phoneNumber);

        if ($status == 'Success') {
            $status = 'Delivered';
        }

        $this::updateDLR($message_id, $status, $phone);
    }


    /**
     * 1s2u delivery reports
     *
     * @param  Request  $request
     */
    public function dlr1s2u(Request $request)
    {
        $message_id = $request->msgid;
        $status     = $request->status;
        $phone      = str_replace(['(', ')', '+', '-', ' '], '', $request->mno);
        $sender_id  = $request->sid;

        if ($status == 'DELIVRD') {
            $status = 'Delivered';
        }

        $this::updateDLR($message_id, $status, $phone, $sender_id);
    }


    /**
     * dlrKeccelSMS delivery reports
     *
     * @param  Request  $request
     */
    public function dlrKeccelSMS(Request $request)
    {
        $message_id = $request->messageID;
        $status     = $request->status;

        if ($status == 'DELIVERED') {
            $status = 'Delivered';
        }

        $this::updateDLR($message_id, $status);
    }

    /**
     * dlrGatewayApi delivery reports
     *
     * @param  Request  $request
     */
    public function dlrGatewayApi(Request $request)
    {

        $message_id = $request->id;
        $status     = $request->status;
        $phone      = str_replace(['(', ')', '+', '-', ' '], '', $request->msisdn);

        if ($status == 'DELIVRD') {
            $status = 'Delivered';
        }

        $this::updateDLR($message_id, $status, $phone);
    }


    /**
     * bulk sms delivery reports
     *
     * @param  Request  $request
     */
    public function dlrBulkSMS(Request $request)
    {

        logger($request->all());

    }


    /**
     * receive inbound message
     *
     * @param $to
     * @param $message
     * @param $sending_sever
     * @param $cost
     * @param  null  $from
     * @param  null  $media_url
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public static function inboundDLR($to, $message, $sending_sever, $cost, $from = null, $media_url = null)
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry!! This options is not available in demo mode',
            ]);
        }

        $to = str_replace(['(', ')', '+', '-', ' '], '', trim($to));

        if ($from != null) {
            $from = str_replace(['(', ')', '+', '-', ' '], '', trim($from));
        }

        $user_id = 1;
        $success = 'Success';
        $failed  = null;

        $phone_number = PhoneNumbers::where('number', $from)->where('status', 'assigned')->first();

        if ($media_url) {
            $sms_type = 'mms';
        } else {
            $sms_type = 'plain';
        }

        if ($phone_number) {

            $user_id = $phone_number->user_id;
            $user    = User::find($user_id);

            $sending_sever = SendingServer::where('settings', $sending_sever)->where('user_id', $user_id)->where('status', true)->first();

            if ($sending_sever) {

                Reports::create([
                        'user_id'           => $user_id,
                        'from'              => $from,
                        'to'                => $to,
                        'message'           => $message,
                        'sms_type'          => $sms_type,
                        'status'            => "Delivered",
                        'send_by'           => "to",
                        'cost'              => $cost,
                        'media_url'         => $media_url,
                        'sending_server_id' => $sending_sever->id,
                ]);

                if (isset($user->customer)) {

                    //checking chat box
                    $chatBox = ChatBox::where('to', $to)->where('from', $from)->where('user_id', $user_id)->first();

                    if ($chatBox) {
                        $chatBox->notification += 1;
                        $chatBox->save();
                    } else {
                        $chatBox = ChatBox::create([
                                'user_id'      => $user_id,
                                'from'         => $from,
                                'to'           => $to,
                                'notification' => 1,
                        ]);
                    }

                    if ($chatBox) {

                        Notifications::create([
                                'user_id'           => $user_id,
                                'notification_for'  => 'customer',
                                'notification_type' => 'chatbox',
                                'message'           => 'New chat message arrive',
                        ]);

                        ChatBoxMessage::create([
                                'box_id'            => $chatBox->id,
                                'message'           => $message,
                                'media_url'         => $media_url,
                                'sms_type'          => 'sms',
                                'send_by'           => 'to',
                                'sending_server_id' => $sending_sever->id,
                        ]);

                        $user = User::find($user_id);
                        event(new MessageReceived($user, $message, $chatBox));

                    } else {
                        $failed .= 'Failed to create chat message ';
                    }

                    //check keywords
                    $keyword = Keywords::where('user_id', $user_id)
                            ->select('*')
                            ->selectRaw('lower(keyword_name) as keyword,keyword_name')
                            ->where('keyword_name', strtolower($message))
                            ->where('status', 'assigned')->first();

                    if ($keyword) {

                        $phoneUtil         = PhoneNumberUtil::getInstance();
                        $phoneNumberObject = $phoneUtil->parse('+'.$to);
                        $country_code      = $phoneNumberObject->getCountryCode();

                        $country = Country::where('country_code', $country_code)->first();
                        if ( ! $country) {
                            $failed .= "Permission to send an SMS has not been enabled for the region indicated by the 'To' number: ".$to;
                        }

                        //checking contact message
                        $contact_groups = ContactGroups::where('customer_id', $user_id)->select('id')->cursor()->pluck('id')->toArray();
                        $optInContacts  = ContactGroupsOptinKeywords::whereIn('contact_group', $contact_groups)->where('keyword', $message)->cursor();
                        $optOutContacts = ContactGroupsOptoutKeywords::whereIn('contact_group', $contact_groups)->where('keyword', $message)->cursor();

                        $blacklist = Blacklists::where('user_id', $user_id)->where('number', $to)->first();


                        if ($optInContacts->count()) {
                            foreach ($optInContacts as $contact) {
                                $exist = Contacts::where('group_id', $contact->contact_group)->where('phone', $to)->first();

                                if ($blacklist) {
                                    $blacklist->delete();
                                }

                                if ( ! $exist) {
                                    $data = Contacts::create([
                                            'customer_id' => $user_id,
                                            'group_id'    => $contact->contact_group,
                                            'phone'       => $to,
                                            'first_name'  => null,
                                            'last_name'   => null,
                                    ]);

                                    if ($data) {

                                        $sendMessage = new EloquentCampaignRepository($campaign = new Campaigns());

                                        if ($contact->ContactGroups->send_keyword_message) {
                                            if ($keyword->reply_text) {

                                                $sendMessage->quickSend($campaign, [
                                                        'sender_id'      => $keyword->sender_id,
                                                        'sms_type'       => 'plain',
                                                        'message'        => $keyword->reply_text,
                                                        'recipient'      => $to,
                                                        'user_id'        => $user_id,
                                                        'sending_server' => $user->api_sending_server,
                                                        'country_code'   => $country->id,
                                                ]);

                                            }
                                        } else {
                                            if ($contact->ContactGroups->send_welcome_sms && $contact->ContactGroups->welcome_sms) {

                                                $sendMessage->quickSend($campaign, [
                                                        'sender_id'      => $contact->ContactGroups->sender_id,
                                                        'sms_type'       => 'plain',
                                                        'message'        => $contact->ContactGroups->welcome_sms,
                                                        'recipient'      => $to,
                                                        'user_id'        => $user_id,
                                                        'sending_server' => $user->api_sending_server,
                                                        'country_code'   => $country->id,
                                                ]);

                                            }
                                        }

                                        $contact->ContactGroups->updateCache('SubscribersCount');
                                    } else {
                                        $failed .= 'Failed to subscribe contact list';
                                    }
                                } else {
                                    $exist->update([
                                            'status' => 'subscribe',
                                    ]);
                                }

                            }
                        } elseif ($optOutContacts->count()) {

                            foreach ($optOutContacts as $contact) {

                                if ( ! $blacklist) {
                                    $exist = Contacts::where('group_id', $contact->contact_group)->where('phone', $to)->first();
                                    if ($exist) {
                                        $data = $exist->update([
                                                'status' => 'unsubscribe',
                                        ]);

                                        if ($data) {
                                            Blacklists::create([
                                                    'user_id' => $user_id,
                                                    'number'  => $to,
                                                    'reason'  => "Optout by User",
                                            ]);

                                            $sendMessage = new EloquentCampaignRepository($campaign = new Campaigns());

                                            if ($contact->ContactGroups->send_keyword_message) {
                                                if ($keyword->reply_text) {

                                                    $sendMessage->quickSend($campaign, [
                                                            'sender_id'      => $keyword->sender_id,
                                                            'sms_type'       => 'plain',
                                                            'message'        => $keyword->reply_text,
                                                            'recipient'      => $to,
                                                            'user_id'        => $user_id,
                                                            'sending_server' => $user->api_sending_server,
                                                            'country_code'   => $country->id,
                                                    ]);

                                                }
                                            } else {
                                                if ($contact->ContactGroups->unsubscribe_notification && $contact->ContactGroups->unsubscribe_sms) {

                                                    $sendMessage->quickSend($campaign, [
                                                            'sender_id'      => $contact->ContactGroups->sender_id,
                                                            'sms_type'       => 'plain',
                                                            'message'        => $contact->ContactGroups->unsubscribe_sms,
                                                            'recipient'      => $to,
                                                            'user_id'        => $user_id,
                                                            'sending_server' => $user->api_sending_server,
                                                            'country_code'   => $country->id,
                                                    ]);

                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {

                            if ($keyword->reply_text) {
                                $sendMessage = new EloquentCampaignRepository($campaign = new Campaigns());
                                $sendMessage->quickSend($campaign, [
                                        'sender_id'      => $keyword->sender_id,
                                        'sms_type'       => 'plain',
                                        'message'        => $keyword->reply_text,
                                        'recipient'      => $to,
                                        'user_id'        => $user_id,
                                        'sending_server' => $user->api_sending_server,
                                        'country_code'   => $country->id,
                                ]);

                            } else {
                                $failed .= 'Related keyword reply message not found.';
                            }
                        }
                    }
                }
            } else {
                Reports::create([
                        'user_id'           => $user_id,
                        'from'              => $from,
                        'to'                => $to,
                        'message'           => $message,
                        'media_url'         => $media_url,
                        'sms_type'          => $sms_type,
                        'status'            => "Delivered",
                        'send_by'           => "to",
                        'cost'              => $cost,
                        'sending_server_id' => null,
                ]);
            }

        } else {
            Reports::create([
                    'user_id'           => $user_id,
                    'from'              => $from,
                    'to'                => $to,
                    'message'           => $message,
                    'media_url'         => $media_url,
                    'sms_type'          => $sms_type,
                    'status'            => "Delivered",
                    'send_by'           => "to",
                    'cost'              => $cost,
                    'sending_server_id' => null,
            ]);
        }

        if (strtolower($message) == 'stop') {
            Blacklists::create([
                    'user_id' => $user_id,
                    'number'  => $to,
                    'reason'  => "Optout by User",
            ]);
        }


        if ($failed == null) {
            return $success;
        }

        return $failed;

    }


    /**
     * twilio inbound sms
     *
     * @param  Request  $request
     *
     * @return Message|MessagingResponse
     * @throws Exception
     */
    public function inboundTwilio(Request $request)
    {
        $to      = $request->input('From');
        $from    = $request->input('To');
        $message = $request->input('Body');

        if ($message == 'NULL') {
            $message = null;
        }

        $response = new MessagingResponse();

        if ($to == null || $from == null) {
            $response->message('From and To value required');

            return $response;
        }

        $feedback = 'Success';

        $NumMedia = (int) $request->input('NumMedia');
        if ($NumMedia > 0) {
            $cost = 1;
            for ($i = 0; $i < $NumMedia; $i++) {
                $mediaUrl = $request->input("MediaUrl$i");
                $feedback = $this::inboundDLR($to, $message, 'Twilio', $cost, $from, $mediaUrl);
            }
        } else {
            $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
            $cost          = ceil($message_count);

            $feedback = $this::inboundDLR($to, $message, 'Twilio', $cost, $from);
        }


        if ($feedback == 'Success') {
            return $response;
        }

        return $response->message($feedback);
    }

    /**
     * TwilioCopilot inbound sms
     *
     * @param  Request  $request
     *
     * @return Message|MessagingResponse
     * @throws Exception
     */
    public function inboundTwilioCopilot(Request $request)
    {
        $to      = $request->input('From');
        $from    = $request->input('To');
        $message = $request->input('Body');

        if ($message == 'NULL') {
            $message = null;
        }

        $response = new MessagingResponse();

        if ($to == null || $from == null) {
            $response->message('From and To value required');

            return $response;
        }

        $feedback = 'Success';

        $NumMedia = (int) $request->input('NumMedia');
        if ($NumMedia > 0) {
            $cost = 1;
            for ($i = 0; $i < $NumMedia; $i++) {
                $mediaUrl = $request->input("MediaUrl$i");
                $feedback = $this::inboundDLR($to, $message, 'Twilio', $cost, $from, $mediaUrl);
            }
        } else {
            $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
            $cost          = ceil($message_count);

            $feedback = $this::inboundDLR($to, $message, 'TwilioCopilot', $cost, $from);
        }


        if ($feedback == 'Success') {
            return $response;
        }

        return $response->message($feedback);
    }

    /**
     * text local inbound sms
     *
     * @param  Request  $request
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundTextLocal(Request $request)
    {
        $to      = $request->input('sender');
        $from    = $request->input('inNumber');
        $message = $request->input('content');

        if ($to == null || $from == null || $message == null) {
            return 'Sender, inNumber and content value required';
        }

        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
        $cost          = ceil($message_count);

        return $this::inboundDLR($to, $message, 'TextLocal', $cost, $from);
    }


    /**
     * inbound plivo messages
     *
     * @param  Request  $request
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundPlivo(Request $request)
    {
        $to      = $request->input('From');
        $from    = $request->input('To');
        $message = $request->input('Text');

        if ($to == null || $message == null) {
            return 'Destination number and message value required';
        }

        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
        $cost          = ceil($message_count);

        return $this::inboundDLR($to, $message, 'Plivo', $cost, $from);
    }


    /**
     * inbound bulk sms messages
     *
     * @param  Request  $request
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundBulkSMS(Request $request)
    {
        $to      = $request->input('msisdn');
        $from    = $request->input('sender');
        $message = $request->input('message');

        if ($to == null || $message == null) {
            return 'Destination number and message value required';
        }

        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
        $cost          = ceil($message_count);

        return $this::inboundDLR($to, $message, 'BulkSMS', $cost, $from);
    }

    /**
     * inbound Vonage messages
     *
     * @param  Request  $request
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundVonage(Request $request)
    {
        $to      = $request->input('msisdn');
        $from    = $request->input('to');
        $message = $request->input('text');

        if ($to == null || $message == null) {
            return 'Destination number, Source number and message value required';
        }

        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
        $cost          = ceil($message_count);

        return $this::inboundDLR($to, $message, 'Vonage', $cost, $from);
    }

    /**
     * inbound messagebird messages
     *
     * @param  Request  $request
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundMessagebird(Request $request)
    {

        $to      = $request->input('originator');
        $from    = $request->input('recipient');
        $message = $request->input('body');

        if ($to == null || $message == null) {
            return 'Destination number, Source number and message value required';
        }

        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
        $cost          = ceil($message_count);

        return $this::inboundDLR($to, $message, 'MessageBird', $cost, $from);
    }

    /**
     * inbound signalwire messages
     *
     * @param  Request  $request
     *
     * @return Message|MessagingResponse
     * @throws Exception
     */
    public function inboundSignalwire(Request $request)
    {

        $response = new MessagingResponse();

        $to      = $request->input('From');
        $from    = $request->input('To');
        $message = $request->input('Body');

        if ($to == null || $from == null || $message == null) {
            $response->message('From, To and Body value required');

            return $response;
        }

        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
        $cost          = ceil($message_count);

        $feedback = $this::inboundDLR($to, $message, 'SignalWire', $cost, $from);

        if ($feedback == 'Success') {
            return $response;
        }

        return $response->message($feedback);
    }


    /**
     * inbound telnyx messages
     *
     * @param  Request  $request
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundTelnyx(Request $request)
    {

        $get_data = $request->getContent();

        $get_data = json_decode($get_data, true);

        if (isset($get_data) && is_array($get_data) && array_key_exists('data', $get_data) && array_key_exists('payload', $get_data['data'])) {

            $to      = $get_data['data']['payload']['from']['phone_number'];
            $from    = $get_data['data']['payload']['to'];
            $message = $get_data['data']['payload']['text'];

            if ($to == '' || $message == '' || $from == '') {
                return 'Destination or Sender number and message value required';
            }

            $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
            $cost          = ceil($message_count);

            return $this::inboundDLR($to, $message, 'Telnyx', $cost, $from);
        }

        return 'Invalid request';
    }


    /**
     * inbound Teletopiasms messages
     *
     * @param  Request  $request
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundTeletopiasms(Request $request)
    {

        $to      = $request->input('sender');
        $from    = $request->input('recipient');
        $message = $request->input('text');

        if ($to == null || $message == null) {
            return 'Destination number, Source number and message value required';
        }

        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
        $cost          = ceil($message_count);

        return $this::inboundDLR($to, $message, 'Teletopiasms', $cost, $from);
    }


    /**
     * receive FlowRoute message
     *
     * @param  Request  $request
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundFlowRoute(Request $request)
    {

        $data = json_decode($request->getContent(), true);

        if (isset($data) && is_array($data) && array_key_exists('data', $data)) {

            $to      = $data['data']['attributes']['from'];
            $from    = $data['data']['attributes']['to'];
            $message = $data['data']['attributes']['body'];

            if ($from == '' || $message == '' || $to == '') {
                return 'From, To and Body value required';
            }

            $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
            $cost          = ceil($message_count);

            return $this::inboundDLR($to, $message, 'FlowRoute', $cost, $from);
        }

        return 'valid data not found';
    }

    /**
     * receive inboundEasySendSMS message
     *
     * @param  Request  $request
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundEasySendSMS(Request $request)
    {

        $to      = $request->input('From');
        $from    = null;
        $message = $request->input('message');

        if ($message == '' || $to == '') {
            return 'To and Message value required';
        }

        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
        $cost          = ceil($message_count);

        return $this::inboundDLR($to, $message, 'FlowRoute', $cost, $from);
    }


    /**
     * receive Skyetel message
     *
     * @param  Request  $request
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundSkyetel(Request $request)
    {

        $to      = $request->input('from');
        $from    = $request->input('to');
        $message = $request->input('text');

        if ($to == '' || $from == '') {
            return 'To and From value required';
        }


        if (isset($request->media) && is_array($request->media) && array_key_exists('1', $request->media)) {

            $mediaUrl = $request->media[1];

            return $this::inboundDLR($to, $message, 'Skyetel', 1, $from, $mediaUrl);
        } else {

            $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
            $cost          = ceil($message_count);

            return $this::inboundDLR($to, $message, 'Skyetel', $cost, $from);
        }


    }

    /**
     * receive chat-api message
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundChatApi()
    {

        $data = json_decode(file_get_contents('php://input'), true);

        foreach ($data['messages'] as $message) {

            $to      = $message['author'];
            $from    = $message['senderName'];
            $message = $message['body'];

            if ($message == '' || $to == '' || $from == '') {
                return 'Author, Sender Name and Body value required';
            }

            $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
            $cost          = ceil($message_count);

            return $this::inboundDLR($to, $message, 'WhatsAppChatApi', $cost, $from);
        }

        return true;
    }

    /**
     * callr delivery reports
     *
     * @param  Request  $request
     */
    public function dlrCallr(Request $request)
    {

        $get_data = json_decode($request->getContent(), true);

        $message_id = $get_data['data']['user_data'];
        $status     = $get_data['data']['status'];

        if ($status == 'RECEIVED' || $status == 'SENT') {
            $status = 'Delivered|'.$message_id;
        }

        $this::updateDLR($message_id, $status);
    }


    /**
     * receive callr message
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundCallr(Request $request)
    {

        $get_data = json_decode($request->getContent(), true);

        $to      = str_replace('+', '', $get_data['data']['from']);
        $from    = str_replace('+', '', $get_data['data']['to']);
        $message = $get_data['data']['text'];

        if ($message == '' || $to == '' || $from == '') {
            return 'From, To and Text value required';
        }

        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
        $cost          = ceil($message_count);

        return $this::inboundDLR($to, $message, 'Callr', $cost, $from);
    }


    /**
     * cm com delivery reports
     *
     * @param  Request  $request
     */
    public function dlrCM(Request $request)
    {

        $get_data = json_decode($request->getContent(), true);

        $message_id = $get_data['messages']['msg']['reference'];
        $status     = $get_data['messages']['msg']['status']['errorCode'];

        if ($status == 'delivered') {
            $status = 'Delivered|'.$message_id;
        }

        $this::updateDLR($message_id, $status);
    }


    /**
     * receive cm com message
     *
     * @return JsonResponse|string
     * @throws Exception
     */
    public function inboundCM(Request $request)
    {

        $get_data = json_decode($request->getContent(), true);

        $to      = str_replace('+', '', $get_data['from']['number']);
        $from    = str_replace('+', '', $get_data['to']['number']);
        $message = $get_data['message']['text'];

        if ($message == '' || $to == '' || $from == '') {
            return 'From, To and Text value required';
        }

        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
        $cost          = ceil($message_count);

        return $this::inboundDLR($to, $message, 'CMCOM', $cost, $from);
    }


    /**
     * receive bandwidth message
     *
     * @param  Request  $request
     *
     * @return false|JsonResponse|resource|string|null
     * @throws Exception
     */
    public function inboundBandwidth(Request $request)
    {

        $data = $request->all();

        if (isset($data) && is_array($data) && count($data) > 0) {
            if ($data['0']['type'] == 'message-received') {
                if (isset($data[0]['message']) && is_array($data[0]['message'])) {
                    $to      = $data[0]['message']['from'];
                    $from    = $data[0]['to'];
                    $message = $data[0]['message']['text'];


                    if ($message == '' || $to == '' || $from == '') {
                        return 'From, To and Text value required';
                    }

                    $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
                    $cost          = ceil($message_count);

                    return $this::inboundDLR($to, $message, 'Bandwidth', $cost, $from);
                } else {
                    return $request->getContent();
                }
            } else {
                return $request->getContent();
            }
        } else {
            return $request->getContent();
        }

    }


    /**
     * receive Solucoesdigitais message
     *
     * @param  Request  $request
     *
     * @return bool|false
     * @throws Exception
     */
    public function inboundSolucoesdigitais(Request $request): bool
    {
        logger($request->all());

        return true;
    }


    /**
     * receive inboundGatewayApi message
     *
     * @param  Request  $request
     *
     * @return bool|false
     * @throws Exception
     */
    public function inboundGatewayApi(Request $request): bool
    {

        $to      = $request->input('msisdn');
        $from    = $request->input('receiver');
        $message = $request->input('message');

        if ($message == '' || $to == '') {
            return 'To and Message value required';
        }

        $message_count = strlen(preg_replace('/\s+/', ' ', trim($message))) / 160;
        $cost          = ceil($message_count);

        return $this::inboundDLR($to, $message, 'Gatewayapi', $cost, $from);
    }

}
