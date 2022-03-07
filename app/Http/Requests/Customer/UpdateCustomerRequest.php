<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit customer');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {

        $customer = $this->route('customer');

        return [
                'first_name' => ['required', 'string', 'max:255'],
                'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$customer->id],
                'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
                'timezone'   => ['required', 'timezone'],
                'locale'     => ['required', 'string', 'min:2', 'max:2'],
        ];
    }
}
