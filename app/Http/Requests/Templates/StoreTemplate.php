<?php

namespace App\Http\Requests\Templates;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreTemplate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('sms_template');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $user_id = Auth::user()->id;
        $name    = $this->name;

        return [
                'name'    => ['required',
                        Rule::unique('templates')->where(function ($query) use ($user_id, $name) {
                            return $query->where('user_id', $user_id)->where('name', $name);
                        })],
                'message' => 'required',
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
                'name.unique' => __('locale.templates.template_available', ['template' => $this->name]),
        ];
    }
}
