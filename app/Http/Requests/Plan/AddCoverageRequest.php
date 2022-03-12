<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class AddCoverageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage plans');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {

        return [
                'country'              => 'required',
                'plain_sms'            => 'required|integer|min:0',
                'receive_plain_sms'    => 'required|integer|min:0',
                'voice_sms'            => 'required|integer|min:0',
                'receive_voice_sms'    => 'required|integer|min:0',
                'mms_sms'              => 'required|integer|min:0',
                'receive_mms_sms'      => 'required|integer|min:0',
                'whatsapp_sms'         => 'required|integer|min:0',
                'receive_whatsapp_sms' => 'required|integer|min:0',
        ];
    }
}
