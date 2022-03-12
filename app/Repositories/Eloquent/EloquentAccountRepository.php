<?php

namespace App\Repositories\Eloquent;

use App\Library\aamarPay;
use App\Library\CoinPayments;
use App\Library\Flutterwave;
use App\Library\PayU;
use App\Library\PayUMoney;
use App\Library\TwoCheckout;
use App\Models\Language;
use App\Models\Notifications;
use App\Models\PaymentMethods;
use App\Models\User;
use App\Models\SocialLogin;
use App\Notifications\TwoFactorCode;
use App\Repositories\Contracts\UserRepository;
use Braintree\Gateway;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use App\Exceptions\GeneralException;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Repositories\Contracts\AccountRepository;
use Illuminate\Support\Facades\Session;
use Paynow\Http\ConnectionException;
use Paynow\Payments\HashMismatchException;
use Paynow\Payments\InvalidIntegrationException;
use Paynow\Payments\Paynow;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\BadRequestError;
use Stripe\Stripe;
use Throwable;


/**
 * Class EloquentAccountRepository.
 */
class EloquentAccountRepository extends EloquentBaseRepository implements AccountRepository
{
    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * EloquentUserRepository constructor.
     *
     * @param  User  $user
     * @param  UserRepository  $users
     *
     * @internal param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(User $user, UserRepository $users)
    {
        parent::__construct($user);
        $this->users = $users;
    }

    /**
     * @param  array  $input
     *
     * @return User
     * @throws Exception
     *
     * @throws Throwable
     */
    public function register(array $input): User
    {
        // Registration is not enabled
        if ( ! config('account.can_register')) {
            throw new GeneralException(__('locale.exceptions.registration_disabled'));
        }

        $user = $this->users->store([
                'first_name'  => $input['first_name'],
                'last_name'   => $input['last_name'],
                'email'       => $input['email'],
                'password'    => $input['password'],
                'status'      => true,
                'phone'       => null,
                'is_customer' => true,
        ], true);

        //
        if (config('account.verify_account')) {
            $user->sendEmailVerificationNotification();
        }

        Notifications::create([
                'user_id'           => 1,
                'notification_for'  => 'admin',
                'notification_type' => 'user',
                'message'           => $user->displayName().' Registered',
        ]);

        \Auth::login($user, true);

        return $user;
    }

    /**
     *
     * get user data
     *
     * @param $provider
     * @param $data
     *
     * @return User|mixed
     * @throws GeneralException
     */
    public function findOrCreateSocial($provider, $data): User
    {
        // Email can be not provided, so set default provider email.
        $user_email = $data->getEmail() ?: $data->getId()."@".$provider.".com";

        // Get user with this email or create new one.
        /** @var User $user */
        $user = $this->users->query()->whereEmail($user_email)->first();

        if ( ! $user) {
            // Registration is not enabled
            if ( ! config('account.can_register')) {
                throw new GeneralException(__('locale.exceptions.registration_disabled'));
            }

            $last_name = null;

            if ($data->getName()) {
                $first_name = $data->getName();
                $last_name  = $data->getNickname();
            } else {
                $first_name = $data->getNickname();
            }

            $user = $this->users->store([
                    'first_name'  => $first_name,
                    'last_name'   => $last_name,
                    'email'       => $user_email,
                    'status'      => true,
                    'phone'       => null,
                    'is_customer' => true,
            ], true);

        }
        if ($user) {
            $user->provider    = $provider;
            $user->provider_id = $data->getId();
            $user->image       = $data->getAvatar();
            $user->save();
        }

        return $user;


    }

    /**
     * @param  Authenticatable  $user
     * @param $name
     *
     * @return bool
     */
    public function hasPermission(Authenticatable $user, $name): bool
    {

        /** @var User $user */
        // First user is always super admin and cannot be deleted
        if ($user->id === 1) {
            return true;
        }

        $permissions = Session::get('permissions');

        if ($permissions == null && $user->is_customer) {
            $permissions = collect(json_decode($user->customer->permissions, true));
        }

        if ($permissions->isEmpty()) {
            return false;
        }

        return $permissions->contains($name);
    }

