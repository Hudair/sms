<?php

namespace App\Http\Requests\SenderID;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property mixed user_id
 * @property mixed sender_id
 */
class StoreSenderidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create sender_id');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $user_id   = $this->user_id;
        $sender_id = $this->sender_id;

        return [
                'sender_id'   => ['required',
                        Rule::unique('senderid')->where(function ($query) use ($user_id, $sender_id) {
                            return $query->where('user_id', $user_id)->where('sender_id', $sender_id);
                        })],
                'user_id'     => 'required|integer',
                'price'       => 'required|min:0|max:12',
                'status'      => 'required|string',
                'currency_id' => 'required',
                'billing_cycle'    => 'required|string',
                'frequency_amount' => 'required_if:billing_cycle,=,custom|nullable|numeric',
                'frequency_unit'   => 'required_if:billing_cycle,=,custom|nullable|string',
        ];
    }

    /**
     * custom message
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
                'sender_id.unique' => __('locale.sender_id.sender_id_available', ['sender_id' => $this->sender_id]),
        ];
    }
}
