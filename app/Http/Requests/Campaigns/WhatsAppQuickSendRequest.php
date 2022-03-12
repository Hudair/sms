<?php

namespace App\Http\Requests\Campaigns;

use App\Rules\Phone;
use Illuminate\Foundation\Http\FormRequest;

class WhatsAppQuickSendRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('whatsapp_quick_send');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
                'recipient'      => ['required', new Phone($this->recipient)],
                'sending_server' => 'required|exists:plans_sending_servers,sending_server_id',
                'country_code'   => 'required|exists:countries,id',
                'message'   => 'required',
        ];
    }
}
