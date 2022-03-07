<?php

namespace App\Http\Requests\Contacts;

use App\Rules\Phone;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContact extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update_contact');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
                'phone' => ['required', new Phone($this->phone)],
                'contact_id' => 'required',
        ];
    }
}
