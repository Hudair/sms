<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class StoreLanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('new languages');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'language' => 'required|min:2|max:2|unique:languages,code',
            'country' => 'required|min:2|max:2|unique:languages,iso_code',
            'status' => 'required|boolean'
        ];
    }
}
