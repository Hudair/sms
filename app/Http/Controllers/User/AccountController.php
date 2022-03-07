<?php

namespace App\Http\Controllers\User;

use App\Exceptions\GeneralException;
use App\Helpers\Helper;
use App\Http\Requests\Account\PayPayment;
use App\Http\Requests\Accounts\ChangePasswordRequest;
use App\Http\Requests\Accounts\UpdateUserInformationRequest;
use App\Http\Requests\Accounts\UpdateUserRequest;
use App\Http\Requests\Customer\AddUnitRequest;
use App\Models\Language;
use App\Models\Notifications;
use App\Models\PaymentMethods;
use App\Notifications\TwoFactorCode;
use Auth;
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
use RuntimeException;

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

        if ($data) {
            return redirect()->route('user.account')->with([
                    'status'  => $data->getData()->status,
                    'message' => $data->getData()->message,
            ]);
        }

        return redirect()->route('user.account')->with([
                'status'  => 'error',
                'message' => __('locale.exceptions.something_went_wrong'),
        ]);

    }


    public function changePassword(ChangePasswordRequest $request)
    {
        if (config('app.env') == 'demo') {
            return redirect()->route('user.account')->with([
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
     * update two factor auth
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

                return redirect()->route('user.account')->with([
                        'status'      => 'success',
                        'backup_code' => $backup_codes,
                        'message'     => 'Two-Factor Authentication was successfully enabled',
                ]);
            }

            $user->update([
                    'two_factor' => false,
            ]);

            return redirect()->route('user.account')->with([
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
            return redirect()->route('user.account')->with([
                    'status'  => 'success',
                    'message' => __('locale.customer.profile_was_successfully_updated'),
            ]);
        }

        return redirect()->route('user.account')->with([
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
                0 => 'uid',
                1 => 'notification_type',
                2 => 'message',
                3 => 'mark_read',
                4 => 'uid',
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


                $nestedData['uid']               = $notification->uid;
                $nestedData['notification_type'] = ucfirst($notification->notification_type);
                $nestedData['message']           = $notification->message;
                $nestedData['mark_read']         = "<div class='custom-control custom-switch switch-lg custom-switch-success'>
                <input type='checkbox' class='custom-control-input get_status' id='status_$notification->uid' data-id='$notification->uid' name='status' $status>
                <label class='custom-control-label' for='status_$notification->uid'>
                  <span class='switch-text-left'>".__('locale.labels.read')."</span>
                  <span class='switch-text-right'>Unread</span>
                </label>
              </div>";
                $nestedData['action']            = "<span class='action-delete text-danger' data-id='$notification->uid'><i class='feather us-2x icon-trash'></i></span>";
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
        if (config('app.env') == 'demo') {
            return redirect()->route('user.home')->with([
                    'status'  => 'error',
                    'message' => 'Sorry! This option is not available in demo mode',
            ]);
        }

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

        if (isset($data)) {

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
}
