<?php

namespace App\Http\Requests\Accounts;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
                'first_name'   => ['required', 'string', 'max:255'],
                'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::user()->id],
                'timezone'     => ['required', 'timezone'],
                'locale'       => ['required', 'string', 'min:2', 'max:2'],
        ];
    }
}
