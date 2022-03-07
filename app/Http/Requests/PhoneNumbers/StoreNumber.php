<?php

namespace App\Http\Requests\PhoneNumbers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNumber extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create phone_numbers');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $user_id = $this->user_id;
        $number  = $this->number;

        return [
                'number'       => ['required',
                        Rule::unique('phone_numbers')->where(function ($query) use ($user_id, $number) {
                            return $query->where('user_id', $user_id)->where('number', $number);
                        })],
                'user_id'      => 'required|integer',
                'price'        => 'required|min:0|max:12',
                'status'       => 'required|string',
                'capabilities' => 'required|array|min:1',
                'capabilities.*' => 'required|string|distinct|min:3',
                'currency_id'  => 'required',
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
                'number.unique' => __('locale.phone_numbers.number_available', ['number' => $this->number]),
        ];
    }
}
