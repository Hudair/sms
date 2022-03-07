<?php

namespace App\Http\Controllers\Customer;

use App\Http\Requests\SenderID\PayPaymentRequest;
use App\Http\Requests\Subscription\UpdatePreferencesRequest;
use App\Models\PaymentMethods;
use App\Models\Plan;
use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubscriptionController extends CustomerBaseController
{
    protected $subscriptions;

    /**
     * SubscriptionController constructor.
     *
     * @param  SubscriptionRepository  $subscriptions
     */
    public function __construct(SubscriptionRepository $subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }


    /**
     * @return Application|Factory|View
     */

    public function index()
    {

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('dashboard'), 'name' => Auth::user()->displayName()],
                ['name' => __('locale.labels.billing')],
        ];

        $subscription = Auth::user()->customer->activeSubscription();

        if ($subscription) {
            return view('customer.Accounts.index', [
                    'breadcrumbs'  => $breadcrumbs,
                    'subscription' => $subscription,
                    'plan'         => $subscription->plan,
            ]);
        }

        $plans = Plan::where('status', 1)->cursor();

        return view('customer.Accounts.plan', compact('breadcrumbs', 'plans'));

    }


    /**
     * @return Application|Factory|View
     */

    public function changePlan()
    {

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('subscriptions'), 'name' => __('locale.labels.billing')],
                ['name' => __('locale.labels.change_plan')],
        ];

        $subscription = Auth::user()->customer->activeSubscription();

        $plans = Plan::where('status', 1)->cursor();

        return view('customer.Accounts.plan', compact('breadcrumbs', 'plans', 'subscription'));

    }


    /**
     * view specific subscription logs
     *
     * @param  Subscription  $subscription
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function logs(Subscription $subscription)
    {

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('dashboard'), 'name' => Auth::user()->displayName()],
                ['name' => __('locale.menu.Subscriptions')],
        ];


        return view('admin.subscriptions.logs', compact('breadcrumbs', 'subscription'));
    }

    /**
     * renew subscription
     *
     * @param  Subscription  $subscription
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function renew(Subscription $subscription)
    {

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('subscriptions'), 'name' => __('locale.labels.billing')],
                ['name' => __('locale.labels.renew')],
        ];

        $pageConfigs = [
                'bodyClass' => 'ecommerce-application',
        ];

        $payment_methods = PaymentMethods::where('status', true)->cursor();

        return view('customer.Accounts.renew', compact('breadcrumbs', 'subscription', 'pageConfigs', 'payment_methods'));
    }

    public function renewPost(Subscription $subscription, PayPaymentRequest $request)
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('customer.subscriptions.renew', $subscription->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $plan = $subscription->plan;
        $data = $this->subscriptions->payPayment($plan, $subscription, $request->except('_token'));

        if (isset($data)) {

            if ($data->getData()->status == 'success') {

                if ($request->payment_methods == 'braintree') {
                    return view('customer.Payments.braintree', [
                            'token'    => $data->getData()->token,
                            'post_url' => route('customer.subscriptions.braintree', $plan->uid),
                    ]);
                }

                if ($request->payment_methods == 'stripe') {
                    return view('customer.Payments.stripe', [
                            'session_id'      => $data->getData()->session_id,
                            'publishable_key' => $data->getData()->publishable_key,
                    ]);
                }

                if ($request->payment_methods == 'authorize_net') {

                    $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];

                    return view('customer.Payments.authorize_net', [
                            'months'   => $months,
                            'post_url' => route('customer.subscriptions.authorize_net', $plan->uid),
                    ]);
                }

                if ($request->payment_methods == 'offline_payment') {
                    return view('customer.Payments.offline', [
                            'data' => $data->getData()->data,
                    ]);
                }

                return redirect()->to($data->getData()->redirect_url);
            }

            return redirect()->route('customer.subscriptions.renew', $subscription->uid)->with([
                    'status'  => 'error',
                    'message' => $data->getData()->message,
            ]);
        }

        return redirect()->route('customer.subscriptions.renew', $subscription->uid)->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);

    }

    /**
     * @param  Plan  $plan
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function purchase(Plan $plan)
    {

        if ($plan->price == 0) {

            if (config('app.env') == 'demo') {

                return redirect()->route('customer.subscriptions.index')->with([
                        'status'  => 'error',
                        'message' => 'Sorry! This option is not available in demo mode',
                ]);
            }

            $data = $this->subscriptions->freeSubscription($plan);

            if ($data) {
                return redirect()->route('customer.subscriptions.index')->with([
                        'status'  => $data->getData()->status,
                        'message' => $data->getData()->message,
                ]);
            }

            return redirect()->route('customer.subscriptions.index')->with([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.something_went_wrong'),
            ]);
        }

        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('subscriptions'), 'name' => __('locale.labels.billing')],
                ['name' => __('locale.labels.purchase')],
        ];

        $pageConfigs = [
                'bodyClass' => 'ecommerce-application',
        ];

        $payment_methods = PaymentMethods::where('status', true)->cursor();

        return view('customer.Accounts.purchase', compact('breadcrumbs', 'plan', 'pageConfigs', 'payment_methods'));
    }

    /**
     * cancelled subscription
     *
     * @param  Subscription  $subscription
     *
     * @return JsonResponse
     */
    public function cancel(Subscription $subscription): JsonResponse
    {

        if (config('app.env') == 'demo') {

            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {
            $subscription->setEnded(Auth::user()->id);

            return response()->json([
                    'status'  => 'success',
                    'message' => __('locale.subscription.log_cancelled', [
                            'plan' => $subscription->plan->name,
                    ]),
            ]);

        } catch (Exception $exception) {

            return response()->json([
                    'status'  => 'success',
                    'message' => $exception->getMessage(),
            ]);
        }
    }


    public function checkoutPurchase(Plan $plan, Subscription $subscription, PayPaymentRequest $request)
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $data = $this->subscriptions->payPayment($plan, $subscription, $request->except('_token'));

        if (isset($data)) {

            if ($data->getData()->status == 'success') {

                if ($request->payment_methods == 'braintree') {
                    return view('customer.Payments.braintree', [
                            'token'    => $data->getData()->token,
                            'post_url' => route('customer.subscriptions.braintree', $plan->uid),
                    ]);
                }

                if ($request->payment_methods == 'stripe') {
                    return view('customer.Payments.stripe', [
                            'session_id'      => $data->getData()->session_id,
                            'publishable_key' => $data->getData()->publishable_key,
                    ]);
                }

                if ($request->payment_methods == 'authorize_net') {

                    $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];

                    return view('customer.Payments.authorize_net', [
                            'months'   => $months,
                            'post_url' => route('customer.subscriptions.authorize_net', $plan->uid),
                    ]);
                }

                if ($request->payment_methods == 'offline_payment') {
                    return view('customer.Payments.offline', [
                            'data' => $data->getData()->data,
                    ]);
                }

                return redirect()->to($data->getData()->redirect_url);
            }

            return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                    'status'  => 'error',
                    'message' => $data->getData()->message,
            ]);
        }

        return redirect()->route('customer.subscriptions.purchase', $plan->uid)->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);

    }


    /**
     * update preferences
     *
     * @param  Subscription  $subscription
     * @param  UpdatePreferencesRequest  $request
     *
     * @return RedirectResponse
     */
    public function preferences(Subscription $subscription, UpdatePreferencesRequest $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('customer.subscriptions.index')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        if ($request->end_period_last_days) {
            $subscription->update([
                    'end_period_last_days' => $request->end_period_last_days,
            ]);
        }

        $input = $request->except('_token', 'end_period_last_days');

        if (empty($request->credit_warning)) {
            $input['credit_warning'] = false;
        } else {
            $input['credit_warning'] = true;
        }

        if (empty($request->subscription_warning)) {
            $input['subscription_warning'] = false;
        } else {
            $input['subscription_warning'] = true;
        }

        $subscription->updateOptions($input);

        return redirect()->route('customer.subscriptions.index')->with([
                'status'  => 'success',
                'message' => __('locale.subscription.preferences_successfully_updated'),
        ]);
    }
}
