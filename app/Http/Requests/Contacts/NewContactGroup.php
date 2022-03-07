<?php

namespace App\Http\Requests\Contacts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property mixed name
 */
class NewContactGroup extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create_contact_group');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $customer_id = auth()->user()->id;
        $name        = $this->name;

        return [
                'name' => ['required',
                        Rule::unique('contact_groups')->where(function ($query) use ($customer_id, $name) {
                            return $query->where('customer_id', $customer_id)->where('name', $name);
                        })],
        ];
    }

    /**
     * custom message
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
                'name.unique' => __('locale.contacts.contact_group_available', ['name' => $this->name]),
        ];
    }
}
