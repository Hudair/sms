<?php

namespace App\Http\Requests\Campaigns;

use App\Rules\Phone;
use Illuminate\Foundation\Http\FormRequest;

class QuickSendRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('sms_quick_send');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
                'recipient' => ['required', new Phone($this->recipient)],
                'message'   => 'required',
        ];
    }
}
