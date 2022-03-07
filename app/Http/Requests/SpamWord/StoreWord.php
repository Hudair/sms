<?php

namespace App\Http\Requests\SpamWord;

use Illuminate\Foundation\Http\FormRequest;

class StoreWord extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('create spam_word');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'word'    => 'required|unique:spam_word|max:100',
        ];
    }

}
