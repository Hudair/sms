<?php

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit currencies');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $currency = $this->route('currency');

        return [
            'name'   => 'required',
            'code'   => 'required|alpha|size:3|unique:currencies,code,' . $currency->id,
            'format' => 'required',
        ];
    }
}
