<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Library\aamarPay;
use App\Library\CoinPayments;
use App\Library\Flutterwave;
use App\Library\PayU;
use App\Library\PayUMoney;
use App\Library\TwoCheckout;
use App\Models\Invoices;
use App\Models\PaymentMethods;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use App\Repositories\Contracts\SubscriptionRepository;
use Auth;
use Braintree\Gateway;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Paynow\Http\ConnectionException;
use Paynow\Payments\HashMismatchException;
use Paynow\Payments\InvalidIntegrationException;
use Paynow\Payments\Paynow;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\BadRequestError;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Session;
use Throwable;

class EloquentSubscriptionRepository extends EloquentBaseRepository implements SubscriptionRepository
{
    /**
     * EloquentSubscriptionRepository constructor.
     *
     * @param  Subscription  $subscription
     */
    public function __construct(Subscription $subscription)
    {
        parent::__construct($subscription);
    }

    /**
     * @param  array  $input
     *
     * @return JsonResponse
     * @throws GeneralException
     */
    public function store(array $input): JsonResponse
    {

        $plan = Plan::find($input['plan_id']);

        if ( ! $plan) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.subscription.plan_not_found'),
            ]);
        }

        $user = User::where('status', true)->where('is_customer', true)->find($input['user_id']);

        if ( ! $user) {
            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.subscription.customer_not_found'),
            ]);
        }

        if ($user->customer->activeSubscription()) {
            $user->customer->activeSubscription()->cancelNow();
        }

        if ($user->customer->subscription) {
            $subscription = $user->customer->subscription;
        } else {
            $subscription           = new Subscription();
            $subscription->user_id  = $user->id;
            $subscription->start_at = Carbon::now();
        }

        $subscription->status                 = Subscription::STATUS_ACTIVE;
        $subscription->plan_id                = $plan->getBillableId();
        $subscription->end_period_last_days   = $input['end_period_last_days'];
        $subscription->current_period_ends_at = $subscription->getPeriodEndsAt(Carbon::now());
        $subscription->end_at                 = null;
        $subscription->end_by                 = null;

        if ( ! $this->save($subscription)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        // add transaction
        $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                'end_at'                 => $subscription->end_at,
                'current_period_ends_at' => $subscription->current_period_ends_at,
                'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                'title'                  => trans('locale.subscription.subscribed_to_plan', ['plan' => $subscription->plan->getBillableName()]),
                'amount'                 => $subscription->plan->getBillableFormattedPrice(),
        ]);

        // add log
        $subscription->addLog(SubscriptionLog::TYPE_ADMIN_PLAN_ASSIGNED, [
                'plan'  => $subscription->plan->getBillableName(),
                'price' => $subscription->plan->getBillableFormattedPrice(),
        ]);

        if ($user->sms_unit == null || $user->sms_unit == '-1' || $subscription->plan->getOption('sms_max') == '-1') {
            $user->sms_unit = $subscription->plan->getOption('sms_max');
        } else {
            if ($subscription->plan->getOption('add_previous_balance') == 'yes') {
                $user->sms_unit += $subscription->plan->getOption('sms_max');
            } else {
                $user->sms_unit = $subscription->plan->getOption('sms_max');
            }
        }


        $user->save();

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.subscription.subscription_successfully_added'),
        ]);


    }

    /**
     * @param  Subscription  $subscription
     *
     * @return bool
     */
    private function save(Subscription $subscription): bool
    {
        if ( ! $subscription->save()) {
            return false;
        }

        return true;
    }


    public function renew(Subscription $subscription)
    {
        // TODO: Implement renew() method.
    }

    /**
     * approve pending subscription
     *
     * @param  Subscription  $subscription
     *
     * @return bool|mixed
     */
    public function approvePending(Subscription $subscription): bool
    {
        //set active subscription
        $subscription->setActive();

        // add transaction
        $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                'end_at'                 => $subscription->end_at,
                'current_period_ends_at' => $subscription->current_period_ends_at,
                'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                'title'                  => trans('locale.subscription.subscribed_to_plan', ['plan' => $subscription->plan->getBillableName()]),
                'amount'                 => $subscription->plan->getBillableFormattedPrice(),
        ]);

        // add log
        $subscription->addLog(SubscriptionLog::TYPE_ADMIN_APPROVED, [
                'plan'  => $subscription->plan->getBillableName(),
                'price' => $subscription->plan->getBillableFormattedPrice(),
        ]);
        sleep(1);
        // add log
        $subscription->addLog(SubscriptionLog::TYPE_SUBSCRIBED, [
                'plan'  => $subscription->plan->getBillableName(),
                'price' => $subscription->plan->getBillableFormattedPrice(),
        ]);

        return true;
    }


    /**
     * reject pending subscription with reason
     *
     * @param  Subscription  $subscription
     * @param  array  $input
     *
     * @return bool|mixed
     */
    public function rejectPending(Subscription $subscription, array $input): bool
    {
        $subscription->setEnded(auth()->user()->id);

        $subscription->addLog(SubscriptionLog::TYPE_ADMIN_REJECTED, [
                'plan'   => $subscription->plan->getBillableName(),
                'price'  => $subscription->plan->getBillableFormattedPrice(),
                'reason' => $input['reason'],
        ]);

        return true;

    }

    public function changePlan(Subscription $subscription)
    {
        // TODO: Implement changePlan() method.
    }

    /**
     * @param  Subscription  $subscription
     *
     * @return bool
     * @throws Exception|Throwable
     *
     */
    public function destroy(Subscription $subscription)
    {
        if ( ! $subscription->delete()) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return true;
    }


    /**
     * @param  array  $ids
     *
     * @return mixed
     * @throws Exception|Throwable
     *
     */
    public function batchApprove(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['status' => true])
            ) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

    /**
     * @param  array  $ids
     *
     * @return mixed
     * @throws Exception|Throwable
     *
     */
    public function batchCancel(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)->update([
                    'status'                 => 'ended',
                    'end_by'                 => Auth::user()->id,
                    'current_period_ends_at' => Carbon::now(),
                    'end_at'                 => Carbon::now(),
            ])) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

    /**
     * pay payment
     *
     * @param  Plan  $plan
     * @param  Subscription  $subscription
     * @param  array  $input
     *
     * @return JsonResponse|mixed
     */
    public function payPayment(Plan $plan, Subscription $subscription, array $input): JsonResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', $input['payment_methods'])->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            $item_name = __('locale.subscription.payment_for_plan').' '.$plan->name;

            switch ($paymentMethod->type) {

                case 'paypal':
                    $environment = new SandboxEnvironment($credentials->client_id, $credentials->secret);

                    $client = new PayPalHttpClient($environment);

                    $request = new OrdersCreateRequest();
                    $request->prefer('return=representation');

                    $request->body = [
                            "intent"              => "CAPTURE",
                            "purchase_units"      => [[
                                    "reference_id" => auth()->user()->id.'_'.$plan->uid,
                                    'description'  => $item_name,
                                    "amount"       => [
                                            "value"         => $plan->price,
                                            "currency_code" => $plan->currency->code,
                                    ],
                            ]],
                            "application_context" => [
                                    'brand_name' => config('app.name'),
                                    'locale'     => config('app.locale'),
                                    "cancel_url" => route('customer.subscriptions.payment_cancel', $plan->uid),
                                    "return_url" => route('customer.subscriptions.payment_success', $plan->uid),
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
                                                'currency'     => $plan->currency->code,
                                                'unit_amount'  => $plan->price * 100,
                                                'product_data' => [
                                                        'name' => $item_name,
                                                ],
                                        ],
                                        'quantity'   => 1,
                                ]],
                                'mode'                 => 'payment',
                                'success_url'          => route('customer.subscriptions.payment_success', $plan->uid),
                                'cancel_url'           => route('customer.subscriptions.payment_cancel', $plan->uid),
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

                    $checkout = new TwoCheckout();

                    $checkout->param('sid', $credentials->merchant_code);
                    if ($credentials->environment == 'sandbox') {
                        $checkout->param('demo', 'Y');
                    }
                    $checkout->param('return_url', route('customer.subscriptions.payment_success', $plan->uid));
                    $checkout->param('li_0_name', $item_name);
                    $checkout->param('li_0_price', $plan->price);
                    $checkout->param('li_0_quantity', 1);
                    $checkout->param('card_holder_name', $input['first_name'].' '.$input['last_name']);
                    $checkout->param('city', $input['city']);
                    $checkout->param('country', $input['country']);
                    $checkout->param('email', $input['email']);
                    $checkout->param('phone', $input['phone']);
                    $checkout->param('currency_code', $plan->currency->code);
                    $checkout->gw_submit();
                    exit();

                case 'paystack':

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                            CURLOPT_URL            => "https://api.paystack.co/transaction/initialize",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST  => "POST",
                            CURLOPT_POSTFIELDS     => json_encode([
                                    'amount'   => $plan->price * 100,
                                    'email'    => $input['email'],
                                    'metadata' => [
                                            'plan_id'      => $plan->uid,
                                            'user_id'      => auth()->user()->id,
                                            'request_type' => 'subscription_payment',
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

                    $signature = "$credentials->client_secret~$credentials->client_id~subscription$plan->uid~$plan->price~$plan->currency->code";
                    $signature = md5($signature);

                    $payu = new PayU();

                    $payu->param('merchantId', $credentials->client_id);
                    $payu->param('ApiKey', $credentials->client_secret);
                    $payu->param('referenceCode', 'subscription'.$plan->uid);
                    $payu->param('description', $item_name);
                    $payu->param('amount', $plan->price);
                    $payu->param('currency', $plan->currency->code);
                    $payu->param('buyerEmail', $input['email']);
                    $payu->param('signature', $signature);
                    $payu->param('confirmationUrl', route('customer.subscriptions.payment_success', $plan->uid));
                    $payu->param('responseUrl', route('customer.subscriptions.payment_cancel', $plan->uid));
                    $payu->gw_submit();

                    exit();

                case 'paynow':

                    $paynow = new Paynow(
                            $credentials->integration_id,
                            $credentials->integration_key,
                            route('customer.callback.paynow'),
                            route('customer.subscriptions.payment_success', $plan->uid)
                    );


                    $payment = $paynow->createPayment($plan->uid, $input['email']);
                    $payment->add($item_name, $plan->price);


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


                    } catch (ConnectionException|HashMismatchException|InvalidIntegrationException|Exception $e) {
                        return response()->json([
                                'status'  => 'error',
                                'message' => $e->getMessage(),
                        ]);
                    }

                case 'coinpayments':

                    Session::put('payment_method', $paymentMethod->type);

                    $coinPayment = new CoinPayments();

                    $order = [
                            'merchant'    => $credentials->merchant_id,
                            'item_name'   => $item_name,
                            'amountf'     => $plan->price,
                            'currency'    => $plan->currency->code,
                            'success_url' => route('customer.subscriptions.payment_success', $plan->uid),
                            'cancel_url'  => route('customer.subscriptions.payment_cancel', $plan->uid),
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
                            'amount'                  => $plan->price,
                            'phone'                   => $input['phone'],
                            'buyer_name'              => $name,
                            'redirect_url'            => route('customer.subscriptions.payment_success', $plan->uid),
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
                    $hash        = strtolower(hash('sha512', $credentials->merchant_key.'|'.$txnid.'|'.$plan->price.'|'.$pinfo.'|'.$input['first_name'].'|'.$input['email'].'||||||||||||'.$credentials->merchant_salt));

                    $payumoney = new PayUMoney($environment);

                    $payumoney->param('key', $credentials->merchant_key);
                    $payumoney->param('amount', $plan->price);
                    $payumoney->param('hash', $hash);
                    $payumoney->param('txnid', $txnid);
                    $payumoney->param('firstname', $input['first_name']);
                    $payumoney->param('email', $input['email']);
                    $payumoney->param('phone', $input['phone']);
                    $payumoney->param('productinfo', $pinfo);
                    $payumoney->param('surl', route('customer.subscriptions.payment_success', $plan->uid));
                    $payumoney->param('furl', route('customer.subscriptions.payment_cancel', $plan->uid));

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
                                'amount'      => $plan->price * 100,
                                'description' => $item_name,
                                'customer'    => [
                                        'email' => $input['email'],
                                ],
                        ]);


                        if (isset($link->id) && isset($link->short_url)) {

                            Session::put('razorpay_order_id', $link->order_id);

                            $plan->update([
                                    'transaction_id' => $link->order_id,
                            ]);

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
                    $post_data['total_amount'] = $plan->price;
                    $post_data['currency']     = $plan->currency->code;
                    $post_data['tran_id']      = $plan->uid;
                    $post_data['success_url']  = route('customer.callback.sslcommerz.subscriptions', $plan->uid);
                    $post_data['fail_url']     = route('customer.callback.sslcommerz.subscriptions', $plan->uid);
                    $post_data['cancel_url']   = route('customer.callback.sslcommerz.subscriptions', $plan->uid);

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
                            ["product" => $item_name, "amount" => $plan->price],
                    ]);
                    $post_data['product_name']    = $item_name;
                    $post_data['product_profile'] = 'non-physical-goods';
                    $post_data['product_amount']  = $plan->price;

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
                    $checkout->param('amount', $plan->price);
                    $checkout->param('currency', $plan->currency->code);
                    $checkout->param('tran_id', $plan->uid);
                    $checkout->param('success_url', route('customer.callback.aamarpay.subscriptions', $plan->uid));
                    $checkout->param('fail_url', route('customer.callback.aamarpay.subscriptions', $plan->uid));
                    $checkout->param('cancel_url', route('customer.callback.aamarpay.subscriptions', $plan->uid));

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

                    $checkout = new Flutterwave();

                    $checkout->param('public_key', $credentials->public_key);
                    $checkout->param('amount', $plan->price);
                    $checkout->param('currency', $plan->currency->code);
                    $checkout->param('tx_ref', $plan->uid);
                    $checkout->param('redirect_url', route('customer.callback.flutterwave.subscriptions'));
                    $checkout->param('customizations[title]', $item_name);
                    $checkout->param('customizations[description]', $item_name);
                    $checkout->param('customer[name]', $input['first_name'].' '.$input['last_name']);
                    $checkout->param('customer[email]', $input['email']);
                    $checkout->param('customer[phone_number]', $input['phone']);
                    $checkout->param('meta[user_id]', auth()->user()->id);
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

    public function freeSubscription(Plan $plan)
    {
        $paymentMethod = PaymentMethods::where('type', 'offline_payment')->first();
        if ($paymentMethod) {

            $invoice = Invoices::create([
                    'user_id'        => Auth::user()->id,
                    'currency_id'    => $plan->currency_id,
                    'payment_method' => $paymentMethod->id,
                    'amount'         => $plan->price,
                    'type'           => Invoices::TYPE_SUBSCRIPTION,
                    'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                    'transaction_id' => $plan->uid,
                    'status'         => Invoices::STATUS_PAID,
            ]);

            if ($invoice) {
                if (Auth::user()->customer->activeSubscription()) {
                    Auth::user()->customer->activeSubscription()->cancelNow();
                }

                if (Auth::user()->customer->subscription) {
                    $subscription = Auth::user()->customer->subscription;
                } else {
                    $subscription           = new Subscription();
                    $subscription->user_id  = Auth::user()->id;
                    $subscription->start_at = Carbon::now();
                }

                $subscription->status                 = Subscription::STATUS_ACTIVE;
                $subscription->plan_id                = $plan->getBillableId();
                $subscription->end_period_last_days   = '10';
                $subscription->current_period_ends_at = $subscription->getPeriodEndsAt(Carbon::now());
                $subscription->end_at                 = null;
                $subscription->end_by                 = null;
                $subscription->payment_method_id      = $paymentMethod->id;
                $subscription->save();

                // add transaction
                $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                        'end_at'                 => $subscription->end_at,
                        'current_period_ends_at' => $subscription->current_period_ends_at,
                        'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                        'title'                  => trans('locale.subscription.subscribed_to_plan', ['plan' => $subscription->plan->getBillableName()]),
                        'amount'                 => $subscription->plan->getBillableFormattedPrice(),
                ]);

                // add log
                $subscription->addLog(SubscriptionLog::TYPE_ADMIN_PLAN_ASSIGNED, [
                        'plan'  => $subscription->plan->getBillableName(),
                        'price' => $subscription->plan->getBillableFormattedPrice(),
                ]);


                return response()->json([
                        'status'  => 'success',
                        'message' => __('locale.payment_gateways.payment_successfully_made'),
                ]);
            }

            return response()->json([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);

        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * pay payment
     *
     * @param  Plan  $plan
     * @param  array  $input
     * @param $user
     *
     * @return JsonResponse|mixed
     */
    public function payRegisterPayment(Plan $plan, array $input, $user): JsonResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', $input['payment_methods'])->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            $item_name = __('locale.subscription.payment_for_plan').' '.$plan->name;

            switch ($paymentMethod->type) {

                case 'paypal':
                    $environment = new SandboxEnvironment($credentials->client_id, $credentials->secret);

                    $client = new PayPalHttpClient($environment);

                    $request = new OrdersCreateRequest();
                    $request->prefer('return=representation');

                    $request->body = [
                            "intent"              => "CAPTURE",
                            "purchase_units"      => [[
                                    "reference_id" => $user->id.'_'.$plan->uid,
                                    'description'  => $item_name,
                                    "amount"       => [
                                            "value"         => $plan->price,
                                            "currency_code" => $plan->currency->code,
                                    ],
                            ]],
                            "application_context" => [
                                    'brand_name' => config('app.name'),
                                    'locale'     => config('app.locale'),
                                    "cancel_url" => route('user.registers.payment_cancel', $user->uid),
                                    "return_url" => route('user.registers.payment_success', ['user' => $user->uid, 'plan' => $plan->uid, 'payment_method' => $paymentMethod->uid]),
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
                                                'currency'     => $plan->currency->code,
                                                'unit_amount'  => $plan->price * 100,
                                                'product_data' => [
                                                        'name' => $item_name,
                                                ],
                                        ],
                                        'quantity'   => 1,
                                ]],
                                'mode'                 => 'payment',
                                "cancel_url"           => route('user.registers.payment_cancel', $user->uid),
                                'success_url'          => route('user.registers.payment_success', ['user' => $user->uid, 'plan' => $plan->uid, 'payment_method' => $paymentMethod->uid]),
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

                    $checkout = new TwoCheckout();

                    $checkout->param('sid', $credentials->merchant_code);
                    if ($credentials->environment == 'sandbox') {
                        $checkout->param('demo', 'Y');
                    }
                    $checkout->param('return_url', route('user.registers.payment_success', ['user' => $user->uid, 'plan' => $plan->uid, 'payment_method' => $paymentMethod->uid]));
                    $checkout->param('li_0_name', $item_name);
                    $checkout->param('li_0_price', $plan->price);
                    $checkout->param('li_0_quantity', 1);
                    $checkout->param('card_holder_name', $input['first_name'].' '.$input['last_name']);
                    $checkout->param('city', $input['city']);
                    $checkout->param('country', $input['country']);
                    $checkout->param('email', $input['email']);
                    $checkout->param('phone', $input['phone']);
                    $checkout->param('currency_code', $plan->currency->code);
                    $checkout->gw_submit();
                    exit();

                case 'paystack':

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                            CURLOPT_URL            => "https://api.paystack.co/transaction/initialize",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST  => "POST",
                            CURLOPT_POSTFIELDS     => json_encode([
                                    'amount'   => $plan->price * 100,
                                    'email'    => $input['email'],
                                    'metadata' => [
                                            'plan_id'      => $plan->uid,
                                            'user_id'      => $user->id,
                                            'request_type' => 'subscription_payment',
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

                    $signature = "$credentials->client_secret~$credentials->client_id~subscription$plan->uid~$plan->price~$plan->currency->code";
                    $signature = md5($signature);

                    $payu = new PayU();

                    $payu->param('merchantId', $credentials->client_id);
                    $payu->param('ApiKey', $credentials->client_secret);
                    $payu->param('referenceCode', 'subscription'.$plan->uid);
                    $payu->param('description', $item_name);
                    $payu->param('amount', $plan->price);
                    $payu->param('currency', $plan->currency->code);
                    $payu->param('buyerEmail', $input['email']);
                    $payu->param('signature', $signature);
                    $payu->param('confirmationUrl', route('user.registers.payment_success', ['user' => $user->uid, 'plan' => $plan->uid, 'payment_method' => $paymentMethod->uid]));
                    $payu->param('responseUrl', route('user.registers.payment_cancel', $user->uid));
                    $payu->gw_submit();

                    exit();

                case 'paynow':

                    $paynow = new Paynow(
                            $credentials->integration_id,
                            $credentials->integration_key,
                            route('customer.callback.paynow'),
                            route('user.registers.payment_success', ['user' => $user->uid, 'plan' => $plan->uid, 'payment_method' => $paymentMethod->uid])
                    );


                    $payment = $paynow->createPayment($plan->uid, $input['email']);
                    $payment->add($item_name, $plan->price);


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


                    } catch (ConnectionException|HashMismatchException|InvalidIntegrationException|Exception $e) {
                        return response()->json([
                                'status'  => 'error',
                                'message' => $e->getMessage(),
                        ]);
                    }

                case 'coinpayments':

                    Session::put('payment_method', $paymentMethod->type);

                    $coinPayment = new CoinPayments();

                    $order = [
                            'merchant'    => $credentials->merchant_id,
                            'item_name'   => $item_name,
                            'amountf'     => $plan->price,
                            'currency'    => $plan->currency->code,
                            'success_url' => route('user.registers.payment_success', ['user' => $user->uid, 'plan' => $plan->uid, 'payment_method' => $paymentMethod->uid]),
                            'cancel_url'  => route('user.registers.payment_cancel', $user->uid),
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
                            'amount'                  => $plan->price,
                            'phone'                   => $input['phone'],
                            'buyer_name'              => $name,
                            'redirect_url'            => route('user.registers.payment_success', ['user' => $user->uid, 'plan' => $plan->uid, 'payment_method' => $paymentMethod->uid]),
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

                    if (isset($response->success)) {
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
                    $hash        = strtolower(hash('sha512', $credentials->merchant_key.'|'.$txnid.'|'.$plan->price.'|'.$pinfo.'|'.$input['first_name'].'|'.$input['email'].'||||||||||||'.$credentials->merchant_salt));

                    $payumoney = new PayUMoney($environment);

                    $payumoney->param('key', $credentials->merchant_key);
                    $payumoney->param('amount', $plan->price);
                    $payumoney->param('hash', $hash);
                    $payumoney->param('txnid', $txnid);
                    $payumoney->param('firstname', $input['first_name']);
                    $payumoney->param('email', $input['email']);
                    $payumoney->param('phone', $input['phone']);
                    $payumoney->param('productinfo', $pinfo);
                    $payumoney->param('surl', route('user.registers.payment_success', ['user' => $user->uid, 'plan' => $plan->uid, 'payment_method' => $paymentMethod->uid]));
                    $payumoney->param('furl', route('user.registers.payment_cancel', $user->uid));

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
                                'amount'      => $plan->price * 100,
                                'description' => $item_name,
                                'customer'    => [
                                        'email' => $input['email'],
                                ],
                        ]);


                        if (isset($link->id) && isset($link->short_url)) {

                            Session::put('razorpay_order_id', $link->order_id);

                            $plan->update([
                                    'transaction_id' => $link->order_id,
                            ]);

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
                    $post_data['total_amount'] = $plan->price;
                    $post_data['currency']     = $plan->currency->code;
                    $post_data['tran_id']      = $plan->uid;
                    $post_data['success_url']  = route('user.callback.sslcommerz.register', ['user' => $user->uid, 'plan' => $plan->uid]);
                    $post_data['fail_url']     = route('user.callback.sslcommerz.register', ['user' => $user->uid, 'plan' => $plan->uid]);
                    $post_data['cancel_url']   = route('user.callback.sslcommerz.register', ['user' => $user->uid, 'plan' => $plan->uid]);

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
                            ["product" => $item_name, "amount" => $plan->price],
                    ]);
                    $post_data['product_name']    = $item_name;
                    $post_data['product_profile'] = 'non-physical-goods';
                    $post_data['product_amount']  = $plan->price;

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
                    $checkout->param('amount', $plan->price);
                    $checkout->param('currency', $plan->currency->code);
                    $checkout->param('tran_id', $plan->uid);
                    $checkout->param('success_url', route('user.callback.aamarpay.register', ['user' => $user->uid, 'plan' => $plan->uid]));
                    $checkout->param('fail_url', route('user.callback.aamarpay.register', ['user' => $user->uid, 'plan' => $plan->uid]));
                    $checkout->param('cancel_url', route('user.callback.aamarpay.register', ['user' => $user->uid, 'plan' => $plan->uid]));

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

                    $checkout = new Flutterwave();

                    $checkout->param('public_key', $credentials->public_key);
                    $checkout->param('amount', $plan->price);
                    $checkout->param('currency', $plan->currency->code);
                    $checkout->param('tx_ref', $plan->uid);
                    $checkout->param('redirect_url', route('user.callback.flutterwave.register'));
                    $checkout->param('customizations[title]', $item_name);
                    $checkout->param('customizations[description]', $item_name);
                    $checkout->param('customer[name]', $input['first_name'].' '.$input['last_name']);
                    $checkout->param('customer[email]', $input['email']);
                    $checkout->param('customer[phone_number]', $input['phone']);
                    $checkout->param('meta[user_id]', $user->id);
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
