<?php

namespace App\Http\Requests\ChatBox;

use App\Rules\Phone;
use Illuminate\Foundation\Http\FormRequest;

class SentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('chat_box');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
                'sender_id' => ['required', new Phone($this->sender_id)],
                'recipient' => ['required', new Phone($this->recipient)],
                'message'   => 'required',
        ];
    }
}
