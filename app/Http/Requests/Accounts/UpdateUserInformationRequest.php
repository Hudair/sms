<?php

namespace App\Http\Requests\Accounts;

use App\Rules\Phone;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserInformationRequest extends FormRequest
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
                'phone'   => ['required', 'numeric', new Phone($this->phone)],
                'website' => ['nullable', 'url'],
                'address' => ['required', 'string'],
                'city'    => ['required', 'string'],
                'country' => ['required', 'string'],
        ];
    }
}