    /**
     * @param  array  $input
     *
     * @return JsonResponse
     *
     */
    public function update(array $input): JsonResponse
    {

        $availLocale = Session::get('availableLocale');

        if ( ! isset($availLocale)) {
            $availLocale = Language::where('status', 1)->select('code')->cursor()->map(function ($name) {
                return $name->code;
            })->toArray();

            session()->put('availableLocale', $availLocale);
        }

        // check for existing language
        if (in_array($input['locale'], $availLocale)) {
            session()->put('locale', $input['locale']);
        }

        /** @var User $user */
        $user = auth()->user();
        $user->fill(Arr::only($input, ['first_name', 'last_name', 'email', 'locale', 'timezone', 'password']));
        $user->save();

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.customer.profile_was_successfully_updated'),
        ]);
    }

    /**
     * @return mixed
     * @throws GeneralException|Exception
     *
     */
    public function delete(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->is_super_admin) {
            throw new GeneralException(__('exceptions.backend.users.first_user_cannot_be_destroyed'));
        }

        if ( ! $user->delete()) {
            throw new GeneralException(__('exceptions.frontend.user.delete_account'));
        }

        return true;
    }

    /**
     * @param  Authenticatable  $user
     *
     * @return Authenticatable
     * @throws GeneralException
     */

    public function redirectAfterLogin(Authenticatable $user): Authenticatable
    {
        if (config('app.two_factor') === false || $user->two_factor == 0 || Session::get('two-factor-login-success') == 'success' || config('app.env') == 'demo') {
            $user->last_access_at = Carbon::now();
            if ($user->is_admin === true) {
                $user->active_portal = 'admin';
                session(['permissions' => $user->getPermissions()]);
            } else {
                $user->active_portal = 'customer';
                $permissions         = collect(json_decode($user->customer->permissions, true));
                session(['permissions' => $permissions]);
            }

            if ( ! $user->save()) {
                throw new GeneralException('Something went wrong. Please try again.');
            }

            return $user;
        }

        if (config('app.two_factor') && $user->two_factor && config('app.env') != 'demo') {
            $user->generateTwoFactorCode();
            $user->notify(new TwoFactorCode());
        }

        return $user;
    }

    /**
     * @param  array  $input
     *
     * @return JsonResponse
     */
    public function payPayment(array $input): JsonResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', $input['payment_methods'])->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            $item_name     = 'Top up sms unit';
            $price         = $input['sms_unit'] * auth()->user()->customer->subscription->plan->getOption('per_unit_price');
            $currency_code = auth()->user()->customer->subscription->plan->currency->code;


            switch ($paymentMethod->type) {

                case 'paypal':
                    $environment = new SandboxEnvironment($credentials->client_id, $credentials->secret);

                    $client = new PayPalHttpClient($environment);

                    $request = new OrdersCreateRequest();
                    $request->prefer('return=representation');

                    $request->body = [
                            "intent"              => "CAPTURE",
                            "purchase_units"      => [[
                                    "reference_id" => auth()->user()->id.'_'.$input['sms_unit'],
                                    'description'  => $item_name,
                                    "amount"       => [
                                            "value"         => $price,
                                            "currency_code" => $currency_code,
                                    ],
                            ]],
                            "application_context" => [
                                    'brand_name' => config('app.name'),
                                    'locale'     => config('app.locale'),
                                    "cancel_url" => route('customer.top_up.payment_cancel'),
                                    "return_url" => route('customer.top_up.payment_success', ['user_id' => auth()->user()->id, 'sms_unit' => $input['sms_unit']]),
                            ],
                    ];

                    try {
                        $response = $client->execute($request);

                        if (isset($response->result->links)) {
                            foreach ($response->result->links as $link) {
                                if ($link->rel == 'approve') {
                                    $redirect_url = $link->href;
                                    break;
                                }
                            }
                        }

                        if (isset($redirect_url)) {
                            if ( ! empty($response->result->id)) {
                                Session::put('payment_method', $paymentMethod->type);
                                Session::put('paypal_payment_id', $response->result->id);
                                Session::put('price', $price);
                            }

                            return response()->json([
                                    'status'       => 'success',
                                    'redirect_url' => $redirect_url,
                            ]);
                        }

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);


                    } catch (Exception $exception) {
                        return response()->json([
                                'status'  => 'error',
                                'message' => $exception->getMessage(),
                        ]);
                    }

                case 'braintree':

                    try {
                        $gateway = new Gateway([
                                'environment' => $credentials->environment,
                                'merchantId'  => $credentials->merchant_id,
                                'publicKey'   => $credentials->public_key,
                                'privateKey'  => $credentials->private_key,
                        ]);

                        $clientToken = $gateway->clientToken()->generate();

                        return response()->json([
                                'status' => 'success',
                                'token'  => $clientToken,
                        ]);
                    } catch (Exception $exception) {
                        return response()->json([
                                'status'  => 'error',
                                'message' => $exception->getMessage(),
                        ]);
                    }

                case 'stripe':

                    $publishable_key = $credentials->publishable_key;
                    $secret_key      = $credentials->secret_key;

                    Stripe::setApiKey($secret_key);

                    try {
                        $checkout_session = \Stripe\Checkout\Session::create([
                                'payment_method_types' => ['card'],
                                'customer_email'       => $input['email'],
                                'line_items'           => [[
                                        'price_data' => [
                                                'currency'     => $currency_code,
                                                'unit_amount'  => $price * 100,
                                                'product_data' => [
                                                        'name' => $item_name,
                                                ],
                                        ],
                                        'quantity'   => 1,
                                ]],
                                'mode'                 => 'payment',
                                'success_url'          => route('customer.top_up.payment_success', ['user_id' => auth()->user()->id, 'sms_unit' => $input['sms_unit']]),
                                'cancel_url'           => route('customer.top_up.payment_cancel'),
                        ]);

                        if ( ! empty($checkout_session->id)) {
                            Session::put('payment_method', $paymentMethod->type);
                            Session::put('session_id', $checkout_session->id);
                        }

                        return response()->json([
                                'status'          => 'success',
                                'session_id'      => $checkout_session->id,
                                'publishable_key' => $publishable_key,
                        ]);

                    } catch (Exception $exception) {

                        return response()->json([
                                'status'  => 'error',
                                'message' => $exception->getMessage(),
                        ]);

                    }

                case 'authorize_net':
                    return response()->json([
                            'status'      => 'success',
                            'credentials' => $credentials,
                    ]);

                case '2checkout':

                    Session::put('payment_method', $paymentMethod->type);
                    Session::put('price', $price);

                    $checkout = new TwoCheckout();

                    $checkout->param('sid', $credentials->merchant_code);
                    if ($credentials->environment == 'sandbox') {
                        $checkout->param('demo', 'Y');
                    }
                    $checkout->param('return_url', route('customer.top_up.payment_success', ['user_id' => auth()->user()->id, 'sms_unit' => $input['sms_unit']]));
                    $checkout->param('li_0_name', $item_name);
                    $checkout->param('li_0_price', $price);
                    $checkout->param('li_0_quantity', 1);
                    $checkout->param('card_holder_name', $input['first_name'].' '.$input['last_name']);
                    $checkout->param('city', $input['city']);
                    $checkout->param('country', $input['country']);
                    $checkout->param('email', $input['email']);
                    $checkout->param('phone', $input['phone']);
                    $checkout->param('currency_code', $currency_code);
                    $checkout->gw_submit();
                    exit();

                case 'paystack':

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                            CURLOPT_URL            => "https://api.paystack.co/transaction/initialize",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST  => "POST",
                            CURLOPT_POSTFIELDS     => json_encode([
                                    'amount'   => $price * 100,
                                    'email'    => $input['email'],
                                    'metadata' => [
                                            'sms_unit'     => $input['sms_unit'],
                                            'user_id'      => auth()->user()->id,
                                            'request_type' => 'top_up_payment',
                                    ],
                            ]),
                            CURLOPT_HTTPHEADER     => [
                                    "authorization: Bearer ".$credentials->secret_key,
                                    "content-type: application/json",
                                    "cache-control: no-cache",
                            ],
                    ]);

                    $response = curl_exec($curl);
                    $err      = curl_error($curl);

                    curl_close($curl);

                    if ($response === false) {
                        return response()->json([
                                'status'  => 'error',
                                'message' => 'Php curl show false value. Please contact with your provider',
                        ]);
                    }

                    if ($err) {
                        return response()->json([
                                'status'  => 'error',
                                'message' => $err,
                        ]);
                    }

                    $result = json_decode($response);


                    if ($result->status != 1) {

                        return response()->json([
                                'status'  => 'error',
                                'message' => $result->message,
                        ]);
                    }


                    return response()->json([
                            'status'       => 'success',
                            'redirect_url' => $result->data->authorization_url,
                    ]);

                case 'payu':

                    Session::put('payment_method', $paymentMethod->type);
                    Session::put('price', $price);

                    $sms_unit = $input['sms_unit'];

                    $signature = "$credentials->client_secret~$credentials->client_id~smsunit$sms_unit~$price~$currency_code";
                    $signature = md5($signature);

                    $payu = new PayU();

                    $payu->param('merchantId', $credentials->client_id);
                    $payu->param('ApiKey', $credentials->client_secret);
                    $payu->param('referenceCode', 'smsunit'.$sms_unit);
                    $payu->param('description', $item_name);
                    $payu->param('amount', $price);
                    $payu->param('currency', $currency_code);
                    $payu->param('buyerEmail', $input['email']);
                    $payu->param('signature', $signature);
                    $payu->param('confirmationUrl', route('customer.top_up.payment_success', ['user_id' => auth()->user()->id, 'sms_unit' => $input['sms_unit']]));
                    $payu->param('responseUrl', route('customer.top_up.payment_cancel'));
                    $payu->gw_submit();

                    exit();

                case 'paynow':

                    $paynow = new Paynow(
                            $credentials->integration_id,
                            $credentials->integration_key,
                            route('customer.callback.paynow'),
                            route('customer.top_up.payment_success', ['user_id' => auth()->user()->id, 'sms_unit' => $input['sms_unit']])
                    );


                    $payment = $paynow->createPayment($input['sms_unit'], $input['email']);
                    $payment->add($item_name, $price);


                    try {
                        $response = $paynow->send($payment);

                        if ($response->success()) {

                            Session::put('payment_method', $paymentMethod->type);
                            Session::put('paynow_poll_url', $response->pollUrl());

                            return response()->json([
                                    'status'       => 'success',
                                    'redirect_url' => $response->redirectUrl(),
                            ]);
                        }

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);


                    } catch (ConnectionException | HashMismatchException | InvalidIntegrationException | Exception $e) {
                        return response()->json([
                                'status'  => 'error',
                                'message' => $e->getMessage(),
                        ]);
                    }

                case 'coinpayments':

                    Session::put('payment_method', $paymentMethod->type);
                    Session::put('price', $price);

                    $coinPayment = new CoinPayments();

                    $order = [
                            'merchant'    => $credentials->merchant_id,
                            'item_name'   => $item_name,
                            'amountf'     => $price,
                            'currency'    => $currency_code,
                            'success_url' => route('customer.top_up.payment_success', ['user_id' => auth()->user()->id, 'sms_unit' => $input['sms_unit']]),
                            'cancel_url'  => route('customer.top_up.payment_cancel'),
                    ];

                    foreach ($order as $item => $value) {
                        $coinPayment->param($item, $value);
                    }

                    $coinPayment->gw_submit();

                    exit();

                case 'instamojo':

                    $name = $input['first_name'];
                    if (isset($input['last_name'])) {
                        $name .= ' '.$input['last_name'];
                    }

                    $payload = [
                            'purpose'                 => $item_name,
                            'amount'                  => $price,
                            'phone'                   => $input['phone'],
                            'buyer_name'              => $name,
                            'redirect_url'            => route('customer.top_up.payment_success', ['user_id' => auth()->user()->id, 'sms_unit' => $input['sms_unit']]),
                            'send_email'              => true,
                            'email'                   => $input['email'],
                            'allow_repeated_payments' => false,
                    ];

                    $headers = [
                            "X-Api-Key:".$credentials->api_key,
                            "X-Auth-Token:".$credentials->auth_token,
                    ];

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payment-requests/');
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
                    $response = curl_exec($ch);
                    curl_close($ch);

                    if (isset($response) && isset($response->success)) {
                        if ($response->success == true) {

                            Session::put('payment_method', $paymentMethod->type);
                            Session::put('payment_request_id', $response->payment_request->id);

                            return response()->json([
                                    'status'       => 'success',
                                    'redirect_url' => $response->payment_request->longurl,
                            ]);
                        }

                        return response()->json([
                                'status'  => 'error',
                                'message' => $response->message,
                        ]);

                    }

                    return response()->json([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                case 'payumoney':

                    Session::put('payment_method', $paymentMethod->type);

                    $environment = $credentials->environment;
                    $txnid       = substr(hash('sha256', mt_rand().microtime()), 0, 20);
                    $pinfo       = $item_name;
                    $hash        = strtolower(hash('sha512', $credentials->merchant_key.'|'.$txnid.'|'.$price.'|'.$pinfo.'|'.$input['first_name'].'|'.$input['email'].'||||||||||||'.$credentials->merchant_salt));

                    $payumoney = new PayUMoney($environment);

                    $payumoney->param('key', $credentials->merchant_key);
                    $payumoney->param('amount', $price);
                    $payumoney->param('hash', $hash);
                    $payumoney->param('txnid', $txnid);
                    $payumoney->param('firstname', $input['first_name']);
                    $payumoney->param('email', $input['email']);
                    $payumoney->param('phone', $input['phone']);
                    $payumoney->param('productinfo', $pinfo);
                    $payumoney->param('surl', route('customer.top_up.payment_success', ['user_id' => auth()->user()->id, 'sms_unit' => $input['sms_unit']]));
                    $payumoney->param('furl', route('customer.top_up.payment_cancel'));

                    if (isset($input['last_name'])) {
                        $payumoney->param('lastname', $input['last_name']);
                    }

                    if (isset($input['address'])) {
                        $payumoney->param('address1', $input['address']);
                    }

                    if (isset($input['city'])) {
                        $payumoney->param('city', $input['city']);
                    }
                    if (isset($input['country'])) {
                        $payumoney->param('country', $input['country']);
                    }

                    $payumoney->gw_submit();

                    exit();

                case 'razorpay':

                    try {
                        $api = new Api($credentials->key_id, $credentials->key_secret);

                        $link = $api->invoice->create([
                                'type'        => 'link',
                                'amount'      => $price * 100,
                                'description' => $item_name,
                                'customer'    => [
                                        'email' => $input['email'],
                                ],
                        ]);


                        if (isset($link->id) && isset($link->short_url)) {

                            Session::put('razorpay_order_id', $link->order_id);
                            Session::put('user_id', auth()->user()->id);
                            Session::put('sms_unit', $input['sms_unit']);
                            Session::put('price', $price);

                            return response()->json([
                                    'status'       => 'success',
                                    'redirect_url' => $link->short_url,
                            ]);
                        }

                        return response()->json([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);

                    } catch (BadRequestError $exception) {
                        return response()->json([
                                'status'  => 'error',
                                'message' => $exception->getMessage(),
                        ]);
                    }

                case 'sslcommerz':

                    $post_data                 = [];
                    $post_data['store_id']     = $credentials->store_id;
                    $post_data['store_passwd'] = $credentials->store_passwd;
                    $post_data['total_amount'] = $price;
                    $post_data['currency']     = $currency_code;
                    $post_data['tran_id']      = $input['sms_unit'];
                    $post_data['success_url']  = route('customer.callback.sslcommerz.top_up', ['user_id' => auth()->user()->id, 'sms_unit' => $input['sms_unit']]);
                    $post_data['fail_url']     = route('customer.callback.sslcommerz.top_up');
                    $post_data['cancel_url']   = route('customer.callback.sslcommerz.top_up');

                    $post_data['product_category'] = "subscriptions";
                    $post_data['emi_option']       = "0";

                    $post_data['cus_name']    = $input['first_name'];
                    $post_data['cus_email']   = $input['email'];
                    $post_data['cus_add1']    = $input['address'];
                    $post_data['cus_city']    = $input['city'];
                    $post_data['cus_country'] = $input['country'];
                    $post_data['cus_phone']   = $input["phone"];


                    if (isset($input['postcode'])) {
                        $post_data['cus_postcode'] = $input['postcode'];
                    }


                    $post_data['shipping_method'] = 'No';
                    $post_data['num_of_item']     = '1';


                    $post_data['cart']            = json_encode([
                            ["product" => $item_name, "amount" => $price],
                    ]);
                    $post_data['product_name']    = $item_name;
                    $post_data['product_profile'] = 'non-physical-goods';
                    $post_data['product_amount']  = $price;

                    if ($credentials->environment == 'sandbox') {
                        $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";
                    } else {
                        $direct_api_url = "https://securepay.sslcommerz.com/gwprocess/v4/api.php";
                    }

                    $handle = curl_init();
                    curl_setopt($handle, CURLOPT_URL, $direct_api_url);
                    curl_setopt($handle, CURLOPT_TIMEOUT, 30);
                    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
                    curl_setopt($handle, CURLOPT_POST, 1);
                    curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC

                    $content = curl_exec($handle);
                    $code    = curl_getinfo($handle, CURLINFO_HTTP_CODE);

                    if ($code == 200 && ! (curl_errno($handle))) {
                        curl_close($handle);
                        $response = json_decode($content, true);

                        if (isset($response['GatewayPageURL']) && $response['GatewayPageURL'] != "") {

                            return response()->json([
                                    'status'       => 'success',
                                    'redirect_url' => $response['GatewayPageURL'],
                            ]);

                        } else {
                            return response()->json([
                                    'status'  => 'error',
                                    'message' => $response['failedreason'],
                            ]);
                        }
                    } else {
                        curl_close($handle);

                        return response()->json([
                                'status'  => 'error',
                                'message' => 'FAILED TO CONNECT WITH SSLCOMMERZ API',
                        ]);
                    }

                case 'aamarpay':

                    Session::put('payment_method', $paymentMethod->type);

                    $checkout = new aamarPay($credentials->environment);

                    $checkout->param('store_id', $credentials->store_id);
                    $checkout->param('signature_key', $credentials->signature_key);
                    $checkout->param('desc', $item_name);
                    $checkout->param('amount', $price);
                    $checkout->param('currency', $currency_code);
                    $checkout->param('tran_id', $input['sms_unit']);
                    $checkout->param('success_url', route('customer.callback.aamarpay.top_up', ['user_id' => auth()->user()->id, 'sms_unit' => $input['sms_unit']]));
                    $checkout->param('fail_url', route('customer.callback.aamarpay.top_up'));
                    $checkout->param('cancel_url', route('customer.callback.aamarpay.top_up'));

                    $checkout->param('cus_name', $input['first_name']);
                    $checkout->param('cus_email', $input['email']);
                    $checkout->param('cus_add1', $input['address']);
                    $checkout->param('cus_add2', $input['address']);
                    $checkout->param('cus_city', $input['city']);
                    $checkout->param('cus_country', $input['country']);
                    $checkout->param('cus_phone', $input['phone']);
                    if (isset($input['postcode'])) {
                        $checkout->param('cus_postcode', $input['postcode']);
                    }

                    $checkout->gw_submit();
                    exit();

                case 'flutterwave':

                    Session::put('payment_method', $paymentMethod->type);
                    Session::put('price', $price);

                    $checkout = new Flutterwave();

                    $checkout->param('public_key', $credentials->public_key);
                    $checkout->param('amount', $price);
                    $checkout->param('currency', $currency_code);
                    $checkout->param('tx_ref', str_random(10));
                    $checkout->param('redirect_url', route('customer.callback.flutterwave.top_up', ['user_id' => auth()->user()->id, 'sms_unit' => $input['sms_unit']]));
                    $checkout->param('customizations[title]', $item_name);
                    $checkout->param('customizations[description]', $item_name);
                    $checkout->param('customer[name]', $input['first_name'].' '.$input['last_name']);
                    $checkout->param('customer[email]', $input['email']);
                    $checkout->param('customer[phone_number]', $input['phone']);
                    $checkout->param('meta[user_id]', auth()->user()->id);
                    $checkout->param('meta[sms_unit]', $input['sms_unit']);
                    $checkout->gw_submit();
                    exit();

                case 'offline_payment':

                    return response()->json([
                            'status' => 'success',
                            'data'   => $credentials,
                    ]);
            }

            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.payment_gateways.not_found'),
            ]);
        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }
}
