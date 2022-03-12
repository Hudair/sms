<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class ThemeCustomizerRequest extends FormRequest
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
                'mainLayoutType'    => 'required',
                'theme'             => 'required',
                'navbarColor'       => 'required',
                'navbarType'        => 'required',
                'footerType'        => 'required',
                'layoutWidth'       => 'required',
        ];
    }
}
