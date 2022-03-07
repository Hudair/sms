<?php

namespace App\Models;

use App\Library\SmsBuilder;
use App\Library\Tool;
use Aws\Sns\Exception\SnsException;
use Aws\Sns\SnsClient;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Model;
use Plivo\Exceptions\PlivoResponseException;
use Plivo\RestClient;
use Psr\Http\Client\ClientExceptionInterface;
use smpp\SMPP;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class SendCampaignSMS extends Model
{

    /**
     * make normal message to unicode message
     *
     * @param $message
     *
     * @return string
     */
    private function sms_unicode($message): string
    {
        $hex1 = '';
        if (function_exists('iconv')) {
            $latin = @iconv('UTF−8', 'ISO−8859−1', $message);
            if (strcmp($latin, $message)) {
                $arr  = unpack('H*hex', @iconv('UTF-8', 'UCS-2BE', $message));
                $hex1 = strtoupper($arr['hex']);
            }
            if ($hex1 == '') {
                $hex2 = '';
                for ($i = 0; $i < strlen($message); $i++) {
                    $hex = dechex(ord($message[$i]));
                    $len = strlen($hex);
                    $add = 4 - $len;
                    if ($len < 4) {
                        for ($j = 0; $j < $add; $j++) {
                            $hex = "0".$hex;
                        }
                    }
                    $hex2 .= $hex;
                }

                return $hex2;
            } else {
                return $hex1;
            }
        } else {
            return 'failed';
        }
    }


    /**
     *
     * send plain message
     *
     * @param $data
     *
     * @return array|Application|Translator|string|null
     */
    public function sendPlainSMS($data)
    {
        $phone          = $data['phone'];
        $sending_server = $data['sending_server'];
        $gateway_name   = $data['sending_server']->settings;
        $message        = null;
        $sms_type       = $data['sms_type'];
        $get_sms_status = $data['status'];

        if (isset($data['message'])) {
            $message = $data['message'];
        }

        if ($get_sms_status == null) {
            if ($sending_server->custom && $sending_server->type == 'http') {
                $cg_info = $sending_server->customSendingServer;

                $send_custom_data = [];


                $username_param = $cg_info->username_param;
                $username_value = $cg_info->username_value;
                $password_value = null;

                if ($cg_info->authorization == 'no_auth') {
                    $send_custom_data[$username_param] = $username_value;
                }

                if ($cg_info->password_status) {
                    $password_param = $cg_info->password_param;
                    $password_value = $cg_info->password_value;

                    if ($cg_info->authorization == 'no_auth') {
                        $send_custom_data[$password_param] = $password_value;
                    }
                }

                if ($cg_info->action_status) {
                    $action_param = $cg_info->action_param;
                    $action_value = $cg_info->action_value;

                    $send_custom_data[$action_param] = $action_value;
                }

                if ($cg_info->source_status) {
                    $source_param = $cg_info->source_param;
                    $source_value = $cg_info->source_value;

                    if ($data['sender_id'] != null || $data['sender_id'] != '') {
                        $send_custom_data[$source_param] = $data['sender_id'];
                    } else {
                        $send_custom_data[$source_param] = $source_value;
                    }
                }

                $destination_param                    = $cg_info->destination_param;
                $send_custom_data[$destination_param] = $data['phone'];

                $message_param                    = $cg_info->message_param;
                $send_custom_data[$message_param] = $data['message'];

                if ($cg_info->unicode_status && $data['sms_type'] == 'unicode') {
                    $unicode_param                    = $cg_info->unicode_param;
                    $unicode_value                    = $cg_info->unicode_value;
                    $send_custom_data[$unicode_param] = $unicode_value;
                }

                if ($cg_info->route_status) {
                    $route_param = $cg_info->route_param;
                    $route_value = $cg_info->route_value;

                    $send_custom_data[$route_param] = $route_value;
                }

                if ($cg_info->language_status) {
                    $language_param = $cg_info->language_param;
                    $language_value = $cg_info->language_value;

                    $send_custom_data[$language_param] = $language_value;
                }

                if ($cg_info->custom_one_status) {
                    $custom_one_param = $cg_info->custom_one_param;
                    $custom_one_value = $cg_info->custom_one_value;

                    $send_custom_data[$custom_one_param] = $custom_one_value;
                }

                if ($cg_info->custom_two_status) {
                    $custom_two_param = $cg_info->custom_two_param;
                    $custom_two_value = $cg_info->custom_two_value;

                    $send_custom_data[$custom_two_param] = $custom_two_value;
                }

                if ($cg_info->custom_three_status) {
                    $custom_three_param = $cg_info->custom_three_param;
                    $custom_three_value = $cg_info->custom_three_value;

                    $send_custom_data[$custom_three_param] = $custom_three_value;
                }

                //if json encoded then encode custom data json_encode($send_custom_data) otherwise do http_build_query
                if ($cg_info->json_encoded_post) {
                    $parameters = json_encode($send_custom_data);
                } else {
                    $parameters = http_build_query($send_custom_data);
                }

                $ch = curl_init();

                //if http method get
                if ($cg_info->http_request_method == 'get') {
                    $gateway_url = $sending_server->api_link.'?'.$parameters;

                    curl_setopt($ch, CURLOPT_URL, $gateway_url);
                    curl_setopt($ch, CURLOPT_HTTPGET, 1);
                } else {

                    //if http method post
                    $gateway_url = $sending_server->api_link;

                    curl_setopt($ch, CURLOPT_URL, $gateway_url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                }

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // if ssl verify ignore set yes then add these two values in curl  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                if ($cg_info->ssl_certificate_verification) {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                }
                $headers = [];
                //if content type value not none then insert content type in curl headers. $headers[] = "Content-Type: application/x-www-form-urlencoded";
                if ($cg_info->content_type != 'none') {
                    $headers[] = "Content-Type: ".$cg_info->content_type;
                }

                //if content type accept value not none then insert content type accept in curl headers. $headers[] = "Accept: application/json";
                if ($cg_info->content_type_accept != 'none') {
                    $headers[] = "Accept: ".$cg_info->content_type_accept;
                }

                //if content encoding value not none then insert content type accept in curl headers. $headers[] = "charset=utf-8";
                if ($cg_info->character_encoding != 'none') {
                    $headers[] = "charset=".$cg_info->character_encoding;
                }
                // if authorization set Bearer then add this line on curl header $header[] = "Authorization: Bearer ".$gateway_user_name;

                if ($cg_info->authorization == 'bearer_token') {
                    $headers[] = "Authorization: Bearer ".$username_value;
                }

                // if authorization set basic auth then add this line on curl header $header[] = "Authorization: Basic ".base64_encode("$gateway_user_name:$gateway_password");

                if ($cg_info->authorization == 'basic_auth') {
                    $headers[] = "Authorization: Basic ".base64_encode("$username_value:$password_value");
                }

                if (count($headers)) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                }

                $get_sms_status = curl_exec($ch);

                if (curl_errno($ch)) {
                    $get_sms_status = curl_error($ch);
                } else {
                    if (substr_count(strtolower($get_sms_status), strtolower($sending_server->success_keyword)) == 1) {
                        $get_sms_status = 'Delivered';
                    }
                }
                curl_close($ch);
            } elseif ($sending_server->type == 'smpp') {

                $sender_id = $data['sender_id'];
                $phone     = $data['phone'];
                $message   = $data['message'];

                if ($sending_server->source_addr_ton != 5) {
                    $source_ton = $sending_server->source_addr_ton;
                } elseif (ctype_digit($sender_id) && strlen($sender_id) <= 8) {
                    $source_ton = SMPP::TON_NETWORKSPECIFIC;
                } elseif (ctype_digit($sender_id) && (strlen($sender_id) <= 15 && strlen($sender_id) >= 10)) {
                    $source_ton = SMPP::TON_INTERNATIONAL;
                } else {
                    $source_ton = SMPP::TON_ALPHANUMERIC;
                }

                if ($sending_server->dest_addr_ton != 1) {
                    $destination_ton = $sending_server->dest_addr_ton;
                } else {
                    $destination_ton = SMPP::TON_INTERNATIONAL;
                }

                try {
                    $output = (new SmsBuilder($sending_server->api_link, $sending_server->port, $sending_server->username, $sending_server->password, 10000))
                            ->setSender($data['sender_id'], $source_ton)
                            ->setRecipient($phone, $destination_ton)
                            ->sendMessage($message);

                    if ($output) {
                        $get_sms_status = 'Delivered';
                    } else {
                        $get_sms_status = __('locale.labels.failed');
                    }
                } catch (Exception $e) {
                    $get_sms_status = $e->getMessage();
                }

            } else {

                $gateway_url = $sending_server->api_link;

                switch ($gateway_name) {

                    case 'Twilio':

                        try {
                            $client       = new Client($sending_server->account_sid, $sending_server->auth_token);
                            $get_response = $client->messages->create($phone, [
                                    'from'           => $data['sender_id'],
                                    'body'           => $message,
                                    'statusCallback' => route('dlr.twilio'),
                            ]);

                            if ($get_response->status == 'queued' || $get_response->status == 'accepted') {
                                $get_sms_status = 'Delivered|'.$get_response->sid;
                            } else {
                                $get_sms_status = $get_response->status.'|'.$get_response->sid;
                            }

                        } catch (ConfigurationException | TwilioException $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'TwilioCopilot':

                        try {
                            $client       = new Client($sending_server->account_sid, $sending_server->auth_token);
                            $get_response = $client->messages->create($phone, [
                                    'messagingServiceSid' => $data['sender_id'],
                                    'body'                => $message,
                            ]);

                            if ($get_response->status == 'queued' || $get_response->status == 'accepted') {
                                $get_sms_status = 'Delivered|'.$get_response->sid;
                            } else {
                                $get_sms_status = $get_response->status.'|'.$get_response->sid;
                            }

                        } catch (ConfigurationException | TwilioException $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'ClickatellTouch':

                        $parameters = [
                                'api_key' => $sending_server->api_key,
                                'to'      => $phone,
                                'content' => $message,
                        ];
                        if (isset($data['sender_id'])) {
                            $parameters['from'] = $data['sender_id'];
                        }

                        $sending_url = $gateway_url.'?'.http_build_query($parameters);

                        try {

                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, $sending_url);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {
                                $get_result = json_decode($response);

                                if (isset($get_result->messages[0]->accepted) && $get_result->messages[0]->accepted) {
                                    $get_sms_status = 'Delivered|'.$get_result->messages[0]->apiMessageId;
                                } elseif (isset($get_result->messages[0]->errorDescription) && $get_result->messages[0]->errorDescription != '') {
                                    $get_sms_status = $get_result->messages[0]->errorDescription;
                                } elseif (isset($get_result->errorDescription) && $get_result->errorDescription != '') {
                                    $get_sms_status = $get_result->errorDescription;
                                } else {
                                    $get_sms_status = 'Invalid request';
                                }
                            }

                            curl_close($ch);

                        } catch (Exception $exception) {
                            $get_sms_status = $exception->getMessage();
                        }
                        break;

                    case 'ClickatellCentral':

                        $parameters = [
                                'user'     => $sending_server->username,
                                'password' => $sending_server->password,
                                'api_id'   => $sending_server->api_key,
                                'to'       => $phone,
                                'text'     => $message,
                        ];

                        if ($sms_type == 'unicode') {
                            $parameters['unicode'] = 1;
                        } else {
                            $parameters['unicode'] = 0;
                        }

                        $sending_url = $gateway_url.'?'.http_build_query($parameters);

                        try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $sending_url);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $get_sms_status = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {
                                if (substr_count($get_sms_status, 'ID:') == 1) {
                                    $get_sms_status = 'Delivered';
                                }
                            }
                            curl_close($ch);
                        } catch (Exception $exception) {
                            $get_sms_status = $exception->getMessage();
                        }
                        break;

                    case 'RouteMobile':
                        $parameters = [
                                'username'    => $sending_server->username,
                                'password'    => $sending_server->password,
                                'source'      => $data['sender_id'],
                                'destination' => $phone,
                                'message'     => $message,
                                'dlr'         => 1,
                        ];

                        if ($sms_type == 'unicode') {
                            $parameters['type'] = 2;
                        } else {
                            $parameters['type'] = 0;
                        }

                        $sending_url = $gateway_url.'?'.http_build_query($parameters);

                        try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $sending_url);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $get_sms_status = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {

                                $get_data = explode('|', $get_sms_status);

                                if (is_array($get_data) && array_key_exists('0', $get_data)) {
                                    switch ($get_data[0]) {
                                        case '1701':
                                            $get_sms_status = 'Delivered|'.$get_data['2'];
                                            break;

                                        case '1702':
                                            $get_sms_status = 'Invalid URL';
                                            break;

                                        case '1703':
                                            $get_sms_status = 'Invalid User or Password';
                                            break;

                                        case '1704':
                                            $get_sms_status = 'Invalid Type';
                                            break;

                                        case '1705':
                                            $get_sms_status = 'Invalid SMS';
                                            break;

                                        case '1706':
                                            $get_sms_status = 'Invalid receiver';
                                            break;

                                        case '1707':
                                            $get_sms_status = 'Invalid sender';
                                            break;

                                        case '1709':
                                            $get_sms_status = 'User Validation Failed';
                                            break;

                                        case '1710':
                                            $get_sms_status = 'Internal Error';
                                            break;

                                        case '1715':
                                            $get_sms_status = 'Response Timeout';
                                            break;

                                        case '1025':
                                            $get_sms_status = 'Insufficient Credit';
                                            break;

                                        default:
                                            $get_sms_status = 'Invalid request';
                                            break;

                                    }
                                } else {
                                    $get_sms_status = 'Invalid request';
                                }
                            }
                            curl_close($ch);
                        } catch (Exception $exception) {
                            $get_sms_status = $exception->getMessage();
                        }
                        break;

                    case 'TextLocal':

                        $unique_id = time();

                        $parameters = [
                                'apikey'      => $sending_server->api_key,
                                'numbers'     => $phone,
                                'sender'      => $data['sender_id'],
                                'receipt_url' => route('dlr.textlocal'),
                                'custom'      => $unique_id,
                        ];

                        if ($sms_type == 'unicode') {
                            $parameters['unicode'] = true;
                            $message               = $this->sms_unicode($message);
                        }

                        $parameters['message'] = $message;

                        try {
                            $ch = curl_init($gateway_url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($ch);
                            $err      = curl_error($ch);
                            curl_close($ch);

                            if ($err) {
                                $get_sms_status = $err;
                            } else {
                                $get_data = json_decode($response, true);

                                if (isset($get_data) && is_array($get_data) && array_key_exists('status', $get_data)) {
                                    if ($get_data['status'] == 'failure') {
                                        foreach ($get_data['errors'] as $err) {
                                            $get_sms_status = $err['message'];
                                        }
                                    } elseif ($get_data['status'] == 'success') {
                                        $get_sms_status = 'Delivered|'.$unique_id;
                                    } else {
                                        $get_sms_status = $response;
                                    }
                                } else {
                                    $get_sms_status = $response;
                                }
                            }
                        } catch (Exception $exception) {
                            $get_sms_status = $exception->getMessage();
                        }
                        break;

                    case 'Plivo':

                        $client = new RestClient($sending_server->auth_id, $sending_server->auth_token);
                        try {
                            $response = $client->messages->create(
                                    $data['sender_id'],
                                    [$phone],
                                    $message,
                                    ['url' => route('dlr.plivo')],
                            );

                            $get_sms_status = 'Delivered|'.$response->getmessageUuid(0)[0];

                        } catch (PlivoResponseException $e) {
                            $get_sms_status = $e->getMessage();
                        }

                        break;

                    case 'PlivoPowerpack':

                        $client = new RestClient($sending_server->auth_id, $sending_server->auth_token);
                        try {
                            $response = $client->messages->create(
                                    null,
                                    [$phone],
                                    $message,
                                    ['url' => route('dlr.plivo')],
                                    $data['sender_id']
                            );

                            $get_sms_status = 'Delivered|'.$response->getmessageUuid(0);

                        } catch (PlivoResponseException $e) {
                            $get_sms_status = $e->getMessage();
                        }

                        break;

                    case 'SMSGlobal':

                        $parameters = [
                                'action'   => 'sendsms',
                                'user'     => $sending_server->username,
                                'password' => $sending_server->password,
                                'from'     => $data['sender_id'],
                                'to'       => $phone,
                                'text'     => $message,
                        ];

                        if (strlen($message) > 160) {
                            $parameters['maxsplit'] = 9;
                        }

                        $sending_url = $gateway_url.'?'.http_build_query($parameters);

                        try {

                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, $sending_url);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $get_sms_status = curl_exec($ch);
                            curl_close($ch);

                            if (substr_count($get_sms_status, 'OK') == 1) {
                                $get_sms_status = explode(':', $get_sms_status);
                                if (isset($get_sms_status) && is_array($get_sms_status) && array_key_exists('3', $get_sms_status)) {
                                    $get_sms_status = 'Delivered|'.trim($get_sms_status['3']);
                                } else {
                                    $get_sms_status = 'Delivered';
                                }
                            } else {
                                $get_sms_status = str_replace('ERROR:', '', $get_sms_status);
                            }
                        } catch (Exception $exception) {
                            $get_sms_status = $exception->getMessage();
                        }
                        break;

                    case 'BulkSMS':

                        $parameters = [
                                'auto-unicode'        => true,
                                'longMessageMaxParts' => 3,
                                'to'                  => $phone,
                                'body'                => $message,
                        ];

                        if (isset($data['sender_id'])) {
                            $parameters['from'] = $data['sender_id'];
                        }

                        try {
                            $ch      = curl_init();
                            $headers = [
                                    'Content-Type:application/json',
                                    'Authorization:Basic '.base64_encode("$sending_server->username:$sending_server->password"),
                            ];
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                            $response = curl_exec($ch);
                            curl_close($ch);

                            $get_data = json_decode($response, true);

                            if (isset($get_data) && is_array($get_data)) {
                                if (array_key_exists('id', $get_data)) {
                                    $get_sms_status = 'Delivered|'.$get_data['id'];
                                } elseif (array_key_exists('detail', $get_data)) {
                                    $get_sms_status = $get_data['detail'];
                                }
                            } else {
                                $get_sms_status = $response;
                            }
                        } catch (Exception $exception) {
                            $get_sms_status = $exception->getMessage();
                        }
                        break;

                    case 'Vonage':

                        $client = new \Vonage\Client(new Basic($sending_server->api_key, $sending_server->api_secret));
                        $text   = new SMS($phone, $data['sender_id'], $message);

                        try {
                            $response = $client->sms()->send($text);
                            $output   = $response->current();

                            if ($output->getStatus() == 0 || $output->getStatus() == 'sent') {
                                $get_sms_status = 'Delivered|'.$output->getMessageId();
                            } else {
                                $get_sms_status = $output->getStatus();
                            }

                        } catch (ClientExceptionInterface | \Vonage\Client\Exception\Exception $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'Infobip':
                        $destination = [
                                'messageId' => time(),
                                'to'        => $phone,
                        ];

                        $parameters = [
                                'messages' => [
                                        "from"              => $data['sender_id'],
                                        "destinations"      => [$destination],
                                        'text'              => $message,
                                        'notifyUrl'         => route('dlr.infobip'),
                                        'notifyContentType' => 'application/json',
                                ],
                        ];

                        try {

                            $ch     = curl_init();
                            $header = [
                                    "Authorization: App $sending_server->api_key",
                                    "Content-Type: application/json",
                                    "Accept: application/json",
                            ];

                            // setting options
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                            curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

                            // response of the POST request
                            $response = curl_exec($ch);
                            $get_data = json_decode($response, true);
                            curl_close($ch);

                            if (is_array($get_data)) {
                                if (array_key_exists('messages', $get_data)) {
                                    foreach ($get_data['messages'] as $msg) {
                                        $get_sms_status = 'Delivered|'.$msg['messageId'];
                                    }
                                } elseif (array_key_exists('requestError', $get_data)) {
                                    foreach ($get_data['requestError'] as $msg) {
                                        $get_sms_status = $msg['messageId'];
                                    }
                                } else {
                                    $get_sms_status = 'Unknown error';
                                }
                            } else {
                                $get_sms_status = 'Unknown error';
                            }

                        } catch (Exception $exception) {
                            $get_sms_status = $exception->getMessage();
                        }
                        break;

                    case '1s2u':

                        if ($sms_type == 'unicode') {
                            $mt      = 1;
                            $message = bin2hex(mb_convert_encoding($message, "UTF-16", "UTF-8"));
                        } else {
                            $mt = 0;
                        }

                        $parameters = [
                                "username" => $sending_server->username,
                                "password" => $sending_server->password,
                                "mno"      => $phone,
                                "msg"      => $message,
                                "sid"      => $data['sender_id'],
                                "mt"       => $mt,
                                "fl"       => 0,
                        ];

                        $sending_url = $gateway_url.'?'.http_build_query($parameters);

                        try {

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $sending_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);

                            $get_sms_status = curl_exec($ch);

                            curl_close($ch);

                            if (strpos($get_sms_status, 'OK') !== false) {
                                $get_sms_status = 'Delivered|'.trim(str_replace('OK: ', '', $get_sms_status));
                            } else {
                                switch ($get_sms_status) {
                                    case '0005':
                                        $get_sms_status = 'Invalid Sender';
                                        break;
                                    case '0010':
                                        $get_sms_status = 'Username not provided';
                                        break;
                                    case '0011':
                                        $get_sms_status = 'Password not provided';
                                        break;
                                    case '00':
                                        $get_sms_status = 'Invalid username/password';
                                        break;
                                    case '0020':
                                        $get_sms_status = 'Insufficient Credits';
                                        break;
                                    case '0030':
                                        $get_sms_status = 'Invalid Sender ID';
                                        break;
                                    case '0040':
                                        $get_sms_status = 'Mobile number not provided';
                                        break;
                                    case '0041':
                                        $get_sms_status = 'Invalid mobile number';
                                        break;
                                    case '0066':
                                    case '0042':
                                        $get_sms_status = 'Network not supported';
                                        break;
                                    case '0050':
                                        $get_sms_status = 'Invalid message';
                                        break;
                                    case '0060':
                                        $get_sms_status = 'Invalid quantity specified';
                                        break;
                                    case '0000':
                                        $get_sms_status = 'Message not sent';
                                        break;

                                    default:
                                        $get_sms_status = 'Unknown Error';
                                        break;
                                }

                            }
                        } catch (Exception $ex) {
                            $get_sms_status = $ex->getMessage();
                        }
                        break;

                    case 'MessageBird':
                        $parameters = [
                                'recipients' => $phone,
                                'originator' => $data['sender_id'],
                                'body'       => $message,
                                'datacoding' => 'auto',
                        ];

                        $ch = curl_init();

                        curl_setopt($ch, CURLOPT_URL, $gateway_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
                        curl_setopt($ch, CURLOPT_POST, 1);

                        $headers   = [];
                        $headers[] = "Authorization: AccessKey $sending_server->api_key";
                        $headers[] = "Content-Type: application/x-www-form-urlencoded";
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);
                        if (curl_errno($ch)) {
                            $get_sms_status = curl_error($ch);
                        } else {
                            $response = json_decode($result, true);

                            if (is_array($response) && array_key_exists('id', $response)) {
                                $get_sms_status = 'Delivered|'.$response['id'];
                            } elseif (is_array($response) && array_key_exists('errors', $response)) {
                                $get_sms_status = $response['errors'][0]['description'];
                            } else {
                                $get_sms_status = 'Unknown Error';
                            }
                        }
                        curl_close($ch);
                        break;

                    case 'AmazonSNS':
                        $credentials = [
                                'credentials' => [
                                        'key'    => $sending_server->access_key,
                                        'secret' => $sending_server->secret_access,
                                ],
                                'region'      => $sending_server->region, // < your aws from SNS Topic region
                                'version'     => 'latest',
                        ];

                        $sns = new SnsClient($credentials);

                        $parameters = [
                                'MessageAttributes' => [
                                        'AWS.SNS.SMS.SenderID' => [
                                                'DataType'    => 'String',
                                                'StringValue' => $data['sender_id'],
                                        ],
                                ],
                                "SMSType"           => $sending_server->route,
                                "PhoneNumber"       => '+'.$phone,
                                "Message"           => $message,
                        ];

                        try {
                            $result = $sns->publish($parameters)->toArray();
                            if (is_array($result) && array_key_exists('MessageId', $result)) {
                                $get_sms_status = 'Delivered|'.$result['MessageId'];
                            } else {
                                $get_sms_status = 'Unknown error';
                            }
                        } catch (SnsException $exception) {
                            $get_sms_status = $exception->getAwsErrorMessage();
                        }

                        break;

                    case 'Tyntec':
                        $parameters = [
                                'from'    => $data['sender_id'],
                                'to'      => $phone,
                                'message' => $message,
                        ];

                        try {
                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_USERPWD, "$sending_server->username".":"."$sending_server->password");

                            $headers   = [];
                            $headers[] = "Content-Type: application/json";
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                            $result = curl_exec($ch);
                            curl_close($ch);
                            $result = json_decode($result, true);

                            if (is_array($result) && array_key_exists('requestId', $result)) {
                                $get_sms_status = 'Delivered';
                            } elseif (is_array($result) && array_key_exists('status', $result)) {
                                $get_sms_status = $result['message'];
                            } else {
                                $get_sms_status = 'Invalid request';
                            }

                        } catch (Exception $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'KarixIO':
                        $parameters = [
                                'channel'     => 'sms',
                                'source'      => $data['sender_id'],
                                'destination' => [$phone],
                                'content'     => [
                                        'text' => $message,
                                ],
                        ];

                        try {

                            $headers = [
                                    'Content-Type:application/json',
                            ];

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_USERPWD, "$sending_server->auth_id".":"."$sending_server->auth_token");
                            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($ch);
                            curl_close($ch);

                            $get_response = json_decode($response, true);

                            if (isset($get_response) && is_array($get_response)) {
                                if (array_key_exists('objects', $get_response)) {
                                    if ($get_response['objects']['0']['status'] == 'queued') {
                                        $get_sms_status = 'Delivered|'.$get_response['objects']['0']['account_uid'];
                                    } else {
                                        $get_sms_status = $get_response['objects']['0']['status'];
                                    }
                                } elseif (array_key_exists('error', $get_response)) {
                                    $get_sms_status = $get_response['error']['message'];
                                } else {
                                    $get_sms_status = 'Unknown error';
                                }
                            } else {
                                $get_sms_status = 'Unknown error';
                            }

                        } catch (Exception $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'SignalWire':

                        $parameters = [
                                'From' => '+'.$data['sender_id'],
                                'Body' => $message,
                                'To'   => '+'.$phone,
                        ];

                        $sending_url = $gateway_url."/api/laml/2010-04-01/Accounts/$sending_server->api_token/Messages.json";

                        $ch = curl_init();

                        curl_setopt($ch, CURLOPT_URL, $sending_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
                        curl_setopt($ch, CURLOPT_USERPWD, "$sending_server->api_token".":"."$sending_server->project_id");

                        $get_response = curl_exec($ch);
                        if (curl_errno($ch)) {
                            $get_sms_status = curl_error($ch);
                        } else {

                            $result = json_decode($get_response, true);

                            if (isset($result) && is_array($result) && array_key_exists('status', $result) && array_key_exists('error_code', $result)) {
                                if ($result['status'] == 'queued' && $result['error_code'] === null) {
                                    $get_sms_status = 'Delivered|'.$result['sid'];
                                } else {
                                    $get_sms_status = $result['error_message'];
                                }
                            } elseif (isset($result) && is_array($result) && array_key_exists('status', $result) && array_key_exists('message', $result)) {
                                $get_sms_status = $result['message'];
                            } else {
                                $get_sms_status = $get_response;
                            }

                            if ($get_sms_status === null) {
                                $get_sms_status = 'Check your settings';
                            }
                        }
                        curl_close($ch);

                        break;

                    case 'Telnyx':

                        if (is_numeric($data['sender_id'])) {
                            $sender_id = '+'.$data['sender_id'];
                        } else {
                            $sender_id = $$data['sender_id'];
                        }

                        $parameters = [
                                "to"   => '+'.$phone,
                                "text" => $message,
                        ];

                        if (strlen($sender_id) > 12) {
                            $parameters['messaging_profile_id'] = $sender_id;
                        } else {
                            $parameters['from'] = $sender_id;
                        }

                        try {

                            $headers = [
                                    'Content-Type:application/json',
                                    'Authorization: Bearer '.$sending_server->api_key,
                            ];

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($ch);
                            curl_close($ch);

                            $get_response = json_decode($response, true);

                            if (isset($get_response) && is_array($get_response)) {
                                if (array_key_exists('data', $get_response) && array_key_exists('to', $get_response['data']) && $get_response['data']['to'][0]['status'] == 'queued') {
                                    $get_sms_status = 'Delivered';
                                } elseif (array_key_exists('errors', $get_response)) {
                                    $get_sms_status = $get_response['errors'][0]['detail'];
                                } else {
                                    $get_sms_status = (string) $response;
                                }
                            } else {
                                $get_sms_status = 'Unknown error';
                            }

                        } catch (Exception $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'Bandwidth':

                        $parameters = [
                                'from'          => '+'.$data['sender_id'],
                                'to'            => ['+'.$phone],
                                'text'          => $message,
                                'applicationId' => $sending_server->application_id,
                        ];

                        try {

                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_USERPWD, $sending_server->api_token.':'.$sending_server->api_secret);

                            $headers   = [];
                            $headers[] = 'Content-Type: application/json';
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                            $result = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {

                                $result = json_decode($result, true);

                                if (isset($result) && is_array($result)) {
                                    if (array_key_exists('id', $result)) {
                                        $get_sms_status = 'Delivered|'.$result['id'];
                                    } elseif (array_key_exists('error', $result)) {
                                        $get_sms_status = $result['error'];
                                    } elseif (array_key_exists('fieldErrors', $result)) {
                                        $get_sms_status = $result['fieldErrors'][0]['fieldName'].' '.$result['fieldErrors'][0]['description'];
                                    } else {
                                        $get_sms_status = implode(" ", $result);
                                    }
                                } else {
                                    $get_sms_status = $result;
                                }
                            }
                            curl_close($ch);
                        } catch (Exception $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'RouteeNet':

                        $curl = curl_init();

                        curl_setopt_array($curl, [
                                CURLOPT_URL            => "https://auth.routee.net/oauth/token",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING       => "",
                                CURLOPT_MAXREDIRS      => 10,
                                CURLOPT_TIMEOUT        => 30,
                                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST  => "POST",
                                CURLOPT_POSTFIELDS     => "grant_type=client_credentials",
                                CURLOPT_HTTPHEADER     => [
                                        "authorization: Basic ".base64_encode($sending_server->application_id.":".$sending_server->api_secret),
                                        "content-type: application/x-www-form-urlencoded",
                                ],
                        ]);

                        $response = curl_exec($curl);
                        $err      = curl_error($curl);

                        curl_close($curl);

                        if ($err) {
                            $get_sms_status = $err;
                        } else {
                            $response = json_decode($response, true);

                            if (isset($response) && is_array($response) && array_key_exists('access_token', $response)) {
                                $access_token = $response['access_token'];

                                $parameters = [
                                        'body' => $message,
                                        'to'   => '+'.$phone,
                                        'from' => $data['sender_id'],
                                ];

                                $sendSMS = json_encode($parameters);
                                $curl    = curl_init();

                                curl_setopt_array($curl, [
                                        CURLOPT_URL            => $gateway_url,
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING       => "",
                                        CURLOPT_MAXREDIRS      => 10,
                                        CURLOPT_TIMEOUT        => 30,
                                        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST  => "POST",
                                        CURLOPT_POSTFIELDS     => $sendSMS,
                                        CURLOPT_HTTPHEADER     => [
                                                "authorization: Bearer ".$access_token,
                                                "content-type: application/json",
                                        ],
                                ]);

                                $response = curl_exec($curl);
                                $err      = curl_error($curl);

                                curl_close($curl);

                                if ($err) {
                                    $get_sms_status = $err;
                                } else {
                                    $response = json_decode($response, true);
                                    if (isset($response) && is_array($response) && array_key_exists('status', $response)) {
                                        if ($response['status'] == 'Queued') {
                                            $get_sms_status = 'Delivered';
                                        } else {
                                            $get_sms_status = $response['status'];
                                        }
                                    } else {
                                        $get_sms_status = 'Invalid Request';
                                    }
                                }

                            } else {
                                $get_sms_status = 'Access token not found';
                            }
                        }
                        break;

                    case 'HutchLk':

                        $parameters  = [
                                "USER" => $sending_server->username,
                                "PWD"  => $sending_server->password,
                                "MASK" => $data['sender_id'],
                                "MSG"  => $message,
                                "NUM"  => $phone,
                        ];
                        $parameters  = http_build_query($parameters);
                        $gateway_url = $gateway_url.'?'.$parameters;

                        try {

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                            $get_sms_status = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {
                                if (substr_count($get_sms_status, 'SUCCESS') == 1) {
                                    $get_sms_status = 'Delivered';
                                }
                            }
                            curl_close($ch);
                        } catch (Exception $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'Teletopiasms':

                        $parameters = [
                                'username'  => $sending_server->username,
                                'password'  => $sending_server->password,
                                'recipient' => $phone,
                                'text'      => $message,
                        ];

                        if ($data['sender_id'] != '') {
                            $parameters['sender'] = $data['sender_id'];
                        }

                        $parameters  = http_build_query($parameters);
                        $gateway_url = $gateway_url.'?'.$parameters;

                        $ch = curl_init();

                        curl_setopt($ch, CURLOPT_URL, $gateway_url);
                        curl_setopt($ch, CURLOPT_HTTPGET, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $headers   = [];
                        $headers[] = "Content-Type: application/x-www-form-urlencoded";
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


                        $get_sms_status = curl_exec($ch);

                        if (curl_errno($ch)) {
                            $get_sms_status = curl_error($ch);
                        } else {
                            if (substr_count($get_sms_status, 'accepted')) {
                                $get_sms_status = 'Delivered';
                            }
                        }

                        curl_close($ch);
                        break;

                    case 'BroadcasterMobile':

                        $dataFields = [
                                'apiKey'  => (int) $sending_server->api_key,
                                'country' => $sending_server->c1,
                                'dial'    => (int) $data['sender_id'],
                                'tag'     => 'Prueba',
                                'message' => $message,
                                'msisdns' => [$phone],
                        ];

                        try {

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataFields));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36");
                            $headers = [
                                    'Content-Type: application/json',
                                    'Authorization: '.$sending_server->api_token,
                            ];

                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            $get_sms_status = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {

                                $output = json_decode($get_sms_status, true);

                                if (isset($output) && is_array($output) && array_key_exists('code', $output)) {
                                    if ($output['code'] == 0) {
                                        $get_sms_status = 'Delivered';
                                    } else {
                                        $get_sms_status = $output['message'];
                                    }
                                }
                            }

                            curl_close($ch);
                        } catch (Exception $ex) {
                            $get_sms_status = $ex->getMessage();
                        }
                        break;

                    case 'Solutions4mobiles':

                        $host         = 'sms.solutions4mobiles.com';
                        $authEndpoint = "https://$host/apis/auth";
                        $sendEndpoint = "https://$host/apis/sms/mt/v2/send";

                        $auth_body = (object) [
                                "type"     => "access_token",
                                "username" => $sending_server->username,
                                "password" => $sending_server->password,
                        ];

                        $auth_curl_params = [
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_POST           => true,
                                CURLOPT_URL            => $authEndpoint,
                                CURLOPT_CONNECTTIMEOUT => 10,
                                CURLOPT_TIMEOUT        => 10,
                                CURLOPT_HTTPHEADER     => ["cache-control: no-cache", "content-type: application/json"],
                                CURLOPT_POSTFIELDS     => json_encode($auth_body),
                        ];

                        //Setup request and execute
                        $auth_curl = curl_init();
                        curl_setopt_array($auth_curl, ($auth_curl_params));
                        $result = curl_exec($auth_curl);


                        $info = curl_getinfo($auth_curl);

                        //If server returned HTTP Status 200 the request was successful
                        if ($info['http_code'] == 200) {
                            //Store access token - Valid for 30 minutes - We must login every 30 minutes
                            $arr_res      = json_decode($result);
                            $access_token = $arr_res->payload->access_token;
                            //Send SMS
                            //Setup body
                            $send_body = [
                                    (object) [
                                            'to'      => [$phone],
                                            'from'    => $data['sender_id'],
                                            'message' => $message,
                                    ],
                            ];

                            $send_curl_params = [
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_POST           => true,
                                    CURLOPT_URL            => $sendEndpoint,
                                    CURLOPT_CONNECTTIMEOUT => 10,
                                    CURLOPT_TIMEOUT        => 10,
                                    CURLOPT_HTTPHEADER     => ["cache-control: no-cache", "content-type: application/json", "Authorization: Bearer $access_token"],
                                    CURLOPT_POSTFIELDS     => json_encode($send_body),
                            ];

                            //Setup request and execute
                            $send_curl = curl_init();
                            curl_setopt_array($send_curl, ($send_curl_params));
                            $result = curl_exec($send_curl);


                            $send_info = curl_getinfo($send_curl);

                            $output = json_decode($result, true);

                            //If server returned HTTP Status 200 the request was successful
                            if ($send_info['http_code'] == 200) {
                                if (isset($output) && is_array($output) && array_key_exists('payload', $output)) {
                                    $get_sms_status = 'Delivered|'.$output['payload'][0]['id'];
                                } else {
                                    $get_sms_status = json_decode($result);
                                }
                            } else {
                                if (isset($output) && is_array($output) && array_key_exists('errors', $output)) {
                                    $get_sms_status = $output['errors'][0]['message'];
                                } else {
                                    $get_sms_status = json_decode($result);
                                }
                            }
                            curl_close($send_curl);
                        } else {
                            $get_sms_status = json_decode($result);
                        }

                        curl_close($auth_curl);
                        break;

                    case 'BeemAfrica':

                        $parameters = [
                                'source_addr'   => $data['sender_id'],
                                'encoding'      => 0,
                                'schedule_time' => '',
                                'message'       => $message,
                                'recipients'    => [['recipient_id' => rand(1000, 99999), 'dest_addr' => $phone]],
                        ];

                        $ch = curl_init($gateway_url);

                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt_array($ch, [
                                CURLOPT_POST           => true,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_HTTPHEADER     => [
                                        'Authorization:Basic '.base64_encode("$sending_server->api_key:$sending_server->api_secret"),
                                        'Content-Type: application/json',
                                ],
                                CURLOPT_POSTFIELDS     => json_encode($parameters),
                        ]);
                        $response = curl_exec($ch);

                        if ($response === false) {
                            $get_sms_status = curl_error($ch);
                        } else {
                            $output = json_decode($response, true);

                            if (isset($output) && is_array($output) && array_key_exists('code', $output)) {
                                if ($output['code'] == 100) {
                                    $get_sms_status = 'Delivered|'.$output['request_id'];
                                } else {
                                    $get_sms_status = $output['message'];
                                }
                            } else {
                                $get_sms_status = (string) $response;
                            }
                        }
                        break;

                    case 'BulkSMSOnline':

                        $parameters = [
                                'username' => $sending_server->username,
                                'password' => $sending_server->password,
                                'to'       => $phone,
                                'source'   => $data['sender_id'],
                                'message'  => $message,
                        ];

                        if ($sms_type == 'unicode' || $sms_type == 'arabic') {
                            $parameters['type'] = 'u';
                        } else {
                            $parameters['type'] = 't';
                        }

                        $gateway_url = $gateway_url.'?'.http_build_query($parameters);


                        try {

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                            $get_sms_status = curl_exec($ch);
                            $get_sms_status = trim($get_sms_status);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {
                                if (strpos($get_sms_status, 'OK') !== false) {
                                    $get_sms_status = 'Delivered|'.str_replace('OK: ', '', $get_sms_status);
                                } else {

                                    switch ($get_sms_status) {

                                        case 'E0002':
                                            $get_sms_status = 'Invalid URL. This means that one of the parameters was not provided or left blank.';
                                            break;

                                        case 'E0003':
                                            $get_sms_status = 'Invalid username or password parameter.';
                                            break;

                                        case 'E0004':
                                            $get_sms_status = 'Invalid type parameter.';
                                            break;

                                        case 'E0005':
                                            $get_sms_status = 'Invalid message.';
                                            break;

                                        case 'E0006':
                                            $get_sms_status = 'Invalid TO number.';
                                            break;

                                        case 'E0007':
                                            $get_sms_status = 'Invalid source (Sender name).';
                                            break;

                                        case 'E0008':
                                            $get_sms_status = 'Authentication failed.';
                                            break;

                                        case 'E0010':
                                            $get_sms_status = 'Internal server error.';
                                            break;

                                        case 'E0022':
                                            $get_sms_status = 'Insufficient credit.';
                                            break;

                                        case 'E0033':
                                            $get_sms_status = 'If more than 30 API request per second throughput restriction by default';
                                            break;

                                        case 'E0044':
                                            $get_sms_status = 'mobile network not supported';
                                            break;
                                    }
                                }
                            }

                            curl_close($ch);
                        } catch (Exception $ex) {
                            $get_sms_status = $ex->getMessage();
                        }
                        break;

                    case 'FlowRoute':
                        $phone     = str_replace(['+', '(', ')', '-', " "], '', $phone);
                        $sender_id = str_replace(['+', '(', ')', '-', " "], '', $data['sender_id']);

                        $sms = [
                                "from" => $sender_id,
                                "to"   => $phone,
                                "body" => $message,
                        ];

                        try {

                            $headers   = [];
                            $headers[] = 'Content-Type: application/vnd.api+json';

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sms));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_USERPWD, $sending_server->access_key.':'.$sending_server->api_secret);

                            $response = curl_exec($ch);
                            curl_close($ch);

                            $get_response = json_decode($response, true);

                            if (isset($get_response) && is_array($get_response)) {
                                if (array_key_exists('data', $get_response)) {
                                    $get_sms_status = 'Delivered';
                                } elseif (array_key_exists('errors', $get_response)) {
                                    $get_sms_status = $get_response['errors'][0]['detail'];
                                } else {
                                    $get_sms_status = (string) $response;
                                }
                            } else {
                                $get_sms_status = (string) $response;
                            }

                        } catch (Exception $ex) {
                            $get_sms_status = $ex->getMessage();
                        }
                        break;

                    case 'CheapGlobalSMS':

                        $parameters = [
                                'sub_account'      => $sending_server->username,
                                'sub_account_pass' => $sending_server->password,
                                'action'           => 'send_sms',
                                'sender_id'        => $data['sender_id'],
                                'recipients'       => $phone,
                                'message'          => $message,
                        ];

                        if ($sms_type == 'unicode') {
                            $parameters['unicode'] = 1;
                        }

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $gateway_url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        $response      = curl_exec($ch);
                        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        if ($response_code != 200) {
                            $get_sms_status = curl_error($ch);
                        } else {
                            if ($response_code != 200) {
                                $get_sms_status = "HTTP ERROR $response_code: $response";
                            } else {
                                $json = @json_decode($response, true);

                                if ($json === null) {
                                    $get_sms_status = "INVALID RESPONSE: $response";
                                } elseif ( ! empty($json['error'])) {
                                    $get_sms_status = $json['error'];
                                } else {
                                    $get_sms_status = 'Delivered|'.$json['batch_id'];
                                }
                            }
                        }
                        curl_close($ch);
                        break;

                    case 'ElitBuzzBD':
                        $parameters = [
                                'api_key'  => $sending_server->api_key,
                                'contacts' => $phone,
                                'senderid' => $data['sender_id'],
                                'msg'      => $message,
                        ];

                        if ($sms_type == 'unicode' || $sms_type == 'arabic') {
                            $parameters['type'] = 'unicode';
                        } else {
                            $parameters['type'] = 'text';
                        }

                        $gateway_url = $gateway_url.'?'.http_build_query($parameters);


                        try {

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                            $get_sms_status = curl_exec($ch);

                            $get_sms_status = trim($get_sms_status);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {
                                if (strpos($get_sms_status, 'SMS SUBMITTED') !== false) {
                                    $get_sms_status = 'Delivered';
                                } else {

                                    switch ($get_sms_status) {

                                        case '1002':
                                            $get_sms_status = 'Sender Id/Masking Not Found';
                                            break;

                                        case '1003':
                                            $get_sms_status = 'API Not found';
                                            break;

                                        case '1004':
                                            $get_sms_status = 'SPAM Detected';
                                            break;

                                        case '1005':
                                        case '1006':
                                            $get_sms_status = 'Internal Error';
                                            break;

                                        case '1007':
                                            $get_sms_status = 'Balance Insufficient';
                                            break;

                                        case '1008':
                                            $get_sms_status = 'Message is empty';
                                            break;

                                        case '1009':
                                            $get_sms_status = 'Message Type Not Set (text/unicode)';
                                            break;

                                        case '1010':
                                            $get_sms_status = 'Invalid User & Password';
                                            break;

                                        case '1011':
                                            $get_sms_status = 'Invalid User Id';
                                            break;

                                        case '1012':
                                            $get_sms_status = 'Invalid Number';
                                            break;

                                        case '1013':
                                            $get_sms_status = 'API limit error';
                                            break;

                                        case '1014':
                                            $get_sms_status = 'No matching template';
                                            break;
                                    }
                                }
                            }

                            curl_close($ch);
                        } catch (Exception $ex) {
                            $get_sms_status = $ex->getMessage();
                        }
                        break;

                    case 'GreenWebBD':

                        $parameters = [
                                'to'      => $phone,
                                'message' => $message,
                                'token'   => $sending_server->api_token,
                        ];

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $gateway_url);
                        curl_setopt($ch, CURLOPT_ENCODING, '');
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($ch);

                        if ($response === false) {
                            $get_sms_status = curl_error($ch);
                        } else {
                            $output = json_decode($response, true);

                            if (isset($output) && is_array($output) && array_key_exists('status', $output[0])) {
                                if ($output[0]['status'] == 'SENT') {
                                    $get_sms_status = 'Delivered';
                                } else {
                                    $get_sms_status = $output[0]['statusmsg'];
                                }
                            } else {
                                $get_sms_status = (string) $response;
                            }
                        }

                        curl_close($ch);

                        break;

                    case 'HablameV2':
                        $parameters = [
                                'account'           => $sending_server->c1,
                                'apiKey'            => $sending_server->api_key,
                                'token'             => $sending_server->api_token,
                                'toNumber'          => $phone,
                                'sms'               => $message,
                                'isPriority'        => 1,
                                'flash'             => 0,
                                'request_dlvr_rcpt' => 0,
                        ];

                        if (isset($data['sender_id'])) {
                            $parameters['sc'] = $data['sender_id'];
                        }

                        $ch = curl_init();

                        curl_setopt($ch, CURLOPT_URL, $gateway_url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                        $response = curl_exec($ch);
                        curl_close($ch);

                        $response = json_decode($response, true);

                        if (isset($response) && is_array($response) && array_key_exists('status', $response)) {
                            if ($response["status"] == '1x000') {
                                $get_sms_status = 'Delivered';
                            } else {
                                $get_sms_status = $response["error_description"];
                            }
                        } else {
                            $get_sms_status = 'Invalid Request';
                        }
                        break;

                    case 'EasySendSMS':

                        if (is_numeric($data['sender_id'])) {
                            $sender_id = str_replace(['(', ')', '+', '-', ' '], '', $data['sender_id']);
                        } else {
                            $sender_id = $data['sender_id'];
                        }

                        if ($sms_type == 'unicode') {
                            $data_encoding = 1;
                        } else {
                            $data_encoding = 0;
                        }

                        $parameters = http_build_query([
                                'username' => $sending_server->username,
                                'password' => $sending_server->password,
                                'to'       => $phone,
                                'text'     => $message,
                                'type'     => $data_encoding,
                                'from'     => $sender_id,
                        ]);

                        try {

                            $sms_sent_to_user = $gateway_url."?".$parameters;

                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, $sms_sent_to_user);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $get_response = curl_exec($ch);
                            curl_close($ch);

                            $get_response = trim($get_response);

                            $result = explode(":", $get_response);
                            if (isset($result) && is_array($result) && count($result) > 0 && $result['0'] == 'OK') {
                                $get_sms_status = 'Delivered|'.trim($result['1']);
                            } else {

                                $data_code = (int) filter_var($get_response, FILTER_SANITIZE_NUMBER_INT);

                                switch ($data_code) {
                                    case '1001':
                                        $get_sms_status = 'Invalid URL. This means that one of the parameters was not provided or left blank';
                                        break;

                                    case '1002':
                                        $get_sms_status = 'Invalid username or password parameter';
                                        break;

                                    case '1003':
                                        $get_sms_status = 'Invalid type parameter';
                                        break;

                                    case '1004':
                                        $get_sms_status = 'Invalid message';
                                        break;

                                    case '1005':
                                        $get_sms_status = 'Invalid mobile number';
                                        break;

                                    case '1006':
                                        $get_sms_status = 'Invalid Sender name';
                                        break;

                                    case '1007':
                                        $get_sms_status = 'Insufficient credit';
                                        break;

                                    case '1008':
                                        $get_sms_status = 'Internal error';
                                        break;

                                    case '1009':
                                        $get_sms_status = 'Service not available';
                                        break;

                                    default:
                                        $get_sms_status = 'Unknown error';
                                        break;
                                }

                            }

                        } catch (Exception $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'ZamtelCoZm':

                        $parameters = [
                                'key'      => $sending_server->api_key,
                                'senderid' => $data['sender_id'],
                                'contacts' => $phone,
                                'message'  => $message,
                        ];

                        $parameters = http_build_query($parameters);

                        try {
                            $gateway_url = $gateway_url.'?'.$parameters;


                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                            $get_sms_status = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {

                                $get_data = json_decode($get_sms_status, true);

                                if (isset($get_data) && is_array($get_data) && array_key_exists('success', $get_data)) {
                                    if ($get_data['success'] == true) {
                                        $get_sms_status = 'Delivered';
                                    } else {
                                        $get_sms_status = $get_data['responseText'];
                                    }
                                }
                            }

                            curl_close($ch);

                        } catch (Exception $ex) {
                            $get_sms_status = $ex->getMessage();
                        }

                        break;

                    case 'CellCast':

                        $parameters = [
                                'sms_text' => $message,
                                'numbers'  => [$phone],
                        ];

                        if (isset($data['sender_id'])) {
                            $parameters['from'] = $data['sender_id'];
                        }

                        try {

                            $headers = [
                                    'APPKEY:'.$sending_server->api_key,
                                    'Accept: application/json',
                                    'Content-Type: application/json',
                            ];

                            $ch = curl_init(); //open connection
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_HEADER, false);
                            curl_setopt($ch, CURLOPT_POST, count($parameters));
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            if ( ! $result = curl_exec($ch)) {
                                $get_sms_status = json_decode(curl_error($ch));
                            } else {
                                $output = json_decode($result, true);
                                if (isset($output) && is_array($output) && array_key_exists('msg', $output)) {
                                    if ($output['msg'] == 'Queued') {
                                        $get_sms_status = 'Delivered';
                                    } else {
                                        $get_sms_status = $output['msg'];
                                    }
                                }
                            }
                            curl_close($ch);

                        } catch (Exception $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'AfricasTalking':

                        $parameters = [
                                'username' => $sending_server->username,
                                'message'  => $message,
                                'to'       => $phone,
                                'from'     => $data['sender_id'],
                        ];

                        try {

                            $headers = [
                                    'apiKey:'.$sending_server->api_key,
                                    'Accept: application/json',
                                    'Content-Type: application/x-www-form-urlencoded',
                            ];

                            $ch = curl_init(); //open connection
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_HEADER, false);
                            curl_setopt($ch, CURLOPT_POST, count($parameters));
                            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            if ( ! $result = curl_exec($ch)) {
                                $get_sms_status = json_decode(curl_error($ch));
                            } else {
                                $output = json_decode($result, true);

                                if (isset($output) && is_array($output) && array_key_exists('SMSMessageData', $output)) {
                                    if (strpos($output['SMSMessageData']['Message'], 'Sent') !== false) {
                                        $get_sms_status = 'Delivered';
                                    } else {
                                        $get_sms_status = $output['SMSMessageData']['Message'];
                                    }
                                }
                            }
                            curl_close($ch);

                        } catch (Exception $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'CaihCom':

                        $parameters = [
                                'toNumber'  => $phone,
                                'message'   => $message,
                                'requestId' => time(),
                                'sendType'  => 'S0001',
                                'token'     => $sending_server->api_token,
                        ];

                        if (isset($data['sender_id'])) {
                            $parameters['from'] = $data['sender_id'];
                        }
                        $parameters = json_encode($parameters);

                        $md5Sum = md5($parameters.$sending_server->c1);

                        try {

                            $headers = [
                                    'Content-Type:application/json;charset=UTF-8',
                                    'md5Sum: '.$md5Sum,
                            ];

                            $ch = curl_init(); //open connection
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_HEADER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            if ( ! $result = curl_exec($ch)) {
                                $get_sms_status = json_decode(curl_error($ch));
                            } else {
                                $output = json_decode($result, true);

                                if (isset($output) && is_array($output) && array_key_exists('success', $output) && array_key_exists('desc', $output)) {
                                    if ($output['success'] == true) {
                                        $get_sms_status = 'Delivered|'.$output['messageId'];
                                    } else {
                                        $get_sms_status = $output['desc'];
                                    }
                                }
                            }
                            curl_close($ch);

                        } catch (Exception $e) {
                            $get_sms_status = $e->getMessage();
                        }
                        break;

                    case 'KeccelSMS':

                        $parameters = [
                                'pass'   => $sending_server->password,
                                'id'     => $sending_server->application_id,
                                'from'   => $data['sender_id'],
                                'to'     => $phone,
                                'text'   => $message,
                                'dlrreq' => 1,
                        ];

                        $parameters = http_build_query($parameters);

                        try {
                            $gateway_url = $gateway_url.'?user=&'.$parameters;

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                            $get_sms_status = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {
                                if (is_numeric($get_sms_status)) {
                                    $get_sms_status = 'Delivered|'.$get_sms_status;
                                } else {
                                    $get_sms_status = 'Invalid gateway information';
                                }
                            }

                            curl_close($ch);

                        } catch (Exception $ex) {
                            $get_sms_status = $ex->getMessage();
                        }
                        break;

                    case 'JohnsonConnect':

                        $parameters = [
                                'appkey'    => $sending_server->api_key,
                                'secretkey' => $sending_server->api_secret,
                                'phone'     => $phone,
                                'content'   => $message,
                        ];

                        if (isset($data['sender_id'])) {
                            $parameters['source_address'] = $data['sender_id'];
                        }

                        $parameters = http_build_query($parameters);

                        try {
                            $gateway_url = $gateway_url.'?'.$parameters;

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                            $result = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {

                                $output = json_decode($result, true);

                                if (isset($output) && is_array($output) && array_key_exists('code', $output) && array_key_exists('result', $output)) {
                                    if ($output['code'] == 0) {
                                        $get_sms_status = 'Delivered|'.$output['messageid'];
                                    } else {
                                        $get_sms_status = $output['result'];
                                    }
                                } else {
                                    $get_sms_status = (string) $result;
                                }
                            }

                            curl_close($ch);

                        } catch (Exception $ex) {
                            $get_sms_status = $ex->getMessage();
                        }
                        break;

                    case 'SMSala':
                    case 'SpeedaMobile':

                        $parameters = [
                                'api_id'       => $sending_server->auth_id,
                                'api_password' => $sending_server->password,
                                'sms_type'     => 'P',
                                'phonenumber'  => $phone,
                                'sender_id'    => $data['sender_id'],
                                'textmessage'  => $message,
                        ];

                        if ($sms_type == 'unicode' || $sms_type == 'arabic') {
                            $parameters['encoding'] = 'U';
                        } else {
                            $parameters['encoding'] = 'T';
                        }

                        $gateway_url = $gateway_url.'?'.http_build_query($parameters);

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $gateway_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_HTTPGET, 1);

                        $get_sms_status = curl_exec($ch);

                        if (curl_errno($ch)) {
                            $get_sms_status = curl_error($ch);
                        } else {

                            $get_data = json_decode($get_sms_status, true);

                            if (is_array($get_data) && array_key_exists('status', $get_data)) {
                                if ($get_data['status'] == 'S') {
                                    $get_sms_status = 'Delivered|'.$get_data['message_id'];
                                } else {
                                    $get_sms_status = $get_data['remarks'];
                                }
                            }
                        }
                        curl_close($ch);
                        break;

                    case 'Text2World':

                        $parameters = http_build_query([
                                'username' => $sending_server->username,
                                'password' => $sending_server->password,
                                'type'     => 'TEXT',
                                'mobile'   => $phone,
                                'message'  => $message,
                                'sender'   => $data['sender_id'],
                        ]);

                        try {

                            $sms_sent_to_user = $gateway_url."?".$parameters;

                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, $sms_sent_to_user);
                            curl_setopt($ch, CURLOPT_HTTPGET, 1);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                            $get_sms_status = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $get_sms_status = curl_error($ch);
                            } else {
                                $get_sms_status = ucfirst(strtolower($get_sms_status));
                            }
                            curl_close($ch);
                        } catch (Exception $e) {
                            $get_sms_status = $e->getMessage();
                        }

                        break;

                    case 'EnableX':

                        $headers   = [];
                        $headers[] = "Content-Type: application/json";
                        $headers[] = "Authorization: Basic ".base64_encode("$sending_server->application_id:$sending_server->api_key");

                        $parameters = [
                                'body'        => $message,
                                'type'        => 'sms',
                                'campaign_id' => $sending_server->c1,
                                'to'          => [
                                        $phone,
                                ],
                        ];

                        if (isset($data['sender_id'])) {
                            $parameters['from'] = $data['sender_id'];
                        }

                        if ($sms_type == 'unicode') {
                            $parameters['data_coding'] = 'unicode';
                        } else {
                            $parameters['data_coding'] = 'auto';
                        }

                        try {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $gateway_url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($ch);
                            curl_close($ch);

                            $get_response = json_decode($response, true);

                            if (isset($get_response) && is_array($get_response) && array_key_exists('result', $get_response)) {
                                if ($get_response['result'] == '0') {
                                    $get_sms_status = 'Delivered|'.$get_response['job_id'];
                                } else {
                                    $get_sms_status = $get_response['desc'];
                                }
                            } else {
                                $get_sms_status = (string) $response;
                            }

                        } catch (Exception $ex) {
                            $get_sms_status = $ex->getMessage();
                        }
                        break;

                    case 'SpoofSend':
                    case 'AlhajSms':
                    case 'SendroidUltimate':
                    case 'RealSMS':

                        $parameters = [
                                'apikey'   => $sending_server->api_key,
                                'apitoken' => $sending_server->api_token,
                                'to'       => $phone,
                                'from'     => $data['sender_id'],
                                'text'     => $message,
                        ];


                        if ($sms_type == 'unicode' || $sms_type == 'arabic') {
                            $parameters['type'] = 'unicode';
                        } else {
                            $parameters['type'] = 'sms';
                        }

                        $parameters  = http_build_query($parameters);
                        $gateway_url = $gateway_url.'?sendsms&'.$parameters;

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $gateway_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_HTTPGET, 1);

                        $get_sms_status = curl_exec($ch);

                        if (curl_errno($ch)) {
                            $get_sms_status = curl_error($ch);
                        } else {

                            $get_data = json_decode($get_sms_status, true);

                            if (is_array($get_data) && array_key_exists('status', $get_data)) {
                                if ($get_data['status'] == 'error') {
                                    $get_sms_status = $get_data['message'];
                                } else {
                                    $get_sms_status = 'Delivered';
                                }
                            }
                        }
                        curl_close($ch);
                        break;

                    default:
                        $get_sms_status = __('locale.sending_servers.sending_server_not_found');
                        break;
                }
            }
        }

        $reportsData = [
                'user_id'           => $data['user_id'],
                'to'                => $phone,
                'message'           => $message,
                'sms_type'          => $data['sms_type'],
                'status'            => $get_sms_status,
                'cost'              => $data['cost'],
                'sending_server_id' => $sending_server->id,
        ];

        if (isset($data['sender_id'])) {
            $reportsData['from'] = $data['sender_id'];
        }

        if (isset($data['campaign_id'])) {
            $reportsData['campaign_id'] = $data['campaign_id'];
        }

        if (isset($data['api_key'])) {
            $reportsData['api_key'] = $data['api_key'];
            $reportsData['send_by'] = 'api';
        } else {
            $reportsData['send_by'] = 'from';
        }

        $status = Reports::create($reportsData);

        if ($status) {
            return $status;
        }

        return __('locale.exceptions.something_went_wrong');

    }


    /**
     * send voice message
     *
     * @param $data
     *
     * @return array|Application|Translator|string|null
     * @throws Exception
     */
    public function sendVoiceSMS($data)
    {
        $phone          = $data['phone'];
        $sending_server = $data['sending_server'];
        $gateway_name   = $data['sending_server']->settings;
        $message        = null;
        $get_sms_status = $data['status'];
        $language       = $data['language'];
        $gender         = $data['gender'];

        if (isset($data['message'])) {
            $message = $data['message'];
        }
        if ($get_sms_status == null) {
            $gateway_url = $sending_server->api_link;
            switch ($gateway_name) {

                case 'Twilio':

                    try {
                        $client = new Client($sending_server->account_sid, $sending_server->auth_token);

                        $response = new VoiceResponse();

                        if ($gender == 'male') {
                            $voice = 'man';
                        } else {
                            $voice = 'woman';
                        }

                        $response->say($message, ['voice' => $voice, 'language' => $language]);

                        $get_response = $client->calls->create($phone, $data['sender_id'], [
                                "twiml" => $response,
                        ]);

                        if ($get_response->status == 'queued') {
                            $get_sms_status = 'Delivered';
                        } else {
                            $get_sms_status = $get_response->status.'|'.$get_response->sid;
                        }

                    } catch (ConfigurationException | TwilioException $e) {
                        $get_sms_status = $e->getMessage();
                    }
                    break;

                case 'Plivo':

                    $client = new RestClient($sending_server->auth_id, $sending_server->auth_token);
                    try {
                        $response = $client->messages->create(
                                $data['sender_id'],
                                [$phone],
                                Tool::createVoiceFile($message, 'Plivo'),
                        );

                        $get_sms_status = 'Delivered|'.$response->getmessageUuid(0);

                    } catch (PlivoResponseException $e) {
                        $get_sms_status = $e->getMessage();
                    }

                    break;


                case 'Infobip':

                    $parameters = [
                            'text'     => $message,
                            'language' => $data['language'],
                            'voice'    => [
                                    'gender' => $data['gender'],
                            ],
                            'from'     => $data['sender_id'],
                            'to'       => $phone,
                    ];
                    try {
                        $curl = curl_init();

                        $header = [
                                "Authorization: App $sending_server->api_key",
                                "Content-Type: application/json",
                                "Accept: application/json",
                        ];

                        curl_setopt_array($curl, [
                                CURLOPT_URL            => 'https://api.infobip.com/tts/3/single',
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING       => '',
                                CURLOPT_MAXREDIRS      => 10,
                                CURLOPT_TIMEOUT        => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST  => 'POST',
                                CURLOPT_POSTFIELDS     => json_encode($parameters),
                                CURLOPT_HTTPHEADER     => $header,
                        ]);


                        // response of the POST request
                        $response = curl_exec($curl);
                        $get_data = json_decode($response, true);
                        curl_close($curl);

                        if (is_array($get_data)) {
                            if (array_key_exists('messages', $get_data)) {
                                foreach ($get_data['messages'] as $msg) {
                                    if ($msg['status']['name'] == 'MESSAGE_ACCEPTED' || $msg['status']['name'] == 'PENDING_ENROUTE' || $msg['status']['name'] == 'PENDING_ACCEPTED') {
                                        $get_sms_status = 'Delivered|'.$msg['messageId'];
                                    } else {
                                        $get_sms_status = $msg['status']['description'];
                                    }
                                }
                            } elseif (array_key_exists('requestError', $get_data)) {
                                foreach ($get_data['requestError'] as $msg) {
                                    $get_sms_status = $msg['text'];
                                }
                            } else {
                                $get_sms_status = 'Unknown error';
                            }
                        } else {
                            $get_sms_status = 'Unknown error';
                        }

                    } catch (Exception $exception) {
                        $get_sms_status = $exception->getMessage();
                    }
                    break;

                case 'MessageBird':
                    $parameters = [
                            'destination' => $phone,
                            'source'      => $data['sender_id'],
                            'callFlow'    => [
                                    'title' => config('app.name').'_'.now().'_flow',
                                    'steps' => [
                                            [
                                                    'action'  => 'say',
                                                    'options' => [
                                                            'payload'  => $message,
                                                            'language' => $data['language'],
                                                            'voice'    => $data['gender'],
                                                    ],
                                            ],
                                    ],
                            ],
                    ];

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, 'https://voice.messagebird.com/calls');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                    curl_setopt($ch, CURLOPT_POST, 1);

                    $headers   = [];
                    $headers[] = "Authorization: AccessKey $sending_server->api_key";
                    $headers[] = "Content-Type: application/x-www-form-urlencoded";
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    if (curl_errno($ch)) {
                        $get_sms_status = curl_error($ch);
                    } else {

                        $response = json_decode($result, true);

                        if (is_array($response) && array_key_exists('data', $response)) {
                            $get_sms_status = 'Delivered|'.$response['data'][0]['id'];
                        } elseif (is_array($response) && array_key_exists('errors', $response)) {
                            $get_sms_status = $response['errors'][0]['message'];
                        } else {
                            $get_sms_status = 'Unknown Error';
                        }
                    }
                    curl_close($ch);

                    break;

                case 'SignalWire':

                    $parameters = [
                            'From' => '+'.$data['sender_id'],
                            'Url'  => Tool::createVoiceFile($message, 'Twilio'),
                            'To'   => '+'.$phone,
                    ];

                    $sending_url = $gateway_url."/api/laml/2010-04-01/Accounts/$sending_server->api_token/Calls.json";

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $sending_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
                    curl_setopt($ch, CURLOPT_USERPWD, "$sending_server->api_token".":"."$sending_server->project_id");

                    $get_response = curl_exec($ch);
                    if (curl_errno($ch)) {
                        $get_sms_status = curl_error($ch);
                    } else {

                        $result = json_decode($get_response, true);

                        if (isset($result) && is_array($result) && array_key_exists('status', $result) && array_key_exists('error_code', $result)) {
                            if ($result['status'] == 'queued' && $result['error_code'] === null) {
                                $get_sms_status = 'Delivered|'.$result['sid'];
                            } else {
                                $get_sms_status = $result['error_message'];
                            }
                        } elseif (isset($result) && is_array($result) && array_key_exists('status', $result) && array_key_exists('message', $result)) {
                            $get_sms_status = $result['message'];
                        } else {
                            $get_sms_status = $get_response;
                        }

                        if ($get_sms_status === null) {
                            $get_sms_status = 'Check your settings';
                        }
                    }
                    curl_close($ch);
                    break;

                default:
                    $get_sms_status = __('locale.sending_servers.sending_server_not_found');
                    break;
            }
        }


        $reportsData = [
                'user_id'           => $data['user_id'],
                'to'                => $phone,
                'message'           => $message,
                'sms_type'          => 'voice',
                'status'            => $get_sms_status,
                'cost'              => $data['cost'],
                'sending_server_id' => $sending_server->id,
        ];

        if (isset($data['sender_id'])) {
            $reportsData['from'] = $data['sender_id'];
        }

        if (isset($data['campaign_id'])) {
            $reportsData['campaign_id'] = $data['campaign_id'];
        }

        if (isset($data['api_key'])) {
            $reportsData['api_key'] = $data['api_key'];
            $reportsData['send_by'] = 'api';
        } else {
            $reportsData['send_by'] = 'from';
        }

        $status = Reports::create($reportsData);

        if ($status) {
            return $status;
        }

        return __('locale.exceptions.something_went_wrong');

    }

    /**
     * send mms message
     *
     * @param $data
     *
     * @return array|Application|Translator|string|null
     */
    public function sendMMS($data)
    {
        $phone          = $data['phone'];
        $sending_server = $data['sending_server'];
        $gateway_name   = $data['sending_server']->settings;
        $message        = null;
        $get_sms_status = $data['status'];
        $media_url      = $data['media_url'];

        if (isset($data['message'])) {
            $message = $data['message'];
        }
        if ($get_sms_status == null) {
            $gateway_url = $sending_server->api_link;
            switch ($gateway_name) {

                case 'Twilio':

                    try {
                        $client = new Client($sending_server->account_sid, $sending_server->auth_token);

                        $get_response = $client->messages->create($phone, [
                                'from'     => $data['sender_id'],
                                'body'     => $message,
                                'mediaUrl' => $media_url,
                        ]);

                        if ($get_response->status == 'queued' || $get_response->status == 'accepted') {
                            $get_sms_status = 'Delivered|'.$get_response->sid;
                        } else {
                            $get_sms_status = $get_response->status.'|'.$get_response->sid;
                        }

                    } catch (ConfigurationException | TwilioException $e) {
                        $get_sms_status = $e->getMessage();
                    }
                    break;

                case 'TextLocal':

                    $parameters = [
                            'apikey'  => $sending_server->api_key,
                            'numbers' => $phone,
                            'url'     => $media_url,
                            'message' => $message,
                    ];

                    if (isset($data['sender_id'])) {
                        $parameters['sender'] = $data['sender_id'];
                    }

                    try {
                        $ch = curl_init("https://api.txtlocal.com/send_mms/");
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($ch);
                        $err      = curl_error($ch);
                        curl_close($ch);

                        if ($err) {
                            $get_sms_status = $err;
                        } else {
                            $get_data = json_decode($response, true);

                            if (isset($get_data) && is_array($get_data) && array_key_exists('status', $get_data)) {
                                if ($get_data['status'] == 'failure') {
                                    foreach ($get_data['errors'] as $err) {
                                        $get_sms_status = $err['message'];
                                    }
                                } elseif ($get_data['status'] == 'success') {
                                    $get_sms_status = 'Delivered';
                                } else {
                                    $get_sms_status = $response;
                                }
                            } else {
                                $get_sms_status = $response;
                            }
                        }
                    } catch (Exception $exception) {
                        $get_sms_status = $exception->getMessage();
                    }
                    break;

                case 'Plivo':

                    $client = new RestClient($sending_server->auth_id, $sending_server->auth_token);
                    try {
                        $response = $client->messages->create(
                                $data['sender_id'],
                                [$phone],
                                $message,
                                ['media_urls' => $media_url]
                        );

                        $get_sms_status = 'Delivered|'.$response->getmessageUuid(0);

                    } catch (PlivoResponseException $e) {
                        $get_sms_status = $e->getMessage();
                    }

                    break;

                case 'PlivoPowerpack':

                    $client = new RestClient($sending_server->auth_id, $sending_server->auth_token);
                    try {
                        $response = $client->messages->create(
                                null,
                                [$phone],
                                $message,
                                ['media_urls' => $media_url],
                                $data['sender_id']
                        );

                        $get_sms_status = 'Delivered|'.$response->getmessageUuid(0);

                    } catch (PlivoResponseException $e) {
                        $get_sms_status = $e->getMessage();
                    }

                    break;

                case 'SMSGlobal':

                    $parameters = [
                            'user'        => $sending_server->username,
                            'password'    => $sending_server->password,
                            'from'        => $data['sender_id'],
                            'number'      => $phone,
                            'message'     => $message,
                            'attachmentx' => $media_url,
                            'typex'       => mime_content_type($media_url),
                            'namex'       => basename($media_url),
                    ];

                    $sending_url = 'https://api.smsglobal.com/mms/sendmms.php?'.http_build_query($parameters);

                    try {

                        $ch = curl_init();

                        curl_setopt($ch, CURLOPT_URL, $sending_url);
                        curl_setopt($ch, CURLOPT_HTTPGET, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $get_sms_status = curl_exec($ch);
                        curl_close($ch);

                        if (substr_count($get_sms_status, 'SUCCESS')) {
                            $get_sms_status = 'Delivered';
                        } else {
                            $get_sms_status = str_replace('ERROR:', '', $get_sms_status);
                        }

                    } catch (Exception $exception) {
                        $get_sms_status = $exception->getMessage();
                    }
                    break;

                case 'MessageBird':
                    $parameters = [
                            'recipients' => $data['phone'],
                            'originator' => $data['sender_id'],
                            'body'       => $message,
                            'mediaUrls'  => [$media_url],
                    ];

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, 'https://rest.messagebird.com/mms');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                    curl_setopt($ch, CURLOPT_POST, 1);

                    $headers   = [];
                    $headers[] = "Authorization: AccessKey $sending_server->api_key";
                    $headers[] = "Content-Type: application/x-www-form-urlencoded";
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    if (curl_errno($ch)) {
                        $get_sms_status = curl_error($ch);
                    } else {
                        $response = json_decode($result, true);

                        if (is_array($response) && array_key_exists('id', $response)) {
                            $get_sms_status = 'Delivered|'.$response['id'];
                        } elseif (is_array($response) && array_key_exists('errors', $response)) {
                            $get_sms_status = $response['errors'][0]['description'];
                        } else {
                            $get_sms_status = 'Unknown Error';
                        }
                    }
                    curl_close($ch);


                    break;

                case 'SignalWire':

                    $parameters = [
                            'From'     => '+'.$data['sender_id'],
                            'Body'     => $message,
                            'MediaUrl' => $media_url,
                            'To'       => '+'.$phone,
                    ];

                    $sending_url = $gateway_url."/api/laml/2010-04-01/Accounts/$sending_server->api_token/Messages.json";

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $sending_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
                    curl_setopt($ch, CURLOPT_USERPWD, "$sending_server->api_token".":"."$sending_server->project_id");

                    $get_response = curl_exec($ch);
                    if (curl_errno($ch)) {
                        $get_sms_status = curl_error($ch);
                    } else {

                        $result = json_decode($get_response, true);

                        if (isset($result) && is_array($result) && array_key_exists('status', $result) && array_key_exists('error_code', $result)) {
                            if ($result['status'] == 'queued' && $result['error_code'] === null) {
                                $get_sms_status = 'Delivered|'.$result['sid'];
                            } else {
                                $get_sms_status = $result['error_message'];
                            }
                        } elseif (isset($result) && is_array($result) && array_key_exists('status', $result) && array_key_exists('message', $result)) {
                            $get_sms_status = $result['message'];
                        } else {
                            $get_sms_status = $get_response;
                        }

                        if ($get_sms_status === null) {
                            $get_sms_status = 'Check your settings';
                        }
                    }
                    curl_close($ch);

                    break;

                case 'Telnyx':
                    if (is_numeric($data['sender_id'])) {
                        $sender_id = '+'.$data['sender_id'];
                    } else {
                        $sender_id = $$data['sender_id'];
                    }

                    $parameters = [
                            "to"         => '+'.$phone,
                            "text"       => $message,
                            "subject"    => 'Picture',
                            "media_urls" => [$media_url],
                    ];

                    if (strlen($sender_id) > 12) {
                        $parameters['messaging_profile_id'] = $sender_id;
                    } else {
                        $parameters['from'] = $sender_id;
                    }

                    try {

                        $headers = [
                                'Content-Type:application/json',
                                'Authorization: Bearer '.$sending_server->api_key,
                        ];

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $gateway_url);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($ch);
                        curl_close($ch);

                        $get_response = json_decode($response, true);

                        if (isset($get_response) && is_array($get_response)) {
                            if (array_key_exists('data', $get_response) && array_key_exists('to', $get_response['data']) && $get_response['data']['to'][0]['status'] == 'queued') {
                                $get_sms_status = 'Delivered';
                            } elseif (array_key_exists('errors', $get_response)) {
                                $get_sms_status = $get_response['errors'][0]['detail'];
                            } else {
                                $get_sms_status = (string) $response;
                            }
                        } else {
                            $get_sms_status = 'Unknown error';
                        }

                    } catch (Exception $e) {
                        $get_sms_status = $e->getMessage();
                    }
                    break;

                case 'Bandwidth':

                    $sender_id = str_replace(['+', '(', ')', '-', ' '], '', $this->sender_id);

                    $parameters = [
                            'from'          => '+'.$sender_id,
                            'to'            => ['+'.$this->cl_phone],
                            'text'          => $this->message,
                            'applicationId' => $sending_server->application_id,
                            'media'         => [
                                    $this->media_url,
                            ],
                    ];

                    try {

                        $ch = curl_init();

                        curl_setopt($ch, CURLOPT_URL, $gateway_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_USERPWD, $sending_server->api_secret.':'.$sending_server->api_token);

                        $headers   = [];
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);

                        if (curl_errno($ch)) {
                            $get_sms_status = curl_error($ch);
                        } else {

                            $result = json_decode($result, true);

                            if (isset($result) && is_array($result)) {
                                if (array_key_exists('id', $result)) {
                                    $get_sms_status = 'Success|'.$result['id'];
                                } elseif (array_key_exists('error', $result)) {
                                    $get_sms_status = $result['error'];
                                } elseif (array_key_exists('fieldErrors', $result)) {
                                    $get_sms_status = $result['fieldErrors'][0]['fieldName'].' '.$result['fieldErrors'][0]['description'];
                                } else {
                                    $get_sms_status = implode(" ", $result);
                                }
                            } else {
                                $get_sms_status = $result;
                            }
                        }
                        curl_close($ch);
                    } catch (Exception $e) {
                        $get_sms_status = $e->getMessage();
                    }
                    break;

                case 'FlowRoute':
                    $phone     = str_replace(['+', '(', ')', '-', " "], '', $phone);
                    $sender_id = str_replace(['+', '(', ')', '-', " "], '', $data['sender_id']);

                    $sms = [
                            "from"       => $sender_id,
                            "to"         => $phone,
                            "body"       => $message,
                            'is_mms'     => true,
                            'media_urls' => [
                                    $media_url,
                            ],
                    ];

                    try {

                        $headers   = [];
                        $headers[] = 'Content-Type: application/vnd.api+json';

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $gateway_url);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sms, JSON_UNESCAPED_SLASHES));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_USERPWD, $sending_server->access_key.':'.$sending_server->api_secret);

                        $response = curl_exec($ch);
                        curl_close($ch);

                        $get_response = json_decode($response, true);

                        if (isset($get_response) && is_array($get_response)) {
                            if (array_key_exists('data', $get_response)) {
                                $get_sms_status = 'Delivered';
                            } elseif (array_key_exists('errors', $get_response)) {
                                $get_sms_status = $get_response['errors'][0]['detail'];
                            } else {
                                $get_sms_status = 'Invalid request';
                            }
                        } else {
                            $get_sms_status = 'Invalid request';
                        }

                    } catch (Exception $ex) {
                        $get_sms_status = $ex->getMessage();
                    }

                    break;

                default:
                    $get_sms_status = __('locale.sending_servers.sending_server_not_found');
                    break;
            }
        }


        $reportsData = [
                'user_id'           => $data['user_id'],
                'to'                => $phone,
                'message'           => $message,
                'sms_type'          => 'mms',
                'status'            => $get_sms_status,
                'cost'              => $data['cost'],
                'sending_server_id' => $sending_server->id,
                'media_url'         => $media_url,
        ];

        if (isset($data['sender_id'])) {
            $reportsData['from'] = $data['sender_id'];
        }

        if (isset($data['campaign_id'])) {
            $reportsData['campaign_id'] = $data['campaign_id'];
        }

        if (isset($data['api_key'])) {
            $reportsData['api_key'] = $data['api_key'];
            $reportsData['send_by'] = 'api';
        } else {
            $reportsData['send_by'] = 'from';
        }

        $status = Reports::create($reportsData);

        if ($status) {
            return $status;
        }

        return __('locale.exceptions.something_went_wrong');

    }


    /**
     * send whatsapp message
     *
     * @param $data
     *
     * @return array|Application|Translator|string|null
     */
    public function sendWhatsApp($data)
    {
        $phone          = $data['phone'];
        $sending_server = $data['sending_server'];
        $gateway_name   = $data['sending_server']->settings;
        $get_sms_status = $data['status'];
        $message        = $data['message'];

        if ($get_sms_status == null) {
            $gateway_url = $sending_server->api_link;
            switch ($gateway_name) {

                case 'Twilio':

                    try {
                        $client = new Client($sending_server->account_sid, $sending_server->auth_token);

                        $get_response = $client->messages->create(
                                'whatsapp:'.$phone, [
                                        'from' => 'whatsapp:'.$data['sender_id'],
                                        'body' => $message,
                                ]
                        );

                        if ($get_response->status == 'queued' || $get_response->status == 'accepted') {
                            $get_sms_status = 'Delivered|'.$get_response->sid;
                        } else {
                            $get_sms_status = $get_response->status.'|'.$get_response->sid;
                        }

                    } catch (ConfigurationException | TwilioException $e) {
                        $get_sms_status = $e->getMessage();
                    }
                    break;

                case 'MessageBird':
                    $parameters = [
                            'to'      => $data['phone'],
                            'from'    => $data['sender_id'],
                            'type'    => 'text',
                            'content' => [
                                    'text' => $message,
                            ],
                    ];

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, 'https://conversations.messagebird.com/v1/send');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
                    curl_setopt($ch, CURLOPT_POST, 1);

                    $headers   = [];
                    $headers[] = "Authorization: AccessKey $sending_server->api_key";
                    $headers[] = "Content-Type: application/x-www-form-urlencoded";
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    if (curl_errno($ch)) {
                        $get_sms_status = curl_error($ch);
                    } else {
                        $response = json_decode($result, true);

                        if (is_array($response) && array_key_exists('id', $response)) {
                            $get_sms_status = 'Delivered|'.$response['id'];
                        } elseif (is_array($response) && array_key_exists('errors', $response)) {
                            $get_sms_status = $response['errors'][0]['description'];
                        } else {
                            $get_sms_status = 'Unknown Error';
                        }
                    }
                    curl_close($ch);

                    break;

                case 'WhatsAppChatApi':

                    $data = [
                            'phone' => $phone,
                            'body'  => $message,
                    ];
                    $json = json_encode($data);

                    $url     = $gateway_url.'/message?token='.$sending_server->api_token;
                    $options = stream_context_create([
                            'http' => [
                                    'method'  => 'POST',
                                    'header'  => 'Content-type: application/json',
                                    'content' => $json,
                            ],
                    ]);

                    try {
                        $result = file_get_contents($url, false, $options);

                        $json_array[] = [];
                        $json_array   = json_decode($result, true);

                        if (isset($json_array) && is_array($json_array) && array_key_exists('sent', $json_array)) {
                            if ($json_array['sent'] == true) {
                                $get_sms_status = 'Success|'.$json_array['queueNumber'];
                            } else {
                                $get_sms_status = $json_array['message'];
                            }
                        } else {
                            $get_sms_status = 'Invalid request';
                        }

                    } catch (Exception $ex) {
                        $get_sms_status = $ex->getMessage();
                    }

                    break;

                case 'WaApi':

                    break;

                default:
                    $get_sms_status = __('locale.sending_servers.sending_server_not_found');
                    break;
            }
        }


        $reportsData = [
                'user_id'           => $data['user_id'],
                'to'                => $phone,
                'message'           => $message,
                'sms_type'          => 'whatsapp',
                'status'            => $get_sms_status,
                'cost'              => $data['cost'],
                'sending_server_id' => $sending_server->id,
        ];

        if (isset($data['sender_id'])) {
            $reportsData['from'] = $data['sender_id'];
        }

        if (isset($data['campaign_id'])) {
            $reportsData['campaign_id'] = $data['campaign_id'];
        }

        if (isset($data['api_key'])) {
            $reportsData['api_key'] = $data['api_key'];
            $reportsData['send_by'] = 'api';
        } else {
            $reportsData['send_by'] = 'from';
        }

        $status = Reports::create($reportsData);

        if ($status) {
            return $status;
        }

        return __('locale.exceptions.something_went_wrong');

    }
}
