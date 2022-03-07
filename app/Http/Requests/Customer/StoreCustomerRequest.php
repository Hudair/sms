<?php

namespace App\Http\Requests\Customer;

use App\Rules\Phone;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create customer');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
                'first_name' => ['required', 'string', 'max:255'],
                'phone'      => ['required', new Phone($this->phone)],
                'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password'   => ['required', 'string', 'min:8', 'confirmed'],
                'timezone'   => ['required', 'timezone'],
                'locale'     => ['required', 'string', 'min:2', 'max:2'],
                'status'     => ['required', 'boolean'],
                'image'      => ['sometimes', 'required', 'image'],
        ];
    }


}
