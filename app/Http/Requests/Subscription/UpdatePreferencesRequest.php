<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferencesRequest extends FormRequest
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
                'credit'               => 'required_if:credit_warning,true|numeric',
                'credit_notify'        => 'required_if:credit_warning,true|string|max:5',
                'end_period_last_days' => 'required_if:subscription_warning,true|numeric',
                'subscription_notify'  => 'required_if:subscription_warning,true|string|max:5',
        ];
    }
}
