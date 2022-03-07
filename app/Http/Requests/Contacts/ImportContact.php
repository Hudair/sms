<?php

namespace App\Http\Requests\Contacts;

use Illuminate\Foundation\Http\FormRequest;

class ImportContact extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create_contact');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
                'option_toggle' => 'sometimes|required',
                'import_file'   => 'required_if:option_toggle,on|mimes:csv,txt',
                'delimiter'   => 'required_with:recipients',
        ];
    }
}
