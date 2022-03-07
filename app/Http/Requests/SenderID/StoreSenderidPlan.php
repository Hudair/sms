<?php

namespace App\Http\Requests\SenderID;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed user_id
 * @property mixed sender_id
 */
class StoreSenderidPlan extends FormRequest
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

        return [
                'price'            => 'required|min:0|max:12',
                'currency_id'      => 'required',
                'billing_cycle'    => 'required|string',
                'frequency_amount' => 'required_if:billing_cycle,=,custom|nullable|numeric',
                'frequency_unit'   => 'required_if:billing_cycle,=,custom|nullable|string',
        ];
    }

}
