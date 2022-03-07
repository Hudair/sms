<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethods;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('payment_methods')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $payment_gateways = [
                [
                        'name'    => 'PayPal',
                        'type'    => 'paypal',
                        'options' => json_encode([
                                'client_id'   => 'AfXkbWwSG5T3dTN2n4HWEJi7AQ54X3CcVHyo0sMPcWHvKBL9ujQvmMOiivrd65PFz1a2pBFlS6xF1QE2',
                                'secret'      => 'ENLQxtqjzZOVGQFUosjjWUTje7JXqgU6CODBIheqEv7Mj8p7BuSTyBOlf8PW4sWaL5lZ-mitEahCqxAC',
                                'environment' => 'sandbox',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'Braintree',
                        'type'    => 'braintree',
                        'options' => json_encode([
                                'merchant_id' => 's999zxpkvhh6dpm2',
                                'public_key'  => 'hw4v45rty67jdxc9',
                                'private_key' => 'cac14c4d9d950e32c947b400b10a7596',
                                'environment' => 'sandbox',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'Stripe',
                        'type'    => 'stripe',
                        'options' => json_encode([
                                'publishable_key' => 'pk_test_AnS4Ov8GS92XmHeVCDRPIZF4',
                                'secret_key'      => 'sk_test_iS0xwfgzBF6cmPBBkgO13sjd',
                                'environment'     => 'sandbox',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'Authorize.net',
                        'type'    => 'authorize_net',
                        'options' => json_encode([
                                'login_id'        => 'login_id',
                                'transaction_key' => 'transaction_key',
                                'environment'     => 'sandbox',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => '2checkout',
                        'type'    => '2checkout',
                        'options' => json_encode([
                                'merchant_code' => 'merchant_code',
                                'private_key'   => 'private_key',
                                'environment'   => 'sandbox',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'Paystack',
                        'type'    => 'paystack',
                        'options' => json_encode([
                                'public_key'     => 'public_key',
                                'secret_key'     => 'secret_key',
                                'merchant_email' => 'merchant_email',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'PayU',
                        'type'    => 'payu',
                        'options' => json_encode([
                                'client_id'     => 'client_id',
                                'client_secret' => 'client_secret',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'Paynow',
                        'type'    => 'paynow',
                        'options' => json_encode([
                                'integration_id'  => 'integration_id',
                                'integration_key' => 'integration_key',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'CoinPayments',
                        'type'    => 'coinpayments',
                        'options' => json_encode([
                                'merchant_id' => 'merchant_id',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'Instamojo',
                        'type'    => 'instamojo',
                        'options' => json_encode([
                                'api_key'    => 'api_key',
                                'auth_token' => 'auth_token',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'PayUmoney',
                        'type'    => 'payumoney',
                        'options' => json_encode([
                                'merchant_key'  => 'merchant_key',
                                'merchant_salt' => 'merchant_salt',
                                'environment'   => 'sandbox',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'Razorpay',
                        'type'    => 'razorpay',
                        'options' => json_encode([
                                'key_id'      => 'key_id',
                                'key_secret'  => 'key_secret',
                                'environment' => 'sandbox',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'SSLcommerz',
                        'type'    => 'sslcommerz',
                        'options' => json_encode([
                                'store_id'     => 'store_id',
                                'store_passwd' => 'store_id@ssl',
                                'environment'  => 'sandbox',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'aamarPay',
                        'type'    => 'aamarpay',
                        'options' => json_encode([
                                'store_id'      => 'store_id',
                                'signature_key' => 'signature_key',
                                'environment'   => 'sandbox',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'Flutterwave',
                        'type'    => 'flutterwave',
                        'options' => json_encode([
                                'public_key'  => 'public_key',
                                'secret_key'  => 'secret_key',
                                'environment' => 'sandbox',
                        ]),
                        'status'  => true,
                ],
                [
                        'name'    => 'Offline Payment',
                        'type'    => 'offline_payment',
                        'options' => json_encode([
                                'payment_details'      => '<p>Please make a deposit to our bank account at:</p>
<h6>US BANK USA</h6>
<p>Routing (ABA): 045134400</p>
<p>Account number: 6216587467378</p>
<p>Beneficiary name: Ultimate sms</p>',
                                'payment_confirmation' => 'After payment please contact with following email address codeglen@gmail.com with your transaction id. Normally it may take 1 - 2 business days to process. Should you have any question, feel free contact with us.',
                        ]),
                        'status'  => true,
                ],
        ];

        foreach ($payment_gateways as $gateway) {
            PaymentMethods::create($gateway);
        }
    }

}
