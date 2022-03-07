<?php

namespace App\Http\Requests\Keywords;

use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update_keywords');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
                'reply_text' => 'required_without_all:reply_voice,reply_mms|nullable|string',
                'reply_mms'  => 'sometimes|required|image',
        ];
    }
}
