<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Library\aamarPay;
use App\Library\CoinPayments;
use App\Library\Flutterwave;
use App\Library\PayU;
use App\Library\PayUMoney;
use App\Library\TwoCheckout;
use App\Models\PaymentMethods;
use App\Models\Senderid;
use App\Models\SenderidPlan;
use App\Repositories\Contracts\SenderIDRepository;
use Braintree\Gateway;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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
use Session;
use Stripe\Stripe;
use Throwable;

class EloquentSenderIDRepository extends EloquentBaseRepository implements SenderIDRepository
{
    /**
     * EloquentSenderIDRepository constructor.
     *
     * @param  Senderid  $senderid
     */
    public function __construct(Senderid $senderid)
    {
        parent::__construct($senderid);
    }

    /**
     * @param  array  $input
     * @param  array  $billingCycle
     *
     * @return Senderid|mixed
     *
     * @throws GeneralException
     */
    public function store(array $input, array $billingCycle): Senderid
    {
        /** @var Senderid $senderid */
        $senderid = $this->make(Arr::only($input, [
                'user_id',
                'sender_id',
                'status',
                'price',
                'billing_cycle',
                'frequency_amount',
                'frequency_unit',
                'currency_id',
        ]));

        if (isset($input['billing_cycle']) && $input['billing_cycle'] != 'custom') {
            $limits                     = $billingCycle[$input['billing_cycle']];
            $senderid->frequency_amount = $limits['frequency_amount'];
            $senderid->frequency_unit   = $limits['frequency_unit'];
        }

        if ($input['status'] == 'active') {
            $current                   = Carbon::now();
            $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
            $senderid->payment_claimed = true;
        }


        if ( ! $this->save($senderid)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $senderid;

    }

    /**
     * @param  array  $input
     *
     * @return Senderid|mixed
     *
     * @throws GeneralException
     */
    public function storeCustom(array $input): Senderid
    {
        /** @var Senderid $senderid */
        $senderid = $this->make(Arr::only($input, [
                'sender_id',
        ]));

        $plan                       = SenderidPlan::find($input['plan']);
        $senderid->user_id          = Auth::user()->id;
        $senderid->currency_id      = $plan->currency_id;
        $senderid->status           = 'Pending';
        $senderid->price            = $plan->price;
        $senderid->billing_cycle    = $plan->billing_cycle;
        $senderid->frequency_amount = $plan->frequency_amount;
        $senderid->frequency_unit   = $plan->frequency_unit;

        if ( ! $this->save($senderid)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $senderid;

    }

    /**
     * @param  Senderid  $senderid
     *
     * @return bool
     */
    private function save(Senderid $senderid): bool
    {
        if ( ! $senderid->save()) {
            return false;
        }

        return true;
    }

    /**
     * @param  Senderid  $senderid
     * @param  array  $input
     * @param  array  $billingCycle
     *
     * @return Senderid
     * @throws GeneralException
     */
    public function update(Senderid $senderid, array $input, array $billingCycle): Senderid
    {
        if (isset($input['billing_cycle']) && $input['billing_cycle'] != 'custom') {
            $limits                    = $billingCycle[$input['billing_cycle']];
            $input['frequency_amount'] = $limits['frequency_amount'];
            $input['frequency_unit']   = $limits['frequency_unit'];
        }

        if ($senderid->status != 'active' && $input['status'] == 'active') {
            $current                  = Carbon::now();
            $input['validity_date']   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
            $input['payment_claimed'] = true;
        }

        if ( ! $senderid->update($input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $senderid;
    }

    /**
     * @param  Senderid  $senderid
     * @param  null  $user_id
     *
     * @return bool
     * @throws GeneralException
     * @throws Exception
     */
    public function destroy(Senderid $senderid, $user_id = null)
    {
        if ($user_id) {
            $exist = $senderid->where('sender_id', $senderid->sender_id)->where('user_id', $user_id)->first();
            if ($exist) {
                if ( ! $exist->delete()) {
                    throw new GeneralException(__('locale.exceptions.something_went_wrong'));
                }

                return true;
            }
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        } else {
            if ( ! $senderid->delete()) {
                throw new GeneralException(__('locale.exceptions.something_went_wrong'));
            }
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
    public function batchDestroy(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            // This won't call eloquent events, change to destroy if needed
            if ($this->query()->whereIn('uid', $ids)->delete()) {
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
    public function batchActive(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['status' => 'active'])
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
    public function batchBlock(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['status' => 'block'])
            ) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

    /**
     * store sender id plan
     *
     * @param  array  $input
     * @param  array  $billingCycle
     *
     * @return mixed
     * @throws GeneralException
     */
    public function storePlan(array $input, array $billingCycle)
    {
        if (isset($input['billing_cycle']) && $input['billing_cycle'] != 'custom') {
            $limits                    = $billingCycle[$input['billing_cycle']];
            $input['frequency_amount'] = $limits['frequency_amount'];
            $input['frequency_unit']   = $limits['frequency_unit'];
        }

        $sender_id_plan = SenderidPlan::create($input);
        if ( ! $sender_id_plan) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $sender_id_plan;

    }


    /**
     * pay the payment
     *
     * @param  Senderid  $senderid
     * @param  array  $input
     *
     * @return JsonResponse
     */
    public function payPayment(Senderid $senderid, array $input): JsonResponse
    {

        $paymentMethod = PaymentMethods::where('status', true)->where('type', $input['payment_methods'])->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);


            switch ($paymentMethod->type) {

                case 'paypal':
                    $environment = new SandboxEnvironment($credentials->client_id, $credentials->secret);

                    $client = new PayPalHttpClient($environment);

                    $request = new OrdersCreateRequest();
                    $request->prefer('return=representation');

                    $request->body = [
                            "intent"              => "CAPTURE",
                            "purchase_units"      => [[
                                    "reference_id" => $senderid->user->id.'_'.$senderid->uid,
                                    'description'  => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                                    "amount"       => [
                                            "value"         => $senderid->price,
                                            "currency_code" => $senderid->currency->code,
                                    ],
                            ]],
                            "application_context" => [
                                    'brand_name' => config('app.name'),
                                    'locale'     => config('app.locale'),
                                    "cancel_url" => route('customer.senderid.payment_cancel', $senderid->uid),
                                    "return_url" => route('customer.senderid.payment_success', $senderid->uid),
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
                                                'currency'     => $senderid->currency->code,
                                                'unit_amount'  => $senderid->price * 100,
                                                'product_data' => [
                                                        'name' => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                                                ],
                                        ],
                                        'quantity'   => 1,
                                ]],
                                'mode'                 => 'payment',
                                'success_url'          => route('customer.senderid.payment_success', $senderid->uid),
                                'cancel_url'           => route('customer.senderid.payment_cancel', $senderid->uid),
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
                    $checkout->param('return_url', route('customer.senderid.payment_success', $senderid->uid));
                    $checkout->param('li_0_name', __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id);
                    $checkout->param('li_0_price', $senderid->price);
                    $checkout->param('li_0_quantity', 1);
                    $checkout->param('card_holder_name', $input['first_name'].' '.$input['last_name']);
                    $checkout->param('city', $input['city']);
                    $checkout->param('country', $input['country']);
                    $checkout->param('email', $input['email']);
                    $checkout->param('phone', $input['phone']);
                    $checkout->param('currency_code', $senderid->currency->code);
                    $checkout->gw_submit();
                    exit();

                case 'paystack':

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                            CURLOPT_URL            => "https://api.paystack.co/transaction/initialize",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST  => "POST",
                            CURLOPT_POSTFIELDS     => json_encode([
                                    'amount'   => $senderid->price * 100,
                                    'email'    => $input['email'],
                                    'metadata' => [
                                            'sender_id'    => $senderid->uid,
                                            'request_type' => 'senderid_payment',
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

                    $signature = "$credentials->client_secret~$credentials->client_id~senderID$senderid->uid~$senderid->price~$senderid->currency->code";
                    $signature = md5($signature);

                    $payu = new PayU();

                    $payu->param('merchantId', $credentials->client_id);
                    $payu->param('ApiKey', $credentials->client_secret);
                    $payu->param('referenceCode', 'senderID'.$senderid->uid);
                    $payu->param('description', __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id);
                    $payu->param('amount', $senderid->price);
                    $payu->param('currency', $senderid->currency->code);
                    $payu->param('buyerEmail', $input['email']);
                    $payu->param('signature', $signature);
                    $payu->param('confirmationUrl', route('customer.senderid.payment_success', $senderid->uid));
                    $payu->param('responseUrl', route('customer.senderid.payment_cancel', $senderid->uid));
                    $payu->gw_submit();

                    exit();

                case 'paynow':

                    $paynow = new Paynow(
                            $credentials->integration_id,
                            $credentials->integration_key,
                            route('customer.callback.paynow'),
                            route('customer.senderid.payment_success', $senderid->uid)
                    );


                    $payment = $paynow->createPayment($senderid->uid, $input['email']);
                    $payment->add(__('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id, $senderid->price);


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

                    $coinPayment = new CoinPayments();

                    $order = [
                            'merchant'    => $credentials->merchant_id,
                            'item_name'   => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                            'amountf'     => $senderid->price,
                            'currency'    => $senderid->currency->code,
                            'success_url' => route('customer.senderid.payment_success', $senderid->uid),
                            'cancel_url'  => route('customer.senderid.payment_cancel', $senderid->uid),
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
                            'purpose'                 => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                            'amount'                  => $senderid->price,
                            'phone'                   => $input['phone'],
                            'buyer_name'              => $name,
                            'redirect_url'            => route('customer.senderid.payment_success', $senderid->uid),
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
                    $pinfo       = __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id;
                    $hash        = strtolower(hash('sha512', $credentials->merchant_key.'|'.$txnid.'|'.$senderid->price.'|'.$pinfo.'|'.$input['first_name'].'|'.$input['email'].'||||||||||||'.$credentials->merchant_salt));

                    $payumoney = new PayUMoney($environment);

                    $payumoney->param('key', $credentials->merchant_key);
                    $payumoney->param('amount', $senderid->price);
                    $payumoney->param('hash', $hash);
                    $payumoney->param('txnid', $txnid);
                    $payumoney->param('firstname', $input['first_name']);
                    $payumoney->param('email', $input['email']);
                    $payumoney->param('phone', $input['phone']);
                    $payumoney->param('productinfo', $pinfo);
                    $payumoney->param('surl', route('customer.senderid.payment_success', $senderid->uid));
                    $payumoney->param('furl', route('customer.senderid.payment_cancel', $senderid->uid));

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
                                'amount'      => $senderid->price * 100,
                                'description' => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                                'customer'    => [
                                        'email' => $input['email'],
                                ],
                        ]);


                        if (isset($link->id) && isset($link->short_url)) {

                            Session::put('razorpay_order_id', $link->order_id);

                            $senderid->update([
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
                    $post_data['total_amount'] = $senderid->price;
                    $post_data['currency']     = $senderid->currency->code;
                    $post_data['tran_id']      = $senderid->uid;
                    $post_data['success_url']  = route('customer.callback.sslcommerz.senderid', $senderid->uid);
                    $post_data['fail_url']     = route('customer.callback.sslcommerz.senderid', $senderid->uid);
                    $post_data['cancel_url']   = route('customer.callback.sslcommerz.senderid', $senderid->uid);

                    $post_data['product_category'] = "senderid";
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
                            ["product" => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id, "amount" => $senderid->price],
                    ]);
                    $post_data['product_name']    = __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id;
                    $post_data['product_profile'] = 'non-physical-goods';
                    $post_data['product_amount']  = $senderid->price;

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
                    $checkout->param('desc', __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id);
                    $checkout->param('amount', $senderid->price);
                    $checkout->param('currency', $senderid->currency->code);
                    $checkout->param('tran_id', $senderid->uid);
                    $checkout->param('success_url', route('customer.callback.aamarpay.senderid', $senderid->uid));
                    $checkout->param('fail_url', route('customer.callback.aamarpay.senderid', $senderid->uid));
                    $checkout->param('cancel_url', route('customer.callback.aamarpay.senderid', $senderid->uid));

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
                    $checkout->param('amount', $senderid->price);
                    $checkout->param('currency', $senderid->currency->code);
                    $checkout->param('tx_ref', $senderid->uid);
                    $checkout->param('redirect_url', route('customer.callback.flutterwave.senderid'));
                    $checkout->param('customizations[title]', __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id);
                    $checkout->param('customizations[description]', __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id);
                    $checkout->param('customer[name]', $input['first_name'].' '.$input['last_name']);
                    $checkout->param('customer[email]', $input['email']);
                    $checkout->param('customer[phone_number]', $input['phone']);
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

        return response()->json(['status'  => 'error',
                                 'message' => __('locale.payment_gateways.not_found'),]);

    }
}
