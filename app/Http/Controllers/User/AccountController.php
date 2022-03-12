<?php

namespace App\Http\Controllers\User;

use App\Exceptions\GeneralException;
use App\Helpers\Helper;
use App\Http\Requests\Account\PayPayment;
use App\Http\Requests\Accounts\ChangePasswordRequest;
use App\Http\Requests\Accounts\UpdateUserInformationRequest;
use App\Http\Requests\Accounts\UpdateUserRequest;
use App\Http\Requests\Customer\AddUnitRequest;
use App\Models\Invoices;
use App\Models\Language;
use App\Models\Notifications;
use App\Models\PaymentMethods;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use App\Notifications\TwoFactorCode;
use Auth;
use Braintree\Gateway;
use Carbon\Carbon;
use Exception;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAccountRequest;
use App\Repositories\Contracts\AccountRepository;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Facades\Image;
use net\authorize\api\constants\ANetEnvironment;
use Paynow\Payments\Paynow;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use RuntimeException;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Session;

class AccountController extends Controller
{
    /**
     * @var AccountRepository
     */
    protected $account;


    /**
     * RegisterController constructor.
     *
     * @param  AccountRepository  $account
     */
    public function __construct(AccountRepository $account)
    {
        $this->account = $account;
    }


    /**
     * show profile page
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['name' => Auth::user()->displayName()],
        ];

        $languages = Language::where('status', 1)->get();

        $user = Auth::user();

        return view('auth.profile.index', compact('breadcrumbs', 'languages', 'user'));
    }

    /**
     * get avatar
     *
     * @return mixed
     */
    public function avatar()
    {
        if ( ! empty(Auth::user()->imagePath())) {

            try {
                $image = Image::make(Auth::user()->imagePath());
            } catch (NotReadableException $exception) {
                Auth::user()->image = null;
                Auth::user()->save();

                $image = Image::make(public_path('images/profile/profile.jpg'));
            }
        } else {
            $image = Image::make(public_path('images/profile/profile.jpg'));
        }

        return $image->response();
    }

