<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('authentication settings');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
                'client_registration'            => 'required|numeric',
                'registration_verification'      => 'required|numeric',
                'captcha_in_login'               => 'required|numeric',
                'captcha_in_client_registration' => 'required|numeric',
                'captcha_site_key'               => 'required_if:captcha_in_login,1|required_if:captcha_in_client_registration,1',
                'captcha_secret_key'             => 'required_if:captcha_in_login,1|required_if:captcha_in_client_registration,1',
                'two_factor'                     => 'required|numeric',
                'two_factor_send_by'             => 'required_if:two_factor,1',
                'login_with_facebook'            => 'required|numeric',
                'facebook_client_id'             => 'required_if:login_with_facebook,1',
                'facebook_client_secret'         => 'required_if:login_with_facebook,1',
                'login_with_twitter'             => 'required|numeric',
                'twitter_client_id'              => 'required_if:login_with_twitter,1',
                'twitter_client_secret'          => 'required_if:login_with_twitter,1',
                'login_with_google'              => 'required|numeric',
                'google_client_id'               => 'required_if:login_with_google,1',
                'google_client_secret'           => 'required_if:login_with_google,1',
                'login_with_github'              => 'required|numeric',
                'github_client_id'               => 'required_if:login_with_github,1',
                'github_client_secret'           => 'required_if:login_with_github,1',
        ];
    }

    public function messages(): array
    {
        return [
                'captcha_site_key.required_if'       => __('locale.settings.recaptcha_site_key_required'),
                'captcha_secret_key.required_if'     => __('locale.settings.recaptcha_secret_key_required'),
                'facebook_client_id.required_if'     => __('locale.settings.client_id_required', ['provider' => 'Facebook']),
                'facebook_client_secret.required_if' => __('locale.settings.client_id_required', ['provider' => 'Facebook']),
                'twitter_client_id.required_if'      => __('locale.settings.client_id_required', ['provider' => 'Twitter']),
                'twitter_client_secret.required_if'  => __('locale.settings.client_id_required', ['provider' => 'Twitter']),
                'google_client_id.required_if'       => __('locale.settings.client_id_required', ['provider' => 'Google']),
                'google_client_secret.required_if'   => __('locale.settings.client_id_required', ['provider' => 'Google']),
                'github_client_id.required_if'       => __('locale.settings.client_id_required', ['provider' => 'Github']),
                'github_client_secret.required_if'   => __('locale.settings.client_id_required', ['provider' => 'Github']),
        ];
    }
}
