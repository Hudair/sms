<?php

namespace App\Http\Requests\SendingServer;

use Illuminate\Foundation\Http\FormRequest;

class StoreSendingServerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $type = $this->input('settings');

        $rules = [
                'name'            => 'required',
                'settings'        => 'required',
                'quota_value'     => 'required|numeric',
                'quota_base'      => 'required|numeric',
                'quota_unit'      => 'required',
                'sms_per_request' => 'required|numeric',
        ];

        switch ($type) {
            case 'Twilio':
            case 'TwilioCopilot':
                $rules['account_sid'] = 'required';
                $rules['auth_token']  = 'required';
                break;

            case 'Clickatell_Touch':
            case 'TextLocal':
            case 'MessageBird':
            case 'Tyntec':
            case 'Telnyx':
            case 'Infobip':
                $rules['api_link'] = 'required|url';
                $rules['api_key']  = 'required';
                break;

            case 'Clickatell_central':
                $rules['api_link'] = 'required|url';
                $rules['api_key']  = 'required';
                $rules['username'] = 'required';
                $rules['password'] = 'required';
                break;

            case 'RouteMobile':
            case 'SMSGlobal':
            case 'BulkSMS':
            case '1s2u':
                $rules['api_link'] = 'required|url';
                $rules['username'] = 'required';
                $rules['password'] = 'required';
                break;

            case 'msg91':
                $rules['api_link']     = 'required|url';
                $rules['auth_key']     = 'required';
                $rules['route']        = 'required';
                $rules['country_code'] = 'required';
                break;

            case 'Plivo':
            case 'PlivoPowerpack':
                $rules['auth_id']    = 'required';
                $rules['auth_token'] = 'required';
                break;

            case 'KarixIO':
                $rules['api_link']   = 'required|url';
                $rules['auth_id']    = 'required';
                $rules['auth_token'] = 'required';
                break;

            case 'Vonage':
                $rules['api_link']   = 'required|url';
                $rules['api_key']    = 'required';
                $rules['api_secret'] = 'required';
                break;

            case 'SmsGatewayMe':
                $rules['api_link']  = 'required|url';
                $rules['api_token'] = 'required';
                $rules['device_id'] = 'required';
                break;

            case 'AmazonSNS':
                $rules['access_key']    = 'required';
                $rules['secret_access'] = 'required';
                $rules['region']        = 'required';
                $rules['sms_type']      = 'required';
                break;

            case 'WhatsAppChatApi':
                $rules['api_link']  = 'required|url';
                $rules['api_token'] = 'required';
                break;

            case 'SignalWire':
                $rules['api_link']   = 'required|url';
                $rules['api_token']  = 'required';
                $rules['project_id'] = 'required';
                break;

            case 'Bandwidth':
                $rules['api_link']       = 'required|url';
                $rules['api_token']      = 'required';
                $rules['api_secret']     = 'required';
                $rules['application_id'] = 'required';
                break;

            case 'SMPP':
                $rules['api_link']        = 'required';
                $rules['username']        = 'required';
                $rules['password']        = 'required';
                $rules['port']            = 'required';
                $rules['source_addr_ton'] = 'required';
                $rules['source_addr_npi'] = 'required';
                $rules['dest_addr_ton']   = 'required';
                $rules['dest_addr_npi']   = 'required';
                break;

        }

        return $rules;
    }

}