    /**
     * update avatar
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('user.account')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $user = Auth::user();

        try {
            // Upload and save image
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Remove old images
                $user->removeImage();
                $user->image = $user->uploadImage($request->file('image'));
                $user->save();

                return redirect()->route('user.account')->with([
                        'status'  => 'success',
                        'message' => __('locale.customer.avatar_update_successful'),
                ]);
            }

            return redirect()->route('user.account')->with([
                    'status'  => 'error',
                    'message' => __('locale.exceptions.invalid_image'),
            ]);

        } catch (Exception $exception) {
            return redirect()->route('user.account')->with([
                    'status'  => 'error',
                    'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * remove avatar
     *
     * @return JsonResponse
     */
    public function removeAvatar(): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }


        $user = Auth::user();
        // Remove old images
        $user->removeImage();
        $user->image = null;
        $user->save();

        return response()->json([
                'status'  => 'success',
                'message' => __('locale.customer.avatar_remove_successful'),
        ]);
    }

    /**
     * switch view
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function switchView(Request $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {

            return redirect()->route(Helper::home_route())->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);

        }

        $user = Auth::user();

        switch ($request->portal) {
            case 'customer':
                if ($user->is_customer == 0) {
                    return redirect()->route(Helper::home_route())->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.invalid_action'),
                    ]);
                }

                $user->last_access_at = Carbon::now();

                $user->active_portal = 'customer';
                $user->save();

                $permissions = collect(json_decode($user->customer->permissions, true));
                session(['permissions' => $permissions]);

                return redirect()->route('user.home')->with([
                        'status'  => 'success',
                        'message' => __('locale.auth.welcome_come_back', ['name' => $user->displayName()]),
                ]);

            case 'admin':
                if ($user->is_admin == 0) {
                    return redirect()->route(Helper::home_route())->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.invalid_action'),
                    ]);
                }

                $user->last_access_at = Carbon::now();

                $user->active_portal = 'admin';

                $user->save();

                session(['permissions' => $user->getPermissions()]);

                return redirect()->route('admin.home')->with([
                        'status'  => 'success',
                        'message' => __('locale.auth.welcome_come_back', ['name' => $user->displayName()]),
                ]);

            default:
                return redirect()->route(Helper::home_route())->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);
        }
    }

    /**
     * profile update
     *
     * @param  UpdateUserRequest  $request
     *
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('user.account')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);

        }

        $input = $request->all();

        $data = $this->account->update($input);

        if (isset($data->getData()->status)) {
            return redirect()->route('user.account')->withInput(['tab' => 'account'])->with([
                    'status'  => $data->getData()->status,
                    'message' => $data->getData()->message,
            ]);
        }

        return redirect()->route('user.account')->withInput(['tab' => 'account'])->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);

    }


    public function changePassword(ChangePasswordRequest $request)
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('user.account')->withInput(['tab' => 'security'])->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);

        }

        Auth::user()->update([
                'password' => Hash::make($request->password),
        ]);

        Auth::logout();

        $request->session()->invalidate();

        return redirect('/login')->with([
                'status'  => 'success',
                'message' => 'Password was successfully changed',
        ]);

    }

    public function twoFactorAuthentication($status)
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('user.account')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);

        }

        $user = Auth::user();

        if ($status == 'disabled') {
            $user->update([
                    'two_factor' => false,
            ]);
        }

        if ($user->two_factor_code == null && $user->two_factor_expires_at == null) {
            $user->generateTwoFactorCode();
            $user->notify(new TwoFactorCode(route('user.account.twofactor.auth', ['status' => $status])));
        }

        return view('auth.profile._update_two_factor_auth', compact('status'));

    }

    /**
     * update two-factor auth
     *
     * @param $status
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function updateTwoFactorAuthentication($status, Request $request): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('user.account')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);

        }

        $request->validate([
                'two_factor_code' => 'integer|required|min:6',
        ]);

        $user = Auth::user();

        if ($request->input('two_factor_code') == $user->two_factor_code) {
            $user->resetTwoFactorCode();
            if ($status == 'enable') {
                $backup_codes = $user->generateTwoFactorBackUpCode();
                $user->update([
                        'two_factor'             => true,
                        'two_factor_backup_code' => $backup_codes,
                ]);

                return redirect()->route('user.account')->withInput(['tab' => 'two_factor'])->with([
                        'status'      => 'success',
                        'backup_code' => $backup_codes,
                        'message'     => 'Two-Factor Authentication was successfully enabled',
                ]);
            }

            $user->update([
                    'two_factor' => false,
            ]);

            return redirect()->route('user.account')->withInput(['tab' => 'two_factor'])->with([
                    'status'  => 'success',
                    'message' => 'Two-Factor Authentication was successfully disabled',
            ]);
        }

        return redirect()->back()->with([
                'status'  => 'error',
                'message' => __('locale.auth.two_factor_code_not_matched'),
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function generateTwoFactorAuthenticationCode(): RedirectResponse
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('user.account')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);

        }

        $user = Auth::user();

        $backup_codes = $user->generateTwoFactorBackUpCode();
        $user->update([
                'two_factor_backup_code' => $backup_codes,
        ]);

        return redirect()->back()->with([
                'status'      => 'success',
                'backup_code' => $backup_codes,
                'message'     => 'Backup codes successfully generated',
        ]);
    }

    /**
     * update information
     *
     * @param  UpdateUserInformationRequest  $request
     *
     * @return RedirectResponse
     */
    public function updateInformation(UpdateUserInformationRequest $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return redirect()->route('user.account')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);

        }

        $input = $request->except('_token');

        $customer = Auth::user()->customer;

        if (isset($input['notifications']) && count($input['notifications']) > 0) {

            $defaultNotifications = [
                    'login'        => 'no',
                    'sender_id'    => 'no',
                    'keyword'      => 'no',
                    'subscription' => 'no',
                    'promotion'    => 'no',
                    'profile'      => 'no',
            ];

            $notifications          = array_merge($defaultNotifications, $input['notifications']);
            $input['notifications'] = json_encode($notifications);
        }

        $data = $customer->update($input);

        if ($data) {
            return redirect()->route('user.account')->withInput(['tab' => 'information'])->with([
                    'status'  => 'success',
                    'message' => __('locale.customer.profile_was_successfully_updated'),
            ]);
        }

        return redirect()->route('user.account')->withInput(['tab' => 'information'])->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);
    }

    /**
     * @param  Request  $request
     *
     * @return mixed
     * @throws RuntimeException
     *
     */
    public function delete(Request $request)
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('user.account')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);

        }

        if (config('app.env') == 'demo') {
            return redirect()->route('user.account')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);

        }

        $this->account->delete();


        auth()->logout();
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->route('user.home');
    }

    public function notifications(Request $request)
    {

        $columns = [
                0 => 'responsive_id',
                1 => 'uid',
                2 => 'uid',
                3 => 'notification_type',
                4 => 'message',
                5 => 'mark_read',
                6 => 'action',
        ];

        $totalData = Notifications::where('user_id', Auth::user()->id)->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $notifications = Notifications::where('user_id', Auth::user()->id)->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');

            $notifications = Notifications::where('user_id', Auth::user()->id)->whereLike(['uid', 'notification_type', 'message'], $search)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = Notifications::where('user_id', Auth::user()->id)->whereLike(['uid', 'notification_type', 'message'], $search)->count();
        }

        $data = [];
        if ( ! empty($notifications)) {
            foreach ($notifications as $notification) {

                if ($notification->mark_read == 1) {
                    $status = 'checked';
                } else {
                    $status = '';
                }

                $nestedData['responsive_id']     = '';
                $nestedData['uid']               = $notification->uid;
                $nestedData['notification_type'] = ucfirst($notification->notification_type);
                $nestedData['message']           = $notification->message;
                $nestedData['mark_read']         = "<div class='form-check form-switch form-check-primary'>
                <input type='checkbox' class='form-check-input get_status' id='status_$notification->uid' data-id='$notification->uid' name='status' $status>
                <label class='form-check-label' for='status_$notification->uid'>
                  <span class='switch-icon-left'><i data-feather='check'></i> </span>
                  <span class='switch-icon-right'><i data-feather='x'></i> </span>
                </label>
              </div>";
                $data[]                          = $nestedData;

            }
        }

        $json_data = [
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data,
        ];

        echo json_encode($json_data);
        exit();
    }


    /**
     * mark notification status
     *
     * @param  Notifications  $notification
     *
     * @return JsonResponse
     * @throws GeneralException
     */
    public function notificationToggle(Notifications $notification): JsonResponse
    {
        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        try {

            if ($notification->update(['mark_read' => ! $notification->mark_read])) {
                return response()->json([
                        'status'  => 'success',
                        'message' => 'Notification read status was successfully changed',
                ]);
            }

            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                    'status'  => 'error',
                    'message' => $exception->getMessage(),
            ]);
        }

    }


    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */

    public function notificationBatchAction(Request $request): JsonResponse
    {

        if (config('app.env') == 'demo') {
            return response()->json([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $action = $request->get('action');
        $ids    = $request->get('ids');

        switch ($action) {
            case 'destroy':

                Notifications::where('user_id', Auth::user()->id)->whereIn('uid', $ids)->delete();

                return response()->json([
                        'status'  => 'success',
                        'message' => 'Notifications was successfully deleted',
                ]);

            case 'read':

                Notifications::where('user_id', Auth::user()->id)->whereIn('uid', $ids)->update([
                        'mark_read' => true,
                ]);

                return response()->json([
                        'status'  => 'success',
                        'message' => 'Mark notifications as read',
                ]);

        }

        return response()->json([
                'status'  => 'error',
                'message' => __('locale.exceptions.invalid_action'),
        ]);

    }

    /**
     * @param  Notifications  $notification
     *
     * @return JsonResponse
     */
    public function deleteNotification(Notifications $notification): JsonResponse
    {
        Notifications::where('uid', $notification->uid)->where('user_id', Auth::user()->id)->delete();

        return response()->json([
                'status'  => 'success',
                'message' => 'Notification was successfully deleted',
        ]);
    }

    public function topUp()
    {
        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('dashboard'), 'name' => Auth::user()->displayName()],
                ['name' => __('locale.labels.top_up')],
        ];

        return \view('customer.Accounts.top_up', compact('breadcrumbs'));

    }


    public function checkoutTopUp(AddUnitRequest $request)
    {
        $breadcrumbs = [
                ['link' => url('dashboard'), 'name' => __('locale.menu.Dashboard')],
                ['link' => url('dashboard'), 'name' => Auth::user()->displayName()],
                ['name' => __('locale.labels.top_up')],
        ];


        $pageConfigs = [
                'bodyClass' => 'ecommerce-application',
        ];

        $payment_methods = PaymentMethods::where('status', true)->cursor();

        return \view('customer.Accounts.checkout_top_up', compact('breadcrumbs', 'request', 'pageConfigs', 'payment_methods'));
    }


    public function payTopUp(PayPayment $request)
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

        $data = $this->account->payPayment($request->except('_token'));

        if (isset($data->getData()->status)) {

            if ($data->getData()->status == 'success') {

                if ($request->payment_methods == 'braintree') {
                    return view('customer.Payments.braintree', [
                            'token'    => $data->getData()->token,
                            'post_url' => route('customer.top_up.braintree', ['user_id' => Auth::user()->id, 'sms_unit' => $request->sms_unit]),
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
                            'post_url' => route('customer.top_up.authorize_net', ['user_id' => Auth::user()->id, 'sms_unit' => $request->sms_unit]),
                    ]);
                }

                if ($request->payment_methods == 'offline_payment') {
                    return view('customer.Payments.offline', [
                            'data' => $data->getData()->data,
                    ]);
                }

                return redirect()->to($data->getData()->redirect_url);
            }

            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => $data->getData()->message,
            ]);
        }

        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);

    }



    /*Version 3.1*/
    /*
    |--------------------------------------------------------------------------
    | Registration Payment
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    /**
     *
     * @param  User  $user
     * @param  Plan  $plan
     * @param  PaymentMethods  $payment_method
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function successfulRegisterPayment(User $user, Plan $plan, PaymentMethods $payment_method, Request $request): RedirectResponse
    {

        switch ($payment_method->type) {
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
                                        'user_id'        => $user->id,
                                        'currency_id'    => $plan->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $plan->price,
                                        'type'           => Invoices::TYPE_SUBSCRIPTION,
                                        'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                        'transaction_id' => $response->id,
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {

                                    $subscription                         = new Subscription();
                                    $subscription->user_id                = $user->id;
                                    $subscription->start_at               = Carbon::now();
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

                                    $user->sms_unit          = $plan->getOption('sms_max');
                                    $user->email_verified_at = Carbon::now();
                                    $user->save();

                                    return redirect()->route('user.home')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('register')->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);

                            }

                        } catch (Exception $ex) {
                            return redirect()->route('register')->with([
                                    'status'  => 'error',
                                    'message' => $ex->getMessage(),
                            ]);
                        }


                        return redirect()->route('register')->with([
                                'status'  => 'info',
                                'message' => __('locale.sender_id.payment_cancelled'),
                        ]);
                    }

                    return redirect()->route('register')->with([
                            'status'  => 'error',
                            'message' => __('locale.payment_gateways.not_found'),
                    ]);
                }

                return redirect()->route('register')->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            case 'stripe':
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'stripe')->first();

                if ($paymentMethod) {
                    $credentials = json_decode($paymentMethod->options);
                    $secret_key  = $credentials->secret_key;
                    $session_id  = Session::get('session_id');

                    $stripe = new StripeClient($secret_key);

                    try {
                        $response = $stripe->checkout->sessions->retrieve($session_id);

                        if ($response->payment_status == 'paid') {
                            $invoice = Invoices::create([
                                    'user_id'        => $user->id,
                                    'currency_id'    => $plan->currency_id,
                                    'payment_method' => $paymentMethod->id,
                                    'amount'         => $plan->price,
                                    'type'           => Invoices::TYPE_SUBSCRIPTION,
                                    'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                    'transaction_id' => $response->payment_intent,
                                    'status'         => Invoices::STATUS_PAID,
                            ]);

                            if ($invoice) {

                                $subscription                         = new Subscription();
                                $subscription->user_id                = $user->id;
                                $subscription->start_at               = Carbon::now();
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

                                $user->sms_unit = $plan->getOption('sms_max');
                                $user->email_verified_at = Carbon::now();
                                $user->save();

                                return redirect()->route('user.home')->with([
                                        'status'  => 'success',
                                        'message' => __('locale.payment_gateways.payment_successfully_made'),
                                ]);
                            }

                            return redirect()->route('register')->with([
                                    'status'  => 'error',
                                    'message' => __('locale.exceptions.something_went_wrong'),
                            ]);

                        }

                    } catch (ApiErrorException $e) {
                        return redirect()->route('register')->with([
                                'status'  => 'error',
                                'message' => $e->getMessage(),
                        ]);
                    }

                }

                return redirect()->route('register')->with([
                        'status'  => 'error',
                        'message' => __('locale.payment_gateways.not_found'),
                ]);

            case '2checkout':
            case 'payu':
            case 'coinpayments':
                $paymentMethod = PaymentMethods::where('status', true)->where('type', $payment_method->type)->first();

                if ($paymentMethod) {
                    $invoice = Invoices::create([
                            'user_id'        => $user->id,
                            'currency_id'    => $plan->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $plan->price,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                            'transaction_id' => $plan->uid,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {

                        $subscription                         = new Subscription();
                        $subscription->user_id                = $user->id;
                        $subscription->start_at               = Carbon::now();
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

                        $user->sms_unit = $plan->getOption('sms_max');
                        $user->email_verified_at = Carbon::now();
                        $user->save();


                        return redirect()->route('user.home')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('register')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('register')->with([
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
                                route('user.registers.payment_success', ['user' => $user->uid, 'plan' => $plan->uid, 'payment_method' => $paymentMethod->uid])
                        );

                        try {
                            $response = $paynow->pollTransaction($pollurl);

                            if ($response->paid()) {

                                $invoice = Invoices::create([
                                        'user_id'        => $user->id,
                                        'currency_id'    => $plan->currency_id,
                                        'payment_method' => $paymentMethod->id,
                                        'amount'         => $plan->price,
                                        'type'           => Invoices::TYPE_SUBSCRIPTION,
                                        'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                        'transaction_id' => $response->reference(),
                                        'status'         => Invoices::STATUS_PAID,
                                ]);

                                if ($invoice) {

                                    $subscription                         = new Subscription();
                                    $subscription->user_id                = $user->id;
                                    $subscription->start_at               = Carbon::now();
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

                                    $user->sms_unit = $plan->getOption('sms_max');
                                    $user->email_verified_at = Carbon::now();
                                    $user->save();


                                    return redirect()->route('user.home')->with([
                                            'status'  => 'success',
                                            'message' => __('locale.payment_gateways.payment_successfully_made'),
                                    ]);
                                }

                                return redirect()->route('register')->with([
                                        'status'  => 'error',
                                        'message' => __('locale.exceptions.something_went_wrong'),
                                ]);
                            }

                        } catch (Exception $ex) {
                            return redirect()->route('register')->with([
                                    'status'  => 'error',
                                    'message' => $ex->getMessage(),
                            ]);
                        }


                        return redirect()->route('register')->with([
                                'status'  => 'info',
                                'message' => __('locale.sender_id.payment_cancelled'),
                        ]);
                    }

                    return redirect()->route('register')->with([
                            'status'  => 'error',
                            'message' => __('locale.payment_gateways.not_found'),
                    ]);
                }

                return redirect()->route('register')->with([
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
                                'currency_id'    => $plan->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $plan->price,
                                'type'           => Invoices::TYPE_SUBSCRIPTION,
                                'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                'transaction_id' => $request->payment_id,
                                'status'         => Invoices::STATUS_PAID,
                        ]);

                        if ($invoice) {

                            $subscription                         = new Subscription();
                            $subscription->user_id                = $user->id;
                            $subscription->start_at               = Carbon::now();
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

                            $user->sms_unit = $plan->getOption('sms_max');
                            $user->email_verified_at = Carbon::now();
                            $user->save();

                            return redirect()->route('user.home')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('register')->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);

                    }

                    return redirect()->route('register')->with([
                            'status'  => 'info',
                            'message' => $request->payment_status,
                    ]);
                }

                return redirect()->route('register')->with([
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
                    return redirect()->route('register')->with([
                            'status'  => 'info',
                            'message' => __('locale.exceptions.invalid_action'),
                    ]);
                }

                if ($status == 'Completed') {

                    $paymentMethod = PaymentMethods::where('status', true)->where('type', 'payumoney')->first();


                    $invoice = Invoices::create([
                            'user_id'        => $user->id,
                            'currency_id'    => $plan->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $plan->price,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                            'transaction_id' => $txnid,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {

                        $subscription                         = new Subscription();
                        $subscription->user_id                = $user->id;
                        $subscription->start_at               = Carbon::now();
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

                        $user->sms_unit = $plan->getOption('sms_max');
                        $user->email_verified_at = Carbon::now();
                        $user->save();

                        return redirect()->route('user.home')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('register')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }

                return redirect()->route('register')->with([
                        'status'  => 'error',
                        'message' => $status,
                ]);
        }


        return redirect()->route('user.home')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * @param  User  $user
     *
     * @return RedirectResponse
     */
    public function cancelledRegisterPayment(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('register')->with([
                'status'  => 'info',
                'message' => __('locale.sender_id.payment_cancelled'),
        ]);
    }

    /**
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function braintreeRegister(Request $request): RedirectResponse
    {

        $plan = Plan::where('uid', $request->plan)->first();
        $user = User::where('uid', $request->user)->first();

        if ( ! $plan) {
            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => __('locale.payment_gateways.payment_info_not_found'),
            ]);
        }
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
                            'user_id'        => $user->id,
                            'currency_id'    => $plan->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $plan->price,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                            'transaction_id' => $result->transaction->id,
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {

                        $subscription                         = new Subscription();
                        $subscription->user_id                = $user->id;
                        $subscription->start_at               = Carbon::now();
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

                        $user->sms_unit = $plan->getOption('sms_max');
                        $user->email_verified_at = Carbon::now();
                        $user->save();

                        return redirect()->route('user.home')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('register')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('register')->with([
                        'status'  => 'error',
                        'message' => $result->message,
                ]);

            } catch (Exception $exception) {
                return redirect()->route('register')->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('register')->with([
                'status'  => 'error',
                'message' => __('locale.payment_gateways.not_found'),
        ]);
    }

    /**
     * @param  User  $user
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function authorizeNetRegister(User $user, Request $request): RedirectResponse
    {

        $plan = Plan::where('uid', $request->plan)->first();

        if ( ! $plan) {
            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => __('locale.payment_gateways.payment_info_not_found'),
            ]);
        }

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
                            'user_id'        => $user->id,
                            'currency_id'    => $plan->currency_id,
                            'payment_method' => $paymentMethod->id,
                            'amount'         => $plan->price,
                            'type'           => Invoices::TYPE_SUBSCRIPTION,
                            'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                            'transaction_id' => $result->getRefId(),
                            'status'         => Invoices::STATUS_PAID,
                    ]);

                    if ($invoice) {

                        $subscription                         = new Subscription();
                        $subscription->user_id                = $user->id;
                        $subscription->start_at               = Carbon::now();
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

                        $user->sms_unit = $plan->getOption('sms_max');
                        $user->email_verified_at = Carbon::now();
                        $user->save();

                        return redirect()->route('user.home')->with([
                                'status'  => 'success',
                                'message' => __('locale.payment_gateways.payment_successfully_made'),
                        ]);
                    }

                    return redirect()->route('register')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);

                }

                return redirect()->route('register')->with([
                        'status'  => 'error',
                        'message' => __('locale.exceptions.invalid_action'),
                ]);

            } catch (Exception $exception) {
                return redirect()->route('register')->with([
                        'status'  => 'error',
                        'message' => $exception->getMessage(),
                ]);
            }
        }

        return redirect()->route('register')->with([
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
    public function sslcommerzRegister(Request $request): RedirectResponse
    {

        if (isset($request->status)) {
            if ($request->status == 'VALID') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'sslcommerz')->first();
                if ($paymentMethod) {

                    $plan = Plan::where('uid', $request->tran_id)->first();
                    $user = User::where('uid', $request->user)->first();

                    if ($plan && $user) {
                        $invoice = Invoices::create([
                                'user_id'        => $user->id,
                                'currency_id'    => $plan->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $plan->price,
                                'type'           => Invoices::TYPE_SUBSCRIPTION,
                                'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                'transaction_id' => $request->bank_tran_id,
                                'status'         => Invoices::STATUS_PAID,
                        ]);


                        if ($invoice) {

                            $subscription                         = new Subscription();
                            $subscription->user_id                = $user->id;
                            $subscription->start_at               = Carbon::now();
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

                            $user->sms_unit = $plan->getOption('sms_max');
                            $user->email_verified_at = Carbon::now();
                            $user->save();

                            return redirect()->route('user.home')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('register')->with([
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
    public function aamarpayRegister(Request $request): RedirectResponse
    {

        if (isset($request->pay_status) && isset($request->mer_txnid)) {

            $plan = Plan::where('uid', $request->mer_txnid)->first();
            $user = User::where('uid', $request->user)->first();

            if ($request->pay_status == 'Successful') {
                $paymentMethod = PaymentMethods::where('status', true)->where('type', 'aamarpay')->first();
                if ($paymentMethod) {

                    if ($plan) {
                        $invoice = Invoices::create([
                                'user_id'        => $user->id,
                                'currency_id'    => $plan->currency_id,
                                'payment_method' => $paymentMethod->id,
                                'amount'         => $plan->price,
                                'type'           => Invoices::TYPE_SUBSCRIPTION,
                                'description'    => __('locale.subscription.payment_for_plan').' '.$plan->name,
                                'transaction_id' => $request->pg_txnid,
                                'status'         => Invoices::STATUS_PAID,
                        ]);


                        if ($invoice) {

                            $subscription                         = new Subscription();
                            $subscription->user_id                = $user->id;
                            $subscription->start_at               = Carbon::now();
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

                            $user->sms_unit = $plan->getOption('sms_max');
                            $user->email_verified_at = Carbon::now();
                            $user->save();


                            return redirect()->route('user.home')->with([
                                    'status'  => 'success',
                                    'message' => __('locale.payment_gateways.payment_successfully_made'),
                            ]);
                        }

                        return redirect()->route('register')->with([
                                'status'  => 'error',
                                'message' => __('locale.exceptions.something_went_wrong'),
                        ]);
                    }

                    return redirect()->route('register')->with([
                            'status'  => 'error',
                            'message' => __('locale.exceptions.something_went_wrong'),
                    ]);
                }
            }

            return redirect()->route('register')->with([
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
