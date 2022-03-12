<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoices;
use App\Models\Keywords;
use App\Models\Notifications;
use App\Models\PaymentMethods;
use App\Models\PhoneNumbers;
use App\Models\Plan;
use App\Models\Senderid;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use Braintree\Gateway;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use net\authorize\api\constants\ANetEnvironment;
use Paynow\Payments\Paynow;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Session;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class PaymentController extends Controller
{

    public function createNotification($type = null, $name = null, $user_name = null)
    {
        Notifications::create([
                'user_id'           => 1,
                'notification_for'  => 'admin',
                'notification_type' => $type,
                'message'           => $name.' Purchased By '.$user_name,
        ]);
    }

    /**
     * successful sender id purchase using PayPal
     *
     * @param  Senderid  $senderid
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function successfulSenderIDPayment(Senderid $senderid, Request $request): RedirectResponse
    {
        $payment_method = Session::get('payment_method');

        switch ($payment_method) {
            case 'paypal':
                $token = Session::get('paypal_payment_id');
                if ($request->token == $token) {
                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'paypal')->first();

                    if ($paymentMethod) {
                        $credentials = json_decode($paymentMethod->options);

                        $environment = new SandboxEnvironment($credentials->client_id, $credentials->secret);
                        $client      = new PayPalHttpClient($environment);

                        $request = new OrdersCaptureRequest($token);
                        $request->prefer('return=representation');

                        try {
                            // Call API with your client and get a response for your call
                            $response = $client->execute($request);

                            if ($response->statusCode == '201' && $response->result->status == 'COMPLETED' && isset($response->id)) {
                                $invoice = Invoices::create([
                                        'user_id'        => $senderid->user_id,
                                        'currency_id'    => $senderid->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $senderid->price,
                                        'type'           => Invoices::TYPE_SENDERID,
                                        'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                                        'transaction_id' => $response->id,
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {
                                    $current                   = Carbon::now();
                                    $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                                    $senderid->status          = 'active';
                                    $senderid->payment_claimed = true;
                                    $senderid->save();

                                    $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                                    return redirect()->route('customer.senderid.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);

                            }

                        } catch (Exception $ex) {
                            return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                    'status'  => 'error',
                                    'message' => $ex->getMessage(),
                            ]);
                        }


                        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                'status'  => 'info',
                                'message' => __('locale.sender_id.payment_cancelled'),
                        ]);
                    }

                    return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.payment_gateways.not_found'),
                    ]);
                }

                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            case 'stripe':
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'stripe')->first();
                if ($payment_method) {
                    $credentials = json_decode($paymentMethod->options);
                    $secret_key  = $credentials->secret_key;
                    $session_id  = Session::get('session_id');

                    $stripe = new StripeClient($secret_key);

                    try {
                        $response = $stripe->checkout->sessions->retrieve($session_id);

                        if ($response->payment_status == 'paid') {

                            $invoice = Invoices::create([
                                    'user_id'        => $senderid->user_id,
                                    'currency_id'    => $senderid->currency_id,
                                    'payment_method' => $paymentMethod->id,
                                    'amount'         => $senderid->price,
                                    'type'           => Invoices::TYPE_SENDERID,
                                    'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                                    'transaction_id' => $response->payment_intent,
                                    'status'         => Invoices::STATUS_PAID,
                            ]);

                            if ($invoice) {
                                $current                   = Carbon::now();
                                $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                                $senderid->status          = 'active';
                                $senderid->payment_claimed = true;
                                $senderid->save();

                                $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                                return redirect()->route('customer.senderid.index')->with([
                                        'status'  => 'success',
                                        'message' => __('locale.payment_gateways.payment_successfully_made'),
                                ]);
                            }

                            return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);

                        }

                    } catch (ApiErrorException $e) {
                        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                'status'  => 'error',
                                'message' => $e->getMessage(),
                        ]);
                    }

                }

                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.payment_gateways.not_found'),
                ]);

            case '2checkout':
            case 'payu':
            case 'coinpayments':
                $paymentMethod = PaymentMethods::where('status', true)->where('type', $payment_method)->first();

                if ($payment_method) {
                    $invoice = Invoices::create([
                            'user_id'        => $senderid->user_id,
                            'currency_id'    => $senderid->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $senderid->price,
                            'type'           => Invoices::TYPE_SENDERID,
                            'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                            'transaction_id' => $senderid->uid,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current                   = Carbon::now();
                        $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                        $senderid->status          = 'active';
                        $senderid->payment_claimed = true;
                        $senderid->save();

                        $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                        return redirect()->route('customer.senderid.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }
                }

                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.something_went_wrong'),
                ]);

            case 'paynow':
                $pollurl = Session::get('paynow_poll_url');
                if (isset($pollurl)) {
                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'paynow')->first();

                    if ($paymentMethod) {
                        $credentials = json_decode($paymentMethod->options);

                        $paynow = new Paynow(
                                $credentials->integration_id,
                                $credentials->integration_key,
                                route('customer.callback.paynow'),
                                route('customer.senderid.payment_success', $senderid->uid)
                        );

                        try {
                            $response = $paynow->pollTransaction($pollurl);

                            if ($response->paid()) {
                                $invoice = Invoices::create([
                                        'user_id'        => $senderid->user_id,
                                        'currency_id'    => $senderid->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $senderid->price,
                                        'type'           => Invoices::TYPE_SENDERID,
                                        'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                                        'transaction_id' => $response->reference(),
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {
                                    $current                   = Carbon::now();
                                    $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                                    $senderid->status          = 'active';
                                    $senderid->payment_claimed = true;
                                    $senderid->save();

                                    $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                                    return redirect()->route('customer.senderid.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);

                            }

                        } catch (Exception $ex) {
                            return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                    'status'  => 'error',
                                    'message' => $ex->getMessage(),
                            ]);
                        }


                        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                'status'  => 'info',
                                'message' => __('locale.sender_id.payment_cancelled'),
                        ]);
                    }

                    return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.payment_gateways.not_found'),
                    ]);
                }

                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            case 'instamojo':
                $payment_request_id = Session::get('payment_request_id');

                if ($request->payment_request_id == $payment_request_id) {
                    if ($request->payment_status == 'Completed') {

                        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'instamojo')->first();

                        $invoice = Invoices::create([
                                'user_id'        => $senderid->user_id,
                                'currency_id'    => $senderid->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $senderid->price,
                                'type'           => Invoices::TYPE_SENDERID,
                                'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                                'transaction_id' => $request->payment_id,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {
                            $current                   = Carbon::now();
                            $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                            $senderid->status          = 'active';
                            $senderid->payment_claimed = true;
                            $senderid->save();

                            $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                            return redirect()->route('customer.senderid.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);

                    }

                    return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                            'status'  => 'info',
                            'message' => $request->payment_status,
                    ]);
                }

                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                        'status'  => 'info',
                        'message' => __('locale.payment_gateways.payment_info_not_found'),
                ]);

            case 'payumoney':

                $status      = $request->status;
                $firstname   = $request->firstname;
                $amount      = $request->amount;
                $txnid       = $request->txnid;
                $posted_hash = $request->hash;
                $key         = $request->key;
                $productinfo = $request->productinfo;
                $email       = $request->email;
                $salt        = "";

                // Salt should be same Post Request
                if (isset($request->additionalCharges)) {
                    $additionalCharges = $request->additionalCharges;
                    $retHashSeq        = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
                } else {
                    $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
                }
                $hash = hash("sha512", $retHashSeq);
                if ($hash != $posted_hash) {
                    return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                            'status'  => 'info',
                            'message' => __('locale.exceptions.invalid_action'),
                    ]);
                }

                if ($status == 'Completed') {

                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'payumoney')->first();

                    $invoice = Invoices::create([
                            'user_id'        => $senderid->user_id,
                            'currency_id'    => $senderid->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $senderid->price,
                            'type'           => Invoices::TYPE_SENDERID,
                            'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                            'transaction_id' => $txnid,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current                   = Carbon::now();
                        $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                        $senderid->status          = 'active';
                        $senderid->payment_claimed = true;
                        $senderid->save();

                        $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                        return redirect()->route('customer.senderid.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }

                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                        'status'  => 'error',
                        'message' => $status,
                ]);
        }

        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);

    }

    /**
     * successful Top up payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function successfulTopUpPayment(Request $request): RedirectResponse
    {
        $payment_method = Session::get('payment_method');
        $user           = User::find($request->user_id);

        if ($user) {
            switch ($payment_method) {
                case 'paypal':
                    $token = Session::get('paypal_payment_id');
                    $price = Session::get('price');
                    if ($request->token == $token) {
                        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'paypal')->first();

                        if ($paymentMethod) {
                            $credentials = json_decode($paymentMethod->options);

                            $environment = new SandboxEnvironment($credentials->client_id, $credentials->secret);
                            $client      = new PayPalHttpClient($environment);

                            $request = new OrdersCaptureRequest($token);
                            $request->prefer('return=representation');

                            try {
                                // Call API with your client and get a response for your call
                                $response = $client->execute($request);

                                if ($response->statusCode == '201' && $response->result->status == 'COMPLETED' && isset($response->id)) {

                                    $invoice = Invoices::create([
                                            'user_id'        => $user->id,
                                            'currency_id'    => $user->customer->subscription->plan->currency->id,
                                            'payment_method' => $paymentMethod->id,
                                            'amount'         => $price,
                                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                                            'description'    => 'Payment for sms unit',
                                            'transaction_id' => $response->id,
                                            'status'         => Invoices::STATUS_PAID,
                                    ]);

                                    if ($invoice) {

                                        if ($user->sms_unit != '-1') {
                                            $user->sms_unit += $request->sms_unit;
                                            $user->save();
                                        }


                                        $subscription = $user->customer->activeSubscription();

                                        $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                                'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                                'title'                  => 'Add '.$request->sms_unit.' sms units',
                                                'amount'                 => $request->sms_unit.' sms units',
                                        ]);

                                        return redirect()->route('user.home')->with([
                                                'status'  => 'success',
                                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                                        ]);
                                    }

                                    return redirect()->route('user.home')->with([
                                            'status'  => 'error',
                                            'message' => __('locale.exceptions.something_went_wrong'),
                                    ]);

                                }

                            } catch (Exception $ex) {
                                return redirect()->route('user.home')->with([
                                        'status'  => 'error',
                                        'message' => $ex->getMessage(),
                                ]);
                            }


                            return redirect()->route('user.home')->with([
                                    'status'  => 'info',
                                    'message' => __('locale.sender_id.payment_cancelled'),
                            ]);
                        }

                        return redirect()->route('user.home')->with([
                                'status'  => 'error',
                                'message' => __('locale.payment_gateways.not_found'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.invalid_action'),
                    ]);

                case 'stripe':
                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'stripe')->first();
                    if ($payment_method) {
                        $credentials = json_decode($paymentMethod->options);
                        $secret_key  = $credentials->secret_key;
                        $session_id  = Session::get('session_id');

                        $stripe = new StripeClient($secret_key);

                        try {
                            $response = $stripe->checkout->sessions->retrieve($session_id);

                            $price = $response->amount_total / 100;

                            if ($response->payment_status == 'paid') {

                                $invoice = Invoices::create([
                                        'user_id'        => $user->id,
                                        'currency_id'    => $user->customer->subscription->plan->currency->id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $price,
                                        'type'           => Invoices::TYPE_SUBSCRIPTION,
                                        'description'    => 'Payment for sms unit',
                                        'transaction_id' => $response->id,
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {

                                    if ($user->sms_unit != '-1') {
                                        $user->sms_unit += $request->sms_unit;
                                        $user->save();
                                    }

                                    $subscription = $user->customer->activeSubscription();

                                    $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                            'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                            'title'                  => 'Add '.$request->sms_unit.' sms units',
                                            'amount'                 => $request->sms_unit.' sms units',
                                    ]);

                                    return redirect()->route('user.home')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('user.home')->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);

                            }

                        } catch (ApiErrorException $e) {
                            return redirect()->route('user.home')->with([
                                    'status'  => 'error',
                                    'message' => $e->getMessage(),
                            ]);
                        }

                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.payment_gateways.not_found'),
                    ]);

                case '2checkout':
                case 'payu':
                case 'coinpayments':
                    $paymentMethod = PaymentMethods::where('status', true)->where('type', $payment_method)->first();

                    if ($payment_method) {

                        $price = Session::get('price');

                        $invoice = Invoices::create([
                                'user_id'        => $user->id,
                                'currency_id'    => $user->customer->subscription->plan->currency->id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $price,
                                'type'           => Invoices::TYPE_SUBSCRIPTION,
                                'description'    => 'Payment for sms unit',
                                'transaction_id' => $user->id.'_'.$request->sms_unit,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {

                            if ($user->sms_unit != '-1') {
                                $user->sms_unit += $request->sms_unit;
                                $user->save();
                            }

                            $subscription = $user->customer->activeSubscription();

                            $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                    'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                    'title'                  => 'Add '.$request->sms_unit.' sms units',
                                    'amount'                 => $request->sms_unit.' sms units',
                            ]);

                            return redirect()->route('user.home')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('user.home')->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);

                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                case 'paynow':
                    $pollurl = Session::get('paynow_poll_url');
                    if (isset($pollurl)) {
                        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'paynow')->first();

                        if ($paymentMethod) {
                            $credentials = json_decode($paymentMethod->options);

                            $paynow = new Paynow(
                                    $credentials->integration_id,
                                    $credentials->integration_key,
                                    route('customer.callback.paynow'),
                                    route('customer.top_up.payment_success', ['user_id' => $user->id, 'sms_unit' => $request->sms_unit])
                            );

                            try {
                                $response = $paynow->pollTransaction($pollurl);

                                if ($response->paid()) {

                                    $invoice = Invoices::create([
                                            'user_id'        => $user->id,
                                            'currency_id'    => $user->customer->subscription->plan->currency->id,
                                            'payment_method' => $paymentMethod->id,
                                            'amount'         => $user->customer->subscription->plan->getOption('per_unit_price') * $request->sms_unit,
                                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                                            'description'    => 'Payment for sms unit',
                                            'transaction_id' => $response->reference(),
                                            'status'         => Invoices::STATUS_PAID,
                                    ]);

                                    if ($invoice) {

                                        if ($user->sms_unit != '-1') {
                                            $user->sms_unit += $request->sms_unit;
                                            $user->save();
                                        }

                                        $subscription = $user->customer->activeSubscription();

                                        $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                                'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                                'title'                  => 'Add '.$request->sms_unit.' sms units',
                                                'amount'                 => $request->sms_unit.' sms units',
                                        ]);


                                        return redirect()->route('user.home')->with([
                                                'status'  => 'success',
                                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                                        ]);
                                    }

                                    return redirect()->route('user.home')->with([
                                            'status'  => 'error',
                                            'message' => __('locale.exceptions.something_went_wrong'),
                                    ]);

                                }

                            } catch (Exception $ex) {
                                return redirect()->route('user.home')->with([
                                        'status'  => 'error',
                                        'message' => $ex->getMessage(),
                                ]);
                            }


                            return redirect()->route('user.home')->with([
                                    'status'  => 'info',
                                    'message' => __('locale.sender_id.payment_cancelled'),
                            ]);
                        }

                        return redirect()->route('user.home')->with([
                                'status'  => 'error',
                                'message' => __('locale.payment_gateways.not_found'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.invalid_action'),
                    ]);

                case 'instamojo':
                    $payment_request_id = Session::get('payment_request_id');

                    if ($request->payment_request_id == $payment_request_id) {
                        if ($request->payment_status == 'Completed') {

                            $paymentMethod = PaymentMethods::where('status', true)->where('type', 'instamojo')->first();

                            $invoice = Invoices::create([
                                    'user_id'        => $user->id,
                                    'currency_id'    => $user->customer->subscription->plan->currency->id,
                                    'payment_method' => $paymentMethod->id,
                                    'amount'         => $user->customer->subscription->plan->getOption('per_unit_price') * $request->sms_unit,
                                    'type'           => Invoices::TYPE_SUBSCRIPTION,
                                    'description'    => 'Payment for sms unit',
                                    'transaction_id' => $request->payment_id,
                                    'status'         => Invoices::STATUS_PAID,
                            ]);

                            if ($invoice) {

                                if ($user->sms_unit != '-1') {
                                    $user->sms_unit += $request->sms_unit;
                                    $user->save();
                                }
                                $subscription = $user->customer->activeSubscription();

                                $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                        'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                        'title'                  => 'Add '.$request->sms_unit.' sms units',
                                        'amount'                 => $request->sms_unit.' sms units',
                                ]);

                                return redirect()->route('user.home')->with([
                                        'status'  => 'success',
                                        'message' => __('locale.payment_gateways.payment_successfully_made'),
                                ]);
                            }

                            return redirect()->route('user.home')->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);

                        }

                        return redirect()->route('user.home')->with([
                                'status'  => 'info',
                                'message' => $request->payment_status,
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'info',
                            'message' => __('locale.payment_gateways.payment_info_not_found'),
                    ]);

                case 'payumoney':

                    $status      = $request->status;
                    $firstname   = $request->firstname;
                    $amount      = $request->amount;
                    $txnid       = $request->txnid;
                    $posted_hash = $request->hash;
                    $key         = $request->key;
                    $productinfo = $request->productinfo;
                    $email       = $request->email;
                    $salt        = "";

                    // Salt should be same Post Request
                    if (isset($request->additionalCharges)) {
                        $additionalCharges = $request->additionalCharges;
                        $retHashSeq        = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
                    } else {
                        $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
                    }
                    $hash = hash("sha512", $retHashSeq);
                    if ($hash != $posted_hash) {
                        return redirect()->route('user.home')->with([
                                'status'  => 'info',
                                'message' => __('locale.exceptions.invalid_action'),
                        ]);
                    }

                    if ($status == 'Completed') {

                        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'payumoney')->first();

                        $invoice = Invoices::create([
                                'user_id'        => $user->id,
                                'currency_id'    => $user->customer->subscription->plan->currency->id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $user->customer->subscription->plan->getOption('per_unit_price') * $request->sms_unit,
                                'type'           => Invoices::TYPE_SUBSCRIPTION,
                                'description'    => 'Payment for sms unit',
                                'transaction_id' => $txnid,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {

                            if ($user->sms_unit != '-1') {
                                $user->sms_unit += $request->sms_unit;
                                $user->save();
                            }
                            $subscription = $user->customer->activeSubscription();

                            $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                    'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                    'title'                  => 'Add '.$request->sms_unit.' sms units',
                                    'amount'                 => $request->sms_unit.' sms units',
                            ]);

                            return redirect()->route('user.home')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('user.home')->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => $status,
                    ]);
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => __('locale.payment_gateways.not_found'),
            ]);
        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.auth.user_not_exist'),
        ]);

    }


    /**
     * cancel payment
     *
     * @param  Senderid  $senderid
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function cancelledSenderIDPayment(Senderid $senderid, Request $request): RedirectResponse
    {

        $payment_method = Session::get('payment_method');

        switch ($payment_method) {
            case 'paypal':

                $token = Session::get('paypal_payment_id');
                if ($request->token == $token) {
                    return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                            'status'  => 'info',
                            'message' => __('locale.sender_id.payment_cancelled'),
                    ]);
                }
                break;

            case 'stripe':
            case 'payu':
            case 'coinpayments':
            case 'payumoney':
                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                        'status'  => 'info',
                        'message' => __('locale.sender_id.payment_cancelled'),
                ]);
        }


        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                'status'  => 'info',
                'message' => __('locale.sender_id.payment_cancelled'),
        ]);

    }

    /**
     * cancel payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function cancelledTopUpPayment(Request $request): RedirectResponse
    {

        $payment_method = Session::get('payment_method');

        switch ($payment_method) {
            case 'paypal':

                $token = Session::get('paypal_payment_id');
                if ($request->token == $token) {
                    return redirect()->route('user.home')->with([
                            'status'  => 'info',
                            'message' => __('locale.sender_id.payment_cancelled'),
                    ]);
                }
                break;

            case 'stripe':
            case 'payu':
            case 'coinpayments':
            case 'payumoney':
                return redirect()->route('user.home')->with([
                        'status'  => 'info',
                        'message' => __('locale.sender_id.payment_cancelled'),
                ]);
        }

        return redirect()->route('user.home')->with([
                'status'  => 'info',
                'message' => __('locale.sender_id.payment_cancelled'),
        ]);

    }

    /**
     * purchase sender id by braintree
     *
     * @param  Senderid  $senderid
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function braintreeSenderID(Senderid $senderid, Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'braintree')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            try {
                $gateway = new Gateway([
                        'environment' => $credentials->environment,
                        'merchantId'  => $credentials->merchant_id,
                        'publicKey'   => $credentials->public_key,
                        'privateKey'  => $credentials->private_key,
                ]);

                $result = $gateway->transaction()->sale([
                        'amount'             => $senderid->price,
                        'paymentMethodNonce' => $request->payment_method_nonce,
                        'deviceData'         => $request->device_data,
                        'options'            => [
                                'submitForSettlement' => true,
                        ],
                ]);

                if ($result->success && isset($result->transaction->id)) {
                    $invoice = Invoices::create([
                            'user_id'        => $senderid->user_id,
                            'currency_id'    => $senderid->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $senderid->price,
                            'type'           => Invoices::TYPE_SENDERID,
                            'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                            'transaction_id' => $result->transaction->id,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current                   = Carbon::now();
                        $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                        $senderid->status          = 'active';
                        $senderid->payment_claimed = true;
                        $senderid->save();

                        $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                        return redirect()->route('customer.senderid.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                        'status'  => 'error',
                        'message' => $result->message,
                ]);

            } catch (Exception $exception) {
                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * top up by braintree
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function braintreeTopUp(Request $request): RedirectResponse
    {

        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'braintree')->first();
        $user          = User::find($request->user_id);

        if ($paymentMethod && $user) {

            $credentials = json_decode($paymentMethod->options);

            try {
                $gateway = new Gateway([
                        'environment' => $credentials->environment,
                        'merchantId'  => $credentials->merchant_id,
                        'publicKey'   => $credentials->public_key,
                        'privateKey'  => $credentials->private_key,
                ]);

                $result = $gateway->transaction()->sale([
                        'amount'             => $user->customer->subscription->plan->getOption('per_unit_price') * $request->sms_unit,
                        'paymentMethodNonce' => $request->payment_method_nonce,
                        'deviceData'         => $request->device_data,
                        'options'            => [
                                'submitForSettlement' => true,
                        ],
                ]);

                if ($result->success && isset($result->transaction->id)) {

                    $invoice = Invoices::create([
                            'user_id'        => $user->id,
                            'currency_id'    => $user->customer->subscription->plan->currency->id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $user->customer->subscription->plan->getOption('per_unit_price') * $request->sms_unit,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => 'Payment for sms unit',
                            'transaction_id' => $result->transaction->id,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {

                        if ($user->sms_unit != '-1') {
                            $user->sms_unit += $request->sms_unit;
                            $user->save();
                        }

                        $subscription = $user->customer->activeSubscription();

                        $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                'title'                  => 'Add '.$request->sms_unit.' sms units',
                                'amount'                 => $request->sms_unit.' sms units',
                        ]);

                        return redirect()->route('user.home')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => $result->message,
                ]);

            } catch (Exception $exception) {
                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * paystack callback
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function paystack(Request $request): RedirectResponse
    {

        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'paystack')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);


            $curl      = curl_init();
            $reference = $request->reference;
            if ( ! $reference) {
                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => 'Reference code not found',
                ]);
            }

            curl_setopt_array($curl, [
                    CURLOPT_URL            => "https://api.paystack.co/transaction/verify/".rawurlencode($reference),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER     => [
                            "accept: application/json",
                            "authorization: Bearer ".$credentials->secret_key,
                            "cache-control: no-cache",
                    ],
            ]);

            $response = curl_exec($curl);
            $err      = curl_error($curl);

            curl_close($curl);

            if ($response === false) {
                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => 'Php curl show false value. Please contact with your provider',
                ]);
            }

            if ($err) {
                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => $err,
                ]);
            }

            $tranx = json_decode($response);

            if ( ! $tranx->status) {
                // there was an error from the API
                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => $tranx->message,
                ]);
            }

            if ('success' == $tranx->data->status) {

                $request_type = $tranx->data->metadata->request_type;

                if ($request_type == 'senderid_payment') {

                    $senderid = Senderid::findByUid($tranx->data->metadata->sender_id);

                    $invoice = Invoices::create([
                            'user_id'        => $senderid->user_id,
                            'currency_id'    => $senderid->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $senderid->price,
                            'type'           => Invoices::TYPE_SENDERID,
                            'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                            'transaction_id' => $reference,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current                   = Carbon::now();
                        $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                        $senderid->status          = 'active';
                        $senderid->payment_claimed = true;
                        $senderid->save();

                        $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                        return redirect()->route('customer.senderid.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
                if ($request_type == 'top_up_payment') {

                    $user = User::find($tranx->data->metadata->user_id);

                    $invoice = Invoices::create([
                            'user_id'        => $user->id,
                            'currency_id'    => $user->customer->subscription->plan->currency->id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $user->customer->subscription->plan->getOption('per_unit_price') * $tranx->data->metadata->sms_unit,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => 'Payment for sms unit',
                            'transaction_id' => $reference,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {

                        if ($user->sms_unit != '-1') {
                            $user->sms_unit += $tranx->data->metadata->sms_unit;
                            $user->save();
                        }
                        $subscription = $user->customer->activeSubscription();

                        $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                'title'                  => 'Add '.$tranx->data->metadata->sms_unit.' sms units',
                                'amount'                 => $tranx->data->metadata->sms_unit.' sms units',
                        ]);

                        return redirect()->route('user.home')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
                if ($request_type == 'number_payment') {

                    $number = PhoneNumbers::findByUid($tranx->data->metadata->number_id);

                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $number->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $number->price,
                            'type'           => Invoices::TYPE_NUMBERS,
                            'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                            'transaction_id' => $reference,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current               = Carbon::now();
                        $number->user_id       = auth()->user()->id;
                        $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                        $number->status        = 'assigned';
                        $number->save();

                        $this->createNotification('number', $number->number, auth()->user()->displayName());

                        return redirect()->route('customer.numbers.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.numbers.pay', $number->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
                if ($request_type == 'keyword_payment') {

                    $keyword = Keywords::findByUid($tranx->data->metadata->keyword_id);

                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $keyword->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $keyword->price,
                            'type'           => Invoices::TYPE_KEYWORD,
                            'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                            'transaction_id' => $reference,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current                = Carbon::now();
                        $keyword->user_id       = auth()->user()->id;
                        $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                        $keyword->status        = 'assigned';
                        $keyword->save();

                        $this->createNotification('keyword', $keyword->keyword_name, auth()->user()->displayName());

                        return redirect()->route('customer.keywords.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
                if ($request_type == 'subscription_payment') {

                    $plan = Plan::where('uid', $tranx->data->metadata->plan_id)->first();

                    if ($plan) {
                        $invoice = Invoices::create([
                                'user_id'        => $tranx->data->metadata->user_id,
                                'currency_id'    => $plan->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $plan->price,
                                'type'           => Invoices::TYPE_SUBSCRIPTION,
                                'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                'transaction_id' => $reference,
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


                            $user = User::find($tranx->data->metadata->user_id);

                            if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                                $user->sms_unit = $plan->getOption('sms_max');
                            } else {
                                if ($plan->getOption('add_previous_balance') == 'yes') {
                                    $user->sms_unit += $plan->getOption('sms_max');
                                } else {
                                    $user->sms_unit = $plan->getOption('sms_max');
                                }
                            }

                            $user->save();

                            $this->createNotification('plan', $plan->name, $user->displayName());

                            return redirect()->route('customer.subscriptions.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }


            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }


        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);

    }

    public function paynow(Request $request)
    {
        logger($request->all());
    }

    /**
     * purchase sender id by authorize net
     *
     * @param  Senderid  $senderid
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function authorizeNetSenderID(Senderid $senderid, Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'authorize_net')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            try {

                $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
                $merchantAuthentication->setName($credentials->login_id);
                $merchantAuthentication->setTransactionKey($credentials->transaction_key);

                // Set the transaction's refId
                $refId      = 'ref'.time();
                $cardNumber = preg_replace('/\s+/', '', $request->cardNumber);

                // Create the payment data for a credit card
                $creditCard = new AnetAPI\CreditCardType();
                $creditCard->setCardNumber($cardNumber);
                $creditCard->setExpirationDate($request->expiration_year."-".$request->expiration_month);
                $creditCard->setCardCode($request->cvv);


                // Add the payment data to a paymentType object
                $paymentOne = new AnetAPI\PaymentType();
                $paymentOne->setCreditCard($creditCard);

                // Create order information
                $order = new AnetAPI\OrderType();
                $order->setInvoiceNumber($senderid->uid);
                $order->setDescription(__('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id);


                // Set the customer's Bill To address
                $customerAddress = new AnetAPI\CustomerAddressType();
                $customerAddress->setFirstName(auth()->user()->first_name);
                $customerAddress->setLastName(auth()->user()->last_name);

                // Set the customer's identifying information
                $customerData = new AnetAPI\CustomerDataType();
                $customerData->setType("individual");
                $customerData->setId(auth()->user()->id);
                $customerData->setEmail(auth()->user()->email);


                // Create a TransactionRequestType object and add the previous objects to it
                $transactionRequestType = new AnetAPI\TransactionRequestType();
                $transactionRequestType->setTransactionType("authCaptureTransaction");
                $transactionRequestType->setAmount($senderid->price);
                $transactionRequestType->setOrder($order);
                $transactionRequestType->setPayment($paymentOne);
                $transactionRequestType->setBillTo($customerAddress);
                $transactionRequestType->setCustomer($customerData);


                // Assemble the complete transaction request
                $requests = new AnetAPI\CreateTransactionRequest();
                $requests->setMerchantAuthentication($merchantAuthentication);
                $requests->setRefId($refId);
                $requests->setTransactionRequest($transactionRequestType);

                // Create the controller and get the response
                $controller = new AnetController\CreateTransactionController($requests);
                if ($credentials->environment == 'sandbox') {
                    $result = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
                } else {
                    $result = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
                }

                if (isset($result) && $result->getMessages()->getResultCode() == 'Ok' && $result->getTransactionResponse()) {

                    $invoice = Invoices::create([
                            'user_id'        => $senderid->user_id,
                            'currency_id'    => $senderid->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $senderid->price,
                            'type'           => Invoices::TYPE_SENDERID,
                            'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                            'transaction_id' => $result->getRefId(),
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current                   = Carbon::now();
                        $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                        $senderid->status          = 'active';
                        $senderid->payment_claimed = true;
                        $senderid->save();

                        $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                        return redirect()->route('customer.senderid.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            } catch (Exception $exception) {
                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * top up by authorize net
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function authorizeNetTopUp(Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'authorize_net')->first();
        $user          = User::find($request->user_id);

        if ($paymentMethod && $user) {
            $credentials = json_decode($paymentMethod->options);

            try {

                $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
                $merchantAuthentication->setName($credentials->login_id);
                $merchantAuthentication->setTransactionKey($credentials->transaction_key);

                // Set the transaction's refId
                $refId      = 'ref'.time();
                $cardNumber = preg_replace('/\s+/', '', $request->cardNumber);

                // Create the payment data for a credit card
                $creditCard = new AnetAPI\CreditCardType();
                $creditCard->setCardNumber($cardNumber);
                $creditCard->setExpirationDate($request->expiration_year."-".$request->expiration_month);
                $creditCard->setCardCode($request->cvv);


                // Add the payment data to a paymentType object
                $paymentOne = new AnetAPI\PaymentType();
                $paymentOne->setCreditCard($creditCard);

                // Create order information
                $order = new AnetAPI\OrderType();
                $order->setInvoiceNumber(str_random(10));
                $order->setDescription('Payment for sms unit');


                // Set the customer's Bill To address
                $customerAddress = new AnetAPI\CustomerAddressType();
                $customerAddress->setFirstName(auth()->user()->first_name);
                $customerAddress->setLastName(auth()->user()->last_name);

                // Set the customer's identifying information
                $customerData = new AnetAPI\CustomerDataType();
                $customerData->setType("individual");
                $customerData->setId(auth()->user()->id);
                $customerData->setEmail(auth()->user()->email);


                // Create a TransactionRequestType object and add the previous objects to it
                $transactionRequestType = new AnetAPI\TransactionRequestType();
                $transactionRequestType->setTransactionType("authCaptureTransaction");
                $transactionRequestType->setAmount($user->customer->subscription->plan->getOption('per_unit_price') * $request->sms_unit);
                $transactionRequestType->setOrder($order);
                $transactionRequestType->setPayment($paymentOne);
                $transactionRequestType->setBillTo($customerAddress);
                $transactionRequestType->setCustomer($customerData);


                // Assemble the complete transaction request
                $requests = new AnetAPI\CreateTransactionRequest();
                $requests->setMerchantAuthentication($merchantAuthentication);
                $requests->setRefId($refId);
                $requests->setTransactionRequest($transactionRequestType);

                // Create the controller and get the response
                $controller = new AnetController\CreateTransactionController($requests);
                if ($credentials->environment == 'sandbox') {
                    $result = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
                } else {
                    $result = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
                }

                if (isset($result) && $result->getMessages()->getResultCode() == 'Ok' && $result->getTransactionResponse()) {

                    $invoice = Invoices::create([
                            'user_id'        => $user->id,
                            'currency_id'    => $user->customer->subscription->plan->currency->id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $user->customer->subscription->plan->getOption('per_unit_price') * $request->sms_unit,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => 'Payment for sms unit',
                            'transaction_id' => $result->getRefId(),
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {

                        if ($user->sms_unit != '-1') {
                            $user->sms_unit += $request->sms_unit;
                            $user->save();
                        }
                        $subscription = $user->customer->activeSubscription();

                        $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                'title'                  => 'Add '.$request->sms_unit.' sms units',
                                'amount'                 => $request->sms_unit.' sms units',
                        ]);

                        return redirect()->route('user.home')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }

                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            } catch (Exception $exception) {
                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * razorpay sender id payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function razorpaySenderID(Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'razorpay')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);
            $order_id    = Session::get('razorpay_order_id');

            if (isset($order_id) && empty($request->razorpay_payment_id) === false) {

                $senderid = Senderid::where('transaction_id', $order_id)->first();
                if ($senderid) {

                    $api        = new Api($credentials->key_id, $credentials->key_secret);
                    $attributes = [
                            'razorpay_order_id'   => $order_id,
                            'razorpay_payment_id' => $request->razorpay_payment_id,
                            'razorpay_signature'  => $request->razorpay_signature,
                    ];

                    try {

                        $response = $api->utility->verifyPaymentSignature($attributes);

                        if ($response) {
                            $invoice = Invoices::create([
                                    'user_id'        => $senderid->user_id,
                                    'currency_id'    => $senderid->currency_id,
                                    'payment_method' => $paymentMethod->id,
                                    'amount'         => $senderid->price,
                                    'type'           => Invoices::TYPE_SENDERID,
                                    'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                                    'transaction_id' => $order_id,
                                    'status'         => Invoices::STATUS_PAID,
                            ]);

                            if ($invoice) {
                                $current                   = Carbon::now();
                                $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                                $senderid->status          = 'active';
                                $senderid->payment_claimed = true;
                                $senderid->save();

                                $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                                return redirect()->route('customer.senderid.index')->with([
                                        'status'  => 'success',
                                        'message' => __('locale.payment_gateways.payment_successfully_made'),
                                ]);
                            }

                            return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);
                        }

                        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);

                    } catch (SignatureVerificationError $exception) {

                        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                'status'  => 'error',
                                'message' => $exception->getMessage(),
                        ]);
                    }
                }

                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.something_went_wrong'),
                ]);

            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * razorpay top up payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function razorpayTopUp(Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'razorpay')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);
            $order_id    = Session::get('razorpay_order_id');
            $user_id     = Session::get('user_id');
            $sms_unit    = Session::get('sms_unit');
            $price       = Session::get('price');

            $user = User::find($user_id);

            if (isset($order_id) && empty($request->razorpay_payment_id) === false && $user) {

                $api        = new Api($credentials->key_id, $credentials->key_secret);
                $attributes = [
                        'razorpay_order_id'   => $order_id,
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'razorpay_signature'  => $request->razorpay_signature,
                ];

                try {

                    $response = $api->utility->verifyPaymentSignature($attributes);

                    if ($response) {
                        $invoice = Invoices::create([
                                'user_id'        => $user->id,
                                'currency_id'    => $user->customer->subscription->plan->currency->id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $price,
                                'type'           => Invoices::TYPE_SUBSCRIPTION,
                                'description'    => 'Payment for sms unit',
                                'transaction_id' => $order_id,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {

                            if ($user->sms_unit != '-1') {
                                $user->sms_unit += $sms_unit;
                                $user->save();
                            }

                            $subscription = $user->customer->activeSubscription();

                            $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                    'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                    'title'                  => 'Add '.$sms_unit.' sms units',
                                    'amount'                 => $sms_unit.' sms units',
                            ]);


                            return redirect()->route('user.home')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('user.home')->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                } catch (SignatureVerificationError $exception) {

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => $exception->getMessage(),
                    ]);
                }
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * sslcommerz sender id payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function sslcommerzSenderID(Request $request): RedirectResponse
    {

        if (isset($request->status)) {
            if ($request->status == 'VALID') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'sslcommerz')->first();
                if ($paymentMethod) {

                    $senderid = Senderid::findByUid($request->tran_id);
                    if ($senderid) {

                        $invoice = Invoices::create([
                                'user_id'        => $senderid->user_id,
                                'currency_id'    => $senderid->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $senderid->price,
                                'type'           => Invoices::TYPE_SENDERID,
                                'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                                'transaction_id' => $request->bank_tran_id,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {
                            $current                   = Carbon::now();
                            $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                            $senderid->status          = 'active';
                            $senderid->payment_claimed = true;
                            $senderid->save();

                            $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                            return redirect()->route('customer.senderid.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $request->status,
            ]);

        }


        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * sslcommerz top up payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function sslcommerzTopUp(Request $request): RedirectResponse
    {

        if (isset($request->status)) {
            if ($request->status == 'VALID') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'sslcommerz')->first();
                $user          = User::find($request->user_id);
                if ($paymentMethod && $user) {

                    $invoice = Invoices::create([
                            'user_id'        => $user->id,
                            'currency_id'    => $user->customer->subscription->plan->currency->id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $user->customer->subscription->plan->getOption('per_unit_price') * $request->sms_unit,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => 'Payment for sms unit',
                            'transaction_id' => $request->bank_tran_id,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {

                        if ($user->sms_unit != '-1') {
                            $user->sms_unit += $request->sms_unit;
                            $user->save();
                        }

                        $subscription = $user->customer->activeSubscription();

                        $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                'title'                  => 'Add '.$request->sms_unit.' sms units',
                                'amount'                 => $request->sms_unit.' sms units',
                        ]);


                        return redirect()->route('user.home')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $request->status,
            ]);

        }


        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * aamarpay sender id payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function aamarpaySenderID(Request $request): RedirectResponse
    {
        if (isset($request->pay_status) && isset($request->mer_txnid)) {

            $senderid = Senderid::findByUid($request->mer_txnid);

            if ($request->pay_status == 'Successful') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'aamarpay')->first();
                if ($paymentMethod) {

                    if ($senderid) {

                        $invoice = Invoices::create([
                                'user_id'        => $senderid->user_id,
                                'currency_id'    => $senderid->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $senderid->price,
                                'type'           => Invoices::TYPE_SENDERID,
                                'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                                'transaction_id' => $request->pg_txnid,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {
                            $current                   = Carbon::now();
                            $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                            $senderid->status          = 'active';
                            $senderid->payment_claimed = true;
                            $senderid->save();

                            $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                            return redirect()->route('customer.senderid.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
            }

            return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                    'status'  => 'error',
                    'message' => $request->pay_status,
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * aamarpay top up payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function aamarpayTopUp(Request $request): RedirectResponse
    {
        if (isset($request->pay_status) && isset($request->mer_txnid)) {

            if ($request->pay_status == 'Successful') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'aamarpay')->first();
                $user          = User::find($request->user_id);
                if ($paymentMethod && $user) {

                    $invoice = Invoices::create([
                            'user_id'        => $user->id,
                            'currency_id'    => $user->customer->subscription->plan->currency->id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $user->customer->subscription->plan->getOption('per_unit_price') * $request->sms_unit,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => 'Payment for sms unit',
                            'transaction_id' => $request->pg_txnid,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {

                        if ($user->sms_unit != '-1') {
                            $user->sms_unit += $request->sms_unit;
                            $user->save();
                        }

                        $subscription = $user->customer->activeSubscription();

                        $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                'title'                  => 'Add '.$request->sms_unit.' sms units',
                                'amount'                 => $request->sms_unit.' sms units',
                        ]);

                        return redirect()->route('user.home')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $request->pay_status,
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * top up payments using flutter wave
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function flutterwaveTopUp(Request $request): RedirectResponse
    {
        if (isset($request->status) && isset($request->transaction_id)) {

            if ($request->status == 'successful') {

                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'flutterwave')->first();

                if ($paymentMethod) {
                    $credentials = json_decode($paymentMethod->options);

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                            CURLOPT_URL            => "https://api.flutterwave.com/v3/transactions/$request->transaction_id/verify",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING       => "",
                            CURLOPT_MAXREDIRS      => 10,
                            CURLOPT_TIMEOUT        => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST  => "GET",
                            CURLOPT_HTTPHEADER     => [
                                    "Content-Type: application/json",
                                    "Authorization: Bearer $credentials->secret_key",
                            ],
                    ]);

                    $response = curl_exec($curl);
                    curl_close($curl);

                    $get_data = json_decode($response, true);

                    if (isset($get_data) && is_array($get_data) && array_key_exists('status', $get_data)) {
                        if ($get_data['status'] == 'success') {
                            $user = User::find($request->user_id);
                            if ($user) {
                                $invoice = Invoices::create([
                                        'user_id'        => $user->id,
                                        'currency_id'    => $user->customer->subscription->plan->currency->id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $user->customer->subscription->plan->getOption('per_unit_price') * $request->sms_unit,
                                        'type'           => Invoices::TYPE_SUBSCRIPTION,
                                        'description'    => 'Payment for sms unit',
                                        'transaction_id' => $request->transaction_id,
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {

                                    if ($user->sms_unit != '-1') {
                                        $user->sms_unit += $request->sms_unit;
                                        $user->save();
                                    }

                                    $subscription = $user->customer->activeSubscription();

                                    $subscription->addTransaction(SubscriptionTransaction::TYPE_SUBSCRIBE, [
                                            'status'                 => SubscriptionTransaction::STATUS_SUCCESS,
                                            'title'                  => 'Add '.$request->sms_unit.' sms units',
                                            'amount'                 => $request->sms_unit.' sms units',
                                    ]);

                                    return redirect()->route('user.home')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('user.home')->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);
                            }

                            return redirect()->route('user.home')->with([
                                    'status'  => 'error',
                                    'message' => __('locale.auth.user_not_exist'),
                            ]);
                        }

                        return redirect()->route('user.home')->with([
                                'status'  => 'error',
                                'message' => $get_data['message'],
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => $request->status,
                    ]);

                }

                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => __('locale.payment_gateways.not_found'),
                ]);
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $request->status,
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * sender id payments using flutter wave
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function flutterwaveSenderID(Request $request): RedirectResponse
    {
        if (isset($request->status) && isset($request->transaction_id) && isset($request->tx_ref)) {

            if ($request->status == 'successful') {

                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'flutterwave')->first();

                if ($paymentMethod) {

                    $credentials = json_decode($paymentMethod->options);

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                            CURLOPT_URL            => "https://api.flutterwave.com/v3/transactions/$request->transaction_id/verify",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING       => "",
                            CURLOPT_MAXREDIRS      => 10,
                            CURLOPT_TIMEOUT        => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST  => "GET",
                            CURLOPT_HTTPHEADER     => [
                                    "Content-Type: application/json",
                                    "Authorization: Bearer $credentials->secret_key",
                            ],
                    ]);

                    $response = curl_exec($curl);
                    curl_close($curl);

                    $get_data = json_decode($response, true);

                    if (isset($get_data) && is_array($get_data) && array_key_exists('status', $get_data)) {
                        if ($get_data['status'] == 'success') {

                            $senderid = Senderid::findByUid($request->tx_ref);

                            if ($senderid) {

                                $invoice = Invoices::create([
                                        'user_id'        => $senderid->user_id,
                                        'currency_id'    => $senderid->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $senderid->price,
                                        'type'           => Invoices::TYPE_SENDERID,
                                        'description'    => __('locale.sender_id.payment_for_sender_id').' '.$senderid->sender_id,
                                        'transaction_id' => $request->transaction_id,
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {
                                    $current                   = Carbon::now();
                                    $senderid->validity_date   = $current->add($senderid->frequency_unit, $senderid->frequency_amount);
                                    $senderid->status          = 'active';
                                    $senderid->payment_claimed = true;
                                    $senderid->save();

                                    $this->createNotification('senderid', $senderid->sender_id, User::find($senderid->user_id)->displayName());

                                    return redirect()->route('customer.senderid.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);
                            }

                            return redirect()->route('customer.senderid.pay', $senderid->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);
                        }

                        return redirect()->route('user.home')->with([
                                'status'  => 'error',
                                'message' => $get_data['message'],
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => $request->status,
                    ]);

                }

                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => __('locale.payment_gateways.not_found'),
                ]);
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $request->status,
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * flutterwave number payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function flutterwaveNumbers(Request $request): RedirectResponse
    {

        if (isset($request->status) && isset($request->transaction_id) && isset($request->tx_ref)) {

            if ($request->status == 'successful') {

                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'flutterwave')->first();

                if ($paymentMethod) {

                    $credentials = json_decode($paymentMethod->options);

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                            CURLOPT_URL            => "https://api.flutterwave.com/v3/transactions/$request->transaction_id/verify",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING       => "",
                            CURLOPT_MAXREDIRS      => 10,
                            CURLOPT_TIMEOUT        => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST  => "GET",
                            CURLOPT_HTTPHEADER     => [
                                    "Content-Type: application/json",
                                    "Authorization: Bearer $credentials->secret_key",
                            ],
                    ]);

                    $response = curl_exec($curl);
                    curl_close($curl);

                    $get_data = json_decode($response, true);

                    if (isset($get_data) && is_array($get_data) && array_key_exists('status', $get_data)) {
                        if ($get_data['status'] == 'success') {

                            $number = PhoneNumbers::findByUid($request->tx_ref);

                            if ($number) {
                                $invoice = Invoices::create([
                                        'user_id'        => $get_data['data']['meta']['user_id'],
                                        'currency_id'    => $number->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $number->price,
                                        'type'           => Invoices::TYPE_NUMBERS,
                                        'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                                        'transaction_id' => $request->transaction_id,
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {
                                    $current               = Carbon::now();
                                    $number->user_id       = $get_data['data']['meta']['user_id'];
                                    $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                                    $number->status        = 'assigned';
                                    $number->save();

                                    $this->createNotification('number', $number->number, User::find($number->user_id)->displayName());

                                    return redirect()->route('customer.numbers.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);
                            }

                            return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);
                        }

                        return redirect()->route('user.home')->with([
                                'status'  => 'error',
                                'message' => $get_data['message'],
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => $request->status,
                    ]);

                }

                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => __('locale.payment_gateways.not_found'),
                ]);
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $request->status,
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * flutterwave keywords payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function flutterwaveKeywords(Request $request): RedirectResponse
    {

        if (isset($request->status) && isset($request->transaction_id) && isset($request->tx_ref)) {

            if ($request->status == 'successful') {

                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'flutterwave')->first();

                if ($paymentMethod) {

                    $credentials = json_decode($paymentMethod->options);

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                            CURLOPT_URL            => "https://api.flutterwave.com/v3/transactions/$request->transaction_id/verify",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING       => "",
                            CURLOPT_MAXREDIRS      => 10,
                            CURLOPT_TIMEOUT        => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST  => "GET",
                            CURLOPT_HTTPHEADER     => [
                                    "Content-Type: application/json",
                                    "Authorization: Bearer $credentials->secret_key",
                            ],
                    ]);

                    $response = curl_exec($curl);
                    curl_close($curl);

                    $get_data = json_decode($response, true);

                    if (isset($get_data) && is_array($get_data) && array_key_exists('status', $get_data)) {
                        if ($get_data['status'] == 'success') {

                            $keyword = Keywords::findByUid($request->tx_ref);

                            if ($keyword) {
                                $invoice = Invoices::create([
                                        'user_id'        => $get_data['data']['meta']['user_id'],
                                        'currency_id'    => $keyword->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $keyword->price,
                                        'type'           => Invoices::TYPE_KEYWORD,
                                        'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                                        'transaction_id' => $request->transaction_id,
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {
                                    $current                = Carbon::now();
                                    $keyword->user_id       = $get_data['data']['meta']['user_id'];
                                    $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                                    $keyword->status        = 'assigned';
                                    $keyword->save();

                                    $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                                    return redirect()->route('customer.keywords.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);
                            }

                            return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);
                        }

                        return redirect()->route('user.home')->with([
                                'status'  => 'error',
                                'message' => $get_data['message'],
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => $request->status,
                    ]);

                }

                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => __('locale.payment_gateways.not_found'),
                ]);
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $request->status,
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * flutterwave subscription payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function flutterwaveSubscriptions(Request $request): RedirectResponse
    {

        if (isset($request->status) && isset($request->transaction_id) && isset($request->tx_ref)) {

            if ($request->status == 'successful') {

                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'flutterwave')->first();

                if ($paymentMethod) {

                    $credentials = json_decode($paymentMethod->options);

                    $curl = curl_init();

                    curl_setopt_array($curl, [
                            CURLOPT_URL            => "https://api.flutterwave.com/v3/transactions/$request->transaction_id/verify",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING       => "",
                            CURLOPT_MAXREDIRS      => 10,
                            CURLOPT_TIMEOUT        => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST  => "GET",
                            CURLOPT_HTTPHEADER     => [
                                    "Content-Type: application/json",
                                    "Authorization: Bearer $credentials->secret_key",
                            ],
                    ]);

                    $response = curl_exec($curl);
                    curl_close($curl);

                    $get_data = json_decode($response, true);

                    if (isset($get_data) && is_array($get_data) && array_key_exists('status', $get_data)) {
                        if ($get_data['status'] == 'success') {

                            $plan = Plan::where('uid', $request->tx_ref)->first();

                            if ($plan) {
                                $invoice = Invoices::create([
                                        'user_id'        => $get_data['data']['meta']['user_id'],
                                        'currency_id'    => $plan->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $plan->price,
                                        'type'           => Invoices::TYPE_SUBSCRIPTION,
                                        'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                        'transaction_id' => $request->transaction_id,
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


                                    $user = User::find($get_data['data']['meta']['user_id']);

                                    if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                                        $user->sms_unit = $plan->getOption('sms_max');
                                    } else {
                                        if ($plan->getOption('add_previous_balance') == 'yes') {
                                            $user->sms_unit += $plan->getOption('sms_max');
                                        } else {
                                            $user->sms_unit = $plan->getOption('sms_max');
                                        }
                                    }

                                    $user->save();

                                    $this->createNotification('plan', $plan->name, $user->displayName());

                                    return redirect()->route('customer.subscriptions.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);
                            }

                            return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);
                        }

                        return redirect()->route('user.home')->with([
                                'status'  => 'error',
                                'message' => $get_data['message'],
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => $request->status,
                    ]);

                }

                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => __('locale.payment_gateways.not_found'),
                ]);
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $request->status,
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * cancel payment
     *
     * @param  PhoneNumbers  $number
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function cancelledNumberPayment(PhoneNumbers $number, Request $request): RedirectResponse
    {

        $payment_method = Session::get('payment_method');

        switch ($payment_method) {
            case 'paypal':

                $token = Session::get('paypal_payment_id');
                if ($request->token == $token) {
                    return redirect()->route('customer.numbers.pay', $number->uid)->with([
                            'status'  => 'info',
                            'message' => __('locale.sender_id.payment_cancelled'),
                    ]);
                }
                break;

            case 'stripe':
            case 'payu':
            case 'coinpayments':
            case 'payumoney':
                return redirect()->route('customer.numbers.pay', $number->uid)->with([
                        'status'  => 'info',
                        'message' => __('locale.sender_id.payment_cancelled'),
                ]);
        }


        return redirect()->route('customer.numbers.pay', $number->uid)->with([
                'status'  => 'info',
                'message' => __('locale.sender_id.payment_cancelled'),
        ]);

    }

    /**
     * purchase number by braintree
     *
     * @param  PhoneNumbers  $number
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function braintreeNumber(PhoneNumbers $number, Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'braintree')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            try {
                $gateway = new Gateway([
                        'environment' => $credentials->environment,
                        'merchantId'  => $credentials->merchant_id,
                        'publicKey'   => $credentials->public_key,
                        'privateKey'  => $credentials->private_key,
                ]);

                $result = $gateway->transaction()->sale([
                        'amount'             => $number->price,
                        'paymentMethodNonce' => $request->payment_method_nonce,
                        'deviceData'         => $request->device_data,
                        'options'            => [
                                'submitForSettlement' => true,
                        ],
                ]);

                if ($result->success && isset($result->transaction->id)) {
                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $number->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $number->price,
                            'type'           => Invoices::TYPE_NUMBERS,
                            'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                            'transaction_id' => $result->transaction->id,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current               = Carbon::now();
                        $number->user_id       = auth()->user()->id;
                        $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                        $number->status        = 'assigned';
                        $number->save();

                        $this->createNotification('number', $number->number, auth()->user()->displayName());

                        return redirect()->route('customer.numbers.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.numbers.pay', $number->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('customer.numbers.pay', $number->uid)->with([
                        'status'  => 'error',
                        'message' => $result->message,
                ]);

            } catch (Exception $exception) {
                return redirect()->route('customer.numbers.pay', $number->uid)->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('customer.numbers.pay', $number->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * successful number purchase
     *
     * @param  PhoneNumbers  $number
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function successfulNumberPayment(PhoneNumbers $number, Request $request): RedirectResponse
    {
        $payment_method = Session::get('payment_method');

        switch ($payment_method) {
            case 'paypal':
                $token = Session::get('paypal_payment_id');
                if ($request->token == $token) {
                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'paypal')->first();

                    if ($paymentMethod) {
                        $credentials = json_decode($paymentMethod->options);

                        $environment = new SandboxEnvironment($credentials->client_id, $credentials->secret);
                        $client      = new PayPalHttpClient($environment);

                        $request = new OrdersCaptureRequest($token);
                        $request->prefer('return=representation');

                        try {
                            // Call API with your client and get a response for your call
                            $response = $client->execute($request);

                            if ($response->statusCode == '201' && $response->result->status == 'COMPLETED' && isset($response->id)) {
                                $invoice = Invoices::create([
                                        'user_id'        => auth()->user()->id,
                                        'currency_id'    => $number->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $number->price,
                                        'type'           => Invoices::TYPE_NUMBERS,
                                        'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                                        'transaction_id' => $response->id,
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {
                                    $current               = Carbon::now();
                                    $number->user_id       = auth()->user()->id;
                                    $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                                    $number->status        = 'assigned';
                                    $number->save();

                                    $this->createNotification('number', $number->number, User::find($number->user_id)->displayName());

                                    return redirect()->route('customer.numbers.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);

                            }

                        } catch (Exception $ex) {
                            return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                    'status'  => 'error',
                                    'message' => $ex->getMessage(),
                            ]);
                        }


                        return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                'status'  => 'info',
                                'message' => __('locale.sender_id.payment_cancelled'),
                        ]);
                    }

                    return redirect()->route('customer.numbers.pay', $number->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.payment_gateways.not_found'),
                    ]);
                }

                return redirect()->route('customer.numbers.pay', $number->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            case 'stripe':
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'stripe')->first();
                if ($payment_method) {
                    $credentials = json_decode($paymentMethod->options);
                    $secret_key  = $credentials->secret_key;
                    $session_id  = Session::get('session_id');

                    $stripe = new StripeClient($secret_key);

                    try {
                        $response = $stripe->checkout->sessions->retrieve($session_id);

                        if ($response->payment_status == 'paid') {
                            $invoice = Invoices::create([
                                    'user_id'        => auth()->user()->id,
                                    'currency_id'    => $number->currency_id,
                                    'payment_method' => $paymentMethod->id,
                                    'amount'         => $number->price,
                                    'type'           => Invoices::TYPE_NUMBERS,
                                    'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                                    'transaction_id' => $response->payment_intent,
                                    'status'         => Invoices::STATUS_PAID,
                            ]);

                            if ($invoice) {
                                $current               = Carbon::now();
                                $number->user_id       = auth()->user()->id;
                                $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                                $number->status        = 'assigned';
                                $number->save();

                                $this->createNotification('number', $number->number, User::find($number->user_id)->displayName());

                                return redirect()->route('customer.numbers.index')->with([
                                        'status'  => 'success',
                                        'message' => __('locale.payment_gateways.payment_successfully_made'),
                                ]);
                            }

                            return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);

                        }

                    } catch (ApiErrorException $e) {
                        return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                'status'  => 'error',
                                'message' => $e->getMessage(),
                        ]);
                    }

                }

                return redirect()->route('customer.numbers.pay', $number->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.payment_gateways.not_found'),
                ]);

            case '2checkout':
            case 'payu':
            case 'coinpayments':
                $paymentMethod = PaymentMethods::where('status', true)->where('type', $payment_method)->first();

                if ($payment_method) {
                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $number->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $number->price,
                            'type'           => Invoices::TYPE_NUMBERS,
                            'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                            'transaction_id' => $number->uid,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current               = Carbon::now();
                        $number->user_id       = auth()->user()->id;
                        $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                        $number->status        = 'assigned';
                        $number->save();

                        $this->createNotification('number', $number->number, User::find($number->user_id)->displayName());

                        return redirect()->route('customer.numbers.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.numbers.pay', $number->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('customer.number.pay', $number->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.something_went_wrong'),
                ]);

            case 'paynow':
                $pollurl = Session::get('paynow_poll_url');
                if (isset($pollurl)) {
                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'paynow')->first();

                    if ($paymentMethod) {
                        $credentials = json_decode($paymentMethod->options);

                        $paynow = new Paynow(
                                $credentials->integration_id,
                                $credentials->integration_key,
                                route('customer.callback.paynow'),
                                route('customer.numbers.payment_success', $number->uid)
                        );

                        try {
                            $response = $paynow->pollTransaction($pollurl);

                            if ($response->paid()) {
                                $invoice = Invoices::create([
                                        'user_id'        => auth()->user()->id,
                                        'currency_id'    => $number->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $number->price,
                                        'type'           => Invoices::TYPE_NUMBERS,
                                        'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                                        'transaction_id' => $response->reference(),
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {
                                    $current               = Carbon::now();
                                    $number->user_id       = auth()->user()->id;
                                    $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                                    $number->status        = 'assigned';
                                    $number->save();

                                    $this->createNotification('number', $number->number, User::find($number->user_id)->displayName());

                                    return redirect()->route('customer.numbers.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);

                            }

                        } catch (Exception $ex) {
                            return redirect()->route('customer.number.pay', $number->uid)->with([
                                    'status'  => 'error',
                                    'message' => $ex->getMessage(),
                            ]);
                        }


                        return redirect()->route('customer.number.pay', $number->uid)->with([
                                'status'  => 'info',
                                'message' => __('locale.sender_id.payment_cancelled'),
                        ]);
                    }

                    return redirect()->route('customer.number.pay', $number->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.payment_gateways.not_found'),
                    ]);
                }

                return redirect()->route('customer.number.pay', $number->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            case 'instamojo':
                $payment_request_id = Session::get('payment_request_id');

                if ($request->payment_request_id == $payment_request_id) {
                    if ($request->payment_status == 'Completed') {

                        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'instamojo')->first();

                        $invoice = Invoices::create([
                                'user_id'        => auth()->user()->id,
                                'currency_id'    => $number->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $number->price,
                                'type'           => Invoices::TYPE_NUMBERS,
                                'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                                'transaction_id' => $request->payment_id,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {
                            $current               = Carbon::now();
                            $number->user_id       = auth()->user()->id;
                            $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                            $number->status        = 'assigned';
                            $number->save();

                            $this->createNotification('number', $number->number, User::find($number->user_id)->displayName());

                            return redirect()->route('customer.numbers.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);

                    }

                    return redirect()->route('customer.number.pay', $number->uid)->with([
                            'status'  => 'info',
                            'message' => $request->payment_status,
                    ]);
                }

                return redirect()->route('customer.number.pay', $number->uid)->with([
                        'status'  => 'info',
                        'message' => __('locale.payment_gateways.payment_info_not_found'),
                ]);

            case 'payumoney':

                $status      = $request->status;
                $firstname   = $request->firstname;
                $amount      = $request->amount;
                $txnid       = $request->txnid;
                $posted_hash = $request->hash;
                $key         = $request->key;
                $productinfo = $request->productinfo;
                $email       = $request->email;
                $salt        = "";

                // Salt should be same Post Request
                if (isset($request->additionalCharges)) {
                    $additionalCharges = $request->additionalCharges;
                    $retHashSeq        = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
                } else {
                    $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
                }
                $hash = hash("sha512", $retHashSeq);
                if ($hash != $posted_hash) {
                    return redirect()->route('customer.number.pay', $number->uid)->with([
                            'status'  => 'info',
                            'message' => __('locale.exceptions.invalid_action'),
                    ]);
                }

                if ($status == 'Completed') {

                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'payumoney')->first();

                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $number->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $number->price,
                            'type'           => Invoices::TYPE_NUMBERS,
                            'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                            'transaction_id' => $txnid,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current               = Carbon::now();
                        $number->user_id       = auth()->user()->id;
                        $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                        $number->status        = 'assigned';
                        $number->save();

                        $this->createNotification('number', $number->number, User::find($number->user_id)->displayName());

                        return redirect()->route('customer.numbers.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }


                    return redirect()->route('customer.number.pay', $number->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }

                return redirect()->route('customer.number.pay', $number->uid)->with([
                        'status'  => 'error',
                        'message' => $status,
                ]);
        }

        return redirect()->route('customer.number.pay', $number->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);

    }

    /**
     * purchase number by authorize net
     *
     * @param  PhoneNumbers  $number
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function authorizeNetNumber(PhoneNumbers $number, Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'authorize_net')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            try {

                $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
                $merchantAuthentication->setName($credentials->login_id);
                $merchantAuthentication->setTransactionKey($credentials->transaction_key);

                // Set the transaction's refId
                $refId      = 'ref'.time();
                $cardNumber = preg_replace('/\s+/', '', $request->cardNumber);

                // Create the payment data for a credit card
                $creditCard = new AnetAPI\CreditCardType();
                $creditCard->setCardNumber($cardNumber);
                $creditCard->setExpirationDate($request->expiration_year."-".$request->expiration_month);
                $creditCard->setCardCode($request->cvv);


                // Add the payment data to a paymentType object
                $paymentOne = new AnetAPI\PaymentType();
                $paymentOne->setCreditCard($creditCard);

                // Create order information
                $order = new AnetAPI\OrderType();
                $order->setInvoiceNumber($number->uid);
                $order->setDescription(__('locale.phone_numbers.payment_for_number').' '.$number->number);


                // Set the customer's Bill To address
                $customerAddress = new AnetAPI\CustomerAddressType();
                $customerAddress->setFirstName(auth()->user()->first_name);
                $customerAddress->setLastName(auth()->user()->last_name);

                // Set the customer's identifying information
                $customerData = new AnetAPI\CustomerDataType();
                $customerData->setType("individual");
                $customerData->setId(auth()->user()->id);
                $customerData->setEmail(auth()->user()->email);


                // Create a TransactionRequestType object and add the previous objects to it
                $transactionRequestType = new AnetAPI\TransactionRequestType();
                $transactionRequestType->setTransactionType("authCaptureTransaction");
                $transactionRequestType->setAmount($number->price);
                $transactionRequestType->setOrder($order);
                $transactionRequestType->setPayment($paymentOne);
                $transactionRequestType->setBillTo($customerAddress);
                $transactionRequestType->setCustomer($customerData);


                // Assemble the complete transaction request
                $requests = new AnetAPI\CreateTransactionRequest();
                $requests->setMerchantAuthentication($merchantAuthentication);
                $requests->setRefId($refId);
                $requests->setTransactionRequest($transactionRequestType);

                // Create the controller and get the response
                $controller = new AnetController\CreateTransactionController($requests);
                if ($credentials->environment == 'sandbox') {
                    $result = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
                } else {
                    $result = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
                }

                if (isset($result) && $result->getMessages()->getResultCode() == 'Ok' && $result->getTransactionResponse()) {

                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $number->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $number->price,
                            'type'           => Invoices::TYPE_NUMBERS,
                            'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                            'transaction_id' => $result->getRefId(),
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current               = Carbon::now();
                        $number->user_id       = auth()->user()->id;
                        $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                        $number->status        = 'assigned';
                        $number->save();

                        $this->createNotification('number', $number->number, User::find($number->user_id)->displayName());

                        return redirect()->route('customer.numbers.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.numbers.pay', $number->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('customer.numbers.pay', $number->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            } catch (Exception $exception) {
                return redirect()->route('customer.numbers.pay', $number->uid)->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('customer.numbers.pay', $number->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * razorpay number payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function razorpayNumbers(Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'razorpay')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);
            $order_id    = Session::get('razorpay_order_id');

            if (isset($order_id) && empty($request->razorpay_payment_id) === false) {

                $number = PhoneNumbers::where('transaction_id', $order_id)->first();

                if ($number) {
                    $api        = new Api($credentials->key_id, $credentials->key_secret);
                    $attributes = [
                            'razorpay_order_id'   => $order_id,
                            'razorpay_payment_id' => $request->razorpay_payment_id,
                            'razorpay_signature'  => $request->razorpay_signature,
                    ];

                    try {

                        $response = $api->utility->verifyPaymentSignature($attributes);

                        if ($response) {
                            $invoice = Invoices::create([
                                    'user_id'        => auth()->user()->id,
                                    'currency_id'    => $number->currency_id,
                                    'payment_method' => $paymentMethod->id,
                                    'amount'         => $number->price,
                                    'type'           => Invoices::TYPE_NUMBERS,
                                    'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                                    'transaction_id' => $order_id,
                                    'status'         => Invoices::STATUS_PAID,
                            ]);

                            if ($invoice) {
                                $current               = Carbon::now();
                                $number->user_id       = auth()->user()->id;
                                $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                                $number->status        = 'assigned';
                                $number->save();

                                $this->createNotification('number', $number->number, User::find($number->user_id)->displayName());

                                return redirect()->route('customer.numbers.index')->with([
                                        'status'  => 'success',
                                        'message' => __('locale.payment_gateways.payment_successfully_made'),
                                ]);
                            }

                            return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);
                        }

                        return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);

                    } catch (SignatureVerificationError $exception) {

                        return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                'status'  => 'error',
                                'message' => $exception->getMessage(),
                        ]);
                    }
                }

                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.something_went_wrong'),
                ]);

            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * sslcommerz number payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function sslcommerzNumbers(Request $request): RedirectResponse
    {

        if (isset($request->status)) {
            if ($request->status == 'VALID') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'sslcommerz')->first();
                if ($paymentMethod) {

                    $number = PhoneNumbers::findByUid($request->tran_id);

                    if ($number) {
                        $invoice = Invoices::create([
                                'user_id'        => auth()->user()->id,
                                'currency_id'    => $number->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $number->price,
                                'type'           => Invoices::TYPE_NUMBERS,
                                'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                                'transaction_id' => $request->bank_tran_id,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {
                            $current               = Carbon::now();
                            $number->user_id       = auth()->user()->id;
                            $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                            $number->status        = 'assigned';
                            $number->save();

                            $this->createNotification('number', $number->number, User::find($number->user_id)->displayName());

                            return redirect()->route('customer.numbers.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $request->status,
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * aamarpay number payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function aamarpayNumbers(Request $request): RedirectResponse
    {

        if (isset($request->pay_status) && isset($request->mer_txnid)) {

            $number = PhoneNumbers::findByUid($request->mer_txnid);

            if ($request->pay_status == 'Successful') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'aamarpay')->first();
                if ($paymentMethod) {

                    if ($number) {
                        $invoice = Invoices::create([
                                'user_id'        => auth()->user()->id,
                                'currency_id'    => $number->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $number->price,
                                'type'           => Invoices::TYPE_NUMBERS,
                                'description'    => __('locale.phone_numbers.payment_for_number').' '.$number->number,
                                'transaction_id' => $request->pg_txnid,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {
                            $current               = Carbon::now();
                            $number->user_id       = auth()->user()->id;
                            $number->validity_date = $current->add($number->frequency_unit, $number->frequency_amount);
                            $number->status        = 'assigned';
                            $number->save();

                            $this->createNotification('number', $number->number, User::find($number->user_id)->displayName());

                            return redirect()->route('customer.numbers.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.numbers.pay', $number->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('customer.numbers.pay', $number->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
            }

            return redirect()->route('customer.numbers.pay', $number->uid)->with([
                    'status'  => 'error',
                    'message' => $request->pay_status,
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * cancel payment
     *
     * @param  Keywords  $keyword
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function cancelledKeywordPayment(Keywords $keyword, Request $request): RedirectResponse
    {

        $payment_method = Session::get('payment_method');

        switch ($payment_method) {
            case 'paypal':

                $token = Session::get('paypal_payment_id');
                if ($request->token == $token) {
                    return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                            'status'  => 'info',
                            'message' => __('locale.sender_id.payment_cancelled'),
                    ]);
                }
                break;

            case 'stripe':
            case 'payu':
            case 'coinpayments':
            case 'payumoney':
                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                        'status'  => 'info',
                        'message' => __('locale.sender_id.payment_cancelled'),
                ]);
        }


        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                'status'  => 'info',
                'message' => __('locale.sender_id.payment_cancelled'),
        ]);

    }

    /**
     * purchase keyword by braintree
     *
     * @param  Keywords  $keyword
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function braintreeKeyword(Keywords $keyword, Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'braintree')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            try {
                $gateway = new Gateway([
                        'environment' => $credentials->environment,
                        'merchantId'  => $credentials->merchant_id,
                        'publicKey'   => $credentials->public_key,
                        'privateKey'  => $credentials->private_key,
                ]);

                $result = $gateway->transaction()->sale([
                        'amount'             => $keyword->price,
                        'paymentMethodNonce' => $request->payment_method_nonce,
                        'deviceData'         => $request->device_data,
                        'options'            => [
                                'submitForSettlement' => true,
                        ],
                ]);

                if ($result->success && isset($result->transaction->id)) {
                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $keyword->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $keyword->price,
                            'type'           => Invoices::TYPE_KEYWORD,
                            'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                            'transaction_id' => $result->transaction->id,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current                = Carbon::now();
                        $keyword->user_id       = auth()->user()->id;
                        $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                        $keyword->status        = 'assigned';
                        $keyword->save();

                        $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                        return redirect()->route('customer.keywords.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                        'status'  => 'error',
                        'message' => $result->message,
                ]);

            } catch (Exception $exception) {
                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * successful keyword purchase
     *
     * @param  Keywords  $keyword
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function successfulKeywordPayment(Keywords $keyword, Request $request): RedirectResponse
    {
        $payment_method = Session::get('payment_method');

        switch ($payment_method) {
            case 'paypal':
                $token = Session::get('paypal_payment_id');
                if ($request->token == $token) {
                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'paypal')->first();

                    if ($paymentMethod) {
                        $credentials = json_decode($paymentMethod->options);

                        $environment = new SandboxEnvironment($credentials->client_id, $credentials->secret);
                        $client      = new PayPalHttpClient($environment);

                        $request = new OrdersCaptureRequest($token);
                        $request->prefer('return=representation');

                        try {
                            // Call API with your client and get a response for your call
                            $response = $client->execute($request);

                            if ($response->statusCode == '201' && $response->result->status == 'COMPLETED' && isset($response->id)) {
                                $invoice = Invoices::create([
                                        'user_id'        => auth()->user()->id,
                                        'currency_id'    => $keyword->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $keyword->price,
                                        'type'           => Invoices::TYPE_KEYWORD,
                                        'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                                        'transaction_id' => $response->id,
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {
                                    $current                = Carbon::now();
                                    $keyword->user_id       = auth()->user()->id;
                                    $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                                    $keyword->status        = 'assigned';
                                    $keyword->save();

                                    $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                                    return redirect()->route('customer.keywords.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);

                            }

                        } catch (Exception $ex) {
                            return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                    'status'  => 'error',
                                    'message' => $ex->getMessage(),
                            ]);
                        }


                        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                'status'  => 'info',
                                'message' => __('locale.sender_id.payment_cancelled'),
                        ]);
                    }

                    return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.payment_gateways.not_found'),
                    ]);
                }

                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            case 'stripe':
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'stripe')->first();
                if ($payment_method) {
                    $credentials = json_decode($paymentMethod->options);
                    $secret_key  = $credentials->secret_key;
                    $session_id  = Session::get('session_id');

                    $stripe = new StripeClient($secret_key);

                    try {
                        $response = $stripe->checkout->sessions->retrieve($session_id);

                        if ($response->payment_status == 'paid') {
                            $invoice = Invoices::create([
                                    'user_id'        => auth()->user()->id,
                                    'currency_id'    => $keyword->currency_id,
                                    'payment_method' => $paymentMethod->id,
                                    'amount'         => $keyword->price,
                                    'type'           => Invoices::TYPE_KEYWORD,
                                    'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                                    'transaction_id' => $response->payment_intent,
                                    'status'         => Invoices::STATUS_PAID,
                            ]);

                            if ($invoice) {
                                $current                = Carbon::now();
                                $keyword->user_id       = auth()->user()->id;
                                $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                                $keyword->status        = 'assigned';
                                $keyword->save();

                                $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                                return redirect()->route('customer.keywords.index')->with([
                                        'status'  => 'success',
                                        'message' => __('locale.payment_gateways.payment_successfully_made'),
                                ]);
                            }

                            return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);

                        }

                    } catch (ApiErrorException $e) {
                        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                'status'  => 'error',
                                'message' => $e->getMessage(),
                        ]);
                    }

                }

                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.payment_gateways.not_found'),
                ]);

            case '2checkout':
            case 'payu':
            case 'coinpayments':
                $paymentMethod = PaymentMethods::where('status', true)->where('type', $payment_method)->first();

                if ($payment_method) {
                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $keyword->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $keyword->price,
                            'type'           => Invoices::TYPE_KEYWORD,
                            'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                            'transaction_id' => $keyword->uid,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current                = Carbon::now();
                        $keyword->user_id       = auth()->user()->id;
                        $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                        $keyword->status        = 'assigned';
                        $keyword->save();

                        $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                        return redirect()->route('customer.keywords.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.something_went_wrong'),
                ]);

            case 'paynow':
                $pollurl = Session::get('paynow_poll_url');
                if (isset($pollurl)) {
                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'paynow')->first();

                    if ($paymentMethod) {
                        $credentials = json_decode($paymentMethod->options);

                        $paynow = new Paynow(
                                $credentials->integration_id,
                                $credentials->integration_key,
                                route('customer.callback.paynow'),
                                route('customer.keywords.payment_success', $keyword->uid)
                        );

                        try {
                            $response = $paynow->pollTransaction($pollurl);

                            if ($response->paid()) {
                                $invoice = Invoices::create([
                                        'user_id'        => auth()->user()->id,
                                        'currency_id'    => $keyword->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $keyword->price,
                                        'type'           => Invoices::TYPE_KEYWORD,
                                        'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                                        'transaction_id' => $response->reference(),
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {
                                    $current                = Carbon::now();
                                    $keyword->user_id       = auth()->user()->id;
                                    $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                                    $keyword->status        = 'assigned';
                                    $keyword->save();

                                    $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                                    return redirect()->route('customer.keywords.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);

                            }

                        } catch (Exception $ex) {
                            return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                    'status'  => 'error',
                                    'message' => $ex->getMessage(),
                            ]);
                        }


                        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                'status'  => 'info',
                                'message' => __('locale.sender_id.payment_cancelled'),
                        ]);
                    }

                    return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.payment_gateways.not_found'),
                    ]);
                }

                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            case 'instamojo':
                $payment_request_id = Session::get('payment_request_id');

                if ($request->payment_request_id == $payment_request_id) {
                    if ($request->payment_status == 'Completed') {

                        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'instamojo')->first();

                        $invoice = Invoices::create([
                                'user_id'        => auth()->user()->id,
                                'currency_id'    => $keyword->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $keyword->price,
                                'type'           => Invoices::TYPE_KEYWORD,
                                'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                                'transaction_id' => $request->payment_id,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {
                            $current                = Carbon::now();
                            $keyword->user_id       = auth()->user()->id;
                            $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                            $keyword->status        = 'assigned';
                            $keyword->save();

                            $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                            return redirect()->route('customer.keywords.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);

                    }

                    return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                            'status'  => 'info',
                            'message' => $request->payment_status,
                    ]);
                }

                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                        'status'  => 'info',
                        'message' => __('locale.payment_gateways.payment_info_not_found'),
                ]);

            case 'payumoney':

                $status      = $request->status;
                $firstname   = $request->firstname;
                $amount      = $request->amount;
                $txnid       = $request->txnid;
                $posted_hash = $request->hash;
                $key         = $request->key;
                $productinfo = $request->productinfo;
                $email       = $request->email;
                $salt        = "";

                // Salt should be same Post Request
                if (isset($request->additionalCharges)) {
                    $additionalCharges = $request->additionalCharges;
                    $retHashSeq        = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
                } else {
                    $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
                }
                $hash = hash("sha512", $retHashSeq);
                if ($hash != $posted_hash) {
                    return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                            'status'  => 'info',
                            'message' => __('locale.exceptions.invalid_action'),
                    ]);
                }

                if ($status == 'Completed') {

                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'payumoney')->first();

                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $keyword->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $keyword->price,
                            'type'           => Invoices::TYPE_KEYWORD,
                            'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                            'transaction_id' => $txnid,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current                = Carbon::now();
                        $keyword->user_id       = auth()->user()->id;
                        $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                        $keyword->status        = 'assigned';
                        $keyword->save();

                        $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                        return redirect()->route('customer.keywords.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }


                    return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }

                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                        'status'  => 'error',
                        'message' => $status,
                ]);
        }

        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);

    }

    /**
     * purchase Keyword by authorize net
     *
     * @param  Keywords  $keyword
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function authorizeNetKeyword(Keywords $keyword, Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'authorize_net')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            try {

                $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
                $merchantAuthentication->setName($credentials->login_id);
                $merchantAuthentication->setTransactionKey($credentials->transaction_key);

                // Set the transaction's refId
                $refId      = 'ref'.time();
                $cardNumber = preg_replace('/\s+/', '', $request->cardNumber);

                // Create the payment data for a credit card
                $creditCard = new AnetAPI\CreditCardType();
                $creditCard->setCardNumber($cardNumber);
                $creditCard->setExpirationDate($request->expiration_year."-".$request->expiration_month);
                $creditCard->setCardCode($request->cvv);


                // Add the payment data to a paymentType object
                $paymentOne = new AnetAPI\PaymentType();
                $paymentOne->setCreditCard($creditCard);

                // Create order information
                $order = new AnetAPI\OrderType();
                $order->setInvoiceNumber($keyword->uid);
                $order->setDescription(__('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name);


                // Set the customer's Bill To address
                $customerAddress = new AnetAPI\CustomerAddressType();
                $customerAddress->setFirstName(auth()->user()->first_name);
                $customerAddress->setLastName(auth()->user()->last_name);

                // Set the customer's identifying information
                $customerData = new AnetAPI\CustomerDataType();
                $customerData->setType("individual");
                $customerData->setId(auth()->user()->id);
                $customerData->setEmail(auth()->user()->email);


                // Create a TransactionRequestType object and add the previous objects to it
                $transactionRequestType = new AnetAPI\TransactionRequestType();
                $transactionRequestType->setTransactionType("authCaptureTransaction");
                $transactionRequestType->setAmount($keyword->price);
                $transactionRequestType->setOrder($order);
                $transactionRequestType->setPayment($paymentOne);
                $transactionRequestType->setBillTo($customerAddress);
                $transactionRequestType->setCustomer($customerData);


                // Assemble the complete transaction request
                $requests = new AnetAPI\CreateTransactionRequest();
                $requests->setMerchantAuthentication($merchantAuthentication);
                $requests->setRefId($refId);
                $requests->setTransactionRequest($transactionRequestType);

                // Create the controller and get the response
                $controller = new AnetController\CreateTransactionController($requests);
                if ($credentials->environment == 'sandbox') {
                    $result = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
                } else {
                    $result = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
                }

                if (isset($result) && $result->getMessages()->getResultCode() == 'Ok' && $result->getTransactionResponse()) {

                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $keyword->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $keyword->price,
                            'type'           => Invoices::TYPE_KEYWORD,
                            'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                            'transaction_id' => $result->getRefId(),
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {
                        $current                = Carbon::now();
                        $keyword->user_id       = auth()->user()->id;
                        $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                        $keyword->status        = 'assigned';
                        $keyword->save();

                        $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                        return redirect()->route('customer.keywords.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            } catch (Exception $exception) {
                return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * razorpay Keywords payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function razorpayKeywords(Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'razorpay')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);
            $order_id    = Session::get('razorpay_order_id');

            if (isset($order_id) && empty($request->razorpay_payment_id) === false) {

                $keyword = Keywords::where('transaction_id', $order_id)->first();

                if ($keyword) {
                    $api        = new Api($credentials->key_id, $credentials->key_secret);
                    $attributes = [
                            'razorpay_order_id'   => $order_id,
                            'razorpay_payment_id' => $request->razorpay_payment_id,
                            'razorpay_signature'  => $request->razorpay_signature,
                    ];

                    try {

                        $response = $api->utility->verifyPaymentSignature($attributes);

                        if ($response) {
                            $invoice = Invoices::create([
                                    'user_id'        => auth()->user()->id,
                                    'currency_id'    => $keyword->currency_id,
                                    'payment_method' => $paymentMethod->id,
                                    'amount'         => $keyword->price,
                                    'type'           => Invoices::TYPE_KEYWORD,
                                    'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                                    'transaction_id' => $order_id,
                                    'status'         => Invoices::STATUS_PAID,
                            ]);

                            if ($invoice) {
                                $current                = Carbon::now();
                                $keyword->user_id       = auth()->user()->id;
                                $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                                $keyword->status        = 'assigned';
                                $keyword->save();

                                $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                                return redirect()->route('customer.keywords.index')->with([
                                        'status'  => 'success',
                                        'message' => __('locale.payment_gateways.payment_successfully_made'),
                                ]);
                            }

                            return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);
                        }

                        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);

                    } catch (SignatureVerificationError $exception) {

                        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                'status'  => 'error',
                                'message' => $exception->getMessage(),
                        ]);
                    }
                }

                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.something_went_wrong'),
                ]);

            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * sslcommerz keyword payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function sslcommerzKeywords(Request $request): RedirectResponse
    {

        if (isset($request->status)) {
            if ($request->status == 'VALID') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'sslcommerz')->first();
                if ($paymentMethod) {

                    $keyword = Keywords::findByUid($request->tran_id);

                    if ($keyword) {
                        $invoice = Invoices::create([
                                'user_id'        => auth()->user()->id,
                                'currency_id'    => $keyword->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $keyword->price,
                                'type'           => Invoices::TYPE_KEYWORD,
                                'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                                'transaction_id' => $request->bank_tran_id,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {
                            $current                = Carbon::now();
                            $keyword->user_id       = auth()->user()->id;
                            $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                            $keyword->status        = 'assigned';
                            $keyword->save();

                            $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                            return redirect()->route('customer.keywords.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $request->status,
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * aamarpay keyword payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function aamarpayKeywords(Request $request): RedirectResponse
    {

        if (isset($request->pay_status) && isset($request->mer_txnid)) {

            $keyword = Keywords::findByUid($request->mer_txnid);

            if ($request->pay_status == 'Successful') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'aamarpay')->first();
                if ($paymentMethod) {

                    if ($keyword) {
                        $invoice = Invoices::create([
                                'user_id'        => auth()->user()->id,
                                'currency_id'    => $keyword->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $keyword->price,
                                'type'           => Invoices::TYPE_KEYWORD,
                                'description'    => __('locale.keywords.payment_for_keyword').' '.$keyword->keyword_name,
                                'transaction_id' => $request->pg_txnid,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {
                            $current                = Carbon::now();
                            $keyword->user_id       = auth()->user()->id;
                            $keyword->validity_date = $current->add($keyword->frequency_unit, $keyword->frequency_amount);
                            $keyword->status        = 'assigned';
                            $keyword->save();

                            $this->createNotification('keyword', $keyword->keyword_name, User::find($keyword->user_id)->displayName());

                            return redirect()->route('customer.keywords.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
            }

            return redirect()->route('customer.keywords.pay', $keyword->uid)->with([
                    'status'  => 'error',
                    'message' => $request->pay_status,
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * successful subscription purchase
     *
     * @param  Plan  $plan
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function successfulSubscriptionPayment(Plan $plan, Request $request): RedirectResponse
    {
        $payment_method = Session::get('payment_method');

        switch ($payment_method) {
            case 'paypal':

                $token = Session::get('paypal_payment_id');
                if ($request->token == $token) {
                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'paypal')->first();

                    if ($paymentMethod) {
                        $credentials = json_decode($paymentMethod->options);

                        $environment = new SandboxEnvironment($credentials->client_id, $credentials->secret);
                        $client      = new PayPalHttpClient($environment);

                        $request = new OrdersCaptureRequest($token);
                        $request->prefer('return=representation');

                        try {
                            // Call API with your client and get a response for your call
                            $response = $client->execute($request);

                            if ($response->statusCode == '201' && $response->result->status == 'COMPLETED' && isset($response->id)) {
                                $invoice = Invoices::create([
                                        'user_id'        => auth()->user()->id,
                                        'currency_id'    => $plan->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $plan->price,
                                        'type'           => Invoices::TYPE_SUBSCRIPTION,
                                        'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                        'transaction_id' => $response->id,
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

                                    $user = User::find(auth()->user()->id);

                                    if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                                        $user->sms_unit = $plan->getOption('sms_max');
                                    } else {
                                        if ($plan->getOption('add_previous_balance') == 'yes') {
                                            $user->sms_unit += $plan->getOption('sms_max');
                                        } else {
                                            $user->sms_unit = $plan->getOption('sms_max');
                                        }
                                    }

                                    $user->save();

                                    $this->createNotification('plan', $plan->name, auth()->user()->displayName());

                                    return redirect()->route('customer.subscriptions.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);

                            }

                        } catch (Exception $ex) {
                            return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                    'status'  => 'error',
                                    'message' => $ex->getMessage(),
                            ]);
                        }


                        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                'status'  => 'info',
                                'message' => __('locale.sender_id.payment_cancelled'),
                        ]);
                    }

                    return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.payment_gateways.not_found'),
                    ]);
                }

                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            case 'stripe':
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'stripe')->first();
                if ($payment_method) {
                    $credentials = json_decode($paymentMethod->options);
                    $secret_key  = $credentials->secret_key;
                    $session_id  = Session::get('session_id');

                    $stripe = new StripeClient($secret_key);

                    try {
                        $response = $stripe->checkout->sessions->retrieve($session_id);

                        if ($response->payment_status == 'paid') {
                            $invoice = Invoices::create([
                                    'user_id'        => auth()->user()->id,
                                    'currency_id'    => $plan->currency_id,
                                    'payment_method' => $paymentMethod->id,
                                    'amount'         => $plan->price,
                                    'type'           => Invoices::TYPE_SUBSCRIPTION,
                                    'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                    'transaction_id' => $response->payment_intent,
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


                                $user = User::find(auth()->user()->id);

                                if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                                    $user->sms_unit = $plan->getOption('sms_max');
                                } else {
                                    if ($plan->getOption('add_previous_balance') == 'yes') {
                                        $user->sms_unit += $plan->getOption('sms_max');
                                    } else {
                                        $user->sms_unit = $plan->getOption('sms_max');
                                    }
                                }

                                $user->save();

                                $this->createNotification('plan', $plan->name, auth()->user()->displayName());


                                return redirect()->route('customer.subscriptions.index')->with([
                                        'status'  => 'success',
                                        'message' => __('locale.payment_gateways.payment_successfully_made'),
                                ]);
                            }

                            return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);

                        }

                    } catch (ApiErrorException $e) {
                        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                'status'  => 'error',
                                'message' => $e->getMessage(),
                        ]);
                    }

                }

                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.payment_gateways.not_found'),
                ]);

            case '2checkout':
            case 'payu':
            case 'coinpayments':
                $paymentMethod = PaymentMethods::where('status', true)->where('type', $payment_method)->first();

                if ($payment_method) {
                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
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


                        $user = User::find(auth()->user()->id);

                        if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                            $user->sms_unit = $plan->getOption('sms_max');
                        } else {
                            if ($plan->getOption('add_previous_balance') == 'yes') {
                                $user->sms_unit += $plan->getOption('sms_max');
                            } else {
                                $user->sms_unit = $plan->getOption('sms_max');
                            }
                        }

                        $user->save();

                        $this->createNotification('plan', $plan->name, auth()->user()->displayName());

                        return redirect()->route('customer.subscriptions.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.something_went_wrong'),
                ]);

            case 'paynow':
                $pollurl = Session::get('paynow_poll_url');
                if (isset($pollurl)) {
                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'paynow')->first();

                    if ($paymentMethod) {
                        $credentials = json_decode($paymentMethod->options);

                        $paynow = new Paynow(
                                $credentials->integration_id,
                                $credentials->integration_key,
                                route('customer.callback.paynow'),
                                route('customer.subscriptions.payment_success', $plan->uid)
                        );

                        try {
                            $response = $paynow->pollTransaction($pollurl);

                            if ($response->paid()) {

                                $invoice = Invoices::create([
                                        'user_id'        => auth()->user()->id,
                                        'currency_id'    => $plan->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $plan->price,
                                        'type'           => Invoices::TYPE_SUBSCRIPTION,
                                        'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                        'transaction_id' => $response->reference(),
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


                                    $user = User::find(auth()->user()->id);

                                    if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                                        $user->sms_unit = $plan->getOption('sms_max');
                                    } else {
                                        if ($plan->getOption('add_previous_balance') == 'yes') {
                                            $user->sms_unit += $plan->getOption('sms_max');
                                        } else {
                                            $user->sms_unit = $plan->getOption('sms_max');
                                        }
                                    }

                                    $user->save();

                                    $this->createNotification('plan', $plan->name, auth()->user()->displayName());

                                    return redirect()->route('customer.subscriptions.index')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);
                            }

                        } catch (Exception $ex) {
                            return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                    'status'  => 'error',
                                    'message' => $ex->getMessage(),
                            ]);
                        }


                        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                'status'  => 'info',
                                'message' => __('locale.sender_id.payment_cancelled'),
                        ]);
                    }

                    return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.payment_gateways.not_found'),
                    ]);
                }

                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            case 'instamojo':
                $payment_request_id = Session::get('payment_request_id');

                if ($request->payment_request_id == $payment_request_id) {
                    if ($request->payment_status == 'Completed') {

                        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'instamojo')->first();

                        $invoice = Invoices::create([
                                'user_id'        => auth()->user()->id,
                                'currency_id'    => $plan->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $plan->price,
                                'type'           => Invoices::TYPE_SUBSCRIPTION,
                                'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                'transaction_id' => $request->payment_id,
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


                            $user = User::find(auth()->user()->id);

                            if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                                $user->sms_unit = $plan->getOption('sms_max');
                            } else {
                                if ($plan->getOption('add_previous_balance') == 'yes') {
                                    $user->sms_unit += $plan->getOption('sms_max');
                                } else {
                                    $user->sms_unit = $plan->getOption('sms_max');
                                }
                            }

                            $user->save();

                            $this->createNotification('plan', $plan->name, auth()->user()->displayName());

                            return redirect()->route('customer.subscriptions.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);

                    }

                    return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                            'status'  => 'info',
                            'message' => $request->payment_status,
                    ]);
                }

                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                        'status'  => 'info',
                        'message' => __('locale.payment_gateways.payment_info_not_found'),
                ]);

            case 'payumoney':

                $status      = $request->status;
                $firstname   = $request->firstname;
                $amount      = $request->amount;
                $txnid       = $request->txnid;
                $posted_hash = $request->hash;
                $key         = $request->key;
                $productinfo = $request->productinfo;
                $email       = $request->email;
                $salt        = "";

                // Salt should be same Post Request
                if (isset($request->additionalCharges)) {
                    $additionalCharges = $request->additionalCharges;
                    $retHashSeq        = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
                } else {
                    $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
                }
                $hash = hash("sha512", $retHashSeq);
                if ($hash != $posted_hash) {
                    return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                            'status'  => 'info',
                            'message' => __('locale.exceptions.invalid_action'),
                    ]);
                }

                if ($status == 'Completed') {

                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'payumoney')->first();


                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $plan->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $plan->price,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                            'transaction_id' => $txnid,
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


                        $user = User::find(auth()->user()->id);

                        if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                            $user->sms_unit = $plan->getOption('sms_max');
                        } else {
                            if ($plan->getOption('add_previous_balance') == 'yes') {
                                $user->sms_unit += $plan->getOption('sms_max');
                            } else {
                                $user->sms_unit = $plan->getOption('sms_max');
                            }
                        }

                        $user->save();

                        $this->createNotification('plan', $plan->name, auth()->user()->displayName());

                        return redirect()->route('customer.subscriptions.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }

                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                        'status'  => 'error',
                        'message' => $status,
                ]);
        }

        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);

    }


    /**
     * cancel payment
     *
     * @param  Plan  $plan
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function cancelledSubscriptionPayment(Plan $plan, Request $request): RedirectResponse
    {

        $payment_method = Session::get('payment_method');

        switch ($payment_method) {
            case 'paypal':

                $token = Session::get('paypal_payment_id');
                if ($request->token == $token) {
                    return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                            'status'  => 'info',
                            'message' => __('locale.sender_id.payment_cancelled'),
                    ]);
                }
                break;

            case 'stripe':
            case 'payu':
            case 'coinpayments':
            case 'payumoney':
                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                        'status'  => 'info',
                        'message' => __('locale.sender_id.payment_cancelled'),
                ]);
        }


        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                'status'  => 'info',
                'message' => __('locale.sender_id.payment_cancelled'),
        ]);

    }

    /**
     * purchase sender id by braintree
     *
     * @param  Plan  $plan
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function braintreeSubscription(Plan $plan, Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'braintree')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            try {
                $gateway = new Gateway([
                        'environment' => $credentials->environment,
                        'merchantId'  => $credentials->merchant_id,
                        'publicKey'   => $credentials->public_key,
                        'privateKey'  => $credentials->private_key,
                ]);

                $result = $gateway->transaction()->sale([
                        'amount'             => $plan->price,
                        'paymentMethodNonce' => $request->payment_method_nonce,
                        'deviceData'         => $request->device_data,
                        'options'            => [
                                'submitForSettlement' => true,
                        ],
                ]);

                if ($result->success && isset($result->transaction->id)) {
                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $plan->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $plan->price,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                            'transaction_id' => $result->transaction->id,
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


                        $user = User::find(auth()->user()->id);

                        if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                            $user->sms_unit = $plan->getOption('sms_max');
                        } else {
                            if ($plan->getOption('add_previous_balance') == 'yes') {
                                $user->sms_unit += $plan->getOption('sms_max');
                            } else {
                                $user->sms_unit = $plan->getOption('sms_max');
                            }
                        }

                        $user->save();

                        $this->createNotification('plan', $plan->name, auth()->user()->displayName());

                        return redirect()->route('customer.subscriptions.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                        'status'  => 'error',
                        'message' => $result->message,
                ]);

            } catch (Exception $exception) {
                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * purchase sender id by authorize net
     *
     * @param  Plan  $plan
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function authorizeNetSubscriptions(Plan $plan, Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'authorize_net')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);

            try {

                $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
                $merchantAuthentication->setName($credentials->login_id);
                $merchantAuthentication->setTransactionKey($credentials->transaction_key);

                // Set the transaction's refId
                $refId      = 'ref'.time();
                $cardNumber = preg_replace('/\s+/', '', $request->cardNumber);

                // Create the payment data for a credit card
                $creditCard = new AnetAPI\CreditCardType();
                $creditCard->setCardNumber($cardNumber);
                $creditCard->setExpirationDate($request->expiration_year."-".$request->expiration_month);
                $creditCard->setCardCode($request->cvv);


                // Add the payment data to a paymentType object
                $paymentOne = new AnetAPI\PaymentType();
                $paymentOne->setCreditCard($creditCard);

                // Create order information
                $order = new AnetAPI\OrderType();
                $order->setInvoiceNumber($plan->uid);
                $order->setDescription(__('locale.subscription.payment_for_plan').' '.$plan->name);


                // Set the customer's Bill To address
                $customerAddress = new AnetAPI\CustomerAddressType();
                $customerAddress->setFirstName(auth()->user()->first_name);
                $customerAddress->setLastName(auth()->user()->last_name);

                // Set the customer's identifying information
                $customerData = new AnetAPI\CustomerDataType();
                $customerData->setType("individual");
                $customerData->setId(auth()->user()->id);
                $customerData->setEmail(auth()->user()->email);


                // Create a TransactionRequestType object and add the previous objects to it
                $transactionRequestType = new AnetAPI\TransactionRequestType();
                $transactionRequestType->setTransactionType("authCaptureTransaction");
                $transactionRequestType->setAmount($plan->price);
                $transactionRequestType->setOrder($order);
                $transactionRequestType->setPayment($paymentOne);
                $transactionRequestType->setBillTo($customerAddress);
                $transactionRequestType->setCustomer($customerData);


                // Assemble the complete transaction request
                $requests = new AnetAPI\CreateTransactionRequest();
                $requests->setMerchantAuthentication($merchantAuthentication);
                $requests->setRefId($refId);
                $requests->setTransactionRequest($transactionRequestType);

                // Create the controller and get the response
                $controller = new AnetController\CreateTransactionController($requests);
                if ($credentials->environment == 'sandbox') {
                    $result = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
                } else {
                    $result = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
                }

                if (isset($result) && $result->getMessages()->getResultCode() == 'Ok' && $result->getTransactionResponse()) {
                    $invoice = Invoices::create([
                            'user_id'        => auth()->user()->id,
                            'currency_id'    => $plan->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $plan->price,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                            'transaction_id' => $result->getRefId(),
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


                        $user = User::find(auth()->user()->id);

                        if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                            $user->sms_unit = $plan->getOption('sms_max');
                        } else {
                            if ($plan->getOption('add_previous_balance') == 'yes') {
                                $user->sms_unit += $plan->getOption('sms_max');
                            } else {
                                $user->sms_unit = $plan->getOption('sms_max');
                            }
                        }

                        $user->save();

                        $this->createNotification('plan', $plan->name, auth()->user()->displayName());

                        return redirect()->route('customer.subscriptions.index')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            } catch (Exception $exception) {
                return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * razorpay subscription payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function razorpaySubscriptions(Request $request): RedirectResponse
    {
        $paymentMethod = PaymentMethods::where('status', true)->where('type', 'razorpay')->first();

        if ($paymentMethod) {
            $credentials = json_decode($paymentMethod->options);
            $order_id    = Session::get('razorpay_order_id');

            if (isset($order_id) && empty($request->razorpay_payment_id) === false) {

                $plan = Plan::where('transaction_id', $order_id)->first();
                if ($plan) {

                    $api        = new Api($credentials->key_id, $credentials->key_secret);
                    $attributes = [
                            'razorpay_order_id'   => $order_id,
                            'razorpay_payment_id' => $request->razorpay_payment_id,
                            'razorpay_signature'  => $request->razorpay_signature,
                    ];

                    try {

                        $response = $api->utility->verifyPaymentSignature($attributes);

                        if ($response) {
                            $invoice = Invoices::create([
                                    'user_id'        => auth()->user()->id,
                                    'currency_id'    => $plan->currency_id,
                                    'payment_method' => $paymentMethod->id,
                                    'amount'         => $plan->price,
                                    'type'           => Invoices::TYPE_SUBSCRIPTION,
                                    'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                    'transaction_id' => $order_id,
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


                                $user = User::find(auth()->user()->id);

                                if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                                    $user->sms_unit = $plan->getOption('sms_max');
                                } else {
                                    if ($plan->getOption('add_previous_balance') == 'yes') {
                                        $user->sms_unit += $plan->getOption('sms_max');
                                    } else {
                                        $user->sms_unit = $plan->getOption('sms_max');
                                    }
                                }

                                $user->save();

                                $this->createNotification('plan', $plan->name, auth()->user()->displayName());

                                return redirect()->route('customer.subscriptions.index')->with([
                                        'status'  => 'success',
                                        'message' => __('locale.payment_gateways.payment_successfully_made'),
                                ]);
                            }

                            return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);
                        }

                        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);

                    } catch (SignatureVerificationError $exception) {

                        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                'status'  => 'error',
                                'message' => $exception->getMessage(),
                        ]);
                    }
                }

                return redirect()->route('user.home')->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.something_went_wrong'),
                ]);

            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);

        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * sslcommerz subscription payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function sslcommerzSubscriptions(Request $request): RedirectResponse
    {

        if (isset($request->status)) {
            if ($request->status == 'VALID') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'sslcommerz')->first();
                if ($paymentMethod) {

                    $plan = Plan::where('uid', $request->tran_id)->first();
                    if ($plan) {
                        $invoice = Invoices::create([
                                'user_id'        => auth()->user()->id,
                                'currency_id'    => $plan->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $plan->price,
                                'type'           => Invoices::TYPE_SUBSCRIPTION,
                                'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                'transaction_id' => $request->bank_tran_id,
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


                            $user = User::find(auth()->user()->id);

                            if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                                $user->sms_unit = $plan->getOption('sms_max');
                            } else {
                                if ($plan->getOption('add_previous_balance') == 'yes') {
                                    $user->sms_unit += $plan->getOption('sms_max');
                                } else {
                                    $user->sms_unit = $plan->getOption('sms_max');
                                }
                            }

                            $user->save();

                            $this->createNotification('plan', $plan->name, auth()->user()->displayName());

                            return redirect()->route('customer.subscriptions.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('user.home')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $request->status,
            ]);

        }


        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


    /**
     * aamarpay subscription payment
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function aamarpaySubscriptions(Request $request): RedirectResponse
    {

        if (isset($request->pay_status) && isset($request->mer_txnid)) {

            $plan = Plan::where('uid', $request->mer_txnid)->first();

            if ($request->pay_status == 'Successful') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'aamarpay')->first();
                if ($paymentMethod) {

                    if ($plan) {
                        $invoice = Invoices::create([
                                'user_id'        => auth()->user()->id,
                                'currency_id'    => $plan->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $plan->price,
                                'type'           => Invoices::TYPE_SUBSCRIPTION,
                                'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                'transaction_id' => $request->pg_txnid,
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


                            $user = User::find(auth()->user()->id);

                            if ($user->sms_unit == null || $user->sms_unit == '-1' || $plan->getOption('sms_max') == '-1') {
                                $user->sms_unit = $plan->getOption('sms_max');
                            } else {
                                if ($plan->getOption('add_previous_balance') == 'yes') {
                                    $user->sms_unit += $plan->getOption('sms_max');
                                } else {
                                    $user->sms_unit = $plan->getOption('sms_max');
                                }
                            }

                            $user->save();

                            $this->createNotification('plan', $plan->name, auth()->user()->displayName());

                            return redirect()->route('customer.subscriptions.index')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
            }

            return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                    'status'  => 'error',
                    'message' => $request->pay_status,
            ]);

        }


        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }


}
