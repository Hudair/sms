<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
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
        return [
            "permissions" => "required|array",
            'permissions.access_backend' => 'required',
        ];
    }

    /**
     * custom message
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'permissions.access_backend.required' => __('locale.permission.access_backend_permission_required'),
        ];
    }

}
