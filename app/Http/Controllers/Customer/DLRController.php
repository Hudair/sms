<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Blacklists;
use App\Models\Campaigns;
use App\Models\ChatBox;
use App\Models\ChatBoxMessage;
use App\Models\ContactGroups;
use App\Models\ContactGroupsOptinKeywords;
use App\Models\ContactGroupsOptoutKeywords;
use App\Models\Contacts;
use App\Models\Keywords;
use App\Models\Notifications;
use App\Models\PhoneNumbers;
use App\Models\Reports;
use App\Models\SendingServer;
use App\Models\User;
use App\Repositories\Eloquent\EloquentCampaignRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    public static function updateDLR($message_id, $status, $sender_id = null, $phone = null)
    {

        $get_data = Reports::query()->when($message_id, function ($query) use ($message_id) {
            $query->whereLike(['status'], '%'.$message_id.'%');
        })->when($sender_id, function ($query) use ($sender_id) {
            $query->whereLike(['from'], '%'.$sender_id.'%');
        })->when($phone, function ($query) use ($phone) {
            $query->whereLike(['to'], '%'.$phone.'%');
        })->first();

        if ($get_data) {
            $get_data->status = $status;
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
     * receive inbound message
     *
     * @param $to
     * @param $message
     * @param $sending_sever
     * @param $cost
     * @param  null  $from
     *
     * @return JsonResponse|string
     */
    public static function inboundDLR($to, $message, $sending_sever, $cost, $from = null)
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

        $sending_sever = SendingServer::where('settings', $sending_sever)->where('status', true)->first();
        $phone_number  = PhoneNumbers::where('number', $from)->where('status', 'assigned')->first();

        if ($phone_number) {
            $user_id = $phone_number->user_id;
            $user    = User::find($user_id);

            if (isset($user->customer)) {
                $unit_price = $user->customer->getOption('plain_sms');
                $price      = $cost * $unit_price;

                if ($price != 0) {
                    $user->customer->countUsage($price);
                }

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
                            'sms_type'          => 'sms',
                            'send_by'           => 'to',
                            'sending_server_id' => $sending_sever->id,
                    ]);
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
                                                    'sender_id' => $keyword->sender_id,
                                                    'sms_type'  => 'plain',
                                                    'message'   => $keyword->reply_text,
                                                    'recipient' => $to,
                                                    'user_id'   => $user_id,
                                            ]);

                                        }
                                    } else {
                                        if ($contact->ContactGroups->send_welcome_sms && $contact->ContactGroups->welcome_sms) {

                                            $sendMessage->quickSend($campaign, [
                                                    'sender_id' => $contact->ContactGroups->sender_id,
                                                    'sms_type'  => 'plain',
                                                    'message'   => $contact->ContactGroups->welcome_sms,
                                                    'recipient' => $to,
                                                    'user_id'   => $user_id,
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
                                                        'sender_id' => $keyword->sender_id,
                                                        'sms_type'  => 'plain',
                                                        'message'   => $keyword->reply_text,
                                                        'recipient' => $to,
                                                        'user_id'   => $user_id,
                                                ]);

                                            }
                                        } else {
                                            if ($contact->ContactGroups->unsubscribe_notification && $contact->ContactGroups->unsubscribe_sms) {

                                                $sendMessage->quickSend($campaign, [
                                                        'sender_id' => $contact->ContactGroups->sender_id,
                                                        'sms_type'  => 'plain',
                                                        'message'   => $contact->ContactGroups->unsubscribe_sms,
                                                        'recipient' => $to,
                                                        'user_id'   => $user_id,
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
                                    'sender_id' => $keyword->sender_id,
                                    'sms_type'  => 'plain',
                                    'message'   => $keyword->reply_text,
                                    'recipient' => $to,
                                    'user_id'   => $user_id,
                            ]);

                        } else {
                            $failed .= 'Related keyword reply message not found.';
                        }
                    }
                }
            }
        }

        Reports::create([
                'user_id'           => $user_id,
                'from'              => $from,
                'to'                => $to,
                'message'           => $message,
                'sms_type'          => 'plain',
                'status'            => "Delivered",
                'send_by'           => "to",
                'cost'              => $cost,
                'sending_server_id' => $sending_sever->id,
        ]);


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
     */
    public function inboundTwilio(Request $request)
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

        $feedback = $this::inboundDLR($to, $message, 'Twilio', $cost, $from);

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
     */
    public function inboundTelnyx(Request $request)
    {

        $get_data = $request->getContent();

        $get_data = json_decode($get_data, true);

        if (isset($get_data) && is_array($get_data) && array_key_exists('data', $get_data) && array_key_exists('payload', $get_data['data'])) {

            $to      = $get_data['data']['payload']['from']['phone_number'];
            $from    = $get_data['data']['payload']['to'][0]['phone_number'];
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


}
