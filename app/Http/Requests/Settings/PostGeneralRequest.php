<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class PostGeneralRequest extends FormRequest
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
                'app_name'        => 'required|max:12',
                'app_title'       => 'required',
                'company_address' => 'required',
                'footer_text'     => 'required',
                'app_logo'        => 'sometimes|required|image',
                'app_favicon'     => 'sometimes|required|mimes:jpeg,bmp,png,ico,jpg',
                'country'         => 'required',
                'timezone'        => 'required|timezone',
                'date_format'     => 'required',
                'language'        => 'required',
        ];
    }
}
