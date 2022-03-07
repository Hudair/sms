<?php

    namespace App\Http\Requests\Blacklists;

    use Illuminate\Foundation\Http\FormRequest;

    class StoreBlacklist extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         *
         * @return bool
         */
        public function authorize(): bool
        {
            return $this->user()->can('create blacklist') || $this->user()->can('create_blacklist');
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules(): array
        {
            return [
                'number'    => 'required',
                'delimiter' => 'required',
            ];
        }

    }
