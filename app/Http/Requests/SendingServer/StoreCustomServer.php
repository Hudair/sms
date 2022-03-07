<?php

namespace App\Http\Requests\SendingServer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomServer extends FormRequest
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
        return [
            'name'                         => 'required',
            'api_link'                     => 'required',
            'success_keyword'              => 'required',
            'http_request_method'          => 'required',
            'json_encoded_post'            => 'required',
            'content_type'                 => 'required',
            'content_type_accept'          => 'required',
            'character_encoding'           => 'required',
            'ssl_certificate_verification' => 'required',
            'authorization'                => 'required',
            'quota_value'                  => 'required|numeric',
            'quota_base'                   => 'required|numeric',
            'quota_unit'                   => 'required',
            'sms_per_request'              => 'required|numeric',
            'multi_sms_delimiter'          => 'required_if:sms_per_request,>,1|nullable|string',

            // Required query parameters
            'username_param'               => 'required',
            'username_value'               => 'required',
            'destination_param'            => 'required',
            'message_param'                => 'required',
        ];
    }

}
