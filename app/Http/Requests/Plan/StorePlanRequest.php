<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
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
                'name'             => 'required',
                'price'            => 'required|numeric',
                'currency_id'      => 'required|numeric',
                'billing_cycle'    => 'required|string',
                'frequency_amount' => 'required_if:billing_cycle,=,custom|nullable|numeric',
                'frequency_unit'   => 'required_if:billing_cycle,=,custom|nullable|string',
        ];
    }
}
