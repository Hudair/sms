<?php

namespace App\Http\Requests\Settings;

use App\Models\PaymentMethods;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethods extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('update payment_gateways');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {

        $type = $this->input('type');

        $rules = [
                'name' => 'required',
        ];

        switch ($type) {
            case PaymentMethods::TYPE_PAYPAL:
                $rules['client_id']   = 'required';
                $rules['secret']      = 'required';
                $rules['environment'] = 'required';
                break;

            case PaymentMethods::TYPE_BRAINTREE:
                $rules['merchant_id'] = 'required';
                $rules['public_key']  = 'required';
                $rules['private_key'] = 'required';
                $rules['environment'] = 'required';
                break;

            case PaymentMethods::TYPE_STRIPE:
                $rules['publishable_key'] = 'required';
                $rules['secret_key']      = 'required';
                $rules['environment']     = 'required';
                break;

            case PaymentMethods::TYPE_AUTHORIZE_NET:
                $rules['login_id']        = 'required';
                $rules['transaction_key'] = 'required';
                $rules['environment']     = 'required';
                break;

            case PaymentMethods::TYPE_2CHECKOUT:
                $rules['merchant_code'] = 'required';
                $rules['private_key']   = 'required';
                $rules['environment']   = 'required';
                break;

            case PaymentMethods::TYPE_PAYSTACK:
                $rules['public_key']     = 'required';
                $rules['secret_key']     = 'required';
                $rules['merchant_email'] = 'required|email';
                break;

            case PaymentMethods::TYPE_PAYU:
                $rules['client_id']     = 'required';
                $rules['client_secret'] = 'required';
                break;

            case PaymentMethods::TYPE_SLYDEPAY:
                $rules['merchant_email']  = 'required|email';
                $rules['merchant_secret'] = 'required';
                break;

            case PaymentMethods::TYPE_PAYNOW:
                $rules['integration_id']  = 'required';
                $rules['integration_key'] = 'required';
                break;

            case PaymentMethods::TYPE_COINPAYMENTS:
                $rules['merchant_id'] = 'required';
                break;

            case PaymentMethods::TYPE_INSTAMOJO:
                $rules['api_key']    = 'required';
                $rules['auth_token'] = 'required';
                break;

            case PaymentMethods::TYPE_PAYUMONEY:
                $rules['merchant_key']  = 'required';
                $rules['merchant_salt'] = 'required';
                $rules['environment']   = 'required';
                break;

            case PaymentMethods::TYPE_RAZORPAY:
                $rules['key_id']      = 'required';
                $rules['key_secret']  = 'required';
                $rules['environment'] = 'required';
                break;

            case PaymentMethods::TYPE_SSLCOMMERZ:
                $rules['store_id']     = 'required';
                $rules['store_passwd'] = 'required';
                $rules['environment']  = 'required';
                break;

            case PaymentMethods::TYPE_AAMARPAY:
                $rules['store_id']      = 'required';
                $rules['signature_key'] = 'required';
                $rules['environment']   = 'required';
                break;

            case PaymentMethods::TYPE_CASH:
                $rules['payment_details']      = 'required';
                $rules['payment_confirmation'] = 'required';
                break;

        }

        return $rules;
    }
}
