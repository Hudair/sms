<?php

namespace App\Http\Requests\SenderID;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomSenderID extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create_sender_id');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $user_id   = Auth::user()->id;
        $sender_id = $this->sender_id;

        return [
                'sender_id' => ['required',
                        Rule::unique('senderid')->where(function ($query) use ($user_id, $sender_id) {
                            return $query->where('user_id', $user_id)->where('sender_id', $sender_id);
                        })],
                'plan'      => 'required|exists:senderid_plans,id',
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
