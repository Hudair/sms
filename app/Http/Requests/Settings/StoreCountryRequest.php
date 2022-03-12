<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class StoreCountryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('general settings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
                'name' => 'required|unique:countries,name',
                'iso_code' => 'required|min:2|max:2|unique:countries,iso_code',
                'country_code' => 'required|min:1|max:3',
                'status' => 'required|boolean'
        ];
    }
}
