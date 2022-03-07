<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\GeneralException;
use App\Library\aamarPay;
use App\Library\CoinPayments;
use App\Library\Flutterwave;
use App\Library\PayU;
use App\Library\PayUMoney;
use App\Library\TwoCheckout;
use App\Models\Keywords;
use App\Models\PaymentMethods;
use App\Models\User;
use App\Repositories\Contracts\KeywordRepository;
use Braintree\Gateway;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

class EloquentKeywordRepository extends EloquentBaseRepository implements KeywordRepository
{
    /**
     * EloquentKeywordRepository constructor.
     *
     * @param  Keywords  $keyword
     */
    public function __construct(Keywords $keyword)
    {
        parent::__construct($keyword);
    }

    /**
     * @param  array  $input
     *
     * @param  array  $billingCycle
     *
     * @return Keywords|mixed
     *
     * @throws GeneralException
     */
    public function store(array $input, array $billingCycle): Keywords
    {

        /** @var Keywords $keyword */
        $keyword = $this->make(Arr::only($input, [
                'title',
                'sender_id',
                'user_id',
                'keyword_name',
                'reply_text',
                'reply_voice',
                'price',
                'billing_cycle',
                'frequency_amount',
                'frequency_unit',
                'currency_id',
                'status',
        ]));

        $media_url = null;

        if (isset($input['reply_mms'])) {
            $image      = $input['reply_mms'];
            $media_path = $image->store('mms_file', 'public');
            $media_url  = asset(Storage::url($media_path));
        }

        $keyword->reply_mms = $media_url;

        if (isset($input['billing_cycle']) && $input['billing_cycle'] != 'custom') {
            $limits                    = $billingCycle[$input['billing_cycle']];
            $keyword->frequency_amount = $limits['frequency_amount'];
            $keyword->frequency_unit   = $limits['frequency_unit'];
        }


        $user = User::find($input['user_id'])->is_customer;
        if ($user) {
            $input['status'] = 'assigned';
            $keyword->status = 'assigned';
        }

        if ($input['status'] == 'assigned') {
            $current                = Carbon::now();
            $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
        }

        if ( ! $this->save($keyword)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $keyword;

    }

    /**
     * @param  Keywords  $keyword
     *
     * @return bool
     */
    private function save(Keywords $keyword): bool
    {
        if ( ! $keyword->save()) {
            return false;
        }

        return true;
    }

    /**
     * @param  Keywords  $keyword
     * @param  array  $input
     *
     * @param  array  $billingCycle
     *
     * @return Keywords
     * @throws GeneralException
     */
    public function update(Keywords $keyword, array $input, array $billingCycle): Keywords
    {

        $media_url = null;

        if (isset($input['reply_mms'])) {
            $image      = $input['reply_mms'];
            $media_path = $image->store('mms_file', 'public');
            $media_url  = asset(Storage::url($media_path));
        } else {
            $media_url = $keyword->reply_mms;
        }

        $input['reply_mms'] = $media_url;

        if (isset($input['billing_cycle']) && $input['billing_cycle'] != 'custom') {
            $limits                    = $billingCycle[$input['billing_cycle']];
            $input['frequency_amount'] = $limits['frequency_amount'];
            $input['frequency_unit']   = $limits['frequency_unit'];
        }

        if ( ! $keyword->update($input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $keyword;
    }

    /**
     * @param  Keywords  $keyword
     *
     * @return bool|null
     * @throws Exception|Throwable
     *
     */
    public function destroy(Keywords $keyword)
    {
        if ( ! $keyword->delete()) {
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
    public function batchDestroy(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            // This wont call eloquent events, change to destroy if needed
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
    public function batchAvailable(array $ids): bool
    {
        DB::transaction(function () use ($ids) {
            if ($this->query()->whereIn('uid', $ids)
                    ->update(['status' => 'available'])
            ) {
                return true;
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        });

        return true;
    }

    /**
     * update keyword information by customer
     *
     * @param  Keywords  $keyword
     * @param  array  $input
     *
     * @return Keywords
     * @throws GeneralException
     */
    public function updateByCustomer(Keywords $keyword, array $input): Keywords
    {
        if (isset($input['originator'])) {
            if ($input['originator'] == 'sender_id') {
                $sender_id = $input['sender_id'];
            } else {
                $sender_id = $input['phone_number'];
            }
            $input['sender_id'] = $sender_id;
        }

        $media_url = null;

        if (isset($input['reply_mms'])) {
            $image      = $input['reply_mms'];
            $media_path = $image->store('mms_file', 'public');
            $media_url  = asset(Storage::url($media_path));
        } else {
            $media_url = $keyword->reply_mms;
        }

        $input['reply_mms'] = $media_url;

        unset($input['originator']);
        unset($input['phone_number']);

        if ( ! $keyword->update($input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $keyword;
    }


    /**
     * release number
     *
     * @param  Keywords  $keyword
     * @param  string  $id
     *
     * @return bool
     * @throws GeneralException
     */
    public function release(Keywords $keyword, string $id): bool
    {
        $available = $keyword->where('user_id', Auth::user()->id)->where('uid', $id)->first();

        if ($available) {
            $available->user_id       = 1;
            $available->status        = 'available';
            $available->validity_date = null;
            if ( ! $available->save()) {
                throw new GeneralException(__('locale.exceptions.something_went_wrong'));
            }

            return true;
        }

        throw new GeneralException(__('locale.exceptions.something_went_wrong'));

    }


    /**
     * pay the payment
     *
     * @param  Keywords  $keyword
     * @param  array  $input
     *
     * @return Application|Factory|View|JsonResponse
     */
    public function payPayment(Keywords $keyword, array $input)
    {

        $paymentMethod = PaymentMethods::where('status', true)->where('type', $input['payment_methods'])->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            $item_name = __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name;

            switch ($paymentMethod->type) {

                case 'paypal':
                    $environment = new SandboxEnvironment($credentials->client_id, $credentials->secret);

                    $client = new PayPalHttpClient($environment);

                    $request = new OrdersCreateRequest();
                    $request->prefer('return=representation');

                    $request->body = [
                            "intent"              => "CAPTURE",
                            "purchase_units"      => [[
                                    "reference_id" => $keyword->user->id.'_'.$keyword->uid,
                                    'description'  => $item_name,
                                    "amount"       => [
                                            "value"         => $keyword->price,
                                            "currency_code" => $keyword->currency->code,
                                    ],
                            ]],
                            "application_context" => [
                                    'brand_name' => config('app.name'),
                                    'locale'     => config('app.locale'),
                                    "cancel_url" => route('customer.keywords.payment_cancel', $keyword->uid),
                                    "return_url" => route('customer.keywords.payment_success', $keyword->uid),
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
                                                'currency'     => $keyword->currency->code,
                                                'unit_amount'  => $keyword->price * 100,
                                                'product_data' => [
                                                        'name' => $item_name,
                                                ],
                                        ],
                                        'quantity'   => 1,
                                ]],
                                'mode'                 => 'payment',
                                'success_url'          => route('customer.keywords.payment_success', $keyword->uid),
                                'cancel_url'           => route('customer.keywords.payment_cancel', $keyword->uid),
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
                    $checkout->param('return_url', route('customer.keywords.payment_success', $keyword->uid));
                    $checkout->param('li_0_name', $item_name);
                    $checkout->param('li_0_price', $keyword->price);
                    $checkout->param('li_0_quantity', 1);
                    $checkout->param('card_holder_name', $input['first_name'].' '.$input['last_name']);
                    $checkout->param('city', $input['city']);
                    $checkout->param('country', $input['country']);
                    $checkout->param('email', $input['email']);
                    $checkout->param('phone', $input['phone']);
                    $checkout->param('currency_code', $keyword->currency->code);
                    $checkout->gw_submit();
                    exit();

                case 'paystack':

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                            CURLOPT_URL            => "https://api.paystack.co/transaction/initialize",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CUSTOMREQUEST  => "POST",
                            CURLOPT_POSTFIELDS     => json_encode([
                                    'amount'   => $keyword->price * 100,
                                    'email'    => $input['email'],
                                    'metadata' => [
                                            'keyword_id'   => $keyword->uid,
                                            'request_type' => 'keyword_payment',
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

                    $signature = "$credentials->client_secret~$credentials->client_id~keyword$keyword->uid~$keyword->price~$keyword->currency->code";
                    $signature = md5($signature);

                    $payu = new PayU();

                    $payu->param('merchantId', $credentials->client_id);
                    $payu->param('ApiKey', $credentials->client_secret);
                    $payu->param('referenceCode', 'keyword'.$keyword->uid);
                    $payu->param('description', $item_name);
                    $payu->param('amount', $keyword->price);
                    $payu->param('currency', $keyword->currency->code);
                    $payu->param('buyerEmail', $input['email']);
                    $payu->param('signature', $signature);
                    $payu->param('confirmationUrl', route('customer.keywords.payment_success', $keyword->uid));
                    $payu->param('responseUrl', route('customer.keywords.payment_cancel', $keyword->uid));
                    $payu->gw_submit();

                    exit();

                case 'paynow':

                    $paynow = new Paynow(
                            $credentials->integration_id,
                            $credentials->integration_key,
                            route('customer.callback.paynow'),
                            route('customer.keywords.payment_success', $keyword->uid)
                    );


                    $payment = $paynow->createPayment($keyword->uid, $input['email']);
                    $payment->add($item_name, $keyword->price);


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
                            'item_name'   => $item_name,
                            'amountf'     => $keyword->price,
                            'currency'    => $keyword->currency->code,
                            'success_url' => route('customer.keywords.payment_success', $keyword->uid),
                            'cancel_url'  => route('customer.keywords.payment_cancel', $keyword->uid),
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
                            'amount'                  => $keyword->price,
                            'phone'                   => $input['phone'],
                            'buyer_name'              => $name,
                            'redirect_url'            => route('customer.keywords.payment_success', $keyword->uid),
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
                    $hash        = strtolower(hash('sha512', $credentials->merchant_key.'|'.$txnid.'|'.$keyword->price.'|'.$pinfo.'|'.$input['first_name'].'|'.$input['email'].'||||||||||||'.$credentials->merchant_salt));

                    $payumoney = new PayUMoney($environment);

                    $payumoney->param('key', $credentials->merchant_key);
                    $payumoney->param('amount', $keyword->price);
                    $payumoney->param('hash', $hash);
                    $payumoney->param('txnid', $txnid);
                    $payumoney->param('firstname', $input['first_name']);
                    $payumoney->param('email', $input['email']);
                    $payumoney->param('phone', $input['phone']);
                    $payumoney->param('productinfo', $pinfo);
                    $payumoney->param('surl', route('customer.keywords.payment_success', $keyword->uid));
                    $payumoney->param('furl', route('customer.keywords.payment_cancel', $keyword->uid));

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
                                'amount'      => $keyword->price * 100,
                                'description' => $item_name,
                                'customer'    => [
                                        'email' => $input['email'],
                                ],
                        ]);


                        if (isset($link->id) && isset($link->short_url)) {

                            Session::put('razorpay_order_id', $link->order_id);

                            $keyword->update([
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
                    $post_data['total_amount'] = $keyword->price;
                    $post_data['currency']     = $keyword->currency->code;
                    $post_data['tran_id']      = $keyword->uid;
                    $post_data['success_url']  = route('customer.callback.sslcommerz.keywords', $keyword->uid);
                    $post_data['fail_url']     = route('customer.callback.sslcommerz.keywords', $keyword->uid);
                    $post_data['cancel_url']   = route('customer.callback.sslcommerz.keywords', $keyword->uid);

                    $post_data['product_category'] = "keywords";
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
                            ["product" => $item_name, "amount" => $keyword->price],
                    ]);
                    $post_data['product_name']    = $item_name;
                    $post_data['product_profile'] = 'non-physical-goods';
                    $post_data['product_amount']  = $keyword->price;

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
                    $checkout->param('amount', $keyword->price);
                    $checkout->param('currency', $keyword->currency->code);
                    $checkout->param('tran_id', $keyword->uid);
                    $checkout->param('success_url', route('customer.callback.aamarpay.keywords', $keyword->uid));
                    $checkout->param('fail_url', route('customer.callback.aamarpay.keywords', $keyword->uid));
                    $checkout->param('cancel_url', route('customer.callback.aamarpay.keywords', $keyword->uid));

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
                    $checkout->param('amount', $keyword->price);
                    $checkout->param('currency', $keyword->currency->code);
                    $checkout->param('tx_ref', $keyword->uid);
                    $checkout->param('redirect_url', route('customer.callback.flutterwave.keywords'));
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
}
