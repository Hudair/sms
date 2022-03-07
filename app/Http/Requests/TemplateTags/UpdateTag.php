<?php

namespace App\Http\Requests\TemplateTags;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTag extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit tags');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {

        $id   = $this->route('tag')->id;
        $name = $this->name;

        return [
                'name'     => ['required',
                        Rule::unique('template_tags')->where(function ($query) use ($name) {
                            return $query->where('name', $name);
                        })->ignore($id)],
                'type'     => 'required|string|min:3|max:6',
                'required' => 'required|string',
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
                'name.unique' => __('locale.template_tags.template_tag_available', ['template_tag' => $this->name]),
        ];
    }
}
