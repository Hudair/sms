<?php

namespace App\Http\Requests\SenderID;

use App\Rules\Phone;
use Illuminate\Foundation\Http\FormRequest;

class PayPaymentRequest extends FormRequest
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
        return [
                'first_name'      => ['required', 'string', 'max:255'],
                'phone'           => ['required', new Phone($this->phone)],
                'email'           => ['required', 'string', 'email', 'max:255'],
                'address'         => ['required', 'string'],
                'city'            => ['required'],
                'country'         => ['required'],
                'payment_methods' => ['required'],
        ];
    }
}
