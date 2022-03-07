<?php

namespace App\Http\Requests\Administrator;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdministrator extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit administrator');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $admin = $this->route('administrator');

        return [
                'first_name' => ['required', 'string', 'max:255'],
                'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$admin->id],
                'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
                'roles'      => ['required'],
                'timezone'   => ['required', 'timezone'],
                'locale'     => ['required', 'string', 'min:2', 'max:2'],
                'image'      => ['sometimes', 'required', 'image'],
        ];
    }
}
