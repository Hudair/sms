<?php

namespace App\Http\Requests\Campaigns;

use Illuminate\Foundation\Http\FormRequest;

class CampaignBuilderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('sms_campaign_builder');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
                'name'             => 'required',
                'recipients'       => 'required_if:contact_groups,null|nullable',
                'delimiter'        => 'required_if:recipients,true',
                'message'          => 'required',
                'schedule_date'    => 'required_if:schedule,true|date|nullable',
                'schedule_time'    => 'required_if:schedule,true|date_format:H:i',
                'timezone'         => 'required_if:schedule,true|timezone',
                'frequency_cycle'  => 'required_if:schedule,true',
                'frequency_amount' => 'required_if:frequency_cycle,custom|nullable|numeric',
                'frequency_unit'   => 'required_if:frequency_cycle,custom|nullable|string',
                'recurring_date'   => 'sometimes|date|nullable',
                'recurring_time'   => 'sometimes|date_format:H:i',
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
                'recipients.required_if' => __('locale.campaigns.contact_groups_required'),
        ];
    }

}
