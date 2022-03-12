<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\PaymentMethods;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use App\Models\SubscriptionTransaction;
use App\Repositories\Contracts\AccountRepository;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Rules\Phone;
use Arcanedev\NoCaptcha\Rules\CaptchaRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Stripe\Exception\ApiErrorException;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default, this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * @var AccountRepository
     */
    protected $account;

    protected $subscriptions;

    /**
     * RegisterController constructor.
     *
     * @param  AccountRepository  $account
     * @param  SubscriptionRepository  $subscriptions
     */
    public function __construct(AccountRepository $account, SubscriptionRepository $subscriptions)
    {
        $this->middleware('guest');
        $this->account       = $account;
        $this->subscriptions = $subscriptions;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
                'first_name' => ['required', 'string', 'max:255'],
                'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password'   => ['required', 'string', 'min:8', 'confirmed'],
                'phone'      => ['required', new Phone($data['phone'])],
                'timezone'   => ['required', 'timezone'],
                'address'    => ['required', 'string'],
                'city'       => ['required', 'string'],
                'country'    => ['required', 'string'],
                'plans'      => ['required'],
                'locale'     => ['required', 'string', 'min:2', 'max:2'],
        ];

        if (config('no-captcha.registration')) {
            $rules['g-recaptcha-response'] = ['required', new CaptchaRule()];
        }

        return Validator::make($data, $rules);
    }

    /**
     * @throws ApiErrorException
     */
    public function register(Request $request)
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('login')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $data = $request->except('_token');

        $rules = [
                'first_name' => ['required', 'string', 'max:255'],
                'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password'   => ['required', 'string', 'min:8', 'confirmed'],
                'phone'      => ['required', new Phone($request->phone)],
                'timezone'   => ['required', 'timezone'],
                'address'    => ['required', 'string'],
                'city'       => ['required', 'string'],
                'country'    => ['required', 'string'],
                'plans'      => ['required'],
                'locale'     => ['required', 'string', 'min:2', 'max:2'],
        ];

        if (config('no-captcha.registration')) {
            $rules['g-recaptcha-response'] = ['required', new CaptchaRule()];
        }

        $v = Validator::make($data, $rules);

        if ($v->fails()) {
            return redirect()->route('register')->withInput()->withErrors($v->errors());
        }

        $plan = Plan::find($data['plans']);
        if ($plan->price == 0.00) {

            $user = $this->account->register($data);

            $subscription                         = new Subscription();
            $subscription->user_id                = $user->id;
            $subscription->start_at               = Carbon::now();
            $subscription->status                 = Subscription::STATUS_ACTIVE;
            $subscription->plan_id                = $plan->getBillableId();
            $subscription->end_period_last_days   = '10';
            $subscription->current_period_ends_at = $subscription->getPeriodEndsAt(Carbon::now());
            $subscription->end_at                 = null;
            $subscription->end_by                 = null;
            $subscription->payment_method_id      = null;
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

            $user->sms_unit = $plan->getOption('sms_max');
            $user->save();

            if (config('account.verify_account')) {
                $user->sendEmailVerificationNotification();
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'success',
                    'message' => __('locale.payment_gateways.payment_successfully_made'),
            ]);

        }

        $user         = $this->account->register($data);
        $user->email_verified_at = Carbon::now();
        $user->save();
        $callback_data = $this->subscriptions->payRegisterPayment($plan, $data, $user);

        if (isset($callback_data->getData()->status)) {

            if ($callback_data->getData()->status == 'success') {

                if ($data['payment_methods'] == 'braintree') {
                    return view('auth.payment.braintree', [
                            'token'    => $callback_data->getData()->token,
                            'post_url' => route('user.registers.braintree', $plan->uid),
                    ]);
                }

                if ($data['payment_methods'] == 'stripe') {
                    return view('auth.payment.stripe', [
                            'session_id'      => $callback_data->getData()->session_id,
                            'publishable_key' => $callback_data->getData()->publishable_key,
                    ]);
                }

                if ($data['payment_methods'] == 'authorize_net') {

                    $months = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];

                    return view('auth.payment.authorize_net', [
                            'months'   => $months,
                            'post_url' => route('user.registers.authorize_net', ['user' => $user->uid, 'plan' => $plan->uid]),
                    ]);
                }

                if ($data['payment_methods'] == 'offline_payment') {
                    return view('auth.payment.offline', [
                            'data' => $callback_data->getData()->data,
                    ]);
                }

                return redirect()->to($callback_data->getData()->redirect_url);
            }

            return redirect()->route('register')->with([
                    'status'  => 'error',
                    'message' => $callback_data->getData()->message,
            ]);
        }

        return redirect()->route('register')->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);

    }

    // Register
    public function showRegistrationForm()
    {
        $pageConfigs     = [
                'blankPage' => true,
        ];
        $languages       = Language::where('status', 1)->get();
        $plans           = Plan::where('status', true)->where('show_in_customer', true)->cursor();
        $payment_methods = PaymentMethods::where('status', 1)->get();

        return view('/auth/register', [
                'pageConfigs'     => $pageConfigs,
                'languages'       => $languages,
                'plans'           => $plans,
                'payment_methods' => $payment_methods,
        ]);
    }

}
